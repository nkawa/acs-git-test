<?php
// $Id: CommunityMemberListAction.class.php,v 1.2 2005/12/28 06:36:34 w-ota Exp $

class CommunityMemberListAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->ACSgetParameter('community_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_row($community_id);

		// ���ߥ�˥ƥ����а������������
		$community_member_user_info_row_array = ACSCommunity::get_community_member_user_info_row_array($community_id);

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('community_member_user_info_row_array', $community_member_user_info_row_array);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
}

?>
