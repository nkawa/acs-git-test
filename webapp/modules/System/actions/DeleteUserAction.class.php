<?php
/**
 * �����ƥࡡ�桼���������桼�������ѹ����� action���饹
 * @package  acs/webapp/modules/System/actions
 * DeleteUserAction
 * @author   akitsu  
 * @since	PHP 4.0
 */
// $Id: DeleteUserAction.class.php,v 1.6 2006/12/08 05:06:39 w-ota Exp $

class DeleteUserAction extends BaseAction
{
	// GET([���]��󥯤��������)
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		// �����Ԥ��ɤ�����ǧ
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$user_id = $request->getParameter('id');
		$user_info_row = ACSUser::get_user_profile_row($user_id, 'include_private_flag');
		
		$request->setAttribute('user_info_row', $user_info_row);
		$user->setAttribute('user_id', $user_id);
		return View::SUCCESS;
	}

	// POST��[OK]�ܥ��󤫤�����ܡ�
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		// �����Ԥ��ɤ�����ǧ
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$user_community_id = $user->getAttribute('user_id');

		$target_user_info_row = ACSUser::get_user_profile_row($user_community_id, 'include_private_flag');

		// �桼������κ���ե饰���ѹ�����
		$ret = ACSUser::delete_user_community($user_community_id);
		if(!$ret){
			echo "Warning : DB ERROR : Delete user failed.";
			return;
		}

		// ����Ͽ: �桼�����
		ACSLog::set_log($acs_user_info_row, 'Remove User', $ret, "[UserID:$target_user_info_row[user_id]]");

		// �桼��������ɽ��
		$user_list_url = $this->getControllerPath('System', 'UserList');
		header("Location: $user_list_url");
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('SYSTEM_ADMIN_USER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �����Ԥξ���OK
		if ($user->hasCredential('SYSTEM_ADMIN_USER')) {
			return true;
		}
		return false;
	}
}

?>
