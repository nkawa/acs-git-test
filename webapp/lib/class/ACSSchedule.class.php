<?php
require_once(ACS_CLASS_DIR . 'ACSDB.class.php');
require_once(ACS_CLASS_DIR . 'ACSCommunityMail.class.php');

define( '_ACSSCHEDULE_ANSWER_COUNT', 7 );
define( '_ACSSCHEDULE_DEFAULT_ANSWER_CHAR',    'o,v,-,x'  );
define( '_ACSSCHEDULE_DEFAULT_ANSWER_SCORE',   '2,1,0,-1' );
define( '_ACSSCHEDULE_DEFAULT_ANSWER_DEFAULT', 'f,f,t,f'  );

/**
 * ACS スケジュールクラス
 *
 * @author  z-satosi
 * @version $Revision: 1.3 $
 */
class ACSSchedule
{
	/* スケジュールID */
	var $schedule_id;

	/* コミュニティID */
	var $community_id;

	/* 幹事ユーザコミュニティID */
	var $user_community_id;

	/* 幹事ユーザコミュニティ名 */
	var $user_community_name;

	/* スケジュール名 */
	var $schedule_name;

	/* 場所 */
	var $schedule_place;

	/* 詳細情報 */
	var $schedule_detail;

	/* 対象 */
	var $schedule_target_kind;

	/* 回答締切日時 */
	var $schedule_closing_datetime;

	/* 候補日時 */
	var $_adjustment_dates_array;

	/* 候補日時更新用スタック */
	var $_adjustment_dates_stack;

	/* 回答選択肢 */
	var $_answer_selection_array;

	/* 回答者リスト */
	var $_answer_users_array;

	/* 作成日時 */
	var $_entry_datetime;

	/* 更新日時 */
	var $_update_datetime;

	/**
	 * コンストラクタ
	 *
	 * @param integer $community_id コミュニティid
	 * @param integer $user_community_id 管理者ユーザコミュニティid
	 * @param integer $schedule_id スケジュールid(未指定時は新規)
	 */
	function ACSSchedule ($community_id,$user_community_id,$schedule_id='') {
		$this->community_id = $community_id;
		$this->user_community_id = $user_community_id;
		$this->user_community_name = "";
		$this->schedule_id = $schedule_id;
		$this->schedule_target_kind = 'ALL';
		$this->decide_adjustment_date_id = 0;
		$this->_adjustment_dates_array = "";
		$this->_answer_selection_array = "";
		$this->_answer_users_array = "";
		$this->_adjustment_dates_stack = array();
	}

	/**
	 * 初期処理
	 *
	 * @param array $schedule_row 該当のスケジュールテーブル行
	 */
	function initialize ($schedule_row) {
		if (is_array($schedule_row)) {
			$this->user_community_name = $schedule_row['user_community_name'];
			$this->schedule_name      = $schedule_row['schedule_name'];
			$this->schedule_target_kind = $schedule_row['schedule_target_kind'];
			$this->schedule_place     = $schedule_row['schedule_place'];
			$this->schedule_detail    = $schedule_row['schedule_detail'];
			$this->schedule_closing_datetime = $schedule_row['schedule_closing_datetime'];
			$this->decide_adjustment_date_id = $schedule_row['decide_adjustment_date_id'];
			$this->_entry_datetime    = $schedule_row['entry_datetime'];
			$this->_update_datetime   = $schedule_row['update_datetime'];
		}
	}

	/**
	 * スケジュール候補日時数の取得
	 *
	 * @param boolean $contain_deleted 削除済データも含む(未指定時はfalse)
	 * @return int 候補日時数
	 */
	function get_adjustment_dates_count ($contain_deleted = FALSE) {
		return count($this->get_adjustment_dates($contain_deleted));
	}

