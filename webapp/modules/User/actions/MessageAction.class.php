<?php
/**
 * ��å�������Action���饹
 * 
 * MessageAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   nakau                    
 * @since    PHP 4.0
 */
// $Id: MessageAction.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $

class MessageAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		// ɽ���оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');

		// ¾�桼���Υǡ����������ʤ��褦�����å�
		if (!$this->get_execute_privilege()) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);

		return View::INPUT;
	}
	
	function execute() {
		
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$request = $context->getRequest();
		$user = $context->getUser();

		// �������桼����NG
		if ($user->hasCredential('PUBLIC_USER')) {
			return false;
		}

		// �桼����������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		if ($request->getParameter('move_id') == 4){
			$message_id = $request->getParameter('message_id');
			// ¾�桼���μ�����å������������ʤ��褦�����å�
			if (!ACSMessage::check_message_receiver($message_id, $acs_user_info_row["user_community_id"])) {
				return false;
			}
		}
		// �ܿͤξ���OK
		return true;
	}

}

?>
