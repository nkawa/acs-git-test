<?php
// $Id: ACSFileDetailInfo.class.php,v 1.4 2007/03/28 09:30:29 w-ota Exp $

/**
 * ファイル詳細情報クラス
 */

class ACSFileDetailInfo
{
	/**
	 * ファイル詳細情報を取得する
	 *
	 * @param 
	 * @return 
	 */
	static function get_file_detail_info_row($file_id) {
		$file_id = pg_escape_string($file_id);

		$sql  = "SELECT *";
		$sql .= " FROM file_detail_info, file_category_master";
		$sql .= " WHERE file_detail_info.file_id = '$file_id'";
		$sql .= "  AND file_detail_info.file_category_code = file_category_master.file_category_code";
		$file_detail_info_row = ACSDB::_get_row($sql);

		$file_contents_type_list_row_array = ACSFileDetailInfo::get_file_contents_type_list_row_array($file_detail_info_row['file_category_code']);

		$file_detail_info_row['file_contents_row_array'] = array();
		foreach ($file_contents_type_list_row_array as $file_contents_type_list_row) {
			$file_detail_info_row['file_contents_row_array'][$file_contents_type_list_row['file_contents_type_code']]
				 = ACSFileDetailInfo::get_file_contents_row($file_id, $file_contents_type_list_row['file_contents_type_code']);
		}

		return $file_detail_info_row;
	}


