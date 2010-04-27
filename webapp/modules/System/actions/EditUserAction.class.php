<?php
/**
 * �����ƥࡡ�桼���������桼�������ѹ����� action���饹
 * @package  acs/webapp/modules/System/actions
 * EditUserAction
 * @author   akitsu  
 * @since	PHP 4.0
 */
// $Id: EditUserAction.class.php,v 1.8 2008/04/24 16:00:00 y-yuki Exp $

class EditUserAction extends BaseAction
{
	// GET([�ѹ�]��󥯤��������)
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
		return View::INPUT;
	}

	// POST��[�ѹ�]�ܥ��󤫤�����ܡ�
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
		$form = $request->ACSGetParameters();
		$user_community_id = $user->getAttribute('user_id');

		$form['user_community_id'] = $user_community_id;
		$target_user_info_row = ACSUser::get_user_profile_row($user_community_id, 'include_private_flag');
		$post_user_info_row = ACSUser::get_user_info_row_by_user_id($form['user_id']);
		if ($post_user_info_row 
				&& $user_community_id != $post_user_info_row['user_community_id'])
			{
				echo ACSMsg::get_msg('System', 'EditUserAction.class.php', 'M002');
				return;
		}

		//�ѥ���ɥ����å�
		if($form['passwd_change'] == 'change_on' && $form['passwd'] == $form['passwd2'] || $form['passwd_change'] == ''){
			// �桼��������ѹ�����
			$ret = ACSUser::update_user_info($form);
			if(!$ret){
				echo "Warning: Update user information failed.";
				return;
			}
		}else{
			echo ACSMsg::get_msg('System', 'EditUserAction.class.php', 'M001');
			return;
		}

		// ����Ͽ: �桼�������ѹ�
		ACSLog::set_log($acs_user_info_row, 'Change User Information', $ret, "[UserID:$target_user_info_row[user_id]]");

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
