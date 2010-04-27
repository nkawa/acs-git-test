<?php
// $Id: CloseChildWindowView::SUCCESS.class.php,v 1.1 2005/12/28 08:59:39 w-ota Exp $

class CloseChildWindowSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// テンプレート
		$this->setTemplate('CloseChildWindow.tpl.php');

		return parent::execute();
	}
}
?>
