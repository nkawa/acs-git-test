<?php
require_once(dirname(__FILE__).'/AnswerScheduleSuccessView.class.php');
require_once(dirname(__FILE__).'/AnswerScheduleSuccessView.class.php');

/**
 * コミュニティのスケジュール決定メール入力画面
 *
 * @author  z-satosi
 * @version $Revision: 1.3 $
 */

class DecideScheduleInputView extends BaseView
{
	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$this->setScreenId("0001");
		$this->setTemplate('DecideSchedule_input.tpl.php');

		$current_module = 'Community';
		$current_action = 'DecideSchedule';

		// ログインユーザ情報
		$acs_user_info_row = $request->getAttribute('acs_user_info_row');
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);

		// コミュニティ情報
		$target_community_row =& $request->getAttribute('target_community_row');
		$this->setAttribute('target_community_row', $target_community_row);

		// スケジュールインスタンスの設定
		$schedule =& $request->getAttribute('schedule');
		$this->setAttribute('schedule', $schedule);

		// 共通URLパラメータ
		$url_params = "community_id=" . $target_community_row['community_id'];

		// コミュニティのURL
		$this->setAttribute('url_community_top',
				$this->getControllerPath($current_module, 'Index') .
				"&" . $url_params);

		// スケジュール調整表一覧のURL
		$this->setAttribute('url_schedule_list',
				$this->getControllerPath($current_module, 'Schedule') .
				"&" . $url_params);

		// コミット先のURL
		$this->setAttribute('url_commit',
				$this->getControllerPath($current_module, $current_action));

		// スケジュール決定日
		$mailentry_adjustment_id = & $request->getAttribute('mailentry_adjustment_id');
		$adjustment_dates_list =& $schedule->get_adjustment_dates();

		$this->setAttribute('mailentry_adjustment_id', $mailentry_adjustment_id);
		$this->setAttribute('adjustment_date', 
				$adjustment_dates_list[$mailentry_adjustment_id]['date_string']);
	
		// 言語一覧
		$html_options_lang_list = ACSMsg::get_lang_list_array();
		$this->setAttribute('html_options_lang_list', $html_options_lang_list);

		// 全言語メッセージ取得
		$java_subject_var_string = "var subject_list = new Array();\n";
		$java_message_var_string = "var msg_list = new Array();\n";
		$sel_index = 0;
		$index = 0;
		foreach($html_options_lang_list as $key => $val){

			$msg = $schedule->get_decision_mail_message($key, $mailentry_adjustment_id);
			$subject = $schedule->get_decision_mail_subject($key);

			if($key == ACSMsg::get_lang()){
				$default_subject = $subject;
				$default_message = $msg;
				$sel_index = $index;
			}

			$java_msg = htmlspecialchars(str_replace("\r", "", str_replace("\n", "\\n", $msg)));

			// "&amp;" -> "&" へ戻す
			$java_subject = str_replace("&amp;", "&", $subject);
			$java_msg = str_replace("&amp;", "&", $java_msg);

			$java_subject_var_string = $java_subject_var_string . 
									'subject_list["' . $key . '"] = "' . $java_subject . '";' . "\n";

			$java_message_var_string = $java_message_var_string . 
									'msg_list["' . $key . '"] = "' . $java_msg . '";' . "\n";
			$index++;
		}
		$this->setAttribute('java_subject_var_string', $java_subject_var_string);
		$this->setAttribute('java_message_var_string', $java_message_var_string);
		$this->setAttribute('java_default_lang_index', $sel_index);

		// 現在の表示言語の設定
		$this->setAttribute('current_lang', ACSMsg::get_lang());
			
		$this->setAttribute('mail_subject', $request->getAttribute('mail_subject'));
		if ($request->getAttribute('mail_message')) {
			$this->setAttribute('mail_subject', $request->getAttribute('mail_subject'));
			$this->setAttribute('mail_message', $request->getAttribute('mail_message'));
		} else {
			$this->setAttribute('mail_subject', $default_subject);
			$this->setAttribute('mail_message', $default_message);
		}

		// スケジュール決定のURL
		$current_module = 'Community';
		$cancel_url = $this->getControllerPath($current_module, 'DecideSchedule') . 
						"&" . $url_params . "&schedule_id=" . $schedule->schedule_id . 
						"&mailentry_adjustment_id=" . $mailentry_adjustment_id;

		$this->setAttribute('cancel_url', $cancel_url);

		// エラー時のメッセージ表示
		$this->setAttribute('error_message',
				$this->getErrorMessage($controller, $request, $user));

		return parent::execute();
	}
}
?>
