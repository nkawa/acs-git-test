<?php
require_once(ACS_CLASS_DIR . 'ACSDB.class.php');

/**
 * ACS スケジュール参加者クラス
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $
 */
class ACSScheduleParticipant
{
	/* スケジュールID */
	var $schedule_id;

	/* 参加者ユーザコミュニティID */
	var $user_community_id;

	/* 参加者ユーザコミュニティ名（ニックネーム） */
	var $user_community_name;

	/* 参加コメント */
	var $participant_comment;

	/* 削除フラグ */
	var $participant_delete_flag;

	/* ＤＢ登録状態 */
	var $_is_new;

	/* 回答配列 array( [adjustment_date_id] => [answer_no] ) */
	var $_schedule_answer_array;

	/**
	 * コンストラクタ
	 *
	 * @param integer $schedule_id スケジュールid
	 * @param integer $user_community_id ユーザコミュニティid
	 */
	function ACSScheduleParticipant ($schedule_id,$user_community_id) {
		$this->schedule_id = $schedule_id;
		$this->user_community_id = $user_community_id;
		$this->participant_delete_flag = "f";
		$this->_schedule_answer_array = array();
		$this->_is_new = 't';
	}

	/**
	 * 初期処理
	 *
	 * @param array $schedule_participant_row スケジュール参加のテーブル行
	 * @param array $schedule_answer_rows スケジュール回答のテーブル行配列
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
	 * スケジュール参加情報のＤＢ更新
	 * 
	 * スケジュール参加情報のＤＢ更新を実施します。<br>
	 * 更新されるテーブルは以下の通り<br>
	 *     schedule_paticipant<br>
	 *     schedule_answer<br>
	 *
	 * @param boolean $is_participant_only 参加登録のみの場合TRUE(未指定時はFALSE)
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

		// 回答(schedule_answer)の登録(Delete/Insert)
		if ($is_participant_only === FALSE) {

			// 一旦Delete
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

			// すべてInsert
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
	 * 指定候補日時の回答を設定
	 *
	 * @param array $answers_array 回答情報配列 array([adjustment_date_id]=>[answer_no])
	 */
	function set_answer ($answers_array) {
		if (is_array($answers_array)) {
			foreach ($answers_array as $adjustment_date_id => $answer_no) {
				$this->_schedule_answer_array[$adjustment_date_id] = $answer_no;
			}
		}
	}

	/**
	 * 指定候補日時の回答を取得
	 *
	 * @param integer $adjustment_date_id 候補日時id
	 * @return integer 回答No(answer_no)
	 */
	function & get_answer ($adjustment_date_id) {
		return $this->_schedule_answer_array[$adjustment_date_id];
	}

	/**
	 * 参加かどうかの判定
	 *
	 * @return boolean TRUE...参加/FALSE...不参加
	 */
	function is_participate () {
		return ((!$this->is_new() && $this->participant_delete_flag == 'f') ? TRUE : FALSE);
	}

	/**
	 * 新規インスタンスかどうかの判定
	 *
	 * 新規でないケース<br>
	 * ・initialize処理時に参加情報が存在した場合<br>
	 * ・update_participantを実行した場合
	 *
	 * @return boolean TRUE...新規/FALSE...更新
	 */
	function is_new () {
		return $this->_is_new == 'f' ? FALSE : TRUE;
	}

	/****************************
	 * スタティックファンクション
	 ****************************/

	/**
	 * スケジュール参加インスタンス生成
	 *
	 * @param string $schedule_id 該当のスケジュールid
	 * @param string $user_community_id 該当のユーザコミュニティid
	 * @return object ACSScheduleParticipantオブジェクト
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
	 * スケジュール一覧インスタンス生成
	 *
	 *  戻り値は、array([user_community_id] => ACSScheduleParticipantインスタンス)
	 *
	 * @param string $schedule_id 該当のスケジュールid
	 * @param boolean $is_target_kind_all 全員参加モードで取得するかどうか
	 * @return array ACSScheduleParticipantインスタンスの配列
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
