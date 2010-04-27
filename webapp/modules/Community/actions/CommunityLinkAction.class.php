<?php
// $Id: CommunityLinkAction.class.php,v 1.2 2006/03/28 02:00:22 kuwayama Exp $

class CommunityLinkAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
 
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		
		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSGetParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// サブコミュニティ情報の一覧
		$sub_community_row_array = ACSCommunity::get_sub_community_row_array($community_id);

		// 親コミュニティ情報の一覧
		$parent_community_row_array = ACSCommunity::get_parent_community_row_array($community_id);

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('sub_community_row_array', $sub_community_row_array);
		$request->setAttribute('parent_community_row_array', $parent_community_row_array);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_ADMIN');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// コミュニティ管理者はOK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		return false;
	}
}

?>
