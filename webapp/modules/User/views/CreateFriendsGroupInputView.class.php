<?php
// $Id: CreateFriendsGroupView::INPUT.class.php,v 1.4 2006/06/16 05:50:21 w-ota Exp $

class CreateFriendsGroupInputView extends BaseView
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
		$friends_row_array_num = count($friends_row_array);

		$form = $request->getAttribute('form');


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
			$friends_group_member_row_array = array();
		}

		// URL
		$action_url = $this->getControllerPath('User', 'CreateFriendsGroup') . '&id=' . $target_user_info_row['user_community_id'];

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('CreateFriendsGroup.tpl.php');

		// set
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));
		$this->setAttribute('form', $form);

		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('friends_row_array', $friends_row_array);
		$this->setAttribute('friends_row_array_num', $friends_row_array_num);
		$this->setAttribute('friends_group_member_row_array', $friends_group_member_row_array);

		return parent::execute();
	}
}

?>
