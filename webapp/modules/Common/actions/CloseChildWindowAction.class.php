<?php
// $Id: CloseChildWindowAction.class.php,v 1.1 2005/12/28 08:58:57 w-ota Exp $

class  CloseChildWindowAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
}

?>
