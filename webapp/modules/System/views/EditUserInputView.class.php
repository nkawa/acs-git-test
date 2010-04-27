<?php
/**
 * システム　ユーザ管理　ユーザ情報変更画面 viewクラス
 * @package  acs/webapp/modules/System/views
 * EditUserView_input
 * @author   akitsu  
 * @since    PHP 4.0
 */
// $Id: EditUserView_input.class.php,v 1.1 2006/03/13 07:04:58 z-akitsu Exp $


class EditUserInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$user_info_row = $request->getAttribute('user_info_row');

		// URL
		$action_url = $this->getControllerPath('System', 'EditUser');
		$back_url = $this->getControllerPath('System', 'UserList');

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('EditUser.tpl.php');

		// set
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);
		
		$this->setAttribute('user_info_row', $user_info_row);

		return parent::execute();
	}
}

?>
