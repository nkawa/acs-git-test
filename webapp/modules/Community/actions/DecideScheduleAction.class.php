<?php
require_once(dirname(__FILE__).'/AnswerScheduleAction.class.php');
//require_once(VALIDATOR_DIR  . 'StringValidator.class.php');

// 入力文字最大長(半角での文字数)
define( '_DECIDESCHEDULEACTION_MAIL_SUBJECT_MAXLEN', 256 );
define( '_DECIDESCHEDULEACTION_MAIL_MESSAGE_MAXLEN', 4096 );

/**
 * コミュニティのスケジュール決定
 *
 * @author  z-satosi
 * @version $Revision: 1.3 $
 */
class DecideScheduleAction extends AnswerScheduleAction
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
		$request = $context->getRequest();
		$user = $context->getUser();

		$params =& $request->getParameters();

		// 決定選択画面からの遷移の場合
		if ($params['post_from_answer']=='t') {
			return $this->execute();
		}

		$target_community_id = $params['community_id'];
		$target_schedule_id = $params['schedule_id'];

		// ログインユーザ情報の設定
		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);

		// 幹事でないスケジュールや決定済の場合セキュリティエラー
		// 最新スケジュール情報を取得して可否を確認
		$schedule =& ACSSchedule::get_schedule_instance(
					$params['community_id'], $params['schedule_id']);
		if ($schedule->is_fixed() || !$schedule->is_organizer($acs_user_info_row)) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// 決定候補日時の設定(メールフォームから"戻る"時の対応)
		$request->setAttribute('mailentry_adjustment_id',
				$request->getParameter('mailentry_adjustment_id'));

		return parent::getDefaultView();
	}

	/**
	 * メールフォーム入力画面の表示
	 */
	function getMailInputView (&$controller, &$request, &$user) {

		$params =& $request->getParameters();
		$schedule =& $request->getAttribute('schedule');

		$target_community_id = $params['community_id'];
		$target_schedule_id = $params['schedule_id'];

		// ログインユーザ情報の設定
		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);

		// 幹事でないスケジュールや決定済の場合セキュリティエラー
		if ($schedule->is_fixed() || !$schedule->is_organizer($acs_user_info_row)) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// コミュニティ情報の取得
		$target_community_row =
				ACSCommunity::get_community_row($target_community_id);
		$request->setAttributeByRef('target_community_row', $target_community_row);

		// 決定候補日時の設定
		$request->setAttributeByRef('mailentry_adjustment_id',
						$params['mailentry_adjustment_id']);

		// エラー時の再表示用
		$request->setAttribute('mail_subject',$params['mail_subject']);
		$request->setAttribute('mail_message',$params['mail_message']);

		return View::INPUT;
	}

	/**
	 * 登録実行処理
	 * POSTメソッドの場合、呼ばれる
	 */
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$params =& $request->getParameters();

		// ログインユーザ情報の設定
		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);

		// 幹事でないスケジュールや決定済の場合セキュリティエラー
		// 最新スケジュール情報を取得して可否を確認
		$schedule =& ACSSchedule::get_schedule_instance(
					$params['community_id'], $params['schedule_id']);
		if ($schedule->is_fixed() || !$schedule->is_organizer($acs_user_info_row)) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// 候補日時選択画面からのPOSTの場合
		if ($params['post_from_answer']=='t') {
			$request->setAttributeByRef('schedule',$schedule);

			$return_view = $this->getMailInputView($controller, $request, $user);

		// メール入力画面からのPOSTの場合
		} else {

			// DB更新
			$schedule->update_decide_schedule($params['mailentry_adjustment_id']);

			// システムのメールアドレスを取得
			$system_mail_addr = ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');

			// メール送信相手の設定
			// 自由参加の場合対象となるメンバの取得
			$target_mmb = "";
			if ($schedule->is_target_all()===FALSE) {
				$p_array =& ACSScheduleParticipant::get_schedule_participant_instance_list(
				$schedule->schedule_id,FALSE);
				foreach ($p_array as $user_community_id => $schedule_participant) {
					$target_mmb[] = $user_community_id;
				}
				$p_array = "";
			}

			// メールの送信
			ACSCommunityMail::send_community_mail(
					$schedule->community_id, $system_mail_addr, 
					$params['mail_subject'], $params['mail_message'], $target_mmb);

			// リダイレクト(リロード対策)
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
	 * 入力値チェック
	 */
	function validate () {
		return TRUE;
	}

	/**
	 * 入力値チェック(ValidatorManager使用)
	 */
	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// パラメータ取得
		$params =& $request->getParameters();

		// メール入力画面からのPOSTの場合
		if ($params['post_from_answer']!='t') {

			// 件名の入力チェック
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

			// 本文の入力チェック
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
	 * 入力チェックエラー時の対応
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
