<?php
// $Id: UserListAction.class.php,v 1.4 2006/03/27 09:50:26 kuwayama Exp $


class UserListAction extends BaseAction
{
	// GET
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
		$form = $request->ACSgetParameters();

		// get
		// ユーザ一覧を取得する
		$user_info_row_array = ACSUser::search_all_user_info_row_array($form);

		// set
		$request->setAttribute('form', $form);
		$request->setAttribute('user_info_row_array', $user_info_row_array);

		return View::SUCCESS;
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
