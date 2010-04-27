<?php
/**
 * ダイアリー　コメント　Actionクラス
 * 
 * DiaryCommentAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   w-ota                     @editor akitsu
 * @since    PHP 4.0
 */
// $Id: DiaryCommentAction.class.php,v 1.17 2007/03/29 01:55:17 w-ota Exp $

class DiaryCommentAction extends BaseAction
{

	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// 対象のdiary_idを取得
		$diary_id = $request->ACSgetParameter('diary_id');

		// ダイアリー親記事の情報を取得
		$diary_row = ACSDiary::get_diary_row($diary_id);
		if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
			$diary_row['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
		}

		// ユーザ情報
		$user_community_id = $diary_row['user_community_id'];
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// 権限
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ダイアリーコメント
		$diary_comment_row_array = ACSDiary::get_diary_comment_row_array($diary_row['diary_id']);

		// ダイアリーアクセス履歴登録
		if ($acs_user_info_row['is_acs_user']) {
			ACSDiary::set_diary_access_history($acs_user_info_row['user_community_id'], $diary_id);
		}

		// 足跡情報取得
		$footprint_url = $this->getControllerPath('User', 'DiaryComment')
				. '&diary_id=' . $diary_row['diary_id'];
		$where  = "foot.contents_link_url = '" . $footprint_url . "'";
		$where .= " AND foot.visitor_community_id = '" . $acs_user_info_row['user_community_id'] . "'";
		$footprint_info = ACSUser::get_footprint_list($user_community_id, $where);
		
		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('diary_row', $diary_row);
		$request->setAttribute('diary_comment_row_array', $diary_comment_row_array);
		$request->setAttribute('footprint_info', $footprint_info);

		return View::INPUT;
	}
	
	function execute() {
		
	}
	
	function getRequestMethods() {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		return array('EXECUTE');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// 公開範囲情報取得
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$diary_row = ACSDiary::get_diary_row($request->ACSgetParameter('diary_id'));
		if (!$diary_row) {
			return false;
		}
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($diary_row['community_id']);
		if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
			$diary_row['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
		}

		// アクセス制御判定
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		$ret = ACSAccessControl::is_valid_user_for_user_community($acs_user_info_row, $role_array, $diary_row);

		return $ret;
	}
}
?>
