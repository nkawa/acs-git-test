<?php
// $Id: CreateFriendsGroupAction.class.php,v 1.6 2006/11/20 08:44:25 w-ota Exp $

class CreateFriendsGroupAction extends BaseAction
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

		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// �ޥ��ե�󥺰������������
		$friends_row_array = ACSUser::get_friends_row_array($user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('friends_row_array', $friends_row_array);

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

		$form = $request->ACSGetParameters();
		$form['user_community_id'] = $user_community_id;

		// ����
		$form['community_id'] = ACSUser::set_friends_group($form);
		// ���Х��å�
		ACSUser::update_friends_group_member($form);

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

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* ɬ�ܥ����å� */
		parent::regValidateName($validatorManager, 
				"community_name", 
				true, 
				ACSMsg::get_msg('User', 'CreateFriendsGroupMemberAction.class.php', 'M001'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		// �����ͤ� set
		$form = $request->ACSGetParameters();
		$request->setAttribute('form', $form);

		// ���ϲ���ɽ��
		return $this->getDefaultView();
	}
}

?>
