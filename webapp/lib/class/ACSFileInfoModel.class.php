<?php
/**
 * ファイル情報 DBアクセス
 *
 * @author  kuwayama
 * @version $Revision: 1.17 $ $Date: 2006/05/18 05:18:00 $
 */
class ACSFileInfoModel
{
	/**
	 * ファイル情報取得（複数）
	 *
	 * @param $file_id_array
	 */
	static function select_file_info_row_array ($file_id_array) {
		$target_file_id = implode(", ", $file_id_array);

		$sql  = "SELECT file_info.*,";
		$sql .=	   " ENTRY_USER_COMMUNITY.community_name AS entry_user_community_name,";
		$sql .=	   " UPDATE_USER_COMMUNITY.community_name AS update_user_community_name";
		$sql .= " FROM file_info, community AS ENTRY_USER_COMMUNITY, community AS UPDATE_USER_COMMUNITY";
		$sql .= " WHERE file_id IN (" . $target_file_id . ")";
		$sql .=   " AND file_info.entry_user_community_id = ENTRY_USER_COMMUNITY.community_id";
		$sql .=   " AND file_info.update_user_community_id = UPDATE_USER_COMMUNITY.community_id";
		$sql .= " ORDER BY display_file_name ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ファイル情報取得
	 *
	 * @param $file_id
	 */
	static function select_file_info_row ($file_id) {
		$file_id_array = array($file_id);
		$row_array = ACSFileInfoModel::select_file_info_row_array($file_id_array);

		return $row_array[0];
	}

	/**
	 * ファイルID取得
	 *
	 * @param none
	 */
	static function get_next_file_id_seq() {
		$file_id = ACSDB::get_next_seq('file_id_seq');
		return $file_id;
	}

	/**
	 * ファイル情報追加
	 *
	 * @param none
	 */
	static function insert_file_info ($file_obj) {
		$sql  = "INSERT INTO file_info";
		$sql .= " (file_id, owner_community_id, display_file_name, server_file_name, ";
		$sql .=   "thumbnail_server_file_name, rss_server_file_name, mime_type, file_size, ";
		$sql .=   "entry_user_community_id, entry_date, update_user_community_id, update_date)";
		$sql .= " VALUES (";
		$sql .=		   "" . $file_obj->get_file_id() . ",";
		$sql .=		   "" . $file_obj->get_owner_community_id() . ",";
		$sql .=		   "'" . pg_escape_string($file_obj->get_display_file_name()) . "',";
		$sql .=		   "'" . pg_escape_string($file_obj->get_server_file_name()) . "',";
		$sql .=		   "'" . pg_escape_string($file_obj->get_thumbnail_server_file_name()) . "',";
		$sql .=		   "'" . pg_escape_string($file_obj->get_rss_server_file_name()) . "',";
		$sql .=		   "'" . pg_escape_string($file_obj->get_mime_type()) . "',";
		$sql .=		   "" . $file_obj->get_file_size() . ",";
		$sql .=		   "" . $file_obj->get_entry_user_community_id() . ",";
		$sql .=		   "'" . $file_obj->get_entry_date() . "',";
		$sql .=		   "" . $file_obj->get_update_user_community_id() . ",";
		$sql .=		   "'" . $file_obj->get_update_date() . "'";
		$sql .=  ")";

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * ファイル情報更新 (共通)
	 *
	 * @param  $target_file_id
	 * @param  $row
	 * @return $ret
	 */
	static function update_file_info ($target_file_id, $row) {
		$set_values = array();
		foreach ($row as $key => $value) {
			$value_str = "";
			$value_str = " " . $key . " = '" . pg_escape_string($value) . "'";

			array_push($set_values, $value_str);
		}

		$sql  = "UPDATE file_info";
		$sql .= " SET";
		$sql .= implode(", ", $set_values);
		$sql .= " WHERE file_id = " . $target_file_id;

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * ファイル情報更新
	 * @param $file_obj
	 * @author akitsu 2005/2/10
	 */
	static function update_all_file_info ($file_obj) {
		/* 更新データセット */
		$row = array();
		$row['display_file_name']          = pg_escape_string($file_obj->get_display_file_name());
		$row['server_file_name']           = pg_escape_string($file_obj->get_server_file_name());
		$row['thumbnail_server_file_name'] = pg_escape_string($file_obj->get_thumbnail_server_file_name());
		$row['mime_type']                  = pg_escape_string($file_obj->get_mime_type());
		$row['file_size']                  = $file_obj->get_file_size();
		$row['update_user_community_id']   = $file_obj->get_update_user_community_id();
		$row['update_date']                = $file_obj->get_update_date();

		/* 更新処理 */
		$ret = ACSFileInfoModel::update_file_info($file_obj->get_file_id(), $row);

		return $ret;
	}

	/**
	 * 表示用ファイル名を更新
	 * エラー処理 (ROLLBACK) は呼び元で行うこと
	 *
	 * @param  $target_file_id
	 * @param  $new_display_file_name
	 * @return $ret 更新結果 (true/false)
	 */
	static function update_display_file_name ($target_file_id, $new_display_file_name) {
		$row = array();

		/* 更新データセット */
		$row['display_file_name'] = $new_display_file_name;

		/* 更新処理 */
		$ret = ACSFileInfoModel::update_file_info($target_file_id, $row);

		return $ret;
	}

	/**
	 * ファイル情報削除
	 * @param $file_obj
	 */
	static function delete_file_info ($file_obj) {
		$sql  = "DELETE FROM file_info";
		$sql .= " WHERE";				
		$sql .=           " file_id = " . $file_obj->get_file_id();
		$sql .= " AND";	
		$sql .=           " owner_community_id = " . $file_obj->get_owner_community_id();
		
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}
}
