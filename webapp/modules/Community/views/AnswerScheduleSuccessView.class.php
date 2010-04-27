<?php

/**
 * ���ߥ�˥ƥ��Υ������塼�����
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $
 */

class AnswerScheduleSuccessView extends BaseView
{
	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$this->setScreenId("0001");
		$this->setTemplate('AnswerSchedule.tpl.php');

		$current_module = 'Community';
		$current_action = 'AnswerSchedule';

		// ������桼������
		$acs_user_info_row = $request->getAttribute('acs_user_info_row');
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);

		// ���ߥ�˥ƥ�����
		$target_community_row =& $request->getAttribute('target_community_row');
		$this->setAttribute('target_community_row', $target_community_row);

		// �������塼�륤�󥹥��󥹤�����
		$schedule =& $request->getAttribute('schedule');
		$this->setAttribute('schedule', $schedule);

		// ����URL�ѥ�᡼��
		$url_params = "community_id=" . $target_community_row['community_id'];

		// ���Ͻ�λ��POST-URL
		$this->setAttribute('url_commit',
				$this->getControllerPath($current_module, $current_action));

		// �����POST-URL
		$this->setAttribute('url_decide',
				$this->getControllerPath($current_module, 'DecideSchedule'));

		// ���ߥ�˥ƥ���URL
		$this->setAttribute('url_community_top',
				$this->getControllerPath($current_module, 'Index') .
				"&" . $url_params);

		// �������塼��Ĵ��ɽ������URL
		$this->setAttribute('url_schedule_list',
				$this->getControllerPath($current_module, 'Schedule') . 
				"&" . $url_params);

		// ��ż�
		// ���ߥ�˥ƥ�����μ���
		$user_community_row =& ACSUser::get_user_profile_row($schedule->user_community_id);
		$this->setAttribute('user_community_name', $user_community_row['community_name']);
		$this->setAttribute('user_community_name_url', 
				$this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . 
				'&id='.$schedule->user_community_id);

		// ��������
		$answer_selection =& $schedule->get_answer_selection();
		$html_options_answer_selection = array();
		$answer_detail_text = "";
		foreach ($answer_selection as $key => $val) {
			if ($val['answer_char'] != ""){
				$html_options_answer_selection[$key] = $val['answer_char'];
				if ($answer_detail_text != ""){
					$answer_detail_text .= "<br>";
				}
				$answer_detail_text .= 
						htmlspecialchars($val['answer_char']) . "..." .
						ACSMsg::get_msg('Community', 'AnswerScheduleSuccessView.class.php', 'M001') . ":" . 
							htmlspecialchars($val['answer_score']);
				if ($val['answer_detail']) {
					$answer_detail_text .= " " .
							ACSMsg::get_msg('Community', 'AnswerScheduleSuccessView.class.php', 'M002') . ":" . 
							htmlspecialchars($val['answer_detail']);
				}
			}
		}

		$this->setAttribute('html_options_answer_selection', $html_options_answer_selection);
		$this->setAttribute('answer_detail_text', $answer_detail_text);

		// �������塼���������
		$this->setAttribute('adjustment_dates_count', 
				$schedule->get_adjustment_dates_count());
		$adjustment_dates_list =& $schedule->get_adjustment_dates();
		$this->setAttribute('adjustment_dates_list', $adjustment_dates_list);
		
		// ����
		if($schedule->is_fixed()){
			$schedule_status = ACSMsg::get_msg('Community', 'AnswerScheduleSuccessView.class.php', 'M003') . 
					"&nbsp;&nbsp;������:" . 
					$adjustment_dates_list[$schedule->decide_adjustment_date_id]['date_string'] ;
		}else{
			$schedule_status = $schedule->is_close() ? ACSMsg::get_msg('Community', 'AnswerScheduleSuccessView.class.php', 'M004') : 
								ACSMsg::get_msg('Community', 'AnswerScheduleSuccessView.class.php', 'M005');
		}
		$this->setAttribute('schedule_status', $schedule_status);

		// �������塼�뻲�þ���
		$schedule_participant_list =& $request->getAttribute('schedule_participant_list');
		$this->setAttribute('schedule_participant', 
				$request->getAttribute('schedule_participant'));
		$this->setAttribute('schedule_participant_list', $schedule_participant_list);

		// ���׾���
		$total_count = array();
		$total_score = array();
		foreach ($adjustment_dates_list as $adjustment_date_id => $adjustment_date_array) {
			foreach ($schedule_participant_list as 
					$user_community_id => $schedule_participant) {
				$answer_no = $schedule_participant->get_answer($adjustment_date_id);
				$total_count[$adjustment_date_id][$answer_no]++;
				$total_score[$adjustment_date_id] += 
						$answer_selection[$answer_no]['answer_score'];
			}
		}
		$this->setAttribute('total_count',$total_count);
		$this->setAttribute('total_score',$total_score);

		return parent::execute();
	}
}
?>
