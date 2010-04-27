<?php
/**
 * ACS Community Folder
 *
 * @author  kuwayama
 * @version $Revision: 1.14 $ $Date: 2007/03/01 09:01:12 y-yuki Exp $
 */
require_once(ACS_CLASS_DIR . 'ACSGenericFolder.class.php');
require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');

define('_ACSCOMMUNITYFOLDER_COMMUNITY_TYPE_MASTER',
		ACSMsg::get_mst('community_type_master','D40'));

class ACSCommunityFolder extends ACSGenericFolder
{
	/* コミュニティタイプ名 */
	var $community_type_name = _ACSCOMMUNITYFOLDER_COMMUNITY_TYPE_MASTER;

	/**
	 * コンストラクタ
	 *
	 * @param $community_id
	 * @param $acs_user_info_row アクセス者情報
	 * @param $folder_id
	 */
	function ACSCommunityFolder ($community_id, $acs_user_info_row, $folder_id) {
		/* フォルダIDの指定がない場合、ルートフォルダを取得する */
		if ($folder_id == "") {
			$folder_row = $this->get_root_folder_row($community_id);

			$folder_id = $folder_row['folder_id'];
		}

		// プットフォルダ取得
		$put_folder_id_array = ACSFolderModel::select_put_folder($community_id, $folder_id);

		// 表示対象となるフォルダの配列を作成（表示するコミュニティのフォルダ + プットされているフォルダ）
		$target_folder_array = array();
		$target_folder_array[] = $folder_id;
		$target_folder_array = array_merge($target_folder_array, $put_folder_id_array);

		parent::ACSGenericFolder($community_id, $acs_user_info_row, $folder_id, $target_folder_array);
	}

	/**
	 * 表示対象コミュニティの全フォルダリストセット
	 *
	 * @param $community_id
	 */
	function set_all_community_folders_obj_array ($community_id) {
		parent::set_all_community_folders_obj_array($community_id);

		// パス順に取得
		$target_folder_obj = $this->get_folder_obj();

		// プットフォルダの場合
		//    プットされているフォルダまで検索
		if ($target_folder_obj->is_put_folder($this->get_community_id())) {
			$add_folder_obj_array = array();    // プットされているフォルダのユーザフォルダ内のパスを格納
			// プットされているフォルダのユーザフォルダを取得
			$put_folder_obj = new ACSUserFolder($target_folder_obj->get_community_id(),
			                                    $target_folder_obj->get_acs_user_info_row(),
			                                    $target_folder_obj->get_folder_id());

			// プットされているフォルダのユーザフォルダ内のパスを取得
			//   １階層目のフォルダしかプットできないため、１階層目のフォルダ以降のパスを取得
			$add_folder_obj_array = array_slice($put_folder_obj->get_path_folder_obj_array(), 1);
		}
		//_debug($add_folder_obj_array);
		//_debug($this->all_community_folders_obj_array);
		if ($this->all_community_folders_obj_array == NULL || $add_folder_obj_array == NULL) {
			return;
		}
		$this->all_community_folders_obj_array = array_merge($this->all_community_folders_obj_array, $add_folder_obj_array);
		//_debug($this->all_community_folders_obj_array);
	}

	/**
	 * 表示対象フォルダのパスフォルダリストセット
	 *    プットされているフォルダに対応
	 */
	function set_path_folder_obj_array () {
		// フォルダパス情報
		$all_community_folders_obj_array  = $this->get_all_community_folders_obj_array();
		$display_all_folders_obj_array = array();

		// パス順に取得
		$target_folder_obj = $this->get_folder_obj();

		$search_parent_folder_id = $target_folder_obj->get_parent_folder_id();
		$is_root_folder = $target_folder_obj->get_is_root_folder();
		if ($is_root_folder) {
			// ルートディレクトリの場合は、パスにルートディレクトリを追加する
			array_push($display_all_folders_obj_array, $target_folder_obj);
		}

		while (!$is_root_folder) {
			// 親フォルダを検索
			foreach ($all_community_folders_obj_array as $all_community_folders_obj) {
				$target_folder_id = $all_community_folders_obj->get_folder_id();

				if ($search_parent_folder_id == $target_folder_id) {
					$search_parent_folder_id = $all_community_folders_obj->get_parent_folder_id();
					// プットされているフォルダの場合、検索するフォルダをプット先フォルダに変更する
					if ($all_community_folders_obj->has_put_community()) {
						$search_parent_folder_id = $all_community_folders_obj->get_put_community_folder_id($this->get_community_id());
						break;
					}

					array_unshift($display_all_folders_obj_array, $all_community_folders_obj);
					break;
				}
			}
			$is_root_folder = $all_community_folders_obj->get_is_root_folder();
			if ($is_root_folder) {
				// 最後に、現在のフォルダを追加
				array_push($display_all_folders_obj_array, $target_folder_obj);
			}
		}

		$this->path_folder_obj_array = $display_all_folders_obj_array;
	}