	/**
	 * スケジュール候補日時の取得
	 *
	 * @param boolean $contain_deleted 削除済データも含む(未指定時はfalse)
	 * @return array 候補日時２次元配列([候補日時id] => array(
	 *                   'date_string' => 候補日時表示文字列
	 *                   'delete_flag' => 削除フラグ 't'=削除済);
	 */
	function & get_adjustment_dates ($contain_deleted = FALSE) {

		if (!is_array($this->_adjustment_dates_array)) {

			$this->_adjustment_dates_array = array();

			if (!$this->is_new()) {
				$sql = "SELECT * FROM schedule_adjustment_dates ".
						"WHERE schedule_id = " . 
						pg_escape_string($this->schedule_id) .
						"ORDER BY adjustment_date_id";

				$rows = ACSDB::_get_row_array($sql);

				foreach ($rows as $row) {
					$index = $row['adjustment_date_id'];
					$this->_adjustment_dates_array[$index] = array(
							'date_string' => $row['adjustment_date_string'],
							'delete_flag' => $row['adjustment_date_delete_flag']);
				}
			}
		}
		if ($contain_deleted) {
			return $this->_adjustment_dates_array;
		} else {
			$dates_array = array();
			foreach ($this->_adjustment_dates_array as $index => $values) {
				if ($values['delete_flag']=='f') $dates_array[$index] = $values;
			}
			reset($this->_adjustment_dates_array);
			return $dates_array;
		}
	}

	/**
	 * スケジュール回答初期値Noの取得
	 *
	 * @return int 回答初期値No
	 */
	function get_answer_selection_default () {
		$selection =& $this->get_answer_selection();
		foreach ($selection as $answer_no => $answer_array) {
			if ($answer_array['answer_default']=='t') {
				return $answer_no;
			}
		}
		return '';
	}

	/**
	 * スケジュール回答選択数の取得
	 *
	 * @return int 選択肢の数
	 */
	function get_answer_selection_count () {
		return count($this->get_answer_selection());
	}

	/**
	 * スケジュール回答選択肢取得
	 *
	 * @return array 回答選択肢の2次元配列
	 *               array( [answer_no] = array(
	 *                   'answer_char'    => 回答記号
	 *                   'answer_score'   => スコア
	 *                   'answer_detail'  => 回答の説明文
	 *                   'answer_default' => 't'=初期選択値 ))
	 */
	function & get_answer_selection () {

		if (!is_array($this->_answer_selection_array)) {

			// 新規の場合は初期値を設定
			if ($this->is_new()) {
				$char_array = explode(",",_ACSSCHEDULE_DEFAULT_ANSWER_CHAR);
				$score_array = explode(",",_ACSSCHEDULE_DEFAULT_ANSWER_SCORE);
				$default_array = explode(",",_ACSSCHEDULE_DEFAULT_ANSWER_DEFAULT);

				for ($cnt = 1; $cnt <= count($char_array); $cnt++) {
					$this->_answer_selection_array[$cnt]['answer_char'] = 
							$char_array[$cnt-1];
					$this->_answer_selection_array[$cnt]['answer_score'] = 
							$score_array[$cnt-1];
					$this->_answer_selection_array[$cnt]['answer_detail'] = "";
					$this->_answer_selection_array[$cnt]['answer_default'] = 
							$default_array[$cnt-1];
				}

			// 既存の場合は登録データを設定
			} else {
				$sql = "SELECT * FROM schedule_answer_selection ".
						"WHERE schedule_id = " . 
						pg_escape_string($this->schedule_id) .
						"ORDER BY answer_no";

				$rows = ACSDB::_get_row_array($sql);

				$this->_answer_selection_array = array();
				foreach ($rows as $row) {
					$index = $row['answer_no'];
					$this->_answer_selection_array[$index]['answer_char'] = 
							$row['answer_char'];
					$this->_answer_selection_array[$index]['answer_score'] = 
							$row['answer_score'];
					$this->_answer_selection_array[$index]['answer_detail'] = 
							$row['answer_detail'];
					$this->_answer_selection_array[$index]['answer_default'] = 
							$row['answer_default'];
				}
			}

			// 選択肢が規定数に達しない場合は空データを設定
			for ($cnt = count($this->_answer_selection_array)+1;
					$cnt <= _ACSSCHEDULE_ANSWER_COUNT; $cnt++) {
				$this->_answer_selection_array[$cnt]['answer_char'] = '';
				$this->_answer_selection_array[$cnt]['answer_score'] = '';
				$this->_answer_selection_array[$cnt]['answer_detail'] = '';
				$this->_answer_selection_array[$cnt]['answer_default'] = 'f';
			}
		}
		return $this->_answer_selection_array;
	}

