<?php
// $Id: EditFriendsGroupMemberView::INPUT.class.php,v 1.6 2006/06/16 05:50:21 w-ota Exp $

class EditFriendsGroupMemberInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$friends_row_array = $request->getAttribute('friends_row_array');
		$friends_group_row = $request->getAttribute('friends_group_row');
		$friends_group_member_row_array = $request->getAttribute('friends_group_member_row_array');
		$form = $request->getAttribute('form');

		// 加工
		foreach ($friends_row_array as $index => $user_info_row) {
			$friends_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $user_info_row['user_community_id'];
			$friends_row_array[$index]['image_url'] = ACSUser::get_image_url($user_info_row['user_community_id']);
		}

		// 入力エラー時の復元処理
		if (is_array($form)) {
			// 選択したマイフレンズ
			$friends_group_member_row_array = array();
			if (is_array($form['trusted_community_id_array'])) {
				foreach ($form['trusted_community_id_array'] as $trusted_community_id) {
					$friends_group_member_row = array();
					$friends_group_member_row['user_community_id'] = $trusted_community_id;
					array_push($friends_group_member_row_array, $friends_group_member_row);
				}
			}
		} else {
			$form['community_name'] = $friends_group_row['community_name'];
		}

		foreach ($friends_group_member_row_array as $index => $user_info_row) {
			$friends_group_member_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $user_info_row['user_community_id'];
			$friends_group_member_row_array[$index]['image_url'] = ACSUser::get_image_url($user_info_row['user_community_id']);
		}
		
		// URL
		$action_url = $this->getControllerPath('User', 'EditFriendsGroupMember') . '&id=' . $target_user_info_row['user_community_id'] . '&community_id=' . $friends_group_row['community_id'];

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('EditFriendsGroupMember.tpl.php');

		// set
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));
		$this->setAttribute('form', $form);

		$this->setAttribute('user_community_id', $user_community_id);
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('friends_row_array', $friends_row_array);
		$this->setAttribute('friends_group_row', $friends_group_row);
		$this->setAttribute('friends_group_member_row_array', $friends_group_member_row_array);
		$this->setAttribute('action_url', $action_url);

		return parent::execute();
	}
}

?>
