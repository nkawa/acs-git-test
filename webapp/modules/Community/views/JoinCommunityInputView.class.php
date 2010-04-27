<?php
// $Id: JoinCommunityInputView.class.php,v 1.1 2006/01/06 09:51:35 w-ota Exp $

class JoinCommunityInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$is_admission_required = $request->getAttribute('is_admission_required');

		// URL
		$action_url = $this->getControllerPath('Community', 'JoinCommunity') . '&community_id=' . $community_row['community_id'];
		$back_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];
		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// テンプレート
		if ($is_admission_required) {
			// 承認が必要
		$this->setScreenId("0001");
			$this->setTemplate('JoinCommunity_admission.tpl.php');
		} else {
			// 自由参加
		$this->setScreenId("0001");
			$this->setTemplate('JoinCommunity.tpl.php');
		}

		// set
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('is_admission_required', $is_admission_required);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('community_top_page_url', $community_top_page_url);

		return parent::execute();
	}
}

?>
