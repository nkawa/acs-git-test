<?php
require_once(ACS_LIB_TEMPLATE_DIR . 'ACSTemplateLib.class.php');

/**
 * スケジュールの作成・修正表示
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $
 */

class EditScheduleInputView extends BaseView
{
	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$this->setScreenId("0001");
		$this->setTemplate('EditSchedule.tpl.php');

		$current_module = 'Community';
		$current_action = 'EditSchedule';

		// コミュニティ情報
		$target_community_row =& $request->getAttribute('target_community_row');
		$this->setAttribute('target_community_row', $target_community_row);

		// スケジュールインスタンスの設定
		$schedule =& $request->getAttribute('schedule');
		$this->setAttribute('schedule', $schedule);

		// 入力値の設定
		$this->setAttribute('edit_append_adjustment_dates', 
				$request->getAttribute('edit_append_adjustment_dates'));
		$this->setAttribute('delete_ajustment_dates_checked',
				$request->getAttribute('delete_ajustment_dates_checked'));
		$this->setAttribute('send_annouce_mail_checked',
				$request->getAttribute('send_annouce_mail_checked'));

		// POST-URL
		$this->setAttribute('posturl', 
				$this->getControllerPath($current_module, $current_action));

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

		// 候補日時自動作成optionsリストの生成
		$this->setAttribute('html_options_generate_year', 
				ACSTemplateLib::get_year_select_options());
		$this->setAttribute('html_options_generate_month', 
				ACSTemplateLib::get_month_select_options());
		$this->setAttribute('html_options_generate_day', 
				ACSTemplateLib::get_day_select_options());

		// 締切日時optionsリストの生成
		$closing_array = $request->getAttribute('closing_datetime_array');
		$this->setAttribute('html_options_closing_year', 
				ACSTemplateLib::get_year_select_options($closing_array['year']));
		$this->setAttribute('html_options_closing_month', 
				ACSTemplateLib::get_month_select_options($closing_array['month']));
		$this->setAttribute('html_options_closing_day', 
				ACSTemplateLib::get_day_select_options($closing_array['day']));
		$this->setAttribute('html_options_closing_hour', 
				ACSTemplateLib::get_hour_select_options($closing_array['hours']));
		$this->setAttribute('html_options_closing_min', 
				ACSTemplateLib::get_min_select_options($closing_array['minutes']));

		// 対象のラジオボタンチェック設定
		if ($schedule->is_target_all()) {
			$this->setAttribute('html_checked_target_all', ' CHECKED');
			$this->setAttribute('html_checked_target_free', '');
		} else {
			$this->setAttribute('html_checked_target_all', '');
			$this->setAttribute('html_checked_target_free', ' CHECKED');
		}

		// エラー時のメッセージ表示
		$this->setAttribute('error_message', 
				$this->getErrorMessage($controller, $request, $user));

		return parent::execute();
	}
}
?>
