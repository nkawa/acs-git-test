<?php
/**
 * コミュニティ機能　Viewクラス
 * コミュニティ情報　変更画面
 * @package  acs/webapp/modules/Community/views
 * @author   w-ota v 1.24 2006/03/08 @update akitsu
 * @since    PHP 4.0
 */
// $Id: IndexView::SUCCESS.class.php,v 1.33 2007/03/28 05:58:21 w-ota Exp $

class IndexSuccessView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$controller = $context->getController();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$sub_community_row_array = $request->getAttribute('sub_community_row_array');
		$parent_community_row_array = $request->getAttribute('parent_community_row_array');
		$community_member_user_info_row_array = $request->getAttribute('community_member_user_info_row_array');
		$community_admin_user_info_row_array = $request->getAttribute('community_admin_user_info_row_array');
		$community_id = $community_row['community_id'];
		$bbs_row_array = $request->getAttribute('bbs_row_array');

		// コミュニティメンバ数
		$community_member_display_max = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D03'), 'COMMUNITY_MEMBER_DISPLAY_MAX_COUNT');

		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_id);
		$is_community_admin = ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $community_id);

		// 掲示板新着記事数
		$bbs_display_max = 10;

		// URL
		$bbs_url = $this->getControllerPath("Community", 'BBS') . '&community_id=' . $community_id;
		$community_folder_url = $this->getControllerPath("Community", 'Folder') . '&community_id=' . $community_id;
		if (!$is_community_member && $acs_user_info_row['is_acs_user']) {
			$join_community_url = $this->getControllerPath("Community", 'JoinCommunity') . '&community_id=' . $community_id;
		}
		if($is_community_admin){		//コミュニティ　プロフィールの編集
			$community_change_url = $this->getControllerPath("Community", 'EditCommunity') . '&community_id=' . $community_id;
		}
		
		// コミュニティ退会URL
		//   コミュニティメンバで、管理者でない場合は表示
		if ($is_community_member and !$is_community_admin) {
			$leave_community_url = $this->getControllerPath("Community", 'LeaveCommunity') . '&community_id=' . $community_id;
		}

		// RSS出力ページのURL
		$PressRelease_community_url = $this->getControllerPath("Community", 'PressReleaseRSS') . '&community_id=' . $community_id; ;

		// 加工 //
		$community_row['register_date'] = ACSLib::convert_pg_date_to_str($community_row['register_date'], 0, 0, 0); // 登録日
		$community_row['community_member_num'] = count($community_member_user_info_row_array); // メンバー数
		// 画像ファイルのパスを設定
		$community_row['image_url'] = ACSCommunity::get_image_url($community_id);

		if ($is_community_admin) {
			$edit_profile_image_url = $this->getControllerPath("Community", 'EditProfileImage') . '&community_id=' . $community_id;
		}


		// 参加資格の自由参加可能コミュニティ
		foreach ($community_row['join_trusted_community_row_array'] as $index => $join_trusted_community_row) {
			$community_row['join_trusted_community_row_array'][$index]['top_page_url'] = $this->getControllerPath("Community", DEFAULT_ACTION) . '&community_id=' . $join_trusted_community_row['community_id'];
		}

		// 掲示板の公開コミュニティ
		foreach ($community_row['contents_row_array']['bbs']['trusted_community_row_array'] as $index => $trusted_community_row) {
			$community_row['contents_row_array']['bbs']['trusted_community_row_array'][$index]['top_page_url'] = $this->getControllerPath("Community", DEFAULT_ACTION) . '&community_id=' . $trusted_community_row['community_id'];
		}
		// コミュニティフォルダの公開コミュニティ
		foreach ($community_row['contents_row_array']['community_folder']['trusted_community_row_array'] as $index => $trusted_community_row) {
			$community_row['contents_row_array']['community_folder']['trusted_community_row_array'][$index]['top_page_url'] = $this->getControllerPath("Community", DEFAULT_ACTION) . '&community_id=' . $trusted_community_row['community_id'];
		}


		// コミュニティメンバ
		$community_member_display_user_info_row_array = array();
		$array_count = 0;
		foreach ($community_member_user_info_row_array as $index => $community_member_user_info_row) {
			$array_count++;
			$_community_member_row = array();

			$_community_member_row['community_name'] = $community_member_user_info_row['community_name'];
			$_community_member_row['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $community_member_user_info_row['user_community_id'];
			$_community_member_row['image_url'] = ACSUser::get_image_url($community_member_user_info_row['user_community_id'], 'thumb');
			$_community_member_row['friends_row_array_num'] = ACSUser::get_friends_row_array_num($community_member_user_info_row['user_community_id']);

			array_push($community_member_display_user_info_row_array, $_community_member_row);

			if ($array_count == $community_member_display_max) {
				break;
			}
		}
		// コミュニティ管理者
		foreach ($community_admin_user_info_row_array as $index => $community_admin_user_info_row) {
			$community_admin_user_info_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $community_admin_user_info_row['user_community_id'];
		}

		// 親コミュニティ
		foreach ($parent_community_row_array as $index => $parent_community_row) {
			$parent_community_row_array[$index]['top_page_url'] = $this->getControllerPath("Community", DEFAULT_ACTION) . '&community_id=' . $parent_community_row['community_id'];
		}
		// サブコミュニティ
		foreach ($sub_community_row_array as $index => $sub_community_row) {
			$sub_community_row_array[$index]['top_page_url'] = $this->getControllerPath("Community", DEFAULT_ACTION) . '&community_id=' . $sub_community_row['community_id'];
		}

		// URL
		// コミュニティ間リンク設定
		if ($is_community_admin) {
			$community_link_url = $this->getControllerPath("Community", 'CommunityLink') . '&community_id=' . $community_row['community_id'];
		}

		// コミュニティメンバ一覧
		$community_member_list_url = $this->getControllerPath("Community", 'CommunityMemberList') . '&community_id=' . $community_row['community_id'];

		// メンバ削除
		$delete_community_member_list_url = "";
		if ($is_community_admin) {
			$delete_community_member_list_url  = $this->getControllerPath(
																				"Community",
																				'DeleteCommunityMemberList');
			$delete_community_member_list_url .= '&community_id=' . $community_row['community_id'];
		}

		// コミュニティ削除
		$delete_community_url = "";
		if ($is_community_admin) {
			$delete_community_url  = $this->getControllerPath(
																	"Community",
																	'DeleteCommunity');
			$delete_community_url .= '&community_id=' . $community_row['community_id'];
		}

		// コミュニティ招待 URL
		if ($is_community_admin) {
			$invite_to_community_url = $this->getControllerPath("Community", 'InviteToCommunity') . '&community_id=' . $community_row['community_id'];
		}

		// コミュニティスケジュール URL
		if($is_community_member){
			$community_schedule_url = $this->getControllerPath("Community", 'Schedule') . '&community_id=' . $community_id;
		}

		// コミュニティ管理者設定
		if ($is_community_admin) {
			$edit_community_admin_url = $this->getControllerPath("Community", 'EditCommunityAdmin') . '&community_id=' . $community_row['community_id'];
		}

		// 外部RSS自動取り込み設定
		if ($is_community_admin) {
			$edit_external_rss_url = $this->getControllerPath("Community", 'EditExternalRSS') . '&community_id=' . $community_row['community_id'];
		}

		if ($is_community_admin) {
			// 待機: コミュニティ参加 承認待ち
			$waiting_for_join_community_row_array = $request->getAttribute('waiting_for_join_community_row_array');
			$waiting_for_join_community_row_array_num = count($waiting_for_join_community_row_array);
			if ($waiting_for_join_community_row_array_num) {
				// マイフレンズ追加 承認待ち URL
				$waiting_for_join_community_url = $this->getControllerPath("Community", 'WaitingList')
					 . '&community_id=' . $community_id
					 . '&waiting_type_code=' . $waiting_for_join_community_row_array[0]['waiting_type_code']
					 . '&waiting_status_code=' . $waiting_for_join_community_row_array[0]['waiting_status_code'];
			}

			// 待機: 親コミュニティ追加
			$waiting_for_parent_community_link_row_array = $request->getAttribute('waiting_for_parent_community_link_row_array');
			$waiting_for_parent_community_link_row_array_num = count($waiting_for_parent_community_link_row_array);
			if ($waiting_for_parent_community_link_row_array_num) {
				// 親コミュニティ追加 承認待ち URL
				$waiting_for_parent_community_link_url = $this->getControllerPath("Community", 'WaitingList')
					 . '&community_id=' . $community_id
					 . '&waiting_type_code=' . $waiting_for_parent_community_link_row_array[0]['waiting_type_code']
					 . '&waiting_status_code=' . $waiting_for_parent_community_link_row_array[0]['waiting_status_code'];
			}

			// 待機: サブコミュニティ追加
			$waiting_for_sub_community_link_row_array = $request->getAttribute('waiting_for_sub_community_link_row_array');
			$waiting_for_sub_community_link_row_array_num = count($waiting_for_sub_community_link_row_array);
			if ($waiting_for_sub_community_link_row_array_num) {
				// サブコミュニティ追加 承認待ち URL
				$waiting_for_sub_community_link_url = $this->getControllerPath("Community", 'WaitingList')
					 . '&community_id=' . $community_id
					 . '&waiting_type_code=' . $waiting_for_sub_community_link_row_array[0]['waiting_type_code']
					 . '&waiting_status_code=' . $waiting_for_sub_community_link_row_array[0]['waiting_status_code'];
			}
		}


		// 掲示板に対するアクセス権
		$bbs_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D41'));
		$bbs_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $bbs_contents_row['contents_type_code'], $bbs_contents_row['open_level_code']);
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row);
		$ret = ACSAccessControl::is_valid_user_for_community($acs_user_info_row, $role_array, $bbs_contents_row);

		if ($ret) {
			// 掲示版最新情報
			foreach ($bbs_row_array as $index => $bbs_row) {
				// 返信画面URL
				$bbs_row_array[$index]['bbs_res_url'] = $this->getControllerPath("Community", 'BBSRes') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
				$bbs_row_array[$index]['bbs_last_post_date'] = ACSLib::convert_pg_date_to_str($bbs_row['bbs_last_post_date'], true, false, false);
			}
			//---- アクセス制御 ----//
			$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row);
			$bbs_row_array = ACSAccessControl::get_valid_row_array_for_community($acs_user_info_row, $role_array, $bbs_row_array);
			//----------------------//
			$bbs_row_array = array_slice($bbs_row_array, 0, $bbs_display_max);

		} else {
			$bbs_row_array = array();
		}

		// set
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('parent_community_row_array', $parent_community_row_array);
		$this->setAttribute('sub_community_row_array', $sub_community_row_array);
		$this->setAttribute('community_member_display_user_info_row_array', $community_member_display_user_info_row_array);
		$this->setAttribute('community_admin_user_info_row_array', $community_admin_user_info_row_array);
		$this->setAttribute('bbs_row_array', $bbs_row_array);

		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('is_community_admin', $is_community_admin);

		$this->setAttribute('bbs_url', $bbs_url);
		$this->setAttribute('community_folder_url', $community_folder_url);
		$this->setAttribute('create_sub_community_url', $create_sub_community_url);
		$this->setAttribute('join_community_url', $join_community_url);
		$this->setAttribute('leave_community_url', $leave_community_url);
		$this->setAttribute('community_change_url',$community_change_url);

		$this->setAttribute('community_schedule_url', $community_schedule_url);
		$this->setAttribute('community_link_url', $community_link_url);
		$this->setAttribute('delete_community_url', $delete_community_url);
		$this->setAttribute('edit_community_admin_url', $edit_community_admin_url);
		$this->setAttribute('invite_to_community_url', $invite_to_community_url);
		$this->setAttribute('edit_community_profile_url', $edit_community_profile_url);
		$this->setAttribute('edit_external_rss_url', $edit_external_rss_url);
		$this->setAttribute('community_member_list_url', $community_member_list_url);
		$this->setAttribute('delete_community_member_list_url', $delete_community_member_list_url);
		$this->setAttribute('PressRelease_community_url', $PressRelease_community_url);
		//画像
		$this->setAttribute('edit_profile_image_url', $edit_profile_image_url);

		// 待機
		$this->setAttribute('waiting_for_join_community_row_array_num', $waiting_for_join_community_row_array_num);
		$this->setAttribute('waiting_for_join_community_url', $waiting_for_join_community_url);
		$this->setAttribute('waiting_for_parent_community_link_row_array_num', $waiting_for_parent_community_link_row_array_num);
		$this->setAttribute('waiting_for_parent_community_link_url', $waiting_for_parent_community_link_url);
		$this->setAttribute('waiting_for_sub_community_link_row_array_num', $waiting_for_sub_community_link_row_array_num);
		$this->setAttribute('waiting_for_sub_community_link_url', $waiting_for_sub_community_link_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('Index.tpl.php');

		return parent::execute();
	}
}

?>
