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
		
		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->ACSGetParameter('community_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// ���֥��ߥ�˥ƥ�����ΰ���
		$sub_community_row_array = ACSCommunity::get_sub_community_row_array($community_id);

		// �ƥ��ߥ�˥ƥ�����ΰ���
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

		// ���ߥ�˥ƥ������Ԥ�OK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		return false;
	}
}

?>