	/**
	 * ファイルコンテンツ種別リストを取得する
	 *
	 * @param 
	 * @return 
	 */
	static function get_file_contents_type_list_row_array($file_category_code) {
		$file_category_code = pg_escape_string($file_category_code);

		$sql  = "SELECT *";
		$sql .= " FROM file_contents_type_list";
		$sql .= " WHERE file_contents_type_list.file_category_code = '$file_category_code'";
		$sql .= " ORDER BY file_contents_type_list.file_contents_type_code ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ファイルカテゴリコードごとのファイルコンテンツ種別の連想配列を取得する
	 *
	 * @param 
	 * @return 
	 */
	static function get_file_contents_type_master_row_array_array() {
		$file_category_master_row_array = ACSDB::get_master_array('file_category');
		$file_contents_type_master_row_array = ACSDB::get_master_array('file_contents_type');

		$file_contents_type_master_row_array_array = array();
		foreach ($file_category_master_row_array as $file_category_code => $file_category_name) {
			$file_contents_type_list_row_array = ACSFileDetailInfo::get_file_contents_type_list_row_array($file_category_code);

			$file_contents_type_master_row_array_array[$file_category_code] = array();
			$file_contents_type_master_row_array_array[$file_category_code]['file_category_code'] = $file_category_code;
			$file_contents_type_master_row_array_array[$file_category_code]['file_category_name'] = $file_category_name;
			$file_contents_type_master_row_array_array[$file_category_code]['file_contents_row_array'] = array();

			foreach ($file_contents_type_list_row_array as $file_contents_type_list_row) {
				$file_contents_row = array();
				$file_contents_row['file_contents_type_code'] = $file_contents_type_list_row['file_contents_type_code'];
				$file_contents_row['file_contents_type_name'] = $file_contents_type_master_row_array[$file_contents_type_list_row['file_contents_type_code']];
				$file_contents_type_master_row_array_array[$file_category_code]['file_contents_type_master_row_array'][$file_contents_type_list_row['file_contents_type_code']]
					 = $file_contents_row;
			}
		}

		return $file_contents_type_master_row_array_array;
	}



	/**
	 * ファイルコンテンツを取得する
	 *
	 * @param 
	 * @param 
	 * @return 
	 */
	static function get_file_contents_row($file_id, $file_contents_type_code) {
		$file_id = pg_escape_string($file_id);

		$sql  = "SELECT file_contents.file_contents_type_code, file_contents_type_master.file_contents_type_name, file_contents.file_contents_value";
		$sql .= " FROM file_contents, file_contents_type_master";
		$sql .= " WHERE file_contents.file_id = '$file_id'";
		$sql .= "  AND file_contents.file_contents_type_code = '$file_contents_type_code'";
		$sql .= "  AND file_contents.file_contents_type_code = file_contents_type_master.file_contents_type_code";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * ファイルコンテンツを登録する
	 *
	 * @param 
	 * @param 
	 * @return 
	 */
	static function set_file_detail_info($file_id, $file_category_code, $file_contents_form_array) {
		$file_id = pg_escape_string($file_id);
		$file_category_code = pg_escape_string($file_category_code);

		ACSDB::_do_query("BEGIN");

		// DELETE: file_detail_info
		$sql  = "DELETE";
		$sql .= " FROM file_detail_info";
		$sql .= " WHERE file_id = '$file_id'";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// DELETE: file_contents
		$sql  = "DELETE";
		$sql .= " FROM file_contents";
		$sql .= " WHERE file_id = '$file_id'";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// INSERT: detail_file_info
		$sql  = "INSERT INTO file_detail_info";
		$sql .= " (file_id, file_category_code)";
		$sql .= " VALUES ('$file_id', '$file_category_code')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// INSERT: file_contents
		foreach ($file_contents_form_array as $file_contents_form) {
			ACSLib::escape_sql_array($file_contents_form);
			ACSLib::get_sql_value_array($file_contents_form);

			$sql  = "INSERT INTO file_contents";
			$sql .= " (file_id, file_contents_type_code, file_contents_value)";
			$sql .= " VALUES ($file_contents_form[file_id], $file_contents_form[file_contents_type_code], $file_contents_form[file_contents_value])";
			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		}

		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * ファイル公開情報を取得する
	 *
	 * @param file_id ファイルID
	 * @return row(正常)/false(失敗)
	 */
	static function get_file_public_access_row($file_id="", $where="") {
		$file_id = pg_escape_string($file_id);

		$sql  = "SELECT * FROM file_public_access";
	
		$condition = "1=1";
		if($file_id != ""){
			$condition = $condition . " AND file_id = " . $file_id;
		}

		if($where != ""){
			$condition = $condition . " AND " . $where;
		}

		$sql = $sql . " WHERE " . $condition;

		$file_public_access_row = ACSDB::_get_row($sql);

		return $file_public_access_row;
	}

	/**
	 * ファイル公開情報を登録する
	 *
	 * @param file_id ファイルID
	 *        form 設定値
	 * @return true(正常)/false(失敗)
	 */
	static function insert_file_public_access($file_id, $form=false) {
		ACSLib::escape_sql_array($form);
		
		if($form === false){
			$form = array();
		}

		ACSDB::_do_query("BEGIN");

		$ret = ACSDB::_do_query(
					"DELETE FROM file_public_access
					 WHERE file_id = " . $file_id);

		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// ハッシュ文字列作成
		$form['access_code'] = md5(uniqid(rand()) . $file_id);

		$form['update_date'] = 'now';

		if(!$form['all_access_count']){ $form['all_access_count'] = 0; }
		if(!$form['access_count']){ $form['access_count'] = 0; }
		if(!$form['access_start_date']){ $form['access_start_date'] = 'now'; }

		$sql = "INSERT INTO file_public_access 
					(file_id, folder_id, community_id, access_code, 
					 all_access_count, access_count, access_start_date, 
					 update_date)
				VALUES
					(" . $file_id . ", " . 
					 $form['folder_id'] . ", " . 
					 $form['community_id'] . ", '" . 
					 $form['access_code'] . "'," . 
					 $form['all_access_count'] . "," .
					 $form['access_count'] . ", '" .
					 $form['access_start_date'] . "', '" . 
					 $form['update_date'] . "')";

		$ret = ACSDB::_do_query($sql);

		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * ファイル公開情報を更新する
	 *
	 * @param file_id ファイルID
	 *        form 設定値
	 * @return true(正常)/false(失敗)
	 */
	static function update_file_public_access($file_id, $form) {

		$sql = "UPDATE file_public_access SET ";

		$set = "";
		foreach($form as $index => $value){
			if($set != ""){
				$set .= ",";
			}
			$set = $set . $index . " = " . $value;
		}

		$sql .= $set . " WHERE file_id = " . $file_id;

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * ファイル公開情報を削除する
	 *
	 * @param file_id ファイルID
	 * @return true(正常)/false(失敗)
	 */
	static function delete_file_public_access($file_id) {
		$sql = "DELETE FROM file_public_access 
				WHERE file_id = " . $file_id;

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}
}

?>