	/**
	 * フォルダ削除
	 * プットフォルダを解除する機能を追加
	 *
	 * @param $target_folder_obj  削除対象のフォルダ
	 */
	function delete_folder ($target_folder_obj) {
		// 配下のフォルダ構成取得
		$target_folder_obj = $this->get_lower_folder_tree($target_folder_obj);

		// 配下の全フォルダID取得
		$folder_id_array = array();  // 配下のフォルダ全てがセットされる
		$this->get_lower_folder_obj_array($target_folder_obj, $folder_id_array);

		// 対象となるフォルダも追加
		array_push($folder_id_array, $target_folder_obj->get_folder_id());

		// プット解除
		// 配下のフォルダのプットフォルダも削除する
		$put_community_id = $target_folder_obj->get_community_id();
		foreach ($folder_id_array as $folder_id) {
			$put_community_folder_id = $folder_id;
			$ret = ACSFolderModel::delete_all_put_community($put_community_id, $put_community_folder_id);
			if (!$ret) {
				return $ret;
			}
		}

		// 対象のフォルダ削除
		return parent::delete_folder($target_folder_obj);
	}

	/**
	 * フォルダ検索
	 *
	 * @param $community_id コミュニティID
	 * @param $form 検索条件
	 * @return フォルダ情報の配列
	 */
	function search_folder_row_array($community_id, $form) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT *";
		$sql .= " FROM folder";
		$sql .= " WHERE folder.community_id = '$community_id'";
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
	 * プットフォルダ検索
	 *
	 * @param $community_id コミュニティID
	 * @param $form 検索条件
	 * @return フォルダ情報の配列
	 */
	function search_put_folder_row_array($community_id, $form) {
		$community_id = pg_escape_string($community_id);

		// 1. コミュニティにプットされてるユーザフォルダのfolder_idを全て取得する
		$sql  = "SELECT folder_id";
		$sql .= " FROM put_community";
		$sql .= " WHERE put_community.put_community_id = '$community_id'";
		$row_array = ACSDB::_get_row_array($sql);
		$folder_id_array = array();
		foreach ($row_array as $row) {
			array_push($folder_id_array, $row['folder_id']);
		}
		if (count($folder_id_array) == 0) {
			// 0件
			return array();
		}

		// 2. コミュニティフォルダからアクセス可能な中身のフォルダのfolder_idを全て取得する
		$user_folder_id_array = ACSGenericFolder::get_recursive_folder_id_array($folder_id_array);
		foreach ($folder_id_array as $folder_id) {
			// プットしたフォルダ自体は検索対象にならない
			$key = array_search($folder_id, $user_folder_id_array);
			if (!($key === false)) {
				unset($user_folder_id_array[$key]);
			}
		}
		//sort($user_folder_id_array);
		if (count($user_folder_id_array) == 0) {
			// 0件
			return array();
		}

		// 3. 検索対象となる、ユーザがプットしたフォルダ以下のフォルダID CSV
		$user_folder_id_array_csv = implode(',', $user_folder_id_array);


		$sql  = "SELECT *";
		$sql .= " FROM folder";
		$sql .= " WHERE folder.folder_id IN ($user_folder_id_array_csv)";
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
	 * @param $community_id コミュニティID
	 * @param $form 検索条件
	 * @return フォルダ情報の配列
	 */
	function search_file_info_row_array($community_id, $form) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT *";
		$sql .= " FROM folder, folder_file, file_info";
		$sql .= " WHERE folder.community_id = '$community_id'";
		$sql .= "  AND folder.folder_id = folder_file.folder_id";
		$sql .= "  AND folder_file.file_id = file_info.file_id";
		$sql .= "  AND file_info.owner_community_id  = '$community_id'";

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
	 * 指定コミュニティプットファイル検索
	 *
	 * @param $community_id コミュニティID
	 * @param $form 検索条件
	 * @return フォルダ情報の配列
	 */
	function search_put_file_info_row_array($community_id, $form) {
		$community_id = pg_escape_string($community_id);

		return ACSCommunityFolder::search_all_put_file_info_row_array(
						$form, "put_community.put_community_id = '$community_id'");
	}

	/**
	 * 全コミュニティプットファイル検索
	 *
	 * @param $community_where putコミュニティ検索条件
	 * @param $unread_check_user_community_id unreadチェック実施時のユーザコミュニティid
	 * @param $form 検索条件
	 * @return フォルダ情報の配列
	 */
	function search_all_put_file_info_row_array(
			$form, $community_where = "", $unread_check_user_community_id = "", $days=false) {

		// 1. コミュニティにプットされてるユーザフォルダのfolder_idを全て取得する
		$sql  = "SELECT folder_id";
		$sql .= " FROM put_community";
		if($community_where != ""){
			$sql .= " WHERE " . $community_where;
		}

		$row_array = ACSDB::_get_row_array($sql);
		$folder_id_array = array();
		foreach ($row_array as $row) {
			array_push($folder_id_array, $row['folder_id']);
		}
		if (count($folder_id_array) == 0) {
			// 0件
			return array();
		}

		// 2. コミュニティフォルダからアクセス可能な中身のフォルダのfolder_idを全て取得する
		$user_folder_id_array = ACSGenericFolder::get_recursive_folder_id_array($folder_id_array);
		if (count($user_folder_id_array) == 0) {
			// 0件
			return array();
		}

		// 3. 検索対象となる、ユーザがプットしたフォルダ以下のフォルダID CSV
		$user_folder_id_array_csv = implode(',', $user_folder_id_array);


		$sql  = "SELECT * ";

		if ($unread_check_user_community_id != '') {
			$sql .= ",acs_is_unread_file(" . 
					$unread_check_user_community_id . ",file_info.file_id) as is_unread ";
		}

		$sql .= " FROM folder LEFT OUTER JOIN put_community ON folder.folder_id = put_community.folder_id, folder_file, file_info";
		$sql .= " WHERE folder.folder_id IN ($user_folder_id_array_csv)";
		$sql .= "  AND folder.folder_id = folder_file.folder_id";
		$sql .= "  AND folder_file.file_id = file_info.file_id";

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

		// 日数指定がある場合
		if($days !== false){
			$sql = $sql . " AND " . 
					ACSLib::get_sql_condition_from_today("file_info.update_date", $days);
		}

		// ORDER
		if($rows != false){
			// 表示件数制御 //
			$display_count = 
					ACSSystemConfig::get_keyword_value(ACSMsg::get_mst(
							'system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
			$sql = $sql . " OFFSET 0 LIMIT ". $display_count;
		} else {
			if ($form['order'] == 'update_date') { 
				$sql .= " ORDER BY file_info.update_date DESC";
			} else {
				$sql .= " ORDER BY file_info.display_file_name ASC";
			}
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * フォルダ全体の公開範囲セット
	 *
	 * @param $community_id
	 */
	function set_contents_folder_open_level ($community_id) {
		// フォルダ全体の公開範囲取得
		$open_level_row = ACSFolderModel::select_contents_folder_open_level_row($community_id);
		if (!$open_level_row) {
			$community_type_name = $this->get_community_type_name();
			$open_level_row = ACSFolderModel::select_folder_open_level_default_row ($community_type_name);
		}

		$this->open_level_code = $open_level_row['open_level_code'];
		$this->open_level_name = $open_level_row['open_level_name'];

		// 閲覧許可コミュニティセット
		$this->set_contents_folder_trusted_community_row_array($open_level_row['trusted_community_row_array']);
	}

	/**
	 * フォルダにアクセス権があるか
	 *
	 * @param  $target_community_row 表示対象コミュニティ情報
	 * @return true / false
	 */
	function has_privilege ($target_community_row) {
		$ret_folder_obj_array = array();

		/* role_array 取得 */
		$role_array = ACSAccessControl::get_community_role_array($this->get_acs_user_info_row(), $target_community_row);

		$folder_obj = $this->get_folder_obj();
		$ret_folder_obj = ACSAccessControl::get_valid_obj_row_array_for_community($this->get_acs_user_info_row(), $role_array, array($folder_obj));

		if ($ret_folder_obj) {
			return true;

		// ない場合は、アクセス不可
		} else {
			return false;
		}
	}

	/**
	 * マイコミュニティの新着フォルダ情報を取得する
	 *
     * @param $user_community_id ユーザコミュニティID (ダイアリーへのアクセス者となるユーザ)
     *        $days 取得する日数(最近何日間の新着情報を取得)
     * @return 新着フォルダ一覧 (連想配列の配列)
	 */
	function get_new_community_folder_row_array($user_community_id, $days=false, $offset=false) {

		// マイコミュニティのコミュニティIDのCSVを作成する
		// マイコミュニティの取得
		$community_row_array = ACSUser::get_community_row_array($user_community_id);

		// マイコミュニティの条件csv文字列取得
		$csv_string = 
			ACSLib::get_csv_string_from_array($community_row_array, 'community_id');

		$row_array = array();

		if ($csv_string!='') {
			// マイコミュニティフォルダの新着情報を取得
			$row_array = ACSCommunityFolder::get_new_folder_row_array(
					$user_community_id, $csv_string, $days, $rows);
		}
		return $row_array;
	}

	/**
	 * マイコミュニティの新着プットフォルダ情報を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID (アクセス者となるユーザ)
	 *        $days 取得する日数(最近何日間の新着情報を取得)
	 * @return 新着フォルダ一覧 (連想配列の配列)
	 */
	function get_new_community_put_folder_row_array($user_community_id, &$form, $days=false, $offset=false) {

		// マイコミュニティのコミュニティIDのCSVを作成する
		// マイコミュニティの取得
		$community_row_array = ACSUser::get_community_row_array($user_community_id);
		// マイコミュニティの条件csv文字列取得
		$csv_string = 
			ACSLib::get_csv_string_from_array($community_row_array, 'community_id');

		$row_array = array();

		if ($csv_string!='') {
			$condition = "put_community.put_community_id IN (" . $csv_string . ")";

			// マイコミュニティプットフォルダの新着情報を取得
			$row_array = ACSCommunityFolder::search_all_put_file_info_row_array(
					$form, $condition, $user_community_id, $days, $rows);
		}
		return $row_array;
	}
}
?>
