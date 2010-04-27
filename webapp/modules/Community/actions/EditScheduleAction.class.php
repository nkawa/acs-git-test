<?php
require_once(ACS_CLASS_DIR . 'ACSSchedule.class.php');
//require_once(VALIDATOR_DIR  . 'StringValidator.class.php');

// 選択肢スコアの範囲
define( '_EDITSCHEDULEACTION_ANSWER_SCORE_MIN', -99 );
define( '_EDITSCHEDULEACTION_ANSWER_SCORE_MAX', 99 );

// 候補日時最大数
define( '_EDITSCHEDULEACTION_ADJUSTMENT_DAYS_MAX', 20 );

// 入力文字最大長(半角での文字数)
define( '_EDITSCHEDULEACTION_SCHEDULE_NAME_MAXLEN',		 256 );
define( '_EDITSCHEDULEACTION_EDIT_APPEND_SCHEDULES_MAXLEN', 4096 );
define( '_EDITSCHEDULEACTION_SCHEDULE_PLACE_MAXLEN',		256 );
define( '_EDITSCHEDULEACTION_SCHEDULE_DETAIL_MAXLEN',	   256 );
define( '_EDITSCHEDULEACTION_ANSWER_DETAIL_MAXLEN',		 128 );

/**
 * スケジュールの作成・修正
 *
 * 画面遷移
 * (GET)View::INPUT -> (POST)View::SUCCESS -> (POST)View::SUCCESS
 *
 * @author  z-satosi
 * @version $Revision: 1.3 $
 */
class EditScheduleAction extends BaseAction
{
	/**
	 * 初期画面
 	 *
	 * GETメソッドの場合、呼ばれる。
	 * "schedule_id"パラメータが無い場合は新規作成とする。
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$params =& $request->getParameters();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$target_community_id = $request->getParameter('community_id');
		$target_schedule_id = $request->getParameter('schedule_id');

		// コミュニティ情報の取得
		$target_community_row = 
				ACSCommunity::get_community_row($target_community_id);
		$request->setAttributeByRef('target_community_row', $target_community_row);

		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');

		// スケジュール情報の取得
		if ($target_schedule_id != "") {
			$schedule =& 
					ACSSchedule::get_schedule_instance(
						$target_community_id, $target_schedule_id);

			// (不正対策)
			// 幹事以外の場合
			if (!$schedule->is_organizer($acs_user_info_row)) {
				// このページへアクセスすることはできません。
				$controller->forward(SECURE_MODULE, SECURE_ACTION);
				return;
			}
		} else {
			$schedule =& new ACSSchedule(
					$target_community_id, $acs_user_info_row['user_community_id']);
		}

		// (不正対策)
		// 決定済みのスケジュールであった場合
		if ($schedule->is_fixed()) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// 変更前の元データをキャッシュ
		$user->setAttribute('org_schedule',serialize($schedule));
		$request->setAttributeByRef('schedule', $schedule);

		// 締切日時の設定
		$request->setAttribute('closing_datetime_array', 
				$schedule->get_schedule_closing_datetime_array());

		// お知らせメールの初期設定
		$request->setAttribute('send_annouce_mail_checked', ' CHECKED');

		return View::INPUT;
	}

	/**
	 * 確認・登録処理
 	 *
	 * POSTメソッドの場合、呼ばれる。
	 * "schedule_id"パラメータが無い場合は新規作成とする。
	 */
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$params =& $request->getParameters();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// 更新するスケジュールインスタンスの取得
		$schedule =& $this->getFormPostSchedule(
				&$params, unserialize($user->getAttribute('org_schedule')));

		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');

		// (不正対策)
		// 幹事以外の場合
		if (!$schedule->is_organizer($acs_user_info_row)) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// (不正対策)
		// 決定済みのスケジュールであった場合
		if ($schedule->is_fixed()) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// 追加する候補日時の設定
		$append_dates = $this->getBlankOff($params['edit_append_adjustment_dates']);
		if ($append_dates) {
			$schedule->set_schdedule_adjustment_datetime_append($append_dates);
		}

