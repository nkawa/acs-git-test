<?php
// $Id: FriendsGroupListView::SUCCESS.class.php,v 1.4 2006/02/17 12:28:15 kuwayama Exp $


class FriendsGroupListSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$friends_group_row_array = $request->getAttribute('friends_group_row_array');

		// マイフレンズグループ数
		$friends_group_row_array_num = count($friends_group_row_array);

		// マイフレンズグループ
		foreach ($friends_group_row_array as $index => $friends_group_row) {
			$friends_group_row_array[$index]['edit_friends_group_member_url'] = $this->getControllerPath('User', 'EditFriendsGroupMember') . '&id=' . $target_user_info_row['user_community_id'] . '&community_id=' . $friends_group_row['community_id'];
			$friends_group_row_array[$index]['delete_friends_group_url'] = $this->getControllerPath('User', 'DeleteFriendsGroup') . '&id=' . $target_user_info_row['user_community_id'] . '&community_id=' . $friends_group_row['community_id'];

			// 各マイフレンズグループのメンバ
			foreach ($friends_group_row_array[$index]['friends_row_array'] as $user_info_row_index => $user_info_row) {
				$friends_group_row_array[$index]['friends_row_array'][$user_info_row_index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $user_info_row['user_community_id'];
				$friends_group_row_array[$index]['friends_row_array'][$user_info_row_index]['image_url'] = ACSUser::get_image_url($user_info_row['user_community_id'], 'thumb');
			}
			$friends_group_row_array[$index]['friends_row_array_num'] = count($friends_group_row_array[$index]['friends_row_array']);
		}

		// マイフレンズグループ作成URL
		$create_friends_group_url = $this->getControllerPath('User', 'CreateFriendsGroup') . '&id=' . $target_user_info_row['user_community_id'];


		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('FriendsGroupList.tpl.php');

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('friends_group_row_array', $friends_group_row_array);
		$this->setAttribute('friends_group_row_array_num', $friends_group_row_array_num);
		$this->setAttribute('create_friends_group_url', $create_friends_group_url);

		return parent::execute();
	}
}

?>
