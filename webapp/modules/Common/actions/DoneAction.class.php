<?php
// $Id: DoneAction.class.php,v 1.2 2006/01/06 07:59:01 kuwayama Exp $

class DoneAction extends BaseAction
{
	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 表示対象のデータを取得
		$done_obj = $request->getAttribute('done_obj');

		// set
		$request->setAttribute('done_obj', $done_obj);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods() {
		return Request::POST;
	}
}

?>
