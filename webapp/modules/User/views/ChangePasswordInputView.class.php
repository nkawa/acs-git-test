<?php
// $Id: ChangePasswordInputView.class.php,v 1.2 2006/03/28 08:26:31 kuwayama Exp $

class ChangePasswordInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// URL
		$action_url = $this->getControllerPath('User', 'ChangePassword') . '&id=' . $acs_user_info_row['user_community_id'];
		$back_url = $this->getControllerPath('User', DEFAULT_ACTION);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('ChangePassword.tpl.php');

		// set
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);

		return parent::execute();
	}
}

?>
