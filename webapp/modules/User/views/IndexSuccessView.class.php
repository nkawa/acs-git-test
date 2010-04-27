<?php
/**
 * マイページ機能　Viewクラス
 * TOP画面
 * @package  acs/webapp/modules/User/views
 * IndexView::SUCCESS
 * @author   kuwayama v 1.23 2006/03/06 06:35:28
 * @update   akitsu
 * @since	PHP 4.0
 */
// $Id: IndexView::SUCCESS.class.php,v 1.39 2008/03/24 07:00:36 y-yuki Exp $

class IndexSuccessView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$controller = $context->getController();

		// index用CSSファイル読み込み
		$this->clearCSSFile();
		$this->setSelectionCSSFile($request->getAttribute('selection_css'));

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$is_self_page = $request->getAttribute('is_self_page');
		$peruse_mode = 9;

		$user_community_id = $request->getAttribute('user_community_id');
		if ($acs_user_info_row['is_acs_user']) {
			$peruse_mode = 1;	//ログインユーザです
		}

		// ラストログイン
		$last_login = $request->getAttribute('last_login');

		if($acs_user_info_row['is_acs_user']) {
			if($last_login == ""){
				$last_login = ACSMsg::get_msg('User', 'IndexSuccessView.class.php','M001');
			}else{
				$last_login = ACSLib::convert_pg_date_to_str($last_login);
			}
		} else {
			$last_login = "";
		}

		// マイフレンズ表示件数
		$my_friends_display_max = 
			ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'FRIENDS_DISPLAY_MAX_COUNT');

		// マイコミュニティ表示件数
		$my_community_display_max = 
			ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'COMMUNITY_DISPLAY_MAX_COUNT');


		$profile = $request->getAttribute('target_user_info_row');

		// 画像ファイルのパスを設定
		$profile['image_url'] = ACSUser::get_image_url($user_community_id);

		// マイフレンズの設定
		$friends_row_array = $request->getAttribute('friends_row_array');
		$friends_row_array_num = count($friends_row_array);
		$friends_row_array = array_slice($friends_row_array, 0, $my_friends_display_max);
		foreach ($friends_row_array as $index => $friends_row) {
			$friends_row_array[$index]['top_page_url'] = 
				$this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $friends_row['user_community_id'];
			$friends_row_array[$index]['image_url'] = ACSUser::get_image_url($friends_row['user_community_id'], 'thumb');
			// マイフレンズ人数
			$friends_row_array[$index]['friends_row_array_num'] = ACSUser::get_friends_row_array_num($friends_row['user_community_id']);
		}

		// マイコミュニティの設定
		$community_row_array = $request->getAttribute('community_row_array');
		$community_row_array_num = count($community_row_array);
		$community_list = array();
		$array_count = 0;
		foreach ($community_row_array as $index => $community_row) {
			$community_data['community_name'] = $community_row['community_name'];
			$community_data['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];
			$community_data['image_url'] = ACSCommunity::get_image_url($community_row['community_id'], 'thumb');
			$community_data['community_member_num'] = ACSCommunity::get_community_member_num($community_row['community_id']);

			// メンバでない非公開コミュニティは表示対象にしない
			if ($community_row['contents_row_array']['self']['open_level_name'] == ACSMsg::get_mst('open_level_master','D03') && !$community_row['is_community_member']) {
				continue;
			}

			array_push($community_list, $community_data);

			$array_count++;
			if ($array_count == $my_community_display_max) {
				break;
			}
		}

		// メニュー設定
		$menu = array();
		$menu['diary_url'] = $this->getControllerPath("User", 'Diary') . '&id=' . $user_community_id;
		$menu['folder_url'] = $this->getControllerPath("User", 'Folder') . '&id=' . $user_community_id;
		
		if ($acs_user_info_row['user_community_id'] == $profile['user_community_id'] && !$acs_user_info_row['is_ldap_user']) {
			$menu['change_password_url'] = $this->getControllerPath("User", 'ChangePassword') . '&id=' . $user_community_id;
		}

		if ($acs_user_info_row['user_community_id'] == $profile['user_community_id']) {
			$menu['image_change_url'] = $this->getControllerPath("User", 'EditProfileImage') . '&id=' . $user_community_id;
		}


		// マイプロフィール編集 自分自身
		if($acs_user_info_row['user_id'] == $profile['user_id']){
			$profile_edit_url = $this->getControllerPath("User", 'EditProfile') . '&id=' . $user_community_id;
			$profile_view_url = $this->getControllerPath("User", 'ProfileCheck') . '&id=' . $user_community_id .'&view_mode=0';
			// 足跡確認URL
			$footprint_url = $this->getControllerPath("User", 'FootprintCheck') . 
								'&id=' . $user_community_id;

			// デザイン選択URL
			$select_design_url = $this->getControllerPath("User", 'SelectDesign') . 
								'&id=' . $user_community_id;

			// バックアップURL
			$backup_url = $this->getControllerPath("User", 'ContentsBackup') . 
								'&id=' . $user_community_id;
			
			// メッセージURL追加
			$message_box_url = $this->getControllerPath("User", 'MessageBox') . 
								'&id=' . $user_community_id;

			$peruse_mode = 1;
		}else {
			if (ACSUser::is_friends($acs_user_info_row['user_community_id'], $user_community_id)) {
				$peruse_mode = 2; // 友人
			}
		}

		// メッセージを送るボタン追加
		$message_btn_url = $this->getControllerPath("User", 'Message') . '&id=' . $user_community_id;

		// マイフレンズ追加
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$is_friends = ACSUser::is_friends($acs_user_info_row['user_community_id'], $target_user_info_row['user_community_id']);
		if (!$is_friends && $acs_user_info_row['is_acs_user']) {
			$add_myfriends_url = $this->getControllerPath("User", 'AddFriends') . '&id=' . $user_community_id;
		}

		// マイフレンズ一覧URL
		$friends_list_url = $this->getControllerPath("User", 'FriendsList') . '&id=' . $user_community_id;

		if ($acs_user_info_row['user_id'] == $target_user_info_row['user_id']) {
			// マイフレンズグループ一覧URL
			$friends_group_list_url = $this->getControllerPath("User", 'FriendsGroupList') . '&id=' . $target_user_info_row['user_community_id'];
		}

		// マイコミュニティURL
		$community_list_url = $this->getControllerPath("User", 'CommunityList') . '&id=' . $user_community_id;

		if ($is_self_page) {
			// 待機: マイフレンズ追加
			$waiting_for_add_friends_row_array = $request->getAttribute('waiting_for_add_friends_row_array');
			$waiting_for_add_friends_row_array_num = count($waiting_for_add_friends_row_array);
			if ($waiting_for_add_friends_row_array_num) {
				// マイフレンズ追加 承認待ち URL
				$waiting_list_for_add_friends_url = $this->getControllerPath("User", 'WaitingList')
					 . '&id=' . $user_community_id
					 . '&waiting_type_code=' . $waiting_for_add_friends_row_array[0]['waiting_type_code']
					 . '&waiting_status_code=' . $waiting_for_add_friends_row_array[0]['waiting_status_code'];
			}

			// 待機: コミュニティ参加
			$waiting_for_join_community_row_array_array = $request->getAttribute('waiting_for_join_community_row_array_array');
			foreach ($waiting_for_join_community_row_array_array as $index => $waiting_for_join_community_row_array) {
				$waiting_for_join_community_row_array_array[$index]['waiting_for_join_community_row_array_num'] = count($waiting_for_join_community_row_array['waiting_row_array']);
				$waiting_for_join_community_row_array_array[$index]['waiting_list_for_join_community_url'] = $this->getControllerPath('Community', 'WaitingList')
					 . '&community_id=' . $waiting_for_join_community_row_array['community_row']['community_id']
					 . '&waiting_type_code=' . $waiting_for_join_community_row_array['waiting_row_array'][0]['waiting_type_code']
					 . '&waiting_status_code=' . $waiting_for_join_community_row_array['waiting_row_array'][0]['waiting_status_code'];
			}

			// 待機: 親コミュニティ追加
			$waiting_for_parent_community_link_row_array_array = $request->getAttribute('waiting_for_parent_community_link_row_array_array');
			foreach ($waiting_for_parent_community_link_row_array_array as $index => $waiting_for_parent_community_link_row_array) {
				$waiting_for_parent_community_link_row_array_array[$index]['waiting_for_parent_community_link_row_array_num'] = count($waiting_for_parent_community_link_row_array['waiting_row_array']);
				$waiting_for_parent_community_link_row_array_array[$index]['waiting_list_for_parent_community_link_url'] = $this->getControllerPath('Community', 'WaitingList')
					 . '&community_id=' . $waiting_for_parent_community_link_row_array['community_row']['community_id']
					 . '&waiting_type_code=' . $waiting_for_parent_community_link_row_array['waiting_row_array'][0]['waiting_type_code']
					 . '&waiting_status_code=' . $waiting_for_parent_community_link_row_array['waiting_row_array'][0]['waiting_status_code'];
			}
			// 待機: サブコミュニティ追加
			$waiting_for_sub_community_link_row_array_array = $request->getAttribute('waiting_for_sub_community_link_row_array_array');
			foreach ($waiting_for_sub_community_link_row_array_array as $index => $waiting_for_sub_community_link_row_array) {
				$waiting_for_sub_community_link_row_array_array[$index]['waiting_for_sub_community_link_row_array_num'] = count($waiting_for_sub_community_link_row_array['waiting_row_array']);
				$waiting_for_sub_community_link_row_array_array[$index]['waiting_list_for_sub_community_link_url'] = $this->getControllerPath('Community', 'WaitingList')
					 . '&community_id=' . $waiting_for_sub_community_link_row_array['community_row']['community_id']
					 . '&waiting_type_code=' . $waiting_for_sub_community_link_row_array['waiting_row_array'][0]['waiting_type_code']
					 . '&waiting_status_code=' . $waiting_for_sub_community_link_row_array['waiting_row_array'][0]['waiting_status_code'];
			}

			// 待機: コミュニティ招待
			$waiting_for_invite_to_community_row_array = $request->getAttribute('waiting_for_invite_to_community_row_array');
			$waiting_for_invite_to_community_row_array_num = count($waiting_for_invite_to_community_row_array);
			if ($waiting_for_invite_to_community_row_array_num) {
				// マイフレンズ追加 承認待ち URL
				$waiting_list_for_invite_to_community_url = $this->getControllerPath("User", 'WaitingList')
					 . '&id=' . $user_community_id
					 . '&waiting_type_code=' . $waiting_for_invite_to_community_row_array[0]['waiting_type_code']
					 . '&waiting_status_code=' . $waiting_for_invite_to_community_row_array[0]['waiting_status_code'];
			}

			// 新着コメント
			$new_comment_diary_row_array = $request->getAttribute('new_comment_diary_row_array');
			$new_comment_diary_row_array_num = count($new_comment_diary_row_array);
			if ($new_comment_diary_row_array_num) {
				// 新着コメントがあるダイアリーの中で、最もダイアリーの投稿日時が古い物([0])のdiary_idを引数に付ける
				$new_comment_diary_url = $this->getControllerPath(DEFAULT_MODULE, 'DiaryComment') . '&id=' . $new_comment_diary_row_array[0]['community_id'] . '&diary_id=' . $new_comment_diary_row_array[0]['diary_id'];
			}

			// システムからのお知らせ
			$system_announce_row_array = $request->getAttribute('system_announce_row_array');
			foreach ($system_announce_row_array as $index => $system_announce_row) {
				$system_announce_row_array[$index]['post_date'] = ACSLib::convert_pg_date_to_str($system_announce_row['post_date'], false, false, false);
			}
			
			// メッセージ機能
			$new_message_row_array = $request->getAttribute('new_message_row_array');
			$new_message_row_array_num = count($new_message_row_array);
			if ($new_message_row_array_num == 1) {
				// 新着メッセージが一件　メッセージ詳細URL
				$new_message_url = $this->getControllerPath("User", 'MessageShow') . '&id=' . $user_community_id. '&message_id=' . $new_message_row_array[0]['message_id'];
			} else if ($new_message_row_array_num > 1) {
				// 新着メッセージが複数件　受信箱URL
				$new_message_url =  $this->getControllerPath("User", 'MessageBox') . '&id=' . $user_community_id;
			}
			// メッセージ機能
		}

		//---- アクセス制御 ----//
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		$profile['contents_row_array']['user_name'] = ACSAccessControl::get_valid_row_for_user_community($acs_user_info_row, $role_array, $profile['contents_row_array']['user_name']);
		$profile['contents_row_array']['birthplace'] = ACSAccessControl::get_valid_row_for_user_community($acs_user_info_row, $role_array, $profile['contents_row_array']['birthplace']);
		$profile['contents_row_array']['birthday'] = ACSAccessControl::get_valid_row_for_user_community($acs_user_info_row, $role_array, $profile['contents_row_array']['birthday']);

		// マイフレンズ表示可否
		if (!ACSAccessControl::is_valid_user_for_user_community($acs_user_info_row, $role_array, $profile['contents_row_array']['friends_list'])) {
			$friends_row_array = array();
			$friends_list_url = '';
			//$friends_row_array_num = 0;
		}
		//----------------------//


		// set
		$this->setAttribute('profile', $profile);
		$this->setAttribute('peruse_mode', $peruse_mode);	//プロフィール自己紹介分岐用
		$this->setAttribute('friends_row_array', $friends_row_array);
		$this->setAttribute('friends_row_array_num', $friends_row_array_num);
		$this->setAttribute('community_list', $community_list);
		$this->setAttribute('community_row_array_num', $community_row_array_num);
		$this->setAttribute('last_login', $last_login);

		// メニュー
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('menu', $menu);
		$this->setAttribute('profile_edit_url', $profile_edit_url);
		$this->setAttribute('profile_view_url', $profile_view_url);
		$this->setAttribute('footprint_url', $footprint_url);
		$this->setAttribute('backup_url', $backup_url);
		$this->setAttribute('select_design_url', $select_design_url);
		$this->setAttribute('add_myfriends_url', $add_myfriends_url);
		$this->setAttribute('friends_list_url', $friends_list_url);
		$this->setAttribute('friends_group_list_url', $friends_group_list_url);
		$this->setAttribute('manage_friends_url', $manage_friends_url);
		$this->setAttribute('community_list_url', $community_list_url);
		$this->setAttribute('message_btn_url', $message_btn_url);
		$this->setAttribute('message_box_url', $message_box_url);

		// 待機: マイフレンズ追加
		$this->setAttribute('waiting_for_add_friends_row_array_num', $waiting_for_add_friends_row_array_num);
		$this->setAttribute('waiting_list_for_add_friends_url', $waiting_list_for_add_friends_url);
		// 待機: コミュニティ参加
		$this->setAttribute('waiting_for_join_community_row_array_array', $waiting_for_join_community_row_array_array);
		// 待機: 親コミュニティ追加
		$this->setAttribute('waiting_for_parent_community_link_row_array_array', $waiting_for_parent_community_link_row_array_array);
		// 待機: サブコミュニティ追加
		$this->setAttribute('waiting_for_sub_community_link_row_array_array', $waiting_for_sub_community_link_row_array_array);
		// 待機: コミュニティ招待
		$this->setAttribute('waiting_for_invite_to_community_row_array_num', $waiting_for_invite_to_community_row_array_num);
		$this->setAttribute('waiting_list_for_invite_to_community_url', $waiting_list_for_invite_to_community_url);

		// 新着コメントのあるマイダイアリー
		$this->setAttribute('new_comment_diary_row_array_num', $new_comment_diary_row_array_num);
		$this->setAttribute('new_comment_diary_url', $new_comment_diary_url);

		// システムからのお知らせ
		$this->setAttribute('system_announce_row_array', $system_announce_row_array);
		
		// メッセージ機能
		$this->setAttribute('new_message_row_array_num', $new_message_row_array_num);
		$this->setAttribute('new_message_url', $new_message_url);

		// インライン表示
		/*--------------- 新着情報を取得 ---------------*/
		// 現在のレンダーモードを取得
		$renderMode = $controller->getRenderMode();

		//レンダーモードを上書き （画面出力をオフにしてる）
		$controller->setRenderMode(View::RENDER_VAR);
		$this->inlineFlg = true;

		// フォワード側で判断する
		$request->setAttribute("inline_mode", "1");
		$request->setAttribute("id", $user_community_id);

		// 新着日記
		$controller->forward("User", "NewDiary");
		$this->setAttribute("NewDiary", $request->getAttribute("NewDiary"));
		
		// 日記コメント履歴
		$controller->forward("User", "DiaryCommentHistory");
		$this->setAttribute("DiaryCommentHistory", $request->getAttribute("DiaryCommentHistory"));
		
		// マイコミュニティ掲示板新着記事
		$controller->forward("User", "NewBBS");
		$this->setAttribute("NewBBS", $request->getAttribute("NewBBS"));
		
		// マイフレンズ：フォルダ新着情報
		$controller->forward("User", "NewFriendsFolder");
		$this->setAttribute("NewFriendsFolder", $request->getAttribute("NewFriendsFolder"));
		
		// マイフレンズ：フォルダ新着情報
		$controller->forward("User", "NewCommunityFolder");
		$this->setAttribute("NewCommunityFolder", $request->getAttribute("NewCommunityFolder"));
		
		// CSS
		$this->setAttribute('include_css_array', $this->css_file_array);
		
		// レンダーモードを元に戻す
		$controller->setRenderMode($renderMode); 
		$this->inlineFlg = false;

		/*----------------------------------------------*/

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('Index.tpl.php');

		return parent::execute();
	}
}

?>
