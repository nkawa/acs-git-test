<?php
// $Id: DeleteSystemAnnounceView_input.class.php,v 1.1 2006/06/13 02:49:45 w-ota Exp $

class DeleteSystemAnnounceInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$system_announce_row = $request->getAttribute('system_announce_row');

		// URL
		$action_url = $this->getControllerPath('System', 'DeleteSystemAnnounce')
			 . '&system_announce_id=' . $system_announce_row['system_announce_id'];
		$system_announce_list_url = $this->getControllerPath('System', 'SystemAnnounceList');

		// 加工
		$system_announce_row['expire_date'] = ACSLib::convert_pg_date_to_str($system_announce_row['expire_date'], false, false, false);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DeleteSystemAnnounce.tpl.php');

		// set
		$this->setAttribute('system_announce_row', $system_announce_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('system_announce_list_url', $system_announce_list_url);

		return parent::execute();
	}

	function isSecure() {
		return false;
	}
}

?>
