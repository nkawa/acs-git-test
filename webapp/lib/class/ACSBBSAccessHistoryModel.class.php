<?php
// $Id: ACSBBSAccessHistoryModel.class.php,v 1.1 2006/04/19 05:33:39 w-ota Exp $


/*
 * bbs_access_history¥â¥Ç¥ë
 */
class ACSBBSAccessHistoryModel {

	/**
	 * bbs_access_history INSERT
	 *
	 * @param $form ·Ç¼¨ÈÄ¥¢¥¯¥»¥¹ÍúÎò¾ðÊó
	 * @return À®¸ù(true) / ¼ºÇÔ(false)
	 */
	static function insert_bbs_access_history($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "INSERT INTO bbs_access_history";
		$sql .= " (user_community_id, bbs_id, access_date)";
		$sql .= " VALUES ($form[user_community_id], $form[bbs_id], $form[access_date])";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * bbs_access_history UPDATE
	 *
	 * @param $form ·Ç¼¨ÈÄ¥¢¥¯¥»¥¹ÍúÎò¾ðÊó
	 * @return À®¸ù(true) / ¼ºÇÔ(false)
	 */
	static function update_bbs_access_history($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "UPDATE bbs_access_history";
		$sql .= " SET";
		$sql .= "  access_date = $form[access_date]";
		$sql .= " WHERE user_community_id = $form[user_community_id]";
		$sql .= "  AND bbs_id = $form[bbs_id]";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
}

?>
