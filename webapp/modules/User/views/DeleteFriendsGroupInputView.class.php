<?php
// $Id: DeleteFriendsGroupView::INPUT.class.php,v 1.3 2006/01/19 10:03:51 w-ota Exp $


class DeleteFriendsGroupInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$friends_group_row = $request->getAttribute('friends_group_row');

		// 加工
		// URL
		$action_url = $this->getControllerPath('User', 'DeleteFriendsGroup') . '&id=' . $target_user_info_row['user_community_id'] . '&community_id=' . $friends_group_row['community_id'];
		$back_url = $this->getControllerPath('User', 'FriendsGroupList') . '&id=' . $target_user_info_row['user_community_id'];

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DeleteFriendsGroup.tpl.php');

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('friends_group_row', $friends_group_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);

		return parent::execute();
	}
}

?>
