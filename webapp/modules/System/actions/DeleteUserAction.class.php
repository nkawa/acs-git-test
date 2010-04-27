<?php
/**
 * システム　ユーザ管理　ユーザ情報変更画面 actionクラス
 * @package  acs/webapp/modules/System/actions
 * DeleteUserAction
 * @author   akitsu  
 * @since	PHP 4.0
 */
// $Id: DeleteUserAction.class.php,v 1.6 2006/12/08 05:06:39 w-ota Exp $

class DeleteUserAction extends BaseAction
{
	// GET([削除]リンクからの遷移)
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		// 管理者かどうか確認
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$user_id = $request->getParameter('id');
		$user_info_row = ACSUser::get_user_profile_row($user_id, 'include_private_flag');
		
		$request->setAttribute('user_info_row', $user_info_row);
		$user->setAttribute('user_id', $user_id);
		return View::SUCCESS;
	}

	// POST（[OK]ボタンからの遷移）
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		// 管理者かどうか確認
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$user_community_id = $user->getAttribute('user_id');

		$target_user_info_row = ACSUser::get_user_profile_row($user_community_id, 'include_private_flag');

		// ユーザ情報の削除フラグを変更する
		$ret = ACSUser::delete_user_community($user_community_id);
		if(!$ret){
			echo "Warning : DB ERROR : Delete user failed.";
			return;
		}

		// ログ登録: ユーザ削除
		ACSLog::set_log($acs_user_info_row, 'Remove User', $ret, "[UserID:$target_user_info_row[user_id]]");

		// ユーザ一覧を表示
		$user_list_url = $this->getControllerPath('System', 'UserList');
		header("Location: $user_list_url");
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('SYSTEM_ADMIN_USER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 管理者の場合はOK
		if ($user->hasCredential('SYSTEM_ADMIN_USER')) {
			return true;
		}
		return false;
	}
}

?>
