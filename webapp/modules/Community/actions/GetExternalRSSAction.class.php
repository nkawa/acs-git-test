<?php
// ����RSS����ư����¹�
// $Id: GetExternalRSSAction.class.php,v 1.1 2007/03/28 05:58:18 w-ota Exp $

class GetExternalRSSAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->ACSgetParameter('community_id');
		$community_row = ACSCommunity::get_community_row($community_id);

		// �¹�
		ACSExternalRSS::do_process($community_row);

		$bbs_url = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_id;
		header("Location: $bbs_url");
	}

	function isSecure() {
		return false;
	}

	function getPrivilege(&$controller, &$request, &$user) {
		return array('COMMUNITY_ADMIN');
	}
}

?>
