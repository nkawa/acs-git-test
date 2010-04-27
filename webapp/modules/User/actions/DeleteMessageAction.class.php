<?php
/**
 * ��å����� ���
 *
 * @author  nakau
 * @version $Revision: 1.1 $ $Date: 2008/03/24 07:09:27 $
 */
class DeleteMessageAction extends BaseAction
{
	
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		// ɬ�ܥ����å�
		//    Validator �ǤǤ��ʤ������å��Ϥ����ǹԤ�
		if (!$request->getParameter('selected_message')) {
			// ���顼�ξ�硢������λ
			return $this->setError($controller, $request, $user, 'selected_message', 
					ACSMsg::get_msg('User', 'DeleteMessageAction.class.php', 'M001'));
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_user_community_id = $request->getParameter('id');
		// �оݤȤʤ��å�����ID�����
		$target_message_id = $request->getParameter('selected_message');

		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('target_message_id', $target_message_id);
		$request->setAttribute('move_id', $request->getParameter('move_id'));

		/* ------------ */
		/* ��ǧ����ɽ�� */
		/* ------------ */
		if ($request->getParameter('action_type') == 'confirm') {
			return View::SUCCESS;
		}

		/* -------- */
		/* ������� */
		/* -------- */
		elseif ($request->getParameter('action_type') == 'delete') {
			$move_id = $request->getParameter('move_id');
			ACSDB::_do_query("BEGIN");
			// �ե����
			$folder_row_array = array();
			$delete_message_id_array = $request->getParameter('selected_message');
			if ($delete_message_id_array) {
				if ($move_id == 2) {
					foreach ($delete_message_id_array as $message_id) {
						// �������
						$ret =ACSMessage::delete_send_message($message_id);
						if (!$ret) {
							ACSDB::_do_query("ROLLBACK;");
							print "ERROR: Delete message failed.";
							exit;
						}
					}
				} else {
					foreach ($delete_message_id_array as $message_id) {
						// �������
						$ret =ACSMessage::delete_receive_message($message_id);
						if (!$ret) {
							ACSDB::_do_query("ROLLBACK;");
							print "ERROR: Delete message failed.";
							exit;
						}
					}
					
				}
			}

			ACSDB::_do_query("COMMIT;");

			// �ե����ɽ�����������ƤӽФ�
			$message_action  = $this->getControllerPath('User', 'MessageBox');
			$message_action .= '&id=' . $target_user_community_id;
			if($move_id == 2){
				$message_action .= '&move_id=2';
			}

			header("Location: $message_action");
		}
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		/* ���顼��å������򥻥å����˥��å� */
		$this->sendError($controller, $request, $user);

		// ��å�����ɽ�����������ƤӽФ�
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_community_id = $request->getParameter('id');
		$move_id = $request->getParameter('move_id');

		$message_action = $this->getControllerPath('User', 'MessageBox');
		$message_action .= '&id=' . $target_user_community_id;
		if($move_id == 2){
				$message_action .= '&move_id=2';
			}
		header("Location: $message_action");
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('USER_PAGE_OWNER');
	}
}
?>
