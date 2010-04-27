<?php
// $Id: CommunityLinkSuccessView.class.php,v 1.1 2006/03/07 07:35:16 w-ota Exp $

class CommunityLinkSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$sub_community_row_array = $request->getAttribute('sub_community_row_array');
		$parent_community_row_array = $request->getAttribute('parent_community_row_array');
		$community_id = $community_row['community_id'];

		// 親コミュニティ
		foreach ($parent_community_row_array as $index => $parent_community_row) {
			$parent_community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $parent_community_row['community_id'];
			$parent_community_row_array[$index]['delete_community_link_url'] = $this->getControllerPath('Community', 'DeleteCommunityLink') . '&community_id=' . $community_row['community_id'] . '&delete_community_id=' . $parent_community_row['community_id'] . '&mode=parent';
		}
		// サブコミュニティ
		foreach ($sub_community_row_array as $index => $sub_community_row) {
			$sub_community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $sub_community_row['community_id'];
			$sub_community_row_array[$index]['delete_community_link_url'] = $this->getControllerPath('Community', 'DeleteCommunityLink') . '&community_id=' . $community_row['community_id'] . '&delete_community_id=' . $sub_community_row['community_id'] . '&mode=sub';
		}

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// コミュニティ間リンク追加URL
		$add_community_link_url = $this->getControllerPath('Community', 'AddCommunityLink') . '&community_id=' . $community_id;

		// set
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('parent_community_row_array', $parent_community_row_array);
		$this->setAttribute('sub_community_row_array', $sub_community_row_array);

		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('add_community_link_url', $add_community_link_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('CommunityLink.tpl.php');

		return parent::execute();
	}
}

?>
