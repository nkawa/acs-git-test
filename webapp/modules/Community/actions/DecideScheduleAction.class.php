<?php
require_once(dirname(__FILE__).'/AnswerScheduleAction.class.php');
//require_once(VALIDATOR_DIR  . 'StringValidator.class.php');

// ����ʸ������Ĺ(Ⱦ�ѤǤ�ʸ����)
define( '_DECIDESCHEDULEACTION_MAIL_SUBJECT_MAXLEN', 256 );
define( '_DECIDESCHEDULEACTION_MAIL_MESSAGE_MAXLEN', 4096 );

/**
 * ���ߥ�˥ƥ��Υ������塼�����
 *
 * @author  z-satosi
 * @version $Revision: 1.3 $
 */
class DecideScheduleAction extends AnswerScheduleAction
{
	/**
	 * �������
 	 *
	 * GET�᥽�åɤξ�硢�ƤФ�롣
	 * "schedule_id"�ѥ�᡼����̵�����Ͽ��������Ȥ��롣
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		$params =& $request->getParameters();

		// ����������̤�������ܤξ��
		if ($params['post_from_answer']=='t') {
			return $this->execute();
		}

		$target_community_id = $params['community_id'];
		$target_schedule_id = $params['schedule_id'];

		// ������桼�����������
		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);

		// �����Ǥʤ��������塼������Ѥξ�祻�����ƥ����顼
		// �ǿ��������塼������������Ʋ��ݤ��ǧ
		$schedule =& ACSSchedule::get_schedule_instance(
					$params['community_id'], $params['schedule_id']);
		if ($schedule->is_fixed() || !$schedule->is_organizer($acs_user_info_row)) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �����������������(�᡼��ե����फ��"���"�����б�)
		$request->setAttribute('mailentry_adjustment_id',
				$request->getParameter('mailentry_adjustment_id'));

		return parent::getDefaultView();
	}

	/**
	 * �᡼��ե��������ϲ��̤�ɽ��
	 */
	function getMailInputView (&$controller, &$request, &$user) {

		$params =& $request->getParameters();
		$schedule =& $request->getAttribute('schedule');

		$target_community_id = $params['community_id'];
		$target_schedule_id = $params['schedule_id'];

		// ������桼�����������
		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);

		// �����Ǥʤ��������塼������Ѥξ�祻�����ƥ����顼
		if ($schedule->is_fixed() || !$schedule->is_organizer($acs_user_info_row)) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ���ߥ�˥ƥ�����μ���
		$target_community_row =
				ACSCommunity::get_community_row($target_community_id);
		$request->setAttributeByRef('target_community_row', $target_community_row);

		// �����������������
		$request->setAttributeByRef('mailentry_adjustment_id',
						$params['mailentry_adjustment_id']);

		// ���顼���κ�ɽ����
		$request->setAttribute('mail_subject',$params['mail_subject']);
		$request->setAttribute('mail_message',$params['mail_message']);

		return View::INPUT;
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

		// ������桼�����������
		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);

		// �����Ǥʤ��������塼������Ѥξ�祻�����ƥ����顼
		// �ǿ��������塼������������Ʋ��ݤ��ǧ
		$schedule =& ACSSchedule::get_schedule_instance(
					$params['community_id'], $params['schedule_id']);
		if ($schedule->is_fixed() || !$schedule->is_organizer($acs_user_info_row)) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ��������������̤����POST�ξ��
		if ($params['post_from_answer']=='t') {
			$request->setAttributeByRef('schedule',$schedule);

			$return_view = $this->getMailInputView($controller, $request, $user);

		// �᡼�����ϲ��̤����POST�ξ��
		} else {

			// DB����
			$schedule->update_decide_schedule($params['mailentry_adjustment_id']);

			// �����ƥ�Υ᡼�륢�ɥ쥹�����
			$system_mail_addr = ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');

			// �᡼��������������
			// ��ͳ���äξ���оݤȤʤ���Фμ���
			$target_mmb = "";
			if ($schedule->is_target_all()===FALSE) {
				$p_array =& ACSScheduleParticipant::get_schedule_participant_instance_list(
				$schedule->schedule_id,FALSE);
				foreach ($p_array as $user_community_id => $schedule_participant) {
					$target_mmb[] = $user_community_id;
				}
				$p_array = "";
			}

			// �᡼�������
			ACSCommunityMail::send_community_mail(
					$schedule->community_id, $system_mail_addr, 
					$params['mail_subject'], $params['mail_message'], $target_mmb);

			// ������쥯��(������к�)
			$controller->redirect(
					$this->getControllerPath(
					'Community', 'Schedule') .
					"&community_id=" . $schedule->community_id);

		}
		return $return_view;
	}

	function getRequestMethods () {
		return Request::POST;
	}

	/**
	 * �����ͥ����å�
	 */
	function validate () {
		return TRUE;
	}

	/**
	 * �����ͥ����å�(ValidatorManager����)
	 */
	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// �ѥ�᡼������
		$params =& $request->getParameters();

		// �᡼�����ϲ��̤����POST�ξ��
		if ($params['post_from_answer']!='t') {

			// ��̾�����ϥ����å�
			parent::regValidateName($validatorManager, 
				"mail_subject", 
				true, 
				ACSMsg::get_msg('Community', 'DecideScheduleAction.class.php', 'M050'));

			if ($params['mail_subject']) {
				$validator =& new StringValidator($controller);
				$validator->initialize(array(
						'max' => _DECIDESCHEDULEACTION_MAIL_SUBJECT_MAXLEN,
						'max_error' => ACSMsg::get_msg('Community', 'DecideScheduleAction.class.php', 'M051').
							_DECIDESCHEDULEACTION_MAIL_SUBJECT_MAXLEN));
			}

			// ��ʸ�����ϥ����å�
			parent::regValidateName($validatorManager, 
				"mail_message", 
				true, 
				ACSMsg::get_msg('Community', 'DecideScheduleAction.class.php', 'M052'));

			if ($params['mail_message']) {
				$validator =& new StringValidator($controller);
				$validator->initialize(array(
						'max' => _DECIDESCHEDULEACTION_MAIL_MESSAGE_MAXLEN,
						'max_error' => ACSMsg::get_msg('Community', 'DecideScheduleAction.class.php', 'M053').
							_DECIDESCHEDULEACTION_MAIL_MESSAGE_MAXLEN));
			}
		}
	}

	/**
	 * ���ϥ����å����顼�����б�
	 */
	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$params =& $request->getParameters();

		$schedule =& ACSSchedule::get_schedule_instance(
					$params['community_id'], $params['schedule_id']);

		$request->setAttributeByRef('schedule',$schedule);

		return $this->getMailInputView($controller, $request, $user);
	}	

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_MEMBER');
	}

}
?>
