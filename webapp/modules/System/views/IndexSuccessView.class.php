<?php
// $Id: IndexView_success.class.php,v 1.6 2006/06/14 05:34:04 w-ota Exp $


class IndexSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ユーザ一覧URL
		$user_list_url = $this->getControllerPath('System', 'UserList');

		// ログ管理URL
		$log_url = $this->getControllerPath('System', 'Log');

		// システムアナウンス(システムからのお知らせ)一覧URL
		$system_announce_list_url = $this->getControllerPath('System', 'SystemAnnounceList');

		// システム設定URL
		$edit_system_config_url = $this->getControllerPath('System', 'EditSystemConfig');

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('Index.tpl.php');

		// set
		$this->setAttribute('user_list_url', $user_list_url);
		$this->setAttribute('log_url', $log_url);
		$this->setAttribute('create_system_announce_url', $create_system_announce_url);
		$this->setAttribute('system_announce_list_url', $system_announce_list_url);
		$this->setAttribute('edit_system_config_url', $edit_system_config_url);

		return parent::execute();
	}
}

?>
