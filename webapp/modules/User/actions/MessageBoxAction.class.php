<?php
/**
 * ��å�����BOX��Action���饹
 * 
 * MessageBoxAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   nakau
 * @since    PHP 4.0
 */
// $Id: MessageBoxAction.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $

class MessageBoxAction extends BaseAction
{
	// GET
	function getDefaultView() {
		
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// ɽ���оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');

		// ¾�桼���Υǡ����������ʤ��褦�����å�
		if (!$this->get_execute_privilege()
				&& $acs_user_info_row["user_community_id"] != $user_community_id) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		//�����Ѳ��̤ν���
		$move_id = $request->getParameter('move_id');
		if($move_id == 2){
			// ���Ƥ������ѥ�å�����
			$message_row_array = ACSMessage::get_send_message_row_array($user_community_id);
		} else {
			// ���Ƥμ�����å�����
			$message_row_array = ACSMessage::get_receive_message_row_array($user_community_id);
		}
		
		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('message_row_array', $message_row_array);
		$request->setAttribute('move_id', $move_id);
		
		return View::INPUT;
	}
	
	function execute() {
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

		// �������桼�����ܿͰʳ���NG
		if ($user->hasCredential('PUBLIC_USER')
				 || !$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
	}

}

?>
