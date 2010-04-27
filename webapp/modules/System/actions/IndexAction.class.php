<?php
// $Id: IndexAction.class.php,v 1.3 2006/03/27 09:50:26 kuwayama Exp $


class IndexAction extends BaseAction
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

		return VIEW::SUCCESS;
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
