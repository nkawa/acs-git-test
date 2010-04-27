<?php
// $Id: DeleteSystemAnnounceAction.class.php,v 1.1 2006/06/13 02:49:43 w-ota Exp $

class DeleteSystemAnnounceAction extends BaseAction
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
		$system_announce_id = $request->ACSgetParameter('system_announce_id');

		// get
		$system_announce_row = ACSSystemAnnounce::get_system_announce_row($system_announce_id);

		$request->setAttribute('system_announce_row', $system_announce_row);

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

		$ret = ACSSystemAnnounce::delete_system_announce($form['system_announce_id']);

		$system_announce_list_url = $this->getControllerPath('System', 'SystemAnnounceList');
		header("Location: $system_announce_list_url");
	}

	function getRequestMethods() {
		return Request::POST;
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

		// 管理者の場合はOK
		if ($user->hasCredential('SYSTEM_ADMIN_USER')) {
			return true;
		}
		return false;
	}
}

?>
