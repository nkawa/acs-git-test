<?php
require_once(ACS_CLASS_DIR . 'ACSSchedule.class.php');

/**
 * ���ߥ�˥ƥ��Υ������塼��ɽ��
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $
 */
class ScheduleAction extends BaseAction
{
	/**
	 * �������
	 * GET�᥽�åɤξ�硢�ƤФ��
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		// ������桼�����������
		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);

		// ���ߥ�˥ƥ�����μ���
		$target_community_id = $request->getParameter('community_id');
		$target_community_row = 
				ACSCommunity::get_community_row($target_community_id);
		$request->setAttributeByRef('target_community_row', $target_community_row);

		// ���ߥ�˥ƥ��Ϳ��μ���
		$request->setAttribute('member_count',
				ACSCommunity::get_community_member_count($target_community_id));

		// �������塼�륤�󥹥�������μ���
		$schedule_array =& 
				ACSSchedule::get_community_schedule_instance_list($target_community_id);

		$request->setAttributeByRef('schedules', $schedule_array);

		// �������塼��Ϳ���������μ���
		$schedule_persons_array =& 
				ACSSchedule::get_total_person_count($target_community_id);

		$request->setAttributeByRef('schedule_persons', $schedule_persons_array);
		
		return View::SUCCESS;
	}

	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		return $this->getDefaultView();
	}

	function getRequestMethods () {
		return Request::POST | Request::GET;
	}

	function isSecure () {
		return false;
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
