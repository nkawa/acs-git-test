<?php
// $Id: SystemAnnounceListAction.class.php,v 1.1 2006/06/13 02:49:43 w-ota Exp $

class SystemAnnounceListAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		// �����Ԥ��ɤ�����ǧ
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �����ƥॢ�ʥ��󥹾������
		$system_announce_row_array = ACSSystemAnnounce::get_all_system_announce_row_array();

		// set
		$request->setAttribute('system_announce_row_array', $system_announce_row_array);

		return View::SUCCESS;
	}

	function isSecure() {
		return false;
	}

	function getCredential() {
		return array('SYSTEM_ADMIN_USER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �����Ԥξ���OK
		if ($user->hasCredential('SYSTEM_ADMIN_USER')) {
			return true;
		}
		return false;
	}
}

?>
