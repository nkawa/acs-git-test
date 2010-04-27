<?php
/**
 * 掲示板　投稿機能　Viewクラス
 * 投稿情報　入力・表示画面
 * @package  acs/webapp/modules/Community/views
 * @author   作成ota					変更akitsu
 * @since	PHP 4.0
 * @version  $Revision: 1.25 $ $Date: 2007/03/28 05:58:21 $
 */
 // $Id: BBSInputView.class.php,v 1.25 2007/03/28 05:58:21 w-ota Exp $

class BBSInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$bbs_row_array = $request->getAttribute('bbs_row_array');

		// コミュニティメンバかどうか
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_row['community_id']);
		//コミュニティ管理者かどうか
		$is_community_admin = ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $community_row['community_id']);

		// form action 確認画面への遷移
		$action_url  = $this->getControllerPath('Community',
									 'BBSPre') . '&community_id=' . $community_row['community_id'] . '&move_id=1';

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// BBSスレッド一覧URL
		$bbs_thread_list_url = $this->getControllerPath('Community', 'BBSThreadList') . '&community_id=' . $community_row['community_id'];

		// 信頼済みコミュニティ選択ページのURL
		$select_trusted_community_url = $this->getControllerPath('Community', 'SelectTrustedCommunity') . '&form_name=bbs_form';
		
		//検索画面url
		$search_bbs_url = $this->getControllerPath('Community', 'SearchBBS') . '&community_id=' . $community_row['community_id'] .'&move_id=1';

		//外部RSS取り込み実行
		if ($community_row['contents_row_array']['external_rss_url']['contents_value'] != '') {
			$get_external_rss_url = $this->getControllerPath('Community', 'GetExternalRSS') . '&community_id=' . $community_row['community_id'];
		}

		// 掲示板RSS URL
		$term = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D03'), 'BBS_RSS_TERM');
		$bbs_rss_url = $this->getControllerPath('Community', 'BBSRSS')
			 . '&community_id=' . $community_row['community_id']
			 . '&term=' . $term;


		// 加工
		foreach ($bbs_row_array as $index => $bbs_row) {
			// 親記事の投稿者 トップページURL
			$bbs_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $bbs_row['user_community_id'];
			// 写真URL
			$bbs_row_array[$index]['image_url'] = ACSUser::get_image_url($bbs_row['user_community_id'], 'thumb');
			// 投稿日時
			$bbs_row_array[$index]['post_date'] = ACSLib::convert_pg_date_to_str($bbs_row['post_date']);
			// 返信画面URL
			$bbs_row_array[$index]['bbs_res_url'] = $this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
			// 編集画面URL
			if ($bbs_row['user_community_id'] == $acs_user_info_row['user_community_id'] || $is_community_admin) {
				$bbs_row_array[$index]['edit_bbs_url'] = $this->getControllerPath('Community', 'EditBBS')
					 . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
			}
			// 削除画面URL
			$bbs_row_array[$index]['bbs_delete_url'] = $this->getControllerPath('Community', 'DeleteBBS') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
			//削除権限フラグ (自分が投稿したもの　又は　コミュニティ管理者)
			if($bbs_row['user_community_id'] == $acs_user_info_row['user_community_id'] || $is_community_admin == 1){
				$bbs_row_array[$index]['bbs_set_delete_flag'] = true;
			}else{
				$bbs_row_array[$index]['bbs_set_delete_flag'] = false;
			}

			//ファイルの画像URL
			$bbs_row_array[$index]['file_url'] = "";
			if($bbs_row['file_id'] != ""){
				$bbs_row_array[$index]['file_url'] = ACSBBSFile::get_image_url($bbs_row['bbs_id'],'thumb');		//投稿内表示用
				$bbs_row_array[$index]['file_url_alink'] = ACSBBSFile::get_image_url($bbs_row['bbs_id'],'');	//ポップアップ用
			}
			// パブリックリリース 掲載終了日 2/21add @akitsu
			$bbs_row_array[$index]['expire_date'] = "";
			if($bbs_row['expire_date'] != ""){
				$bbs_row_array[$index]['expire_date'] = ACSLib::convert_pg_date_to_str($bbs_row['expire_date'],true,false,false);
			}
			if($bbs_row['bbs_delete_flag'] != 't'){
			// 返信記事
				$bbs_res_display_max = 10;
				$bbs_row_array[$index]['bbs_res_row_array_num'] = count($bbs_row_array[$index]['bbs_res_row_array']);
				if ($bbs_row_array[$index]['bbs_res_row_array_num'] > $bbs_res_display_max) {
					// 最新の10件のみslice
					$bbs_row_array[$index]['bbs_res_row_array'] = array_slice($bbs_row_array[$index]['bbs_res_row_array'], -1 * $bbs_res_display_max);
					// 省略件数を算出
					$bbs_row_array[$index]['omission_num'] = $bbs_row_array[$index]['bbs_res_row_array_num'] - $bbs_res_display_max;
				}
				foreach ($bbs_row_array[$index]['bbs_res_row_array'] as $res_index => $bbs_res_row) {
					// 返信記事の投稿者 トップページURL
					$bbs_row_array[$index]['bbs_res_row_array'][$res_index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $bbs_res_row['user_community_id'];
					// 写真URL
					$bbs_row_array[$index]['bbs_res_row_array'][$res_index]['image_url'] = ACSUser::get_image_url($bbs_res_row['user_community_id'], 'thumb');
					// 投稿日時
					$bbs_row_array[$index]['bbs_res_row_array'][$res_index]['post_date'] = ACSLib::convert_pg_date_to_str($bbs_res_row['post_date']);
					//削除情報
					$bbs_row_array[$index]['bbs_res_row_array'][$res_index]['delete_flag'] = ACSLib::get_boolean($bbs_res_row['res_delete_flag']);
				}
			}
		}

		// 親コミュニティ / サブコミュニティの情報を取得する
		$parent_community_row_array = ACSCommunity::get_parent_community_row_array($community_row['community_id']);
		foreach ($parent_community_row_array as $index => $parent_community_row) {
			$parent_community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $parent_community_row['community_id'];
		}
		$sub_community_row_array = ACSCommunity::get_sub_community_row_array($community_row['community_id']);
		foreach ($sub_community_row_array as $index => $sub_community_row) {
			$sub_community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $sub_community_row['community_id'];
		}


		//---- アクセス制御 ----//
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row);
		$bbs_row_array = ACSAccessControl::get_valid_row_array_for_community($acs_user_info_row, $role_array, $bbs_row_array);
		//----------------------//


		// 外部RSS情報
		foreach ($bbs_row_array as $index => $bbs_row) {
			$external_rss_row = ACSExternalRSS::get_external_rss_row($bbs_row['bbs_id']);
			if ($external_rss_row) {
				if ($external_rss_row['rss_item_date'] != '') {
					// YYYY/MM/DD H:MM
					$external_rss_row['rss_item_date'] = ACSLib::convert_pg_date_to_str($external_rss_row['rss_item_date'], 0, 1, 0);
				}
				$bbs_row_array[$index]['external_rss_row'] = $external_rss_row;
			}
		}


		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $bbs_row_array, $display_count);

		// set
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('is_community_admin', $is_community_admin);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('bbs_thread_list_url', $bbs_thread_list_url);
		$this->setAttribute('select_trusted_community_url', $select_trusted_community_url);
		$this->setAttribute('search_bbs_url', $search_bbs_url);
		$this->setAttribute('get_external_rss_url', $get_external_rss_url);
		$this->setAttribute('bbs_rss_url', $bbs_rss_url);
		
		$this->setAttribute('community_row', $request->getAttribute('community_row'));
		$this->setAttribute('bbs_row_array', $bbs_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('open_level_master_row_array', $request->getAttribute('open_level_master_row_array'));
		$this->setAttribute('parent_community_row_array', $parent_community_row_array);
		$this->setAttribute('sub_community_row_array', $sub_community_row_array);

		// ML送信チェック表示設定
		if($community_row['contents_row_array']['ml_status']['contents_value'] == 'ACTIVE') {
			$this->setAttribute('is_ml_active', TRUE);
		}

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('BBS.tpl.php');

		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));
		
		if($request->getParameter('move_id') == 3){
		//ユーザ入力情報
			$form = $user->getAttribute('new_form_obj');
			// パブリックリリース 掲載終了日
			$form['expire_date'] = "";
			if($form['expire_date'] != ""){
				$form['expire_date'] = ACSLib::convert_pg_date_to_str($form['expire_date'],false,false,false);
			}
			$this->setAttribute('form', $form);
			$this->setAttribute('move_id', $request->getParameter('move_id'));
		}
		return parent::execute();
	}
}

?>
