<?php
/**
 * ���ߥ�˥ƥ��Υ������塼��ɽ��
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $
 */

class ScheduleSuccessView extends BaseView
{
	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$this->setScreenId("0001");
		$this->setTemplate('Schedule.tpl.php');

		$current_module = 'Community';

		// ������桼������
		$this->setAttribute('acs_user_info_row', 
				$request->getAttribute('acs_user_info_row'));

		// ���ߥ�˥ƥ�����
		$target_community_row =& $request->getAttribute('target_community_row');
		$this->setAttribute('target_community_row', $target_community_row);

		// ����URL�ѥ�᡼��
		$url_params = "community_id=" . $target_community_row['community_id'];

		// �������塼�륤�󥹥��󥹤�����
		$schedules =& $request->getAttribute('schedules');

		// ɽ�������������
		$schedule_list = array();
		$schedule_persons =& $request->getAttribute('schedule_persons');
		foreach ($schedules as $schedule) {
			$schedule_params = $url_params . "&schedule_id=" . $schedule->schedule_id;

			$person_count_info = $schedule_persons[$schedule->schedule_id];

			if ($schedule->is_target_all()) {
				$person_count_info['participate_person_count'] =
						$request->getAttribute('member_count');
			}

			$schedule_list[] = array(
					'instance' => $schedule,
					'url_edit' =>
						$this->getControllerPath($current_module, 'EditSchedule') .
						"&" . $schedule_params,
					'url_decide' =>
						$this->getControllerPath($current_module, 'DecideSchedule') .
						"&" . $schedule_params,
					'url_answer' =>
						$this->getControllerPath($current_module, 'AnswerSchedule') .
						"&" . $schedule_params,
					'disp_detail' =>
						(mb_strlen($schedule->schedule_detail) > 15 ?
						mb_substr($schedule->schedule_detail,0,15)."..." :
						$schedule->schedule_detail),
					'disp_person_count' =>
						$person_count_info['answer_person_count'] . " / " .
						$person_count_info['participate_person_count'] . " " .
						ACSMsg::get_msg('Community', 'ScheduleSuccessView.class.php', 'M004'),
					'disp_closing' =>
						ACSLib::convert_pg_date_to_str($schedule->schedule_closing_datetime),
					'disp_status' =>
						($schedule->is_fixed() ? ACSMsg::get_msg('Community', 'ScheduleSuccessView.class.php', 'M001') : 
							($schedule->is_close() ? 
							ACSMsg::get_msg('Community', 'ScheduleSuccessView.class.php', 'M002') : 
							ACSMsg::get_msg('Community', 'ScheduleSuccessView.class.php', 'M003')))

			);
		}

		// ���ߥ�˥ƥ���URL
		$this->setAttribute('url_community_top',
				$this->getControllerPath($current_module, 'Index') .
				"&" . $url_params);

		// ����������URL
		$this->setAttribute('url_schedule_new',
				$this->getControllerPath($current_module, 'EditSchedule') .
				"&" . $url_params);

		// �ڡ���������
		$display_count = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D03'), 'COMMUNITY_SCHEDULE_LIST_DISPLAY_MAX_COUNT');

		$paging_info = $this->getPagingInfo($controller, $request, $schedule_list, $display_count);
		$this->setAttribute('schedule_list', $schedule_list);

		$this->setAttribute('paging_info', $paging_info);

		return parent::execute();
	}
}
?>
