<?php
// $Id: ACSWaitingModel.class.php,v 1.1 2006/01/26 06:48:50 w-ota Exp $


/*
 * waitingモデル
 */
class ACSWaitingModel {

	/**
	 * waiting INSERT
	 *
	 * @param $form 待機コミュニティメンバ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function insert_waiting($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "INSERT INTO waiting";
		$sql .= " (waiting_id, community_id, waiting_community_id, waiting_type_code, waiting_status_code, message, entry_user_community_id)";
		$sql .= " VALUES ($form[waiting_id], $form[community_id], $form[waiting_community_id], $form[waiting_type_code], $form[waiting_status_code], $form[message], $form[entry_user_community_id])";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
}

?>
