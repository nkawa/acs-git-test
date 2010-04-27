<?php
// $Id: ACSFileHistory.class.php,v 1.2 2006/05/29 08:03:42 w-ota Exp $

/**
 * �ե��������򥯥饹
 */
class ACSFileHistory
{
	/**
	 * �ե������������������������
	 *
	 * @param $file_id �ե�����ID
	 * @return �ե����������������� (Ϣ�����������)
	 */
	static function get_file_history_row_array($file_id) {
		$file_id = pg_escape_string($file_id);

		$sql  = "SELECT *";
		$sql .= " FROM file_history, file_history_operation_master, community as USER_COMMUNITY";
		$sql .= " WHERE file_history.file_id = '$file_id'";
		$sql .= "  AND file_history.file_history_operation_code = file_history_operation_master.file_history_operation_code";
		$sql .= "  AND file_history.update_user_community_id = USER_COMMUNITY.community_id";
		$sql .= " ORDER BY file_history.update_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �ե��������������������
	 *
	 * @param $file_id �ե�����ID
	 * @return �ե������������
	 */
	static function get_file_history_row($file_history_id) {
		$file_id = pg_escape_string($file_id);

		$sql  = "SELECT *";
		$sql .= " FROM file_history, file_history_operation_master, community as USER_COMMUNITY";
		$sql .= " WHERE file_history.file_history_id = '$file_history_id'";
		$sql .= "  AND file_history.file_history_operation_code = file_history_operation_master.file_history_operation_code";
		$sql .= "  AND file_history.update_user_community_id = USER_COMMUNITY.community_id";

		$row = ACSDB::_get_row($sql);
		return $row;
	}


	/**
	 * �ե���������������Ͽ����
	 *
	 * @param $file_info_row �ե��������
	 * @param $update_user_community_id ��Ͽ/�����ԤΥ桼�����ߥ�˥ƥ�ID
	 * @param $comment ������
	 * @param $file_history_operation_name �ե������������̾
	 * @return ����(file_history_id) / ����(false)
	 */
	static function set_file_history($file_info_row, $update_user_community_id, $comment, $file_history_operation_name) {
		$file_history_operation_master_array = ACSDB::get_master_array('file_history_operation');

		$file_history_id_seq = ACSDB::get_next_seq('file_history_id_seq');
		$file_history_operation_code = array_search($file_history_operation_name, $file_history_operation_master_array);

		ACSLib::escape_sql_array($file_info_row);
		ACSLib::get_sql_value_array($file_info_row);

		// �ե������������Ͽ
		$sql  = "INSERT INTO file_history";
		$sql .= " (file_history_id, file_id, display_file_name, server_file_name, thumbnail_server_file_name, mime_type, file_size, update_date, update_user_community_id, file_history_operation_code)";
		$sql .= " VALUES ($file_history_id_seq, $file_info_row[file_id], $file_info_row[display_file_name], $file_info_row[server_file_name], $file_info_row[thumbnail_server_file_name], $file_info_row[mime_type], $file_info_row[file_size], $file_info_row[update_date], '$update_user_community_id', '$file_history_operation_code')";

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// �ե��������򥳥��Ȥ���Ͽ
		$ret = ACSFileHistoryComment::set_file_history_comment($file_history_id_seq, $update_user_community_id, $comment);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// true�ξ��ϥե���������ID�򥻥å�
		if ($ret) {
			$ret = $file_history_id_seq;
		}

		return $ret;
	}
}

?>
