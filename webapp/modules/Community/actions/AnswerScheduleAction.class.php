<?php
require_once(ACS_CLASS_DIR . 'ACSSchedule.class.php');
require_once(ACS_CLASS_DIR . 'ACSScheduleParticipant.class.php');

/**
 * コミュニティのスケジュール回答
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $
 */
class AnswerScheduleAction extends BaseAction
{
	/**
	 * 初期画面
	 * GETメソッドの場合、呼ばれる
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		$target_community_id = $request->getParameter('community_id');
		$target_schedule_id = $request->getParameter('schedule_id');

		if (!$this->get_execute_privilege()) {

			// 2010.03.24 未ログイン時の誘導
			// ログインユーザでない場合はログイン画面へ
			if ($user->hasCredential('PUBLIC_USER')) {
				$controller->forward("User", "Login");
				return;
			}

			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ログインユーザ情報の設定
		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);

		// コミュニティ情報の取得
		$target_community_row = 
				ACSCommunity::get_community_row($target_community_id);
		$request->setAttributeByRef('target_community_row', $target_community_row);

		// スケジュール情報の取得
		$schedule =& ACSSchedule::get_schedule_instance(
				   	$target_community_id, $target_schedule_id);

		$request->setAttributeByRef('schedule', $schedule);

		// スケジュール情報をセッションにキャッシュ
		$user->setAttribute('schedule', serialize($schedule));

		// ログインユーザの参加情報の取得
		if ($request->getAttribute('schedule_participant')) {
			$schedule_participant =& $request->getAttribute('schedule_participant');
		} else {
			$schedule_participant =&
					ACSScheduleParticipant::get_schedule_participant_instance(
					$schedule->schedule_id, $acs_user_info_row['user_community_id']);
			$request->setAttributeByRef('schedule_participant', $schedule_participant);
		}

		// ログインユーザの参加情報をセッションにキャッシュ
		$user->setAttribute('org_participant', serialize($schedule_participant));

		// スケジュール参加者全員の情報を取得
		$schedule_participant_list =&
				ACSScheduleParticipant::get_schedule_participant_instance_list(
				$schedule->schedule_id, $schedule->is_target_all());
		$request->setAttributeByRef('schedule_participant_list', $schedule_participant_list);

		return View::SUCCESS;
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

		$redirect_url = $this->getControllerPath(
				'Community', 'Schedule') .
				"&community_id=" . $params['community_id'];

		$schedule_participant =& $this->getFormPostParticipant(
				&$params, unserialize($user->getAttribute('org_participant')));

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// 登録できないスケジュールの場合スルー
		// 最新スケジュール情報を取得して可否を確認
		$schedule =& ACSSchedule::get_schedule_instance(
					$params['community_id'], $schedule_participant->schedule_id);
		if ($schedule->is_fixed() || $schedule->is_close()) {
			$controller->redirect($redirect_url);
		}

		// 参加切替の場合再表示
		if ($params['participate']) {
			$request->setAttributeByRef('schedule_participant',$schedule_participant);

			// ＤＢ更新(参加登録のみ)
			$schedule_participant->update_participant(TRUE);

			return $this->getDefaultView();

		} else {

			// ＤＢ更新
			$schedule_participant->update_participant();
		}

		// リダイレクト(リロード対策)
		$controller->redirect($redirect_url);
	}

	/**
	 * POSTパラメータからスケジュールインスタンスの作成
	 *
	 * @param array $post_params POSTパラメータ
	 * @param object $org_answer 変更前の元データ
	 * @return object ACSScheduleParticipantインスタンス
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

		// コミュニティメンバはOK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}
		return false;
	}
}
?>
