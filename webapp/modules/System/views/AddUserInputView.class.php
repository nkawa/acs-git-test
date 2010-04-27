<?php
// $Id: AddUserView_input.class.php,v 1.2 2006/01/19 10:03:42 w-ota Exp $


class AddUserInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// URL
		$action_url = $this->getControllerPath('System', 'AddUser');
		$back_url = $this->getControllerPath('System', 'UserList');

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('AddUser.tpl.php');

		// set
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);

		return parent::execute();
	}
}

?>
