<?php
/**
 * 掲示板　返信投稿機能　Viewクラス
 * 投稿情報　入力・表示画面
 * @package  acs/webapp/modules/Community/views
 * @author   作成ota					変更akitsu
 * @since    PHP 4.0
 */
// $Id: BBSResInputView.class.php,v 1.15 2007/03/28 05:58:21 w-ota Exp $


class BBSResInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$bbs_row = $request->getAttribute('bbs_row');
		//ユーザ入力情報
		$form = $user->getAttribute('new_form_obj');

		// コミュニティメンバかどうか
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_row['community_id']);
		//コミュニティ管理者かどうか
		$is_community_admin = ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $community_row['community_id']);
		// 加工
		if($bbs_row['bbs_res_delete_flag'] != 't'){
			// 親記事の投稿者 トップページURL
			$bbs_row['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $bbs_row['user_community_id'];
			// 写真URL
			$bbs_row['image_url'] =  ACSUser::get_image_url($bbs_row['user_community_id'], 'thumb');
			// 投稿日時
			$bbs_row['post_date'] = ACSLib::convert_pg_date_to_str($bbs_row['post_date']);
			//ファイルの画像URL
			$bbs_row['file_url'] = "";
			if($bbs_row['file_id'] != ""){
				$bbs_row['file_url'] = ACSBBSFile::get_image_url($bbs_row['bbs_id'],'thumb');	//投稿内表示用
				$bbs_row['file_url_alink'] = ACSBBSFile::get_image_url($bbs_row['bbs_id'],'');	//ポップアップ用
			}
			// パブリックリリース 掲載終了日
			if($bbs_row['expire_date'] != ""){
				$bbs_row['expire_date'] = ACSLib::convert_pg_date_to_str($bbs_row['expire_date'],true,false,false);
			}

			// 外部RSS情報
			$external_rss_row = ACSExternalRSS::get_external_rss_row($bbs_row['bbs_id']);
			if ($external_rss_row) {
				if ($external_rss_row['rss_item_date'] != '') {
					// YYYY/MM/DD H:MM
					$external_rss_row['rss_item_date'] = ACSLib::convert_pg_date_to_str($external_rss_row['rss_item_date'], 0, 1, 0);
				}
				$bbs_row['external_rss_row'] = $external_rss_row;
			}
	
			// 返信記事
			foreach ($bbs_row['bbs_res_row_array'] as $res_index => $bbs_res_row) {
				// 返信記事の投稿者 トップページURL
				$bbs_row['bbs_res_row_array'][$res_index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $bbs_res_row['user_community_id'];
				// 写真URL
				$bbs_row['bbs_res_row_array'][$res_index]['image_url'] =ACSUser::get_image_url($bbs_res_row['user_community_id'], 'thumb');
				// 投稿日時
				$bbs_row['bbs_res_row_array'][$res_index]['post_date'] = ACSLib::convert_pg_date_to_str($bbs_res_row['post_date']);
				// 返信の削除URL
				$bbs_row['bbs_res_row_array'][$res_index]['delete_bbs_res_url'] = $this->getControllerPath('Community', 'DeleteBBSRes')
				 . '&community_id=' . $community_row['community_id'] . '&bbs_res_id=' . $bbs_row['bbs_res_row_array'][$res_index]['bbs_res_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
				// 編集URL
				if (!ACSLib::get_boolean($bbs_res_row['bbs_res_delete_flag'])
					&& ($bbs_res_row['user_community_id'] == $acs_user_info_row['user_community_id'] || $is_community_admin)) {
					$bbs_row['bbs_res_row_array'][$res_index]['edit_bbs_res_url'] = $this->getControllerPath('Community', 'EditBBSRes')
						 . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_res_row['bbs_id'] . '&bbs_res_id=' . $bbs_res_row['bbs_res_id'];
				}
				//削除権限フラグ (自分が投稿したもの　又は　コミュニティ管理者)
				if($bbs_res_row['user_community_id'] == $acs_user_info_row['user_community_id'] || $is_community_admin == 1){
					$bbs_row['bbs_res_row_array'][$res_index]['bbs_set_delete_flag'] = true;
				}else{
					$bbs_row['bbs_res_row_array'][$res_index]['bbs_set_delete_flag'] = false;
				}
			}
		}

		// form action 確認画面への遷移
		$action_url  = $this->getControllerPath('Community', 'BBSResPre') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'] . '&move_id=1';

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'] ;
		// BBS URL
		$bbs_top_page_url = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_row['community_id'];

		// set
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('community_row', $request->getAttribute('community_row'));
		$this->setAttribute('bbs_row', $bbs_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('bbs_top_page_url', $bbs_top_page_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('BBSRes.tpl.php');
		
		if($request->getParameter('move_id') == 3){
		//ユーザ入力情報
			$form = $user->getAttribute('new_form_obj');
			$this->setAttribute('form', $form);
			$this->setAttribute('move_id', $request->getParameter('move_id'));
		}

		return parent::execute();
	}
}

?>
