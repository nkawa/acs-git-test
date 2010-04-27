<?php
// $Id: DeleteFriendsGroupAction.class.php,v 1.4 2006/03/28 04:38:13 kuwayama Exp $

class DeleteFriendsGroupAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		// �оݤȤʤ�ޥ��ե�󥺥��롼�ץ��ߥ�˥ƥ�ID�����
		$friends_group_community_id = $request->ACSgetParameter('community_id');
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// get
		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);
		// ���ꤵ�줿�ޥ��ե�󥺥��롼�פξ���
		$friends_group_row = ACSCommunity::get_community_row($friends_group_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('friends_group_row', $friends_group_row);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		// �оݤȤʤ�ޥ��ե�󥺥��롼�ץ��ߥ�˥ƥ�ID�����
		$friends_group_community_id = $request->ACSgetParameter('community_id');

		// ����
		ACSUser::delete_friends_group($friends_group_community_id);

		$friends_group_list_top_page_url = $this->getControllerPath('User', 'FriendsGroupList') . '&id=' . $user_community_id;
		header("Location: $friends_group_list_top_page_url");
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �ܿͤǡ�LDAPǧ�ڰʳ��ξ���OK
		if ($user->hasCredential('USER_PAGE_OWNER')) {
			return true;
		}
		return false;
	}
}

?>
