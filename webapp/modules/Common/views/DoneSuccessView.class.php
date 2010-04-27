<?php
// $Id: DoneView::SUCCESS.class.php,v 1.2 2006/01/06 07:56:22 kuwayama Exp $


class DoneSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$done_obj = $request->getAttribute('done_obj');

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('Done.tpl.php');

		// set
		$this->setAttribute('title', $done_obj->get_title());
		$this->setAttribute('message', $done_obj->get_message());
		$this->setAttribute('link_row_array', $done_obj->get_link_row_array());

		return parent::execute();
	}
}

?>
