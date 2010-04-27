<?php
/**
 * 画像ファイル情報 DBアクセス
 * ACSCommunityImageFileModel.class.php
 *
 * community_image_fileテーブル
 * @author  akitsu
 * @version $Revision: 1.5 $Date: 2008/03/24 07:00:36 $
 */
class ACSCommunityImageFileModel
{
	/* ファイルID */
	var $file_id;

	/* コミュニティID */
	var $community_id;
		
	/**
	 * 画像ファイル情報取得（複数）
	 * ひとりのユーザが持つ画像情報のすべて
	 * @param $file_id_array
	 */
	static function select_community_image_row_array ($community_id) {
		$sql  = "SELECT community_image_file.*,";
		$sql .=	   " file_info.display_file_name AS save_file_name,file_info.server_file_name AS insystem_file_name,";
		$sql .=	   " file_info.mime_type AS file_kind";	//種類
		$sql .= " FROM file_info, community_image_file";
		$sql .= " WHERE community_id = " . $community_id . ")";
		$sql .=   " AND file_info.owner_community_id = community_image_file.community_id";
		$sql .= " ORDER BY community_image_file.file_id ASC";
		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 画像ファイル情報取得（１件）
	 *
	 * @param $file_id
	 */
	static function select_community_image_row ($community_id) {
		$community_id_array = array($community_id);
		$row_array = ACSCommunityImageModel::select_community_image_row_array($file_id_array);

		return $row_array[0];
	}

	/**
	 * コミュニティ画像ファイルIDを取得する
	 *
	 * @param $community_id コミュニティID
	 * @return file_id
	 */
	static function get_file_id($community_id) {
		if (!$community_id) {
			return;
		}
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT file_id";
		$sql .= " FROM community_image_file";
		$sql .= " WHERE community_image_file.community_id = '$community_id'";

		$value = ACSDB::_get_value($sql);
		return $value;
	}

	/**
	 * 画像ファイル情報追加
	 *
	 * @param $file_obj 
	 */
	static function insert_community_image ($file_obj) {
		$sql  = "INSERT INTO community_image_file";
		$sql .= " (file_id, community_id)";
		$sql .= " VALUES (";
		$sql .=		   "" . $file_obj->get_file_id() . ",";
		$sql .=		   "" . $file_obj->get_owner_community_id();
		$sql .=  ")";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
	/**
	 * 画像ファイル情報削除
	 *
	* @param $file_obj
	*/
	static function delete_community_image ($file_obj) {
		$sql  = "DELETE FROM community_image_file";
		$sql .= " WHERE";
		$sql .=		 " file_id = " . $file_obj->get_file_id();
		$sql .= " AND";
		$sql .=		" community_id = " . $file_obj->get_owner_community_id();

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * コミュニティ画像ファイルIDを取得する（公開範囲選択有り）
	 *
	 * @param $community_id コミュニティID
	 * @return 各公開範囲毎のファイルID
	 */
	static function get_file_id_with_open_level($community_id) {
		if (!$community_id) {
			return;
		}
		$sql  = "SELECT file_id, file_id_ol01, file_id_ol02, file_id_ol05 ";
		$sql .= " FROM community_image_file";
		$sql .= " WHERE community_image_file.community_id = '$community_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * 画像ファイル情報の論理削除（公開範囲選択有り）
	 *
	 * @param $file_obj 画像ファイルのオブジェクト
	 * @param $open_level_code 公開レベルコード
	 * @return 成功(true) / 失敗(false)
	*/
	static function delete_community_image_with_open_level ($file_obj, $open_level_code) {

		$row = ACSCommunityImageFileModel::get_file_id_with_open_level(
				$file_obj->get_owner_community_id());
				
		// 公開範囲に絡むファイルIDはNULLとする
		$sql  = "UPDATE community_image_file";
		$sql .= " SET";
		// 友人向けに対して削除の場合
		if ($open_level_code == "05" ) {
			if ($row['file_id_ol02'] != NULL) {
				// ログインユーザ向けのファイルIDがあれば、file_idはログインユーザ向けにする
				$sql .= " file_id = " . $row['file_id_ol02'] . ",";
			} else if ($row['file_id_ol01'] != NULL) {
				// 一般向けのファイルIDがあれば、file_idは一般向けにする
				$sql .= " file_id = " . $row['file_id_ol01'] . ",";
			}
		}
		
		// ログインユーザ向けに対して削除の場合 
		if ($open_level_code == "02" ) {
			if ($row['file_id_ol05'] != NULL) {
				// 友人向けのファイルIDがあれば、file_idは友人向けにする
				$sql .= " file_id = " . $row['file_id_ol05'] . ",";
			} else if ($row['file_id_ol01'] != NULL) {
				// 一般向けのファイルIDがあれば、file_idは一般向けにする
				$sql .= " file_id = " . $row['file_id_ol01'] . ",";
			}
		}

		// 一般向けに対して削除の場合
		if ($open_level_code == "01" ) {
			if ($row['file_id_ol05'] != NULL) {
				// 友人向けのファイルIDがあれば、file_idは友人向けにする
				$sql .= " file_id = " . $row['file_id_ol05'] . ",";
			} else if ($row['file_id_ol02'] != NULL) {
				// ログインユーザ向けのファイルIDがあれば、file_idはログインユーザ向けにする
				$sql .= " file_id = " . $row['file_id_ol02'] . ",";
			}
		}
		$sql .=		" file_id_ol" . $open_level_code . " = NULL ";
		$sql .= " WHERE";
		$sql .=		" community_id = " . $file_obj->get_owner_community_id();
		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * 画像ファイル情報追加（公開範囲選択有り）
	 *
	 * @param $file_obj ファイル情報
	 * @param $open_level_code 公開レベルコード
	 * @return 成功(true) / 失敗(false)
	 */
	static function put_community_image_with_open_level ($file_obj, $open_level_code) {
		
		// レコードが存在するか？
		$row = ACSCommunityImageFileModel::get_file_id_with_open_level($file_obj->get_owner_community_id());

		if (!$row) {
			$ret = ACSCommunityImageFileModel::insert_community_image_with_open_level($file_obj, $open_level_code);
			
		} else {
			// 存在する場合→アップデート
			$ret = ACSCommunityImageFileModel::update_community_image_with_open_level($file_obj, $open_level_code, $row);
		}
		return $ret;
	}

	/**
	 * 画像ファイル情報INSERT（公開範囲選択有り）
	 *
	 * @param $file_obj ファイル情報
	 * @param $open_level_code 公開レベルコード
	 * @return 成功(true) / 失敗(false)
	 */
	static function insert_community_image_with_open_level ($file_obj, $open_level_code) {
		
		$sql  = "INSERT INTO community_image_file";
		$sql .= " (";
		$sql .= 	" file_id, ";
		if ($open_level_code == "01" ) {
			$sql .= 	" file_id_ol01,";
			$sql .= 	" file_id_ol02,";
			$sql .= 	" file_id_ol05,";
		} else if ($open_level_code == "02" ) {
			$sql .= 	" file_id_ol02,";
			$sql .= 	" file_id_ol05,";
		} else if ($open_level_code == "05" ) {
			$sql .= 	" file_id_ol05,";
		}
		$sql .= 	" community_id";
		$sql .= " )";
		$sql .= " VALUES (";
		$sql .=		   "" . $file_obj->get_file_id() . ",";
		if ($open_level_code == "01" ) {
			$sql .=		   "" . $file_obj->get_file_id() . ",";
			$sql .=		   "" . $file_obj->get_file_id() . ",";
			$sql .=		   "" . $file_obj->get_file_id() . ",";
		} else if ($open_level_code == "02" ) {
			$sql .=		   "" . $file_obj->get_file_id() . ",";
			$sql .=		   "" . $file_obj->get_file_id() . ",";
		} else if ($open_level_code == "05" ) {
			$sql .=		   "" . $file_obj->get_file_id() . ",";
		}
		$sql .=		   "" . $file_obj->get_owner_community_id();
		$sql .=  ")";
		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
	

	/**
	 * 画像ファイル情報UPDATE（公開範囲選択有り）
	 *
	 * @param $file_obj ファイル情報
	 * @param $open_level_code 公開レベルコード
	 * @param $file_id_upd_flg = NULL ファイル情報更新フラグ
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_community_image_with_open_level ($file_obj, $open_level_code, $row = NULL) {
		
		$sql  = "UPDATE community_image_file";
		$sql .= " SET ";
		if ($row != NULL) {
			$sql .= 	" file_id = " . $file_obj->get_file_id() . ",";

			// 一般向けに対して更新する場合
			if ($open_level_code == "01" ) {
				if ($row['file_id_ol05'] == NULL) {
					$sql .= " file_id_ol05 = " . $file_obj->get_file_id(). ",";
				}
				if ($row['file_id_ol02'] == NULL) {
					$sql .= " file_id_ol02 = " . $file_obj->get_file_id(). ",";
				}
			}
			// ログインユーザ向けに対して更新する場合
			if ($open_level_code == "02" ) {
				if ($row['file_id_ol05'] == NULL) {
					$sql .= " file_id_ol05 = " . $file_obj->get_file_id(). ",";
				}
			}
		}
		$sql .= 	" file_id_ol" . $open_level_code . " = " . $file_obj->get_file_id();
		$sql .= " WHERE ";
		$sql .=		" community_id = " . $file_obj->get_owner_community_id() ;
		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
	

	/**
	 * 該当の公開範囲のファイルIDを取得する
	 *
	 * @param $community_id コミュニティID
	 * @param $open_level_code 公開範囲
	 * @return 該当の公開範囲のファイルID
	 */
	static function get_file_id_for_open_level($community_id, $open_level_code) {
		if (!$community_id) {
			return null;
		}
		if (!$open_level_code) {
			return null;
		}
		$row = ACSCommunityImageFileModel::get_file_id_with_open_level($community_id);
		$match_file_id = $row['file_id_ol' . $open_level_code];
		if (!$match_file_id) {
			return NULL;
		}
		$match_count = 0;
		if ($match_file_id == $row['file_id_ol05']) {
			$match_count++;
		}
		if ($match_file_id == $row['file_id_ol02']) {
			$match_count++;
		}
		if ($match_file_id == $row['file_id_ol01']) {
			$match_count++;
		}
		
		if ($match_count == 1) {
			return $match_file_id;
		}
		return NULL;
	}

}
