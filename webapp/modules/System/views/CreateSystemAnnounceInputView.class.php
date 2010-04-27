<?php
// $Id: CreateSystemAnnounceView_input.class.php,v 1.2 2006/06/23 07:53:51 w-ota Exp $

class CreateSystemAnnounceInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$form = $request->getAttribute('form');

		// URL
		$action_url = $this->getControllerPath('System', 'CreateSystemAnnounce');
		$system_announce_list_url = $this->getControllerPath('System', 'SystemAnnounceList');

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('CreateSystemAnnounce.tpl.php');

		// set
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));
		$this->setAttribute('form', $form);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('system_announce_list_url', $system_announce_list_url);

		return parent::execute();
	}
}

?>
