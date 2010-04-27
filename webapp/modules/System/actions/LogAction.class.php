<?php
// $Id: LogAction.class.php,v 1.2 2006/03/27 09:50:26 kuwayama Exp $

class LogAction extends BaseAction
{
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

		// ログ情報を取得する
		$log_row_array = ACSLog::search_log_row_array($form);

		// set
		$request->setAttribute('form', $form);
		$request->setAttribute('log_row_array', $log_row_array);

		return View::INPUT;
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
