<?php
/**
 * ���ߥ�˥ƥ����к�� ���а���ɽ��
 *
 * @author  kuwayama
 * @version $Revision: 1.4 $ $Date: 2006/03/28 02:00:22 $
 */
class DeleteCommunityMemberListAction extends BaseAction
{
	/**
	 * �������
	 * GET�᥽�åɤξ�硢�ƤФ��
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$target_community_id = $request->getParameter('community_id');


		/* ���о������ */
		$target_community_member_info_row_array = ACSCommunity::get_community_member_user_info_row_array($target_community_id);

		$request->setAttribute('target_community_member_info_row_array', $target_community_member_info_row_array);

		$this->set_request_community_info(&$request);

		return View::INPUT;
	}

	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		/* INPUT ����ɽ�� */
		if ($request->getParameter('action_type') == 'back') {
			// ���򤵤�Ƥ��� user_community_id ����
			$selected_user_community_id_array = array();
			$selected_user_community_id_array = $request->getParameter('delete_user_community_id_array');

			$request->setAttribute('selected_user_community_id_array', $selected_user_community_id_array);

			return $this->getDefaultView();
		}

		/* CONFIRM ����ɽ�� */
		else if ($request->getParameter('action_type') == 'confirm') {
			/* ���顼����� */
			if ($request->hasErrors()) {
				// ���顼��������ϡ�INPUT ����ɽ��
				$user->removeAttribute('error_row');
				$request->setAttribute('error_row', $error_row);

				return $this->getDefaultView();
			}
		
			/* POST �ǡ����������� */
			$delete_user_community_id_array = $request->getParameter('delete_user_community_id_array');
			$delete_user_info_row_array = $this->get_user_info_row_array($delete_user_community_id_array);

			/* View ���Ϥ��ͥ��å� */
			$this->set_request_community_info(&$request);
			$request->setAttribute('delete_user_info_row_array', $delete_user_info_row_array);
			
			return View::SUCCESS;
		}
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {

		parent::regValidateName($validatorManager, 
				"delete_user_community_id_array", 
				true, 
				'���򤷤Ƥ���������');
	}

	function handleError () {
		// ���顼�ξ�硢INPUT ���̤�ɽ��
		return $this->getDefaultView();
	}

	function set_request_community_info (&$request) {
		/* ���ߥ�˥ƥ�������� */
		$target_community_id = $request->getParameter('community_id');
		$target_community_row = ACSCommunity::get_community_row($request->getParameter('community_id'));

		$request->setAttribute('target_community_row', $target_community_row);
	}

	function get_user_info_row_array ($user_community_id_array) {
		$user_info_row_array = array();
		foreach ($user_community_id_array as $user_community_id) {
			$user_info_row = array();
			$user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

			array_push($user_info_row_array, $user_info_row);
		}

		return $user_info_row_array;
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
