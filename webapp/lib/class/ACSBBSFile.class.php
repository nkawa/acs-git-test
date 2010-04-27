<?php
/**
 * bbs用ファイル情報 DBアクセス
 * bbs_fileとfile_infoの結合ファイル情報
 * ACSBBSFile.class.php
 *
 * bbs_fileテーブルをManageする
 * @author  akitsu
 * @version $Revision: 1.5 $
 */
class ACSBBSFile
{
	/** ACSBBSFileのクラス
	* @type ACSBBSFileModel */
	var $bbs_file_obj;

	/** ACSFileのクラス
	* @type ACSFileModel */
	var $file_info;

	/**
	 * BBSファイル情報取得（１件）
	 * ひとつのBBS（掲示板）が持つFile情報
	 *
	 * @param $bbs_id
	 * @return bbs_fileとfile_infoの結合ファイル情報
	 */
	static function select_bbs_file_row ($bbs_id) {
		$sql  = "SELECT bbs_file.*,";
		$sql .=	   " file_info.display_file_name AS save_file_name,file_info.server_file_name AS insystem_file_name,";
		$sql .=	   " file_info.mime_type AS file_kind";	//種類
		$sql .= " FROM file_info, bbs_file";
		$sql .= " WHERE bbs_id = " . $bbs_id;
		$sql .=   " AND file_info.file_id = bbs_file.file_id";
		$sql .= " ORDER BY bbs_file.file_id ASC";

		$result = ACSDB::_get_row($sql);
		return $result;
	}

	/**
	 * BBSファイル情報取得
	 *
	 * @param $file_obj 
	 */
	static function get_bbs_file ($file_obj,$bbs_id) {
		$sql  = "SELECT bbs_file.*,";
		$sql .= " FROM bbs_file";

		$ret = ACSDB::_do_query($sql);
		if($ret){
			$bbs_file_obj = ACSBBSFileModel::get_bbs_file_info_instance($file_obj,$bbs_id);
			return $bbs_file_obj;
		}else{
			return $ret;
		}
	}


	/**
	 * BBSファイル情報追加
	 *
	 * @param $file_obj 
	 */
	static function insert_bbs_file ($file_obj,$bbs_id) {
		$id = $file_obj->get_file_id();
		$sql  = "INSERT INTO bbs_file";
		$sql .= " (file_id, bbs_id)";
		$sql .= " VALUES (";
		$sql .=		   "" . $id . ",";
		$sql .=		   "" . $bbs_id;
		$sql .=  ")";

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * BBSファイル情報更新 (要BEGIN, COMMIT)
	 *
	 * @param $file_obj 
	 */
	static function update_bbs_file ($file_obj, $bbs_id) {
		$file_id = pg_escape_string($file_obj->get_file_id());
		$bbs_id = pg_escape_string($bbs_id);

		// 削除
		$sql  = "DELETE";
		$sql .= " FROM bbs_file";
		$sql .= " WHERE bbs_id = '$bbs_id'";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// INSERT
		$sql  = "INSERT INTO bbs_file";
		$sql .= " (file_id, bbs_id)";
		$sql .= " VALUES ('$file_id', '$bbs_id')";

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		return $ret;
	}

	/**
	 * BBSファイル情報削除
	 * 
	* @param $file_id
	* @param $bbs_id
	*/
	static function delete_bbs_file ($file_id,$bbs_id) {
		$sql  = "DELETE FROM bbs_file";
		$sql .= " WHERE";
		$sql .=		 " file_id = " . $file_id;
		$sql .= " AND";
		$sql .=		" bbs_id = " . $bbs_id;

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
	
	
	/**
	 * image_urlを加工する
	 *
	 * @param bbs_id
	 * @param view_mode         表示モード : NULL, thumb, rss
	 */
	static function get_image_url($bbs_id, $view_mode = '') {
		$image_url  = SCRIPT_PATH . '?';
		$image_url .= MODULE_ACCESSOR . '=Community';
		$image_url .= '&' . ACTION_ACCESSOR . '=BBSImage';
		$image_url .= '&id=' . $bbs_id;
		$image_url .= '&mode=' . $view_mode;

		return $image_url;
	}

}