	/**
	 * スケジュール回答選択肢設定
	 *
	 * @param array $char_array   回答記号配列(1origin)
	 * @param array $score_array  スコア配列(1origin)
	 * @param array $detail_array 回答の説明文配列(1origin)
	 * @param array $default_no   初期選択値No(1〜max)
	 */
	function set_answer_selection_by_arrays (
		$char_array,$score_array,$detail_array,$default_no) {

		$this->_answer_selection_array = array();
		for ($cnt = 1;$cnt <= _ACSSCHEDULE_ANSWER_COUNT; $cnt++) {
			$this->_answer_selection_array[$cnt]['answer_char'] = $char_array[$cnt];
			$this->_answer_selection_array[$cnt]['answer_score'] = $score_array[$cnt];
			$this->_answer_selection_array[$cnt]['answer_detail'] = $detail_array[$cnt];
			$this->_answer_selection_array[$cnt]['answer_default'] = 
					$cnt == $default_no ? 't' : 'f';
		}
	}

	/**
	 * 回答締切日時の配列取得
	 *
	 * @return array 日付時刻の配列
	 *                         'year'    => 年
	 *                         'month    => 月
	 *                         'day'     => 日
	 *                         'hours'   => 時
	 *                         'minutes' => 分
	 */
	function get_schedule_closing_datetime_array () {
		$dt_array = getdate(strtotime($this->schedule_closing_datetime));
		return array(
				'year'    => $dt_array['year'],
				'month'   => $dt_array['mon'],
				'day'     => $dt_array['mday'],
				'hours'   => $dt_array['hours'],
				'minutes' => $dt_array['minutes'] );
	}

	/**
	 * 回答締切日時の設定(年,月,日,時,分の個別指定)
	 *
	 * @param array $ymd_array 年,月,日,時,分の個別指定の配列
	 *                         'year'    => 年
	 *                         'month    => 月
	 *                         'day'     => 日
	 *                         'hours'   => 時
	 *                         'minutes' => 分
	 */
	function set_schedule_closing_datetime_by_array ($ymd_array) {
		$this->schedule_closing_datetime = sprintf("%04d-%02d-%02d %02d:%02d:00",
				$ymd_array['year'],
				$ymd_array['month'],
				$ymd_array['day'],
				$ymd_array['hours'],
				$ymd_array['minutes'] );
	}

	/**
	 * 候補日時の削除指定
	 * 
	 * 候補日時の削除指定を行います。<br>
	 * ＤＢへの反映は update_schedule にて実施されます。
	 *
	 * @param string $adjustment_date_id 削除指定する候補日時id
	 */
	function set_schdedule_adjustment_datetime_delete ($adjustment_date_id) {

		$this->_adjustment_dates_stack['delete'][] = $adjustment_date_id;
	}

	/**
	 * 候補日時の追加指定
	 * 
	 * 候補日時の追加指定を行います。<br>
	 * ＤＢへの反映は update_schedule にて実施されます。
	 * 追加する文字列中に改行文字がある場合、複数の候補日時として追加されます。
	 *
	 * @param string $append_adjustment_date_string 追加指定する候補日時文字列
	 */
	function set_schdedule_adjustment_datetime_append ($append_adjustment_date_string) {
		$append = explode("\n",
				str_replace("\r\n","\n",$append_adjustment_date_string));
		if ($this->_adjustment_dates_stack['append'] != null) {
			$this->_adjustment_dates_stack['append'] = 
					array_merge($this->_adjustment_dates_stack['append'],$append);
		}
	}

