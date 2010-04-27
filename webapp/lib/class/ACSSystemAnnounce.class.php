<?php
// $Id: ACSSystemAnnounce.class.php,v 1.1 2006/06/13 02:50:10 w-ota Exp $

/**
 * SystemAnnounce
 * システムアナウンス (システムからのお知らせ)
 */
class ACSSystemAnnounce
{
	/**
	 * システムアナウンスを取得する
	 * 
	 * @param $system_announce_id
	 * @return システムアナウンス情報
	 */
	static function get_system_announce_row($system_announce_id) {
		$system_announce_id = pg_escape_string($system_announce_id);

		$sql  = "SELECT *, acs_is_expire_date(system_announce.expire_date) AS is_expire";
		$sql .= " FROM (system_announce LEFT OUTER JOIN community ON system_announce.user_community_id = community.community_id)";
		$sql .= " WHERE system_announce.system_announce_id = '$system_announce_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * 掲載中のシステムアナウンス情報一覧を取得する
	 * 
	 * @return システムアナウンス情報一覧
	 */
	static function get_valid_system_announce_row_array() {
		$sql  = "SELECT *";
		$sql .= " FROM (system_announce LEFT OUTER JOIN community ON system_announce.user_community_id = community.community_id)";
		$sql .= " WHERE acs_is_expire_date(system_announce.expire_date) = 'f'";
		$sql .= "  AND system_announce.system_announce_delete_flag = 'f'";
		$sql .= " ORDER BY system_announce.system_announce_id DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 掲載中のシステムアナウンス情報一覧を取得する
	 * 
	 * @return システムアナウンス情報一覧
	 */
	static function get_all_system_announce_row_array() {
		$sql  = "SELECT *, acs_is_expire_date(system_announce.expire_date) as is_expire";
		$sql .= " FROM (system_announce LEFT OUTER JOIN community ON system_announce.user_community_id = community.community_id)";
		$sql .= " ORDER BY system_announce.system_announce_id DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * システムアナウンスを登録する
	 *
	 * @param システムアナウンス情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_system_announce($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$system_announce_id_seq = ACSDB::get_next_seq('system_announce_id_seq');

		$sql  = "INSERT INTO system_announce";
		$sql .= " (system_announce_id, user_community_id, subject, body, expire_date)";
		$sql .= " VALUES ($system_announce_id_seq, $form[user_community_id], $form[subject], $form[body], $form[expire_date])";

		ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * システムアナウンスを削除する (掲載中止)
	 *
	 * @param システムアナウンスID
	 * @return 成功(true) / 失敗(false)
	 */
	static function delete_system_announce($system_announce_id) {
		$system_announce_id = pg_escape_string($system_announce_id);

		$sql  = "UPDATE system_announce";
		$sql .= " SET system_announce_delete_flag = 't'";
		$sql .= " WHERE system_announce_id = '$system_announce_id'";

		ACSDB::_do_query($sql);
		return $ret;
	}
}

?>
