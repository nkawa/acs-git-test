<?php
require_once(ACS_CLASS_DIR . 'ACSDB.class.php');

/**
 * ACS �������塼�뻲�üԥ��饹
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $
 */
class ACSScheduleParticipant
{
	/* �������塼��ID */
	var $schedule_id;

	/* ���üԥ桼�����ߥ�˥ƥ�ID */
	var $user_community_id;

	/* ���üԥ桼�����ߥ�˥ƥ�̾�ʥ˥å��͡���� */
	var $user_community_name;

	/* ���å����� */
	var $participant_comment;

	/* ����ե饰 */
	var $participant_delete_flag;

	/* �ģ���Ͽ���� */
	var $_is_new;

	/* �������� array( [adjustment_date_id] => [answer_no] ) */
	var $_schedule_answer_array;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param integer $schedule_id �������塼��id
	 * @param integer $user_community_id �桼�����ߥ�˥ƥ�id
	 */
	function ACSScheduleParticipant ($schedule_id,$user_community_id) {
		$this->schedule_id = $schedule_id;
		$this->user_community_id = $user_community_id;
		$this->participant_delete_flag = "f";
		$this->_schedule_answer_array = array();
		$this->_is_new = 't';
	}

	/**
	 * �������
	 *
	 * @param array $schedule_participant_row �������塼�뻲�äΥơ��֥��
	 * @param array $schedule_answer_rows �������塼������Υơ��֥������
	 */
	function initialize (&$schedule_participant_row,&$schedule_answer_rows) {
		if (is_array($schedule_participant_row)) {
			$this->participant_comment     = 
					$schedule_participant_row['participant_comment'];
			$this->participant_delete_flag = 
					$schedule_participant_row['participant_delete_flag'] == '' ?
					'f' : $schedule_participant_row['participant_delete_flag'];
			$this->user_community_name     = 
					$schedule_participant_row['user_community_name'];
			$this->_is_new = 'f';
		}
		$this->_schedule_answer_array = array();
		if (is_array($schedule_answer_rows)) {
			foreach ($schedule_answer_rows as $answer_row) {
				$this->_schedule_answer_array[$answer_row['adjustment_date_id']]
						= $answer_row['answer_no'];	
			}
			reset($schedule_answer_rows);
		}
	}

