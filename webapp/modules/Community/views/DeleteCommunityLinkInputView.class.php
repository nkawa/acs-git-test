<?php
// $Id: DeleteCommunityLinkInputView.class.php,v 1.1 2006/03/07 07:35:16 w-ota Exp $

class DeleteCommunityLinkInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$delete_community_row = $request->getAttribute('delete_community_row');
		$mode = $request->getAttribute('mode');

		// 加工
		$delete_community_row['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $delete_community_row['community_id'];

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// コミュニティ間リンク設定URL
		$community_link_url = $this->getControllerPath('Community', 'CommunityLink') . '&community_id=' . $community_row['community_id'];

		// action URL
		$action_url = $this->getControllerPath('Community', 'DeleteCommunityLink') . '&community_id=' . $community_row['community_id'] . '&delete_community_id=' . $delete_community_row['community_id'] . '&mode=' . $mode;
		// back URL
		$back_url = $this->getControllerPath('Community', 'CommunityLink') . '&community_id=' . $community_row['community_id'];

		// set
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('delete_community_row', $delete_community_row);
		$this->setAttribute('mode', $mode);

		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('community_link_url', $community_link_url);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DeleteCommunityLink.tpl.php');

		return parent::execute();
	}
}

?>
