<?php
// $Id: CreateSystemAnnounceAction.class.php,v 1.2 2006/11/20 08:44:20 w-ota Exp $

class CreateSystemAnnounceAction extends BaseAction
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
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$form = $request->ACSGetParameters();

		// Validatorで出来ないエラーチェックを行う //
		if ($form['expire_date'] != '' && !ACSErrorCheck::is_valid_date($form['expire_date'])) {
			$this->setError($controller, $request, $user, 'expire_date', ACSMsg::get_msg('System', 'CreateSystemAnnounceAction.class.php', 'M001'));
			return $this->handleError();
		}

		$form['user_community_id'] = $acs_user_info_row['user_community_id'];

		$ret = ACSSystemAnnounce::set_system_announce($form);

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

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* 必須チェック */
		parent::regValidateName($validatorManager, 
					"subject", 
					true, 
					ACSMsg::get_msg('System', 'CreateSystemAnnounceAction.class.php', 'M002'));
		parent::regValidateName($validatorManager, 
					"body", 
					true, 
					ACSMsg::get_msg('System', 'CreateSystemAnnounceAction.class.php', 'M003'));
		parent::regValidateName($validatorManager, 
					"expire_date", 
					true, 
					ACSMsg::get_msg('System', 'CreateSystemAnnounceAction.class.php', 'M004'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		// 入力値を set
		$form = $request->ACSGetParameters();
		$request->setAttribute('form', $form);

		// 入力画面表示
		return $this->getDefaultView();
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
