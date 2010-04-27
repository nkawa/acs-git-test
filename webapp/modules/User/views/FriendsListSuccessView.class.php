<?php
// $Id: FriendsListView::SUCCESS.class.php,v 1.8 2006/11/20 08:44:28 w-ota Exp $

class FriendsListSuccessView extends BaseView
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


		// 本人のページかどうか
		if ($target_user_info_row['user_community_id'] == $acs_user_info_row['user_community_id']) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}

		// トップページURL
		$link_page_url['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $user_info_row['user_community_id'];
		//他人の日記を閲覧している場合のトップページURL
		$link_page_url['else_user_diary_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Index') . '&id=' . $target_user_info_row['community_id'];

		// 加工
		if ($acs_user_info_row['user_id'] == $target_user_info_row['user_id']) {
			$friends_group_list_url = $this->getControllerPath('User', 'FriendsGroupList') . '&id=' . $target_user_info_row['user_community_id'];
		}

		foreach ($friends_row_array as $index => $friends_row) {
			$friends_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $friends_row['user_community_id'];
			$friends_row_array[$index]['image_url'] = ACSUser::get_image_url($friends_row['user_community_id'], 'thumb');
			if ($acs_user_info_row['user_id'] == $target_user_info_row['user_id']) {
				$friends_row_array[$index]['delete_friends_url'] = $this->getControllerPath(DEFAULT_MODULE, 'DeleteFriends') . '&id=' . $target_user_info_row['user_community_id'] . '&delete_user_community_id=' . $friends_row['user_community_id'];
			}
			// マイフレンズ人数
			$friends_row_array[$index]['friends_row_array_num'] = ACSUser::get_friends_row_array_num($friends_row['user_community_id']);
		}

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $friends_row_array, $display_count);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('FriendsList.tpl.php');

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('friends_row_array', $friends_row_array);
		$this->setAttribute('friends_row_array_num', $friends_row_array_num);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('friends_group_list_url', $friends_group_list_url);

		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('link_page_url', $link_page_url);

		return parent::execute();
	}
}

?>
