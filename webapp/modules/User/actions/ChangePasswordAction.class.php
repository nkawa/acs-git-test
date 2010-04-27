<?php
// $Id: ChangePasswordAction.class.php,v 1.3 2006/03/28 07:55:34 kuwayama Exp $

class ChangePasswordAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$form = $request->ACSGetParameters();

		// �ѥ���ɤ��ѹ�����
		if($form['passwd'] != '' && $form['passwd'] == $form['passwd2']) {
			ACSSystem::update_passwd($acs_user_info_row['user_id'], $form['passwd']);
		}

		$user_list_url = $this->getControllerPath('User', 'Index');
		header("Location: $user_list_url");
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �ܿͤǡ�LDAPǧ�ڰʳ��ξ���OK
		if ($user->hasCredential('USER_PAGE_OWNER') 
				&& $user->hasCredential('NOT_LDAP_USER')) {
			return true;
		}
		return false;
	}
}

?>
