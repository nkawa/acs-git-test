<?php
// $Id: CommunityMemberListSuccessView.class.php,v 1.4 2006/06/23 07:32:54 w-ota Exp $


class CommunityMemberListSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$community_member_user_info_row_array = $request->getAttribute('community_member_user_info_row_array');
		$community_member_user_info_row_array_num = count($community_member_user_info_row_array);

		foreach ($community_member_user_info_row_array as $index => $user_info_row) {
			$community_member_user_info_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $user_info_row['user_community_id'];
			$community_member_user_info_row_array[$index]['image_url'] = ACSUser::get_image_url($user_info_row['user_community_id'], 'thumb');
			$community_member_user_info_row_array[$index]['friends_row_array_num'] = ACSUser::get_friends_row_array_num($user_info_row['user_community_id']);
		}

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('CommunityMemberList.tpl.php');

		// set
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('community_member_user_info_row_array', $community_member_user_info_row_array);
		$this->setAttribute('community_member_user_info_row_array_num', $community_member_user_info_row_array_num);
		$this->setAttribute('community_top_page_url', $community_top_page_url);

		return parent::execute();
	}
}

?>
