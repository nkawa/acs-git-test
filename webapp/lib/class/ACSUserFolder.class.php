<?php
/**
 * ACS User Folder
 *
 * @author  kuwayama
 * @version $Revision: 1.13 $ $Date: 2008/04/24 16:00:00 y-yuki Exp $
 */
require_once(ACS_CLASS_DIR . 'ACSGenericFolder.class.php');

define('_ACSUSERFOLDER_COMMUNITY_TYPE_MASTER', 
		ACSMsg::get_mst('community_type_master','D10'));

class ACSUserFolder extends ACSGenericFolder
{
	/* コミュニティタイプ名 */
	var $community_type_name = _ACSUSERFOLDER_COMMUNITY_TYPE_MASTER;

	/**
	 * コンストラクタ
	 *
	 * @param $user_community_id
	 * @param $acs_user_info_row アクセス者情報
	 * @param $folder_id
	 */
	function ACSUserFolder ($user_community_id, $acs_user_info_row, $folder_id) {
		/* フォルダIDの指定がない場合、ルートフォルダを取得する */
		if ($folder_id == "") {
			$folder_row = $this->get_root_folder_row($user_community_id);

			$folder_id = $folder_row['folder_id'];
		}

		// ユーザフォルダでは、取得対象のフォルダ情報と表示するフォルダは同一
		parent::ACSGenericFolder($user_community_id, $acs_user_info_row, $folder_id, array($folder_id));
	}

	/**
	 * フォルダ検索
	 *
	 * @param $user_community_id
	 * @param $form 検索条件
	 * @return フォルダ情報の配列
	 */
	function search_folder_row_array($user_community_id, $form) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT *";
		$sql .= " FROM folder";
		$sql .= " WHERE folder.community_id = '$user_community_id'";
		$sql .= "  AND folder.parent_folder_id is not null";

		if ($form['q'] != '') {
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
					$sub_where_sql .= " folder.folder_name ILIKE '%$query%'";
					$sub_where_sql .= " OR folder.comment ILIKE '%$query%'";
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

		// ORDER
		if ($form['order'] == 'update_date') {
			$sql .= " ORDER BY folder.update_date DESC";
		} else {
			$sql .= " ORDER BY folder.folder_name ASC";
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ファイル検索
	 *
	 * @param $user_community_id
	 * @param $form 検索条件
	 * @return フォルダ情報の配列
	 */
	function search_file_info_row_array($user_community_id, $form) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT *";
		$sql .= " FROM folder, folder_file, file_info";
		$sql .= " WHERE folder.community_id = '$user_community_id'";
		$sql .= "  AND folder.folder_id = folder_file.folder_id";
		$sql .= "  AND folder_file.file_id = file_info.file_id";
		$sql .= "  AND file_info.owner_community_id  = '$user_community_id'";

		if ($form['q'] != '') {
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
					$sub_where_sql .= " file_info.display_file_name ILIKE '%$query%'";
					$sub_where_sql .= " OR file_info.comment ILIKE '%$query%'";
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

		// ORDER 
		if ($form['order'] == 'update_date') { 
			$sql .= " ORDER BY file_info.update_date DESC";
		} else {
			$sql .= " ORDER BY file_info.display_file_name ASC";
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * フォルダ全体の公開範囲セット
	 * ユーザのフォルダの場合は、一般公開
	 *
	 * @param $community_id
	 */
	function set_contents_folder_open_level ($community_id) {
		$open_level_code = "";
		$open_level_name = "";

		// フォルダ全体の公開範囲 一般公開を取得
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D32'));
		foreach ($open_level_master_row_array as $open_level_master_row) {
			if ($open_level_master_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D01')) {
				$open_level_code = $open_level_master_row['open_level_code'];
				$open_level_name = $open_level_master_row['open_level_name'];

				break;
			}
		}

		$this->open_level_code = $open_level_code;
		$this->open_level_name = $open_level_name;

		// 閲覧許可コミュニティセット
		// 設定なし
		$this->set_contents_folder_trusted_community_row_array(array());
	}

	/**
	 * フォルダにアクセス権があるか
	 *
	 * @param  $target_user_info_row 表示対象マイページ情報
	 * @return true / false
	 */
	function has_privilege ($target_user_info_row) {
		$ret_folder_obj_array = array();

		/* role_array 取得 */
		$role_array = ACSAccessControl::get_user_community_role_array($this->get_acs_user_info_row(), $target_user_info_row);

		$folder_obj = $this->get_folder_obj();
		$ret_folder_obj = ACSAccessControl::get_valid_obj_row_array_for_user_community($this->get_acs_user_info_row(), $role_array, array($folder_obj));

		if ($ret_folder_obj) {
			return true;

		// ない場合は、アクセス不可
		} else {
			return false;
		}
	}

	/**
	 * マイフレンズの新着ファイル情報を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID (ダイアリーへのアクセス者となるユーザ)
	 *        $days 取得する日数(最近何日間の新着情報を取得)
	 * @return 新着フォルダ一覧 (連想配列の配列)
	 */
	function get_new_friends_file_row_array($user_community_id, $days=false, $offset=false) {
		// マイフレンズのユーザコミュニティIDのCSVを作成する
		// マイフレンズの条件csv文字列取得
		$csv_string = ACSUserFolder::get_new_friends_csv_string($user_community_id);

		$row_array = array();

		if ($csv_string!='') {
			// マイフレンズフォルダの新着情報を取得
			$row_array = ACSUserFolder::get_new_folder_row_array(
								$user_community_id, $csv_string, $days, $offset);
		}

		return $row_array;

	}

	/**
	 * マイフレンズのcsv文字列を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID (アクセス者となるユーザ)
	 * @return csv文字列
	 */
	function get_new_friends_csv_string($user_community_id) {

		// マイフレンズのユーザコミュニティIDのCSVを作成する
		// マイフレンズの取得
		$friends_row_array = ACSUser::get_simple_friends_row_array($user_community_id);

		// マイフレンズの条件csv文字列取得
		$csv_string = 
			ACSLib::get_csv_string_from_array($friends_row_array, 'user_community_id');

		return $csv_string;
	}

	/**
	 * マイフレンズの新着フォルダ情報を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID (ダイアリーへのアクセス者となるユーザ)
	 * @return 新着フォルダ一覧 (連想配列の配列)
	 */
	function get_new_friends_folder_row_array($user_community_id) {

		// マイフレンズのユーザコミュニティIDのCSVを作成する
		// マイフレンズの取得
		$friends_row_array = ACSUser::get_simple_friends_row_array($user_community_id);

		// マイフレンズの条件csv文字列取得
		$csv_string = 
			ACSLib::get_csv_string_from_array($friends_row_array, 'user_community_id');

		// マイフレンズフォルダの新着情報を取得
		$row_array = ACSUserFolder::get_new_folder_row_array(
							$user_community_id, $csv_string);

		return $row_array;

	}
}
?>
