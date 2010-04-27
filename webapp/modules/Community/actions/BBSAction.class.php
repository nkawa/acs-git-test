<?php
/**
 * 掲示板　投稿機能　Actionクラス
 * 投稿情報　入力・表示
 * @package  acs/webapp/modules/Community/action
 * @author   作成ota					変更akitsu
 * @since	PHP 4.0
 * @version  $Revision: 1.14 $ $Date: 2007/03/28 05:58:18 $
 */
// $Id: BBSAction.class.php,v 1.14 2007/03/28 05:58:18 w-ota Exp $


class BBSAction extends BaseAction
{
	// GET	
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$community_id = $request->getParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);
		$community_row = ACSExternalRSS::add_contents_row_array($community_row);

		// BBS記事一覧
		$bbs_row_array = ACSBBS::get_bbs_row_array($community_id);

		foreach ($bbs_row_array as $index => $bbs_row) {
			// 信頼済みコミュニティ一覧
			$bbs_row_array[$index]['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);

			// 返信記事
			$bbs_row_array[$index]['bbs_res_row_array'] = ACSBBS::get_bbs_res_row_array($bbs_row['bbs_id']);
		}
		if($community_row['contents_row_array']['self']['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
			// 非公開コミュニティ
			$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D43'));
		}else{
			// 公開範囲
			$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D42'));
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('bbs_row_array', $bbs_row_array);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);

		return View::INPUT;
	}

	// POST
	function execute() {
	//キャンセルで戻ってきたときのみの処理
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();

		$user = $context->getUser();
		$move_id = $request->getParameter('move_id');
		if($move_id == 3){
			$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		
		// 対象となるコミュニティIDを取得
			$community_id = $request->ACSGetParameter('community_id');

		// 元のフォーム情報を取得する
			$form = $user->getAttribute('new_form_obj');//件名：subject 内容：body 公開範囲：open_level_code 画像：new_file 掲載終了日：xdate
			$form['community_id'] = $community_id;
			$form['user_community_id'] = $acs_user_info_row['user_community_id']; // 投稿者

		$user->setAttribute('new_form_obj',$form);
		// GETの処理へ
		return $this->getDefaultView();
		//	$bbs_top_page_url = $this->getControllerPath('Community', $controller->getCurrentAction()) . '&community_id=' . $community_id;
		//	header("Location: $bbs_top_page_url");
		}
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods() {
		return Request::POST;
	}

	// アクセス制御情報
	function get_access_control_info(&$controller, &$request, &$user) {
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$community_id = $request->getParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// アクセス制御情報 //
		$bbs_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D41'));
		$bbs_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $bbs_contents_row['contents_type_code'], $bbs_contents_row['open_level_code']);
		$access_control_info = array(
				 'role_array' => ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row),
				 'contents_row_array' => array($bbs_contents_row)
		);

		return	 $access_control_info;
	}
}

?>