	/**
	 * �������塼�뻲�þ���Σģ¹���
	 * 
	 * �������塼�뻲�þ���Σģ¹�����»ܤ��ޤ���<br>
	 * ���������ơ��֥�ϰʲ����̤�<br>
	 *     schedule_paticipant<br>
	 *     schedule_answer<br>
	 *
	 * @param boolean $is_participant_only ������Ͽ�Τߤξ��TRUE(̤�������FALSE)
	 */
	function update_participant($is_participant_only=FALSE) {

		// BEGIN
		ACSDB::_do_query("BEGIN");

		if ($this->is_new()) {

			$sql = "INSERT INTO schedule_participant (".
					" schedule_id, ".
					" user_community_id, ".
					" participant_comment, ".
					" participant_delete_flag ".
					") VALUES (" .
					" ".pg_escape_string($this->schedule_id)."," .
					" ".pg_escape_string($this->user_community_id)."," .
					" '".pg_escape_string($this->participant_comment)."'," .
					" '".pg_escape_string($this->participant_delete_flag)."' " .
					")";


		// Update
		} else {
			$sql = "UPDATE schedule_participant SET ".
					" participant_comment = ".
					" '".pg_escape_string($this->participant_comment)."'," .
					" participant_delete_flag = ".
					" '".pg_escape_string($this->participant_delete_flag)."' " .
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
		$this->_is_new = 'f';

		// ����(schedule_answer)����Ͽ(Delete/Insert)
		if ($is_participant_only === FALSE) {

			// ��öDelete
			$sql = "DELETE FROM schedule_answer ".
					"WHERE schedule_id ".
							"= ".pg_escape_string($this->schedule_id) .
					" AND user_community_id ".
							"= ".pg_escape_string($this->user_community_id) ;

			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			// ���٤�Insert
			foreach ($this->_schedule_answer_array as $adjustment_date_id => $answer_no) {
	
				$sql = "INSERT INTO schedule_answer ( ".
						" schedule_id, ".
						" user_community_id, ".
						" adjustment_date_id, ".
						" answer_no ".
						") VALUES (".
						" ".pg_escape_string($this->schedule_id)."," .
						" ".pg_escape_string($this->user_community_id)."," .
						" ".pg_escape_string($adjustment_date_id)."," .
						" ".pg_escape_string($answer_no)." " .
						")";
	
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
	 * ������������β���������
	 *
	 * @param array $answers_array ������������ array([adjustment_date_id]=>[answer_no])
	 */
	function set_answer ($answers_array) {
		if (is_array($answers_array)) {
			foreach ($answers_array as $adjustment_date_id => $answer_no) {
				$this->_schedule_answer_array[$adjustment_date_id] = $answer_no;
			}
		}
	}

	/**
	 * ������������β��������
	 *
	 * @param integer $adjustment_date_id ��������id
	 * @return integer ����No(answer_no)
	 */
	function & get_answer ($adjustment_date_id) {
		return $this->_schedule_answer_array[$adjustment_date_id];
	}

	/**
	 * ���ä��ɤ�����Ƚ��
	 *
	 * @return boolean TRUE...����/FALSE...�Ի���
	 */
	function is_participate () {
		return ((!$this->is_new() && $this->participant_delete_flag == 'f') ? TRUE : FALSE);
	}

	/**
	 * �������󥹥��󥹤��ɤ�����Ƚ��
	 *
	 * �����Ǥʤ�������<br>
	 * ��initialize�������˻��þ���¸�ߤ������<br>
	 * ��update_participant��¹Ԥ������
	 *
	 * @return boolean TRUE...����/FALSE...����
	 */
	function is_new () {
		return $this->_is_new == 'f' ? FALSE : TRUE;
	}

	/****************************
	 * �����ƥ��å��ե��󥯥����
	 ****************************/

	/**
	 * �������塼�뻲�å��󥹥�������
	 *
	 * @param string $schedule_id �����Υ������塼��id
	 * @param string $user_community_id �����Υ桼�����ߥ�˥ƥ�id
	 * @return object ACSScheduleParticipant���֥�������
	 */
	function & get_schedule_participant_instance ($schedule_id,$user_community_id) {

		$sql = "SELECT pa.*, cm.community_name AS user_community_name " .
				"FROM schedule_participant AS pa " .
				"LEFT JOIN community AS cm " .
				"    ON pa.user_community_id = cm.community_id " .
				"WHERE pa.schedule_id = " . pg_escape_string($schedule_id) .
				" AND pa.user_community_id = " . pg_escape_string($user_community_id);

		$participant_row = ACSDB::_get_row($sql);

		$sql = "SELECT * FROM schedule_answer " .
				"WHERE schedule_id = " . pg_escape_string($schedule_id) .
				" AND user_community_id = " . pg_escape_string($user_community_id) .
				" ORDER BY adjustment_date_id";

		$answer_rows = ACSDB::_get_row_array($sql);

		$schedule_participant =& 
				new ACSScheduleParticipant($schedule_id,$user_community_id);
		$schedule_participant->initialize($participant_row,$answer_rows);

		return $schedule_participant;
	}

	/**
	 * �������塼��������󥹥�������
	 *
	 *  ����ͤϡ�array([user_community_id] => ACSScheduleParticipant���󥹥���)
	 *
	 * @param string $schedule_id �����Υ������塼��id
	 * @param boolean $is_target_kind_all �������å⡼�ɤǼ������뤫�ɤ���
	 * @return array ACSScheduleParticipant���󥹥��󥹤�����
	 */
	function & get_schedule_participant_instance_list ($schedule_id,$is_target_kind_all) {

		if ($is_target_kind_all) {
			$sql = "SELECT " .
					" sc.schedule_id, " .
					" mmb.user_community_id, " .
					" pa.participant_comment, ".
					" pa.participant_delete_flag, ".
					" cmm.community_name AS user_community_name " .
					"FROM (((schedule AS sc " .
					"LEFT JOIN community AS cm ".
					"    ON sc.community_id = cm.community_id) " .
					"LEFT JOIN community_member AS mmb ".
					"    ON sc.community_id = mmb.community_id) " .
					"LEFT JOIN community AS cmm  ".
					"    ON mmb.user_community_id = cmm.community_id) " .
					"LEFT JOIN schedule_participant AS pa ".
					"    ON sc.schedule_id = pa.schedule_id " .
					"    AND mmb.user_community_id = pa.user_community_id ".
					"WHERE sc.schedule_id = " . pg_escape_string($schedule_id) .
					"  AND cmm.delete_flag != 't'";
					" ORDER BY mmb.user_community_id ";
		} else {
			$sql = "SELECT ".
					" pa.schedule_id, ".
					" pa.user_community_id, ".
					" pa.participant_comment, ".
					" pa.participant_delete_flag, ".
					" cm.community_name AS user_community_name " .
					"FROM schedule_participant AS pa " .
					"LEFT JOIN community AS cm " .
					"    ON pa.user_community_id = cm.community_id " .
					"WHERE pa.schedule_id = " . pg_escape_string($schedule_id) .
					" AND pa.participant_delete_flag = FALSE " .
					" AND cm.delete_flag != 't'";
					" ORDER BY pa.user_community_id ";
		}
	
		$participant_rows = ACSDB::_get_row_array($sql);

		$sql = "SELECT * FROM schedule_answer " .
				"WHERE schedule_id = " . pg_escape_string($schedule_id) .
				" ORDER BY user_community_id, adjustment_date_id";

		$answer_res = ACSDB::_get_res($sql);
		$answer_all_rows = array();
		while ($row = $answer_res->fetchRow(DB_FETCHMODE_ASSOC)) {
			$answer_all_rows[$row['user_community_id']][] = $row;
		}
		$answer_res->free();

		$schedule_participant_array = array();
		foreach ($participant_rows as $participant_row) {
			$user_community_id = $participant_row['user_community_id'];
			$schedule_participant =& 
					new ACSScheduleParticipant($schedule_id,$user_community_id);
			$schedule_participant->initialize(
					$participant_row,$answer_all_rows[$user_community_id]);
			$schedule_participant_array[$user_community_id] = $schedule_participant;
		}
		return $schedule_participant_array;
	}
}
?>
