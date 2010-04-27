<?php
// $Id: ACSDiaryAccessHistoryModel.class.php,v 1.1 2006/02/16 11:27:26 w-ota Exp $


/*
 * diary_access_historyモデル
 */
class ACSDiaryAccessHistoryModel {

	/**
	 * diary_access_history INSERT
	 *
	 * @param $form ダイアリーアクセス履歴情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function insert_diary_access_history($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "INSERT INTO diary_access_history";
		$sql .= " (user_community_id, diary_id, access_date)";
		$sql .= " VALUES ($form[user_community_id], $form[diary_id], $form[access_date])";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * diary_access_history UPDATE
	 *
	 * @param $form ダイアリーアクセス履歴情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_diary_access_history($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "UPDATE diary_access_history";
		$sql .= " SET";
		$sql .= "  access_date = $form[access_date]";
		$sql .= " WHERE user_community_id = $form[user_community_id]";
		$sql .= "  AND diary_id = $form[diary_id]";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
}

?>
