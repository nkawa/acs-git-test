<?php
// $Id: FriendsListAction.class.php,v 1.5 2006/11/20 08:44:25 w-ota Exp $

class FriendsListAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');

		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// �ޥ��ե�󥺰������������
		$friends_row_array = ACSUser::get_friends_row_array($user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('friends_row_array', $friends_row_array);

		return View::SUCCESS;
	}

	// ���������������
	function get_access_control_info(&$controller, &$request, &$user) {
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->getParameter('id');

		// ���ߥ�˥ƥ�����
		$user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// ��������������� //
		$friends_list_contents_row = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D11'));
		$access_control_info = array(
				 'role_array' => ACSAccessControl::get_user_community_role_array($acs_user_info_row, $user_info_row),
				 'contents_row_array' => array($friends_list_contents_row)
		);
		return $access_control_info;
	}

	function isSecure () {
		return false;
	}
}

?>
