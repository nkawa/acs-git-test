<?php
// $Id: BBSResAction.class.php,v 1.10 2006/11/20 08:44:12 w-ota Exp $


class BBSResAction extends BaseAction
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
		// 対象となるbbs_idを取得
		$bbs_id = $request->getParameter('bbs_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		// BBS記事一覧
		$bbs_row = ACSBBS::get_bbs_row($bbs_id);

		// 信頼済みコミュニティ一覧
		$bbs_row['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);
		// 返信記事
		$bbs_row['bbs_res_row_array'] = ACSBBS::get_bbs_res_row_array($bbs_row['bbs_id']);

		// 公開範囲
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D42'));

		// 掲示板アクセス履歴
		if ($acs_user_info_row['is_acs_user']) {
			ACSBBS::set_bbs_access_history($acs_user_info_row['user_community_id'], $bbs_row['bbs_id']);
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('bbs_row', $bbs_row);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();  

		//キャンセルで戻ってきたときのみの処理
		$move_id = $request->getParameter('move_id');
		if($move_id == 3){
			$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		
		// 対象となるコミュニティIDを取得
			$community_id = $request->ACSGetParameter('community_id');
		// 対象となるbbs_idを取得
			$bbs_id = $request->getParameter('bbs_id');
		
		// 元のフォーム情報を取得する
			$form = $user->getAttribute('new_form_obj');//件名：subject 内容：body
			$form['community_id'] = $community_id;
			$form['user_community_id'] = $acs_user_info_row['user_community_id']; // 投稿者

			$user->setAttribute('new_form_obj',$form);
			// GETの処理へ
			return $this->getDefaultView();
		}
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

		return $access_control_info;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('EXECUTE');
	}

	function get_execute_privilege (&$controller, &$request, &$user) {

		// 公開範囲情報取得
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_community_row = ACSCommunity::get_community_profile_row($request->getParameter('community_id'));
		$bbs_row = ACSBBS::get_bbs_row($request->getParameter('bbs_id'));
		if (!$bbs_row) {
			return false;
		}
		$bbs_row['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);

		// アクセス制御判定
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $target_community_row);
		$ret = ACSAccessControl::is_valid_user_for_community($acs_user_info_row, $role_array, $bbs_row);

		return $ret;
	}
}

?>
