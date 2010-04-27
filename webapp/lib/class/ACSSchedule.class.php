<?php
require_once(ACS_CLASS_DIR . 'ACSDB.class.php');
require_once(ACS_CLASS_DIR . 'ACSCommunityMail.class.php');

define( '_ACSSCHEDULE_ANSWER_COUNT', 7 );
define( '_ACSSCHEDULE_DEFAULT_ANSWER_CHAR',    'o,v,-,x'  );
define( '_ACSSCHEDULE_DEFAULT_ANSWER_SCORE',   '2,1,0,-1' );
define( '_ACSSCHEDULE_DEFAULT_ANSWER_DEFAULT', 'f,f,t,f'  );

/**
 * ACS �������塼�륯�饹
 *
 * @author  z-satosi
 * @version $Revision: 1.3 $
 */
class ACSSchedule
{
	/* �������塼��ID */
	var $schedule_id;

	/* ���ߥ�˥ƥ�ID */
	var $community_id;

	/* �����桼�����ߥ�˥ƥ�ID */
	var $user_community_id;

	/* �����桼�����ߥ�˥ƥ�̾ */
	var $user_community_name;

	/* �������塼��̾ */
	var $schedule_name;

	/* ��� */
	var $schedule_place;

	/* �ܺپ��� */
	var $schedule_detail;

	/* �о� */
	var $schedule_target_kind;

	/* ������������ */
	var $schedule_closing_datetime;

	/* �������� */
	var $_adjustment_dates_array;

	/* �������������ѥ����å� */
	var $_adjustment_dates_stack;

	/* ��������� */
	var $_answer_selection_array;

	/* �����ԥꥹ�� */
	var $_answer_users_array;

	/* �������� */
	var $_entry_datetime;

	/* �������� */
	var $_update_datetime;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param integer $community_id ���ߥ�˥ƥ�id
	 * @param integer $user_community_id �����ԥ桼�����ߥ�˥ƥ�id
	 * @param integer $schedule_id �������塼��id(̤������Ͽ���)
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
	 * �������
	 *
	 * @param array $schedule_row �����Υ������塼��ơ��֥��
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
	 * �������塼������������μ���
	 *
	 * @param boolean $contain_deleted ����ѥǡ�����ޤ�(̤�������false)
	 * @return int ����������
	 */
	function get_adjustment_dates_count ($contain_deleted = FALSE) {
		return count($this->get_adjustment_dates($contain_deleted));
	}

	/**
	 * �������塼����������μ���
	 *
	 * @param boolean $contain_deleted ����ѥǡ�����ޤ�(̤�������false)
	 * @return array ������������������([��������id] => array(
	 *                   'date_string' => ��������ɽ��ʸ����
	 *                   'delete_flag' => ����ե饰 't'=�����);
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
	 * �������塼����������No�μ���
	 *
	 * @return int ���������No
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
	 * �������塼�����������μ���
	 *
	 * @return int �����ο�
	 */
	function get_answer_selection_count () {
		return count($this->get_answer_selection());
	}

