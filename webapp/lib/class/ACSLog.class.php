<?php
// $Id: ACSLog.class.php,v 1.3 2007/03/27 02:12:31 w-ota Exp $

/*
 * Logクラス
 */
class ACSLog {
	/**
	 * ログ情報を検索する
	 *
	 * @param $form 検索条件
	 * @return ログ情報の配列 (連想配列の配列)
	 */
	static function search_log_row_array($form) {
		$sql  = "SELECT *";
		$sql .= " FROM log, operation_master";
		$sql .= " WHERE log.operation_code = operation_master.operation_code";

		// 検索条件 //
		// キーワード
		if ($form['q'] != '') {

			$sqlfunc = "acs_convert_timestamp_".ACSMsg::get_lang();

			$query_array_array = ACSLib::get_query_array_array($form['q']);
			$where_sql = '';
			foreach ($query_array_array as $query_array) {
				if (!count($query_array)) {
					continue;
				}

				$sub_where_sql = '';
				foreach ($query_array as $query) {
					$query = pg_escape_string($query);
					ACSLib::escape_ilike($query);

					if ($sub_where_sql != '') {
						$sub_where_sql .= " OR ";
					}

					$sub_where_sql .= "(";
					$sub_where_sql .= " log.log_id ILIKE '%$query%'";
					//$sub_where_sql .= " OR acs_convert_timestamp_to_jdate(log.log_date, 'YYYY/MM/DD', 'FMHH24:MI:SS') ILIKE '%$query%'";
					$sub_where_sql .= " OR ".$sqlfunc."(log.log_date, 'YYYY/MM/DD', 'FMHH24:MI:SS') ILIKE '%$query%'";
					$sub_where_sql .= " OR log.user_id ILIKE '%$query%'";
					$sub_where_sql .= " OR log.user_name ILIKE '%$query%'";
					$sub_where_sql .= " OR log.community_name ILIKE '%$query%'";
					$sub_where_sql .= " OR (CASE WHEN administrator_flag = 't' THEN '".ACSMsg::get_mdmsg(__FILE__,'M001')."' ELSE '".ACSMsg::get_mdmsg(__FILE__,'M002')."' END) ILIKE '%$query%'";
					$sub_where_sql .= " OR operation_master.operation_name ILIKE '%$query%'";
					$sub_where_sql .= " OR log.message ILIKE '%$query%'";
					$sub_where_sql .= " OR (CASE WHEN log.operation_result = 't' THEN '".ACSMsg::get_mdmsg(__FILE__,'M003')."' ELSE '".ACSMsg::get_mdmsg(__FILE__,'M004')."' END) ILIKE '%$query%'";
					$sub_where_sql .= ")";
				}

				if ($sub_where_sql != '') {
					if ($where_sql != '') {
						$where_sql .= " AND ";
					}
					$where_sql .= "($sub_where_sql)";
				}
			}

			if ($where_sql != '') {
				$sql .= " AND ($where_sql)";
			}
		}
		//

		$sql .= " ORDER BY log_id DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ログを登録する
	 *
	 * @param $acs_user_info_row ACSユーザ情報
	 * @param $operation_type_name 操作名
	 * @param $operation_result 操作結果 (true/false)
	 * @param $message メッセージ
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_log($acs_user_info_row, $operation_type_name, $operation_result, $message = '') {
		// 操作コードを取得
		$operation_master_row_array = ACSDB::get_master_array('operation');

		// ログIDのシーケンス取得
		$log_id_seq = ACSDB::get_next_seq('log_id_seq');

		// INSERTデータ
		$form['log_id'] = $log_id_seq;
		$form['log_date'] = 'now';
		$form['user_id'] = $acs_user_info_row['user_id'];
		// 2009.09.08 user_name
		if ($acs_user_info_row['user_name'] == NULL || $acs_user_info_row['user_name'] == "") {
			$form['user_name'] = "anonymous";
		} else {
			$form['user_name'] = $acs_user_info_row['user_name'];
		}
		//$form['user_name'] = $acs_user_info_row['user_name'];
		$form['user_community_id'] = $acs_user_info_row['user_community_id'];
		$form['community_name'] = $acs_user_info_row['community_name'];
		$form['administrator_flag'] = $acs_user_info_row['administrator_flag'];
		$form['operation_code'] = array_search($operation_type_name, $operation_master_row_array);
		$form['operation_result'] = ACSLib::get_pg_boolean($operation_result);
		$form['message'] = $operation_master_row_array[$form['operation_code']] . $message;

		// escape
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "INSERT INTO log (log_id, log_date, user_id, user_name, user_community_id, community_name, administrator_flag, operation_code, operation_result, message)";
		$sql .= " VALUES ($form[log_id], $form[log_date], $form[user_id], $form[user_name], $form[user_community_id], $form[community_name], $form[administrator_flag], $form[operation_code], $form[operation_result], $form[message])";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * ログ情報を削除する
	 *
	 * @param $before_date 現在日時より何日前より過去のログ情報を消すか
	 * @return 成功(true) / 失敗(false)
	 */
	static function delete_log($before_date) {
		
		// BEGIN
		ACSDB::_do_query("BEGIN");
		
		$sql  = "DELETE";
		$sql .= " FROM ";
		$sql .= " log";
		$sql .= " WHERE ";
		$sql .= " log_date < current_timestamp + '-$before_date days'";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}

// 2009.12.25 add
	/**
	 * ログを登録する
	 *
	 * @param $acs_user_info_row ACSユーザ情報
	 * @param $operation_type_name 操作名
	 * @param $operation_result 操作結果 (true/false)
	 * @param $message メッセージ
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_log_debug($user_community_id, $message) {

		// ログIDのシーケンス取得
		$log_id_seq = ACSDB::get_next_seq('log_id_seq');

		// INSERTデータ
		$form['log_id'] = $log_id_seq;
		$form['log_date'] = 'now';
		$form['user_id'] = 'debug';
		$form['user_name'] = 'anonymous';
		$form['user_community_id'] = $user_community_id;
		$form['community_name'] = 'DEBUG';
		$form['administrator_flag'] = false;
		$form['operation_code'] = '9999';
		$form['operation_result'] = true;
		$form['message'] = $message;

		// escape
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "INSERT INTO log (log_id, log_date, user_id, user_name, user_community_id, community_name, administrator_flag, operation_code, operation_result, message)";
		$sql .= " VALUES ( $log_id_seq, 'now', 'debug', 'anonymous', $user_community_id, 'DEBUG', false, '9999', true, '$message')";
		$ret = ACSDB::_do_query($sql);

//		return $ret;
return null;
	}

}

?>
