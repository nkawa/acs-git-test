<?php
// $Id: LoginInfoAction.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $


class LoginInfoAction extends BaseAction
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
		$user_id = $request->getParameter('id');


		// get
		// �桼���������
		$target_user_info_row = ACSUser::get_user_info_row($user_id);
		// ���������������������
		$login_info_row_array = ACSUser::get_login_info_row_array($user_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('login_info_row_array', $login_info_row_array);

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

		// �����Ԥξ���OK
		if ($user->hasCredential('SYSTEM_ADMIN_USER')) {
			return true;
		}
		return false;
	}
}

?>