	/**
	 * �������塼�������������
	 *
	 * @return array ����������2��������
	 *               array( [answer_no] = array(
	 *                   'answer_char'    => ��������
	 *                   'answer_score'   => ������
	 *                   'answer_detail'  => ����������ʸ
	 *                   'answer_default' => 't'=��������� ))
	 */
	function & get_answer_selection () {

		if (!is_array($this->_answer_selection_array)) {

			// �����ξ��Ͻ���ͤ�����
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

			// ��¸�ξ�����Ͽ�ǡ���������
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

			// ����褬�������ã���ʤ����϶��ǡ���������
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
	 * �������塼��������������
	 *
	 * @param array $char_array   ������������(1origin)
	 * @param array $score_array  ����������(1origin)
	 * @param array $detail_array ����������ʸ����(1origin)
	 * @param array $default_no   ���������No(1��max)
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
	 * ���������������������
	 *
	 * @return array ���ջ��������
	 *                         'year'    => ǯ
	 *                         'month    => ��
	 *                         'day'     => ��
	 *                         'hours'   => ��
	 *                         'minutes' => ʬ
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
	 * ������������������(ǯ,��,��,��,ʬ�θ��̻���)
	 *
	 * @param array $ymd_array ǯ,��,��,��,ʬ�θ��̻��������
	 *                         'year'    => ǯ
	 *                         'month    => ��
	 *                         'day'     => ��
	 *                         'hours'   => ��
	 *                         'minutes' => ʬ
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
	 * ���������κ������
	 * 
	 * ���������κ�������Ԥ��ޤ���<br>
	 * �ģ¤ؤ�ȿ�Ǥ� update_schedule �ˤƼ»ܤ���ޤ���
	 *
	 * @param string $adjustment_date_id ������ꤹ���������id
	 */
	function set_schdedule_adjustment_datetime_delete ($adjustment_date_id) {

		$this->_adjustment_dates_stack['delete'][] = $adjustment_date_id;
	}

	/**
	 * �����������ɲû���
	 * 
	 * �����������ɲû����Ԥ��ޤ���<br>
	 * �ģ¤ؤ�ȿ�Ǥ� update_schedule �ˤƼ»ܤ���ޤ���
	 * �ɲä���ʸ������˲���ʸ���������硢ʣ���θ��������Ȥ����ɲä���ޤ���
	 *
	 * @param string $append_adjustment_date_string �ɲû��ꤹ���������ʸ����
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
	 * �������塼�����η�����ģ¹�������
	 * 
	 * �������塼�����η�����ģ¹�����»ܤ��ޤ���<br>
	 * ���������ơ��֥�ϰʲ����̤�<br>
	 *     schedule.decide_adjustment_date_id ... 0 -> ���ꤷ����������id<br>
	 * @return boolean TRUE...����/FALSE...����
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
	 * �������塼�����Σģ¹���
	 * 
	 * �������塼�����Σģ¹�����»ܤ��ޤ���<br>
	 * ���������ơ��֥�ϰʲ����̤�<br>
	 *     schedule<br>
	 *     schedule_adjustment_dates<br>
	 *     schedule_answer_selection<br>
	 * ���������ϡ�set_schdedule_adjustment_datetime_append() ����� <br>
	 * set_schdedule_adjustment_datetime_delete() �ǻ��ꤵ�줿���Ƥ�ȿ�Ǥ��줢�ޤ���
	 * 
	 * @return boolean TRUE...����/FALSE...����
	 */
	function update_schedule () {

		// BEGIN
		ACSDB::_do_query("BEGIN");

		if ($this->is_new()) {

			// �������塼��id�μ���
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

		// ������������Ͽ(���)
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

		// ������������Ͽ(�ɲ�)
		if (is_array($this->_adjustment_dates_stack['append'])) {

			foreach ($this->_adjustment_dates_stack['append'] as $append_str) {

				// �������塼���������id�μ���
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

		// ��������Ͽ(Delete&Insert)
		if (is_array($this->_answer_selection_array)) {

			// ��öDelete
			$sql = "DELETE FROM schedule_answer_selection ".
					"WHERE schedule_id ".
							"= ".pg_escape_string($this->schedule_id);

			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			// ���٤�Insert
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
	 * �������塼��Ĵ�����������귿ʸ����
	 *
	 * @return true / false
	 */
	function get_decision_mail_message ($lang, $decide_adjustment_date_id) {
		$current_lang = ACSMsg::get_lang();
		ACSMsg::set_lang($lang);
		$msg = ACSMsg::get_serial_msg('lib',basename(__FILE__),'DEC%03d');

		// �����ƥ�URL
		$system_group = ACSMsg::get_mst('system_config_group','D01');
		$system_base_url = ACSSystemConfig::get_keyword_value(
				$system_group, 'SYSTEM_BASE_URL');
		$system_base_login_url = ACSSystemConfig::get_keyword_value(
				$system_group, 'SYSTEM_BASE_LOGIN_URL');

		// ���ߥ�˥ƥ�����μ���
		$community_row =& ACSCommunity::get_community_row($this->community_id);

		// ������
		$adjustment_dates_list = $this->get_adjustment_dates();

		// ��������
		$person_count_array =& ACSSchedule::get_total_person_count(
				$this->community_id, $this->schedule_id);
		$person_count = $person_count_array[$this->schedule_id];

		// �оݤ������ξ��ϻ��ÿͿ�����п��˽���
		if ($this->is_target_all()) {
			$person_count['participate_person_count'] =
					ACSCommunity::get_community_member_count($this->community_id);
		}

		// �������塼��գң�
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
	 * �������塼��Ĵ���������η�̾����
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
	 * �оݤ��������ɤ�����Ƚ��
	 *
	 * @param string $community_id �����Υ��ߥ�˥ƥ�id
	 * @return boolean TRUE...�оݤ�����/FALSE...�оݤϼ�ͳ����
	 */
	function is_target_all () {
		return ($this->schedule_target_kind == 'ALL' ? TRUE : FALSE);
	}

	/**
	 * ���Υ������塼��δ������ɤ�����Ƚ��
	 *
	 * @param string $user_info_row �桼������
	 * @return boolean TRUE...����/FALSE...�����Ǥʤ�
	 */
	function is_organizer ($user_info_row) {
		return ($user_info_row['user_community_id'] 
				== $this->user_community_id ? TRUE : FALSE);
	}

	/**
	 * ����ѥ������塼�뤫�ɤ�����Ƚ��
	 *
	 * @return boolean TRUE...�����/FALSE...̤����
	 */
	function is_fixed () {
		return ($this->decide_adjustment_date_id > 0 ? TRUE : FALSE);
	}

	/**
	 * ���ڤ�᤮�Ƥ��뤫�ɤ�����Ƚ��
	 *
	 * @return boolean TRUE...����/FALSE...���ڤǤʤ�
	 */
	function is_close () {
		$close_tm = strtotime($this->schedule_closing_datetime);
		return (time() > strtotime($this->schedule_closing_datetime) ?
				TRUE : FALSE);
	}

	/**
	 * �����������塼���ѥ��󥹥��󥹤��ɤ�����Ƚ��
	 *
	 * @return boolean TRUE...����/FALSE...��¸
	 */
	function is_new () {
		return ($this->schedule_id == "" ? TRUE : FALSE);
	}

	/****************************
	 * �����ƥ��å��ե��󥯥����
	 ****************************/

	/**
	 * �������塼�륤�󥹥�������
	 *
	 * @param string $community_id �����Υ��ߥ�˥ƥ�id
	 * @param string $schedule_id �����Υ������塼��id
	 * @return object ACSSchedule���֥�������
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
	 * �������塼��������󥹥�������(���������塼���о�)
	 *
	 * @param string $additional_where �ɲþ��(̤������)
	 * @return array ACSSchedule���󥹥��󥹤�����
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
	 * ���ꥳ�ߥ�˥ƥ��������塼��������󥹥�������
	 *
	 * @param string $community_id �����Υ��ߥ�˥ƥ�id
	 * @return array ACSSchedule���󥹥��󥹤�����
	 */
	function & get_community_schedule_instance_list ($community_id) {
		return ACSSchedule::get_schedule_instance_list(
				"community_id = ". pg_escape_string($community_id));
	}

	/**
	 * �����������ꥹ�����塼��������󥹥�������
	 *
	 * @param string $datetime_from �ϰϳ�������
	 * @param string $datetime_to �ϰϽ�λ����
	 * @return array ACSSchedule���󥹥��󥹤�����
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
	 * �ƥ������塼��λ��á������Ϳ��μ���
	 *
	 * FREE�ǤοͿ�����Ȥʤ�ޤ���<br>
	 * ALL�����ӥ��ߥ�˥ƥ����üԿ����������ɬ�פ�����ޤ���
	 *
	 * @param string $community_id ���ߥ�˥ƥ�id(̤������)
	 * @param string $schedule_id �������塼��id(̤������)
	 * @return array ���ò����Ϳ�����
	 *               array([schedule_id]=> array(
	 *                       'participate_person_count' => [���ÿͿ�]
	 *                       'answer_person_count'      => [�����Ϳ�] ))
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
