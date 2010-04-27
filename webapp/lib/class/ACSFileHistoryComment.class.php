<?php
// $Id: ACSFileHistoryComment.class.php,v 1.1 2006/05/18 05:18:00 w-ota Exp $

/**
 * �ե��������򥳥��ȥ��饹
 */
class ACSFileHistoryComment
{
	/**
	 * �ե��������򥳥��Ȱ������������
	 *
	 * @param $file_history_id �ե���������ID
	 * @param $entry_user_community_id ��Ͽ�ԤΥ桼�����ߥ�˥ƥ�ID
	 * @param $comment ������
	 * @return 
	 */
	static function get_file_history_comment_row_array($file_history_id) {
		$file_history_id = pg_escape_string($file_history_id);

		$sql  = "SELECT *";
		$sql .= " FROM file_history_comment, community as USER_COMMUNITY";
		$sql .= " WHERE file_history_comment.file_history_id = '$file_history_id'";
		$sql .= "  AND file_history_comment.user_community_id = USER_COMMUNITY.community_id";
		$sql .= " ORDER BY file_history_comment.post_date ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �ե��������򥳥��Ȥ���Ͽ����
	 *
	 * @param $file_history_id �ե���������ID
	 * @param $entry_user_community_id ��Ͽ�ԤΥ桼�����ߥ�˥ƥ�ID
	 * @param $comment ������
	 * @return 
	 */
	static function set_file_history_comment($file_history_id, $entry_user_community_id, $comment) {
		$file_history_comment_id_seq = ACSDB::get_next_seq('file_history_comment_id_seq');
		$comment = ACSLib::get_sql_value(pg_escape_string($comment));

		$sql  = "INSERT INTO file_history_comment";
		$sql .= " (file_history_comment_id, file_history_id, user_community_id, comment)";
		$sql .= " VALUES ($file_history_comment_id_seq, $file_history_id, '$entry_user_community_id', $comment)";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}
}

?>