	/**
	 * スケジュール情報の決定時ＤＢ更新処理
	 * 
	 * スケジュール情報の決定時ＤＢ更新を実施します。<br>
	 * 更新されるテーブルは以下の通り<br>
	 *     schedule.decide_adjustment_date_id ... 0 -> 決定した候補日時id<br>
	 * @return boolean TRUE...成功/FALSE...失敗
	 */
	function update_decide_schedule ($decide_adjustment_date_id) {

		// BEGIN
		ACSDB::_do_query("BEGIN");

		$sql = "UPDATE schedule SET decide_adjustment_date_id = ".
				pg_escape_string($decide_adjustment_date_id) . 
				" WHERE schedule_id = " . pg_escape_string($this->schedule_id);

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// COMMIT
		$ret = ACSDB::_do_query("COMMIT");

		$this->decide_adjustment_date_id = $decide_adjustment_date_id;

		return $ret;
	}

	/**
	 * スケジュール情報のＤＢ更新
	 * 
	 * スケジュール情報のＤＢ更新を実施します。<br>
	 * 更新されるテーブルは以下の通り<br>
	 *     schedule<br>
	 *     schedule_adjustment_dates<br>
	 *     schedule_answer_selection<br>
	 * 候補日時は、set_schdedule_adjustment_datetime_append() および <br>
	 * set_schdedule_adjustment_datetime_delete() で指定された内容が反映されあます。
	 * 
	 * @return boolean TRUE...成功/FALSE...失敗
	 */
	function update_schedule () {

		// BEGIN
		ACSDB::_do_query("BEGIN");

		if ($this->is_new()) {

			// スケジュールidの取得
			$this->schedule_id = ACSDB::get_next_seq('schedule_id_seq');

			$sql = "INSERT INTO schedule (".
					" schedule_id, ".
					" community_id, ".
					" user_community_id, ".
					" schedule_name, ".
					" schedule_place, ".
					" schedule_detail, ".
					" schedule_closing_datetime, ".
					" schedule_target_kind, ".
					" decide_adjustment_date_id ".
					") VALUES (" .
					" ".pg_escape_string($this->schedule_id)."," .
					" ".pg_escape_string($this->community_id)."," .
					" ".pg_escape_string($this->user_community_id)."," .
					" '".pg_escape_string($this->schedule_name)."'," .
					" '".pg_escape_string($this->schedule_place)."'," .
					" '".pg_escape_string($this->schedule_detail)."'," .
					" '".$this->schedule_closing_datetime."'," .
					" '".pg_escape_string($this->schedule_target_kind)."'," .
					" ".pg_escape_string($this->decide_adjustment_date_id)." " .
					")";
		// Update
		} else {

			$sql = "UPDATE schedule SET ".
					" schedule_name ".
							"= '".pg_escape_string($this->schedule_name)."'," .
					" schedule_place ".
							"= '".pg_escape_string($this->schedule_place)."'," .
					" schedule_detail ".
							"= '".pg_escape_string($this->schedule_detail)."'," .
					" schedule_closing_datetime ".
							"= '".$this->schedule_closing_datetime."'," .
					" schedule_target_kind ".
							"= '".pg_escape_string($this->schedule_target_kind)."', " .
					" update_datetime = CURRENT_TIMESTAMP " .
					"WHERE schedule_id ".
							"= ".pg_escape_string($this->schedule_id) .
					" AND user_community_id ".
							"= ".pg_escape_string($this->user_community_id) ;
		}

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// 候補日時の登録(削除)
		if (is_array($this->_adjustment_dates_stack['delete'])) {
			$sql = "UPDATE schedule_adjustment_dates ".
					"SET adjustment_date_delete_flag = TRUE ";
			$where = "";
			foreach ($this->_adjustment_dates_stack['delete'] as $delete_dates) {
				$where .= ($where != "" ? "," : "").$delete_dates;
			}
			$sql .= "WHERE schedule_id = ".pg_escape_string($this->schedule_id) .
					" AND adjustment_date_id IN (" . $where . ")";

			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		}

		// 候補日時の登録(追加)
		if (is_array($this->_adjustment_dates_stack['append'])) {

			foreach ($this->_adjustment_dates_stack['append'] as $append_str) {

				// スケジュール候補日時idの取得
				$seq = ACSDB::get_next_seq('adjustment_date_id_seq');

				$sql = "INSERT INTO schedule_adjustment_dates " .
						"(schedule_id, adjustment_date_id, adjustment_date_string" .
						") VALUES (".
						"".pg_escape_string($this->schedule_id)."," .
						"".$seq."," .
						"'".pg_escape_string($append_str)."')";

				$ret = ACSDB::_do_query($sql);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// 選択肢の登録(Delete&Insert)
		if (is_array($this->_answer_selection_array)) {

			// 一旦Delete
			$sql = "DELETE FROM schedule_answer_selection ".
					"WHERE schedule_id ".
							"= ".pg_escape_string($this->schedule_id);

			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			// すべてInsert
			foreach ($this->_answer_selection_array as $answer_no => $selection) {

				ACSLib::escape_sql_array($selection);

				if ($selection['answer_score'] == '') {
					$selection['answer_score'] = "null";
				}

				$sql = "INSERT INTO schedule_answer_selection ( ".
						" schedule_id,answer_no,answer_char,".
						" answer_score, answer_detail, answer_default ".
						") VALUES (".
						" ".pg_escape_string($this->schedule_id).", ".
						" ".pg_escape_string($answer_no).", ".
						"'".$selection['answer_char']."', ".
						"".$selection['answer_score'].", ".
						"'".$selection['answer_detail']."', ".
						"'".$selection['answer_default']."') ";

				$ret = ACSDB::_do_query($sql);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// COMMIT
		$ret = ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * スケジュール調整決定通知定型文取得
	 *
	 * @return true / false
	 */
	function get_decision_mail_message ($lang, $decide_adjustment_date_id) {
		$current_lang = ACSMsg::get_lang();
		ACSMsg::set_lang($lang);
		$msg = ACSMsg::get_serial_msg('lib',basename(__FILE__),'DEC%03d');

		// システムURL
		$system_group = ACSMsg::get_mst('system_config_group','D01');
		$system_base_url = ACSSystemConfig::get_keyword_value(
				$system_group, 'SYSTEM_BASE_URL');
		$system_base_login_url = ACSSystemConfig::get_keyword_value(
				$system_group, 'SYSTEM_BASE_LOGIN_URL');

		// コミュニティ情報の取得
		$community_row =& ACSCommunity::get_community_row($this->community_id);

		// 決定日
		$adjustment_dates_list = $this->get_adjustment_dates();

		// 回答状況
		$person_count_array =& ACSSchedule::get_total_person_count(
				$this->community_id, $this->schedule_id);
		$person_count = $person_count_array[$this->schedule_id];

		// 対象が全員の場合は参加人数をメンバ数に修正
		if ($this->is_target_all()) {
			$person_count['participate_person_count'] =
					ACSCommunity::get_community_member_count($this->community_id);
		}

		// スケジュールＵＲＬ
		$schedule_url  = $system_base_login_url . SCRIPT_PATH .
				"?" . MODULE_ACCESSOR . "=Community" .
				"&" . ACTION_ACCESSOR . "=AnswerSchedule" .
				"&community_id=" . $this->community_id .
				"&schedule_id=" . $this->schedule_id;

		$user_community_row =& ACSUser::get_user_profile_row($this->user_community_id);

		$msg = ACSMsg::get_tag_replace($msg, array(
				'{SYSTEM_BASE_URL}'         	=> $system_base_url,
				'{COMMUNITY_ID}'            	=> $this->community_id,
				'{COMMUNITY_NAME}'          	=> $community_row['community_name'],
				'{USER_NAME}'					=> $user_community_row['user_name'],
				'{USER_COMMUNITY_NAME}'			=> $user_community_row['community_name'],
				'{SCHEDULE_NAME}'				=> $this->schedule_name,
				'{SCHEDULE_DETAIL}'				=> $this->schedule_detail,
				'{SCHEDULE_CLOSING_DATETIME}'	=> 
						ACSLib::convert_pg_date_to_str($this->schedule_closing_datetime),
				'{SCHEDULE_ANSWER_COUNT}'		=> 
						$person_count['answer_person_count'],
				'{SCHEDULE_PARTICIPATE_COUNT}'	=> 
						$person_count['participate_person_count'],
				'{SCHEDULE_DECISION_DATE}'		=> 
						$adjustment_dates_list[$decide_adjustment_date_id]['date_string'],
				'{ANSWER_SCHEDULE_URL}'			=> $schedule_url
		));

		ACSMsg::set_lang($current_lang);

		return $msg;
	}

	/**
	 * スケジュール調整決定通知件名取得
	 *
	 * @return true / false
	 */
	function get_decision_mail_subject ($lang) {
		$current_lang = ACSMsg::get_lang();
		ACSMsg::set_lang($lang);
		$subject = ACSMsg::get_mdmsg(__FILE__,'M001');

		$subject = ACSMsg::get_tag_replace($subject, array(
				'{SUBJECT_NAME}'         		=> $this->schedule_name
		));

		ACSMsg::set_lang($current_lang);

		return $subject;
	}

	/**
	 * 対象が全員かどうかの判定
	 *
	 * @param string $community_id 該当のコミュニティid
	 * @return boolean TRUE...対象が全員/FALSE...対象は自由参加
	 */
	function is_target_all () {
		return ($this->schedule_target_kind == 'ALL' ? TRUE : FALSE);
	}

	/**
	 * このスケジュールの幹事かどうかの判定
	 *
	 * @param string $user_info_row ユーザ情報
	 * @return boolean TRUE...幹事/FALSE...幹事でない
	 */
	function is_organizer ($user_info_row) {
		return ($user_info_row['user_community_id'] 
				== $this->user_community_id ? TRUE : FALSE);
	}

	/**
	 * 決定済スケジュールかどうかの判定
	 *
	 * @return boolean TRUE...決定済/FALSE...未決定
	 */
	function is_fixed () {
		return ($this->decide_adjustment_date_id > 0 ? TRUE : FALSE);
	}

	/**
	 * 締切を過ぎているかどうかの判定
	 *
	 * @return boolean TRUE...締切/FALSE...締切でない
	 */
	function is_close () {
		$close_tm = strtotime($this->schedule_closing_datetime);
		return (time() > strtotime($this->schedule_closing_datetime) ?
				TRUE : FALSE);
	}

	/**
	 * 新規スケジュール用インスタンスかどうかの判定
	 *
	 * @return boolean TRUE...新規/FALSE...既存
	 */
	function is_new () {
		return ($this->schedule_id == "" ? TRUE : FALSE);
	}

	/****************************
	 * スタティックファンクション
	 ****************************/

	/**
	 * スケジュールインスタンス生成
	 *
	 * @param string $community_id 該当のコミュニティid
	 * @param string $schedule_id 該当のスケジュールid
	 * @return object ACSScheduleオブジェクト
	 */
	function & get_schedule_instance ($community_id, $schedule_id) {

		$sql = "SELECT SCH.*, COM.community_name AS user_community_name " .
				" FROM schedule AS SCH LEFT JOIN community AS COM " .
					" ON SCH.user_community_id = COM.community_id " .
				" WHERE SCH.community_id = " .
					pg_escape_string($community_id) .
				" AND SCH.schedule_id = " . 
					pg_escape_string($schedule_id);

		$row = ACSDB::_get_row($sql);

		$schedule = new ACSSchedule(
				$community_id,$row['user_community_id'],$schedule_id);
		$schedule->initialize($row);

		return $schedule;
	}

	/**
	 * スケジュール一覧インスタンス生成(全スケジュール対象)
	 *
	 * @param string $additional_where 追加条件(未指定も可)
	 * @return array ACSScheduleインスタンスの配列
	 */
	function & get_schedule_instance_list ($additional_where='') {

		$sql = "SELECT * FROM ( " .
					"SELECT SCH.*, COM.community_name AS user_community_name " .
					" FROM schedule AS SCH LEFT JOIN community AS COM " .
					" ON SCH.user_community_id = COM.community_id " .
				") AS SUBQ ";

		if ($additional_where != '') {
			$sql .=" WHERE " . $additional_where ;
		}

		$sql .=" ORDER BY schedule_id desc";
	
		$rows = ACSDB::_get_row_array($sql);

		$schedule_array = array();

		foreach ($rows as $row) {
			$schedule =& new ACSSchedule(
					$row['community_id'],$row['user_community_id'],$row['schedule_id']);
			$schedule->initialize($row);
			$schedule_array[] = $schedule;
		}

		return $schedule_array;
	}

	/**
	 * 指定コミュニティスケジュール一覧インスタンス生成
	 *
	 * @param string $community_id 該当のコミュニティid
	 * @return array ACSScheduleインスタンスの配列
	 */
	function & get_community_schedule_instance_list ($community_id) {
		return ACSSchedule::get_schedule_instance_list(
				"community_id = ". pg_escape_string($community_id));
	}

	/**
	 * 締切日時指定スケジュール一覧インスタンス生成
	 *
	 * @param string $datetime_from 範囲開始日時
	 * @param string $datetime_to 範囲終了日時
	 * @return array ACSScheduleインスタンスの配列
	 */
	function & get_schedule_instance_list_by_closing_datetime (
			$datetime_from, $datetime_to) {
		$where = "schedule_closing_datetime >= " .
				"'" . ACSLib::convert_timestamp_to_pg_date($datetime_from) . "'" .
				" AND schedule_closing_datetime <= " .
				"'" . ACSLib::convert_timestamp_to_pg_date($datetime_to) . "'";

		return ACSSchedule::get_schedule_instance_list($where);
	}

	/**
	 * 各スケジュールの参加、回答人数の取得
	 *
	 * FREEでの人数情報となります。<br>
	 * ALLは別途コミュニティ参加者数を取得する必要があります。
	 *
	 * @param string $community_id コミュニティid(未指定も可)
	 * @param string $schedule_id スケジュールid(未指定も可)
	 * @return array 参加回答人数配列
	 *               array([schedule_id]=> array(
	 *                       'participate_person_count' => [参加人数]
	 *                       'answer_person_count'      => [回答人数] ))
	 */
	function & get_total_person_count ($community_id='',$schedule_id='') {

		$where = '';
		if ($community_id != '') {
			$where .= ($where == '' ? '' : ' AND ');
			$where .= 'SCH.community_id = ' . pg_escape_string($community_id);
		}
		if ($schedule_id != '') {
			$where .= ($where == '' ? '' : ' AND ');
			$where .= 'SCH.schedule_id = ' . pg_escape_string($schedule_id);
		}
		$where = $where == '' ? '' : 'WHERE '.$where;

		$sql = "SELECT ".
				"  SCH.schedule_id, ".
				"  PAT.cnt AS participate_person_count, ".
				"  ANS.cnt AS answer_person_count ".
				"FROM ".
				"( ".
				"    schedule AS SCH LEFT JOIN ".
				"    ( ".
				"        SELECT schedule_id, COUNT(*) AS cnt FROM schedule_participant ".
				"        WHERE participant_delete_flag = FALSE GROUP BY schedule_id ".
				"    ) AS PAT ".
				"    ON SCH.schedule_id = PAT.schedule_id ".
				") ".
				"LEFT JOIN ".
				"( ".
				"    SELECT schedule_id, COUNT(*) AS cnt FROM ".
				"    ( ".
				"        SELECT DISTINCT schedule_id, user_community_id " .
				"        FROM schedule_answer ".
				"    ) AS SBUQ ".
				"    GROUP BY schedule_id ".
				") AS ANS ".
				"ON SCH.schedule_id = ANS.schedule_id ".
				" " . $where . " ".
				"ORDER BY SCH.schedule_id ";

		$rows = ACSDB::_get_row_array($sql);

		$persons_count_array = array();
		foreach ($rows as $row) {
			$persons_count_array[$row['schedule_id']] = array(
					'participate_person_count' => 
						$row['participate_person_count']=='' ? 
						0 : $row['participate_person_count'],
					'answer_person_count' => 
						$row['answer_person_count']=='' ?
						0 : $row['answer_person_count']);
		}
		
		return $persons_count_array;
	}
}
?>
