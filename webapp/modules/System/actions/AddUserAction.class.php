<?php
// $Id: AddUserAction.class.php,v 1.7 2008/04/24 16:00:00 y-yuki Exp $

class AddUserAction extends BaseAction
{
	// GET
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

		return View::INPUT;
	}

	// POST
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
		$form = $request->ACSGetParameters();

		$user_id = $form['user_id'];
		if (ACSUser::get_user_info_row_by_user_id($user_id)) {
			echo ACSMsg::get_msg('System', 'AddUserAction.class.php', 'M001');
			return;
		}

		// ユーザ情報を新規登録する
		$ret = ACSUser::set_user_info($form);
		// ログ登録: ユーザ新規登録
		ACSLog::set_log($acs_user_info_row, 'New User Registration', $ret, "[UserID:{$form['user_id']}]");

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