		// 削除指定する候補日時の設定
		$delete_dates = $params['delete_adjustment_dates'];
		if (is_array($delete_dates)) {
			foreach ($delete_dates as $delete_adjustment_date_id) {
				$schedule->set_schdedule_adjustment_datetime_delete(
						$delete_adjustment_date_id);
			}
		}

		// 新規登録か更新かの取得
		$is_new = $schedule->is_new();

		// 更新の実行
		$schedule->update_schedule();

		// コミュニティＭＬへの通知
		if ($params['send_annouce_mail']=='t') {

			if ($is_new) {
				$subject_msg_key = "M003";
				$action_msg_key = "M006";
			} else {
				$subject_msg_key = "M004";
				$action_msg_key = "M007";
			}

			// 全メンバへメールを送信
			ACSCommunityMail::send_schedule_announce_mail(
					$schedule, 
					"UPD%03d", 
					$subject_msg_key, 
					'',
					array('{ACTION}' => $action_msg_key));
		}

		// リダイレクト(リロード対策)
		$controller->redirect(
				$this->getControllerPath(
				'Community', 'Schedule') .
				"&community_id=" . $schedule->community_id);

	}

	/**
	 * POSTパラメータからスケジュールインスタンスの作成
	 *
	 * @param array $post_params POSTパラメータ
	 * @param object $org_schedule 変更前の元データ
	 * @return object ACSScheduleインスタンス
	 */
	function getFormPostSchedule($post_params,$org_schedule) {

		// スケジュールインスタンスへの設定
		$schedule =& $org_schedule;
		$schedule->schedule_name		= $post_params['schedule_name'];
		if ($post_params['schedule_target_kind'] != '') {
			$schedule->schedule_target_kind = $post_params['schedule_target_kind'];
		}
		$schedule->schedule_place	   = $post_params['schedule_place'];
		$schedule->schedule_detail	  = $post_params['schedule_detail'];

		// 締切日時の設定
		$schedule->set_schedule_closing_datetime_by_array( array(
				'year'	=> $post_params['edit_closing_year'],
				'month'   => $post_params['edit_closing_month'],
				'day'	 => $post_params['edit_closing_day'],
				'hours'   => $post_params['edit_closing_hour'],
				'minutes' => $post_params['edit_closing_min']));

		// 回答選択肢の設定
	 	$schedule->set_answer_selection_by_arrays(	
				$post_params['answer_char'],
				$post_params['answer_score'],
				$post_params['answer_detail'],
				$post_params['answer_default']);

		return $schedule;
	}

	/**
	 * 候補日時のブランク削除
	 *
	 * @param string $append_adjustment_dates 追加候補日時
	 * @return string ブランク削除した追加候補日時
	 */
	function getBlankOff($append_adjustment_dates) {
		$suppress = "";
		foreach (explode("\n",trim($append_adjustment_dates)) as $line) {
			if (trim(str_replace("　","",$line)) != '') $suppress .= $line . "\n";
		}
		return trim($suppress);
	}

	function getRequestMethods () {
		return Request::POST;
	}

	/**
	 * 入力値チェック
	 */
	function validate () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$params =& $request->getParameters();

		$schedule =& $this->getFormPostSchedule(
				&$params, unserialize($user->getAttribute('org_schedule')));

		$result = TRUE;

		// **** 候補日時最大数・最少数のチェック
		$adjust_cnt = $schedule->get_adjustment_dates_count(FALSE);
		$adjust_cnt -= count($params['delete_adjustment_dates']);
		$append_adjustment_dates = 
				$this->getBlankOff($params['edit_append_adjustment_dates']);
		if ($append_adjustment_dates != '') {
			$adjust_cnt += count(explode("\n",$append_adjustment_dates));
		}
		if ($adjust_cnt > _EDITSCHEDULEACTION_ADJUSTMENT_DAYS_MAX ) {
			$request->setError("schedule_closing_datetime",
					ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M014') .
					_EDITSCHEDULEACTION_ADJUSTMENT_DAYS_MAX);
			$result = FALSE;
		}
		if ($adjust_cnt <= 0) {
			$request->setError("schedule_closing_datetime",
					ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M015'));
			$result = FALSE;
		}

		// **** 締切日時選択値のチェック
		if (!checkdate($params['edit_closing_month'],
				$params['edit_closing_day'],
				$params['edit_closing_year']) || 
				$params['edit_closing_hour'] == "" || 
				$params['edit_closing_hour'] < 0 ||
				$params['edit_closing_hour'] > 23 ||
				$params['edit_closing_min'] == "" || 
				$params['edit_closing_min'] < 0 ||
				$params['edit_closing_min'] > 59 ) {
			$request->setError("schedule_closing_datetime",
					ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M016'));
			$result = FALSE;

		} else {
			// **** 締切日時選択値のチェック(新規登録時のみ過去日時をチェック)
			if ($schedule->is_new()) {
				$closing_datetime = mktime(
						$params['edit_closing_hour'],
						$params['edit_closing_min'],
						0,
						$params['edit_closing_month'],
						$params['edit_closing_day'],
						$params['edit_closing_year']);
				if ($closing_datetime <= time()) {
					$request->setError("schedule_closing_datetime",
							ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M017'));
					$result = FALSE;
				}
			}
		}

		// **** 対象の選択値チェック [schedule_target_kind]
		if ($schedule->is_new()) {
			if ($params['schedule_target_kind'] <> 'ALL' &&
					$params['schedule_target_kind'] <> 'FREE') {
				$request->setError("schedule_target_kind",
						ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M001'));
				$result = FALSE;
			}
		}

		// **** 選択肢のチェック
		$char_check = array();
		$answer_cnt = 0;
		for ($cnt = 1; $cnt <= count($params['answer_char']); $cnt++) {
			$answer_char = trim($params['answer_char'][$cnt]);
			$answer_score = trim($params['answer_score'][$cnt]);
			$answer_detail = trim($params['answer_detail'][$cnt]);
			// 重複
			if ($answer_char != '') {
				if ($char_check[$answer_char]) {
					$request->setError("answer_duplicate",
							ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M003'));
					$result = FALSE;
				}
				$char_check[$answer_char] = TRUE;
				$answer_cnt++;
			}	
			// 設定値不足
			if ($answer_char != '' || $answer_score != '' || $answer_detail != '') {
				if ($answer_char == '' || $answer_score == '') {
					$request->setError("answer_incomplete",
							ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M004'));
					$result = FALSE;
				}
			}
			// スコア設定値・範囲チェック
			if ($answer_score != '') {
				if ($answer_score < _EDITSCHEDULEACTION_ANSWER_SCORE_MIN ||
						$answer_score > _EDITSCHEDULEACTION_ANSWER_SCORE_MAX ||
						is_numeric($answer_score) == FALSE ||
						ereg_replace("[-0123456789]+","",$answer_score) != "" ) {
					$request->setError("answer_disable_score",
							ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M007') . 
							' (Min:'._EDITSCHEDULEACTION_ANSWER_SCORE_MIN . 
							' Max:'._EDITSCHEDULEACTION_ANSWER_SCORE_MAX . ')');
					$result = FALSE;
				}
			}
			// 回答の説明長さ
			if (mb_strlen($answer_detail) > _EDITSCHEDULEACTION_ANSWER_DETAIL_MAXLEN) {
				$request->setError("answer_detail_len",
						ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M013').
						_EDITSCHEDULEACTION_ANSWER_DETAIL_MAXLEN);
				$result = FALSE;
			}
		}
		// 選択肢未設定
		if ($answer_cnt==0) {
			$request->setError("answer_none",
					ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M005'));
			$result = FALSE;
		}
		// 初期値誤り
		$answer_default = $params['answer_default'];
		if (trim($params['answer_char'][$answer_default]) == '' &&
				trim($params['answer_score'][$answer_default]) == '') {
			$request->setError("answer_disable_def",
					ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M006'));
			$result = FALSE;
		}

		return $result;
	}

	/**
	 * 入力値チェック(ValidatorManager使用)
	 */
	function registerValidators (&$validatorManager) {

		$context = $this->getContext();
		$request =  $context->getRequest();
		$params =& $request->getParameters();

		// システム用パラメータ
		parent::regValidateName($validatorManager, 
				"community_id", 
				true, 
				ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M001'));

		// 件名 [schedule_name]
		parent::regValidateName($validatorManager, 
				"schedule_name", 
				true, 
				ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M002'));

		if ($params['schedule_name']) {
			$validator =& new StringValidator($controller);
			$validator->initialize(array(
					'max' => _EDITSCHEDULEACTION_SCHEDULE_NAME_MAXLEN,
					'max_error' => ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M009').
						_EDITSCHEDULEACTION_SCHEDULE_NAME_MAXLEN));
			$validatorManager->registerValidator('schedule_name',$validator);
		}

		// 候補日時(追加分) [edit_append_adjustment_dates]
		if ($params['edit_append_adjustment_dates']) {
			$validator =& new StringValidator($controller);
			$validator->initialize(array(
					'max' => _EDITSCHEDULEACTION_EDIT_APPEND_SCHEDULES_MAXLEN,
					'max_error' => ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M012').
						_EDITSCHEDULEACTION_EDIT_APPEND_SCHEDULES_MAXLEN));
			$validatorManager->registerValidator('edit_append_adjustment_dates',$validator);
		}

		// 場所 [schedule_place]
		if ($params['schedule_place']) {
			$validator =& new StringValidator($controller);
			$validator->initialize(array(
					'max' => _EDITSCHEDULEACTION_SCHEDULE_PLACE_MAXLEN,
					'max_error' => ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M010').
						_EDITSCHEDULEACTION_SCHEDULE_PLACE_MAXLEN));
			$validatorManager->registerValidator('schedule_place',$validator);
		}

		// 詳細情報 [schedule_detail]
		if ($params['schedule_detail']) {
			$validator =& new StringValidator($controller);
			$validator->initialize(array(
					'max' => _EDITSCHEDULEACTION_SCHEDULE_DETAIL_MAXLEN,
					'max_error' => ACSMsg::get_msg('Community', 'EditScheduleAction.class.php', 'M011').
						_EDITSCHEDULEACTION_SCHEDULE_DETAIL_MAXLEN));
			$validatorManager->registerValidator('schedule_detail',$validator);
		}
	}

	/**
	 * 入力チェックエラー時の対応
	 */
	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$params =& $request->getParameters();

		// コミュニティ情報の取得
		$target_community_row = 
				ACSCommunity::get_community_row($params['community_id']);
		$request->setAttributeByRef('target_community_row', $target_community_row);

		// スケジュール情報の取得
		$schedule =& $this->getFormPostSchedule(
				&$params, unserialize($user->getAttribute('org_schedule')));
		$request->setAttributeByRef('schedule', $schedule);

		// POST値の引継ぎ設定
		$request->setAttribute('edit_append_adjustment_dates', 
				$this->getBlankOff($params['edit_append_adjustment_dates']));
		$request->setAttribute('closing_datetime_array', array(
				'year'	=> $params['edit_closing_year'],
				'month'   => $params['edit_closing_month'],
				'day'	 => $params['edit_closing_day'],
				'hours'   => $params['edit_closing_hour'],
				'minutes' => $params['edit_closing_min']));
		$request->setAttribute('send_annouce_mail_checked',
				$params['send_annouce_mail'] != '' ? ' CHECKED' : '');

		$delete_ajustment_dates_checked = array();
		if (is_array($params['delete_adjustment_dates'])) {
			foreach ($params['delete_adjustment_dates'] as $adjust_id) {
				$delete_ajustment_dates_checked[$adjust_id] = ' CHECKED';
			}
		}
		$request->setAttributeByRef('delete_ajustment_dates_checked',
				$delete_ajustment_dates_checked);

		return View::INPUT;
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

		// コミュニティメンバはOK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}
		return false;
	}
}
?>
