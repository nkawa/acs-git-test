<?php
// $Id: AddFriendsInputView.class.php,v 1.2 2006/01/19 10:03:51 w-ota Exp $


class AddFriendsInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');

		//
		$action_url =  $this->getControllerPath('User', 'AddFriends') . '&id=' . $target_user_info_row['user_community_id'];
		$back_url =  $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $target_user_info_row['user_community_id'];

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('AddFriends.tpl.php');

		return parent::execute();
	}
}

?>
