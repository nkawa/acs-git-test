<?php
/**
 * �ޥ��ڡ�����ǽ��action���饹
 * �ץ�ե������ǧ
 * @package  acs/webapp/modules/User/action
 * ProfileCheckAction
 * @author   akitsu
 * @since	PHP 4.0
 */
// $Id: ProfileCheckAction.class.php,v 1.2 2006/03/28 04:47:43 kuwayama Exp $

class ProfileCheckAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// ɽ���оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		if (empty($user_community_id)) {
			$user_community_id = $acs_user_info_row['user_community_id'];
		}
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �ץ�ե�����
		$target_user_info_row = ACSUser::get_user_profile_row($user_community_id);

		//�桼���������Ԥ����򤷤����
		$view_mode = $request->getParameter('view_mode');

		// set
		$request->setAttribute('user_community_id', $user_community_id);
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('view_mode', $view_mode);


		return View::SUCCESS;
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
