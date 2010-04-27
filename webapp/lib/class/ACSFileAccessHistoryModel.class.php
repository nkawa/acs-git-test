<?php
// $Id: ACSFileAccessHistoryModel.class.php,v 1.1 2006/12/18 07:41:48 w-ota Exp $


/*
 * file_access_history��ǥ�
 */
class ACSFileAccessHistoryModel {

	/**
	 * file_access_history INSERT
	 *
	 * @param $form �ե����륢�������������
	 * @return ����(true) / ����(false)
	 */
	static function insert_file_access_history($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "INSERT INTO file_access_history" . 
				" (user_community_id, file_id, access_date)" . 
				" VALUES (" . $form['user_community_id'] . "," . 
					$form['file_id'] . "," . $form['access_date'] . ")";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * file_access_history UPDATE
	 *
	 * @param $form �ե����륢�������������
	 * @return ����(true) / ����(false)
	 */
	static function update_file_access_history($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "UPDATE file_access_history" . 
				" SET" . 
				"  access_date = " . $form['access_date'] . 
				" WHERE user_community_id = " . $form['user_community_id'] . 
				"  AND file_id = " . $form['file_id'];

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
}

?>
