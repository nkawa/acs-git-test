<?php
require_once(ACS_CLASS_DIR . 'ACSSchedule.class.php');
require_once(ACS_CLASS_DIR . 'ACSScheduleParticipant.class.php');

/**
 * ���ߥ�˥ƥ��Υ������塼�����
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $
 */
class AnswerScheduleAction extends BaseAction
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

		$target_community_id = $request->getParameter('community_id');
		$target_schedule_id = $request->getParameter('schedule_id');

		if (!$this->get_execute_privilege()) {

			// 2010.03.24 ̤���������ͶƳ
			// ������桼���Ǥʤ����ϥ�������̤�
			if ($user->hasCredential('PUBLIC_USER')) {
				$controller->forward("User", "Login");
				return;
			}

			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ������桼�����������
		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);

		// ���ߥ�˥ƥ�����μ���
		$target_community_row = 
				ACSCommunity::get_community_row($target_community_id);
		$request->setAttributeByRef('target_community_row', $target_community_row);

		// �������塼�����μ���
		$schedule =& ACSSchedule::get_schedule_instance(
				   	$target_community_id, $target_schedule_id);

		$request->setAttributeByRef('schedule', $schedule);

		// �������塼�����򥻥å����˥���å���
		$user->setAttribute('schedule', serialize($schedule));

		// ������桼���λ��þ���μ���
		if ($request->getAttribute('schedule_participant')) {
			$schedule_participant =& $request->getAttribute('schedule_participant');
		} else {
			$schedule_participant =&
					ACSScheduleParticipant::get_schedule_participant_instance(
					$schedule->schedule_id, $acs_user_info_row['user_community_id']);
			$request->setAttributeByRef('schedule_participant', $schedule_participant);
		}

		// ������桼���λ��þ���򥻥å����˥���å���
		$user->setAttribute('org_participant', serialize($schedule_participant));

		// �������塼�뻲�ü������ξ�������
		$schedule_participant_list =&
				ACSScheduleParticipant::get_schedule_participant_instance_list(
				$schedule->schedule_id, $schedule->is_target_all());
		$request->setAttributeByRef('schedule_participant_list', $schedule_participant_list);

		return View::SUCCESS;
	}

	/**
	 * ��Ͽ�¹Խ���
	 * POST�᥽�åɤξ�硢�ƤФ��
	 */
	function execute () {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$params =& $request->getParameters();

		$redirect_url = $this->getControllerPath(
				'Community', 'Schedule') .
				"&community_id=" . $params['community_id'];

		$schedule_participant =& $this->getFormPostParticipant(
				&$params, unserialize($user->getAttribute('org_participant')));

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ��Ͽ�Ǥ��ʤ��������塼��ξ�祹�롼
		// �ǿ��������塼������������Ʋ��ݤ��ǧ
		$schedule =& ACSSchedule::get_schedule_instance(
					$params['community_id'], $schedule_participant->schedule_id);
		if ($schedule->is_fixed() || $schedule->is_close()) {
			$controller->redirect($redirect_url);
		}

		// �������ؤξ���ɽ��
		if ($params['participate']) {
			$request->setAttributeByRef('schedule_participant',$schedule_participant);

			// �ģ¹���(������Ͽ�Τ�)
			$schedule_participant->update_participant(TRUE);

			return $this->getDefaultView();

		} else {

			// �ģ¹���
			$schedule_participant->update_participant();
		}

		// ������쥯��(������к�)
		$controller->redirect($redirect_url);
	}

	/**
	 * POST�ѥ�᡼�����饹�����塼�륤�󥹥��󥹤κ���
	 *
	 * @param array $post_params POST�ѥ�᡼��
	 * @param object $org_answer �ѹ����θ��ǡ���
	 * @return object ACSScheduleParticipant���󥹥���
	 */
	function & getFormPostParticipant($post_params, $org_answer) {

		$participant =& $org_answer;

		if ($post_params['participate']) {
			if ($post_params['participate'] == 't') {
				$participant->participant_delete_flag = 'f';
			} else {
				$participant->participant_delete_flag = 't';
			}
		}

		$participant->set_answer($post_params['answers']);
		$participant->participant_comment = $post_params['participant_comment'];

		return $participant;	
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods () {
		return Request::POST;
	}


	function getCredential () {
		return array('COMMUNITY_MEMBER');
	}


	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// ���ߥ�˥ƥ����Ф�OK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}
		return false;
	}
}
?>
