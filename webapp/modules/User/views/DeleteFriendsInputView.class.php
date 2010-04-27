<?php
// $Id: DeleteFriendsView::INPUT.class.php,v 1.1 2006/03/03 13:22:23 w-ota Exp $

class DeleteFriendsInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$delete_user_info_row = $request->getAttribute('delete_user_info_row');

		// 加工
		$delete_user_info_row['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $delete_user_info_row['user_community_id'];
		$delete_user_info_row['image_url'] = ACSUser::get_image_url($delete_user_info_row['user_community_id'], 'thumb');

		// URL
		$action_url = $this->getControllerPath('User', 'DeleteFriends') . '&id=' . $target_user_info_row['user_community_id'] . '&delete_user_community_id=' . $delete_user_info_row['user_community_id'];
		$back_url = $this->getControllerPath('User', 'FriendsList') . '&id=' . $target_user_info_row['user_community_id'];

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('delete_user_info_row', $delete_user_info_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DeleteFriends.tpl.php');

		return parent::execute();
	}
}

?>
