<?php
/**
 * ACS Generic Folder (ACS 汎用フォルダ)
 *
 * ユーザのフォルダ、コミュニティのフォルダで継承される
 *
 * @author  kuwayama
 * @version $Revision: 1.21 $ $Date: 2008/04/24 16:00:00 y-yuki Exp $
 */
require_once(ACS_CLASS_DIR . 'ACSFolder.class.php');
require_once(ACS_CLASS_DIR . 'ACSFolderModel.class.php');
class ACSGenericFolder
{
	/* コミュニティID */
	var $community_id;

	/* 公開範囲 */
	var $open_level_code;
	/* 閲覧許可コミュニティリスト */
	var $trusted_community_row_array = array();

	/* フォルダ */
	var $folder_obj;

	/* 表示対象コミュニティの全フォルダリスト */
	var $all_community_folders_obj_array;

	/* 表示対象フォルダのパスフォルダリスト */
	var $path_folder_obj_array;

	/* コミュニティタイプ名 */
	// 継承するクラスで定義
	var $community_type_name = "";

	/* アクセスユーザ情報 */
	var $acs_user_info_row = array();

	/**
	 * コンストラクタ
	 *
	 * アクセスユーザ情報、ユーザフォルダ情報、
	 * 対象フォルダのオブジェクトを取得し、セットする。
	 *
	 * @param $community_id
	 * @param $acs_user_info_row	  アクセスユーザ情報
	 * @param $folder_id
	 * @param $target_folder_id_array フォルダ・ファイルのリスト取得対象となるフォルダID
	 */
	function ACSGenericFolder ($community_id, $acs_user_info_row, $folder_id, $target_folder_id_array) {
		/* コミュニティIDセット */
		$this->set_community_id($community_id);

		/* フォルダ情報セット */
		// フォルダ自身の公開範囲等をセット
		$this->set_folder_info($community_id);

		/* フォルダオブジェクトセット */
		$this->set_folder_obj($community_id, $acs_user_info_row, $folder_id, $target_folder_id_array);

		/* 表示対象コミュニティの全フォルダリストセット */
		$this->set_all_community_folders_obj_array($community_id);

		/* 表示対象フォルダのパスフォルダリストセット */
		$this->set_path_folder_obj_array();

		/* フォルダの公開範囲セット */
		$this->set_folder_obj_open_level();

		/* アクセスユーザ情報セット*/
		$this->set_acs_user_info_row($acs_user_info_row);
	}

	/**
	 * コミュニティIDセット
	 *
	 * @param $community_id
	 */
	function set_community_id ($community_id) {
		$this->community_id = $community_id;
	}

	/**
	 * コミュニティIDゲット
	 *
	 * @param $community_id
	 */
	function get_community_id () {
		return $this->community_id;
	}

	/**
	 * アクセスユーザ情報セット
	 *
	 * @param $acs_user_info_row
	 */
	function set_acs_user_info_row ($acs_user_info_row) {
		$this->acs_user_info_row = $acs_user_info_row;
	}

	/**
	 * アクセスユーザ情報ゲット
	 *
	 * @param $acs_user_info_row
	 */
	function get_acs_user_info_row () {
		return $this->acs_user_info_row;
	}

	/**
	 * フォルダオブジェクトセット
	 *
	 * @param $folder_id
	 */
	function set_folder_obj ($community_id, $acs_user_info_row, $folder_id, $target_folder_id_array) {
		static $cache_rows;

		if (is_array($cache_rows[$folder_id])) {
			$folder_row = $cache_rows[$folder_id];
		} else {
			/* フォルダ情報取得 */
			$folder_row = ACSFolderModel::select_folder_row($folder_id);
			$cache_rows[$folder_id] = $folder_row;
		}

		/* フォルダインスタンスセット */
		$this->folder_obj = new ACSFolder($folder_row, $target_folder_id_array, $acs_user_info_row);
	}

	/**
	 * フォルダオブジェクトゲット
	 *
	 * @param none
	 */
	function get_folder_obj () {
	 	return $this->folder_obj;
	}

	/**
	 * 表示対象コミュニティの全フォルダリストセット
	 *
	 * @param $community_id
	 */
	function set_all_community_folders_obj_array ($community_id) {
	 	$all_community_folders_obj_array = array();
		/* 全フォルダ情報取得 */
		$folder_row_array = ACSFolderModel::select_all_community_folder_row_array($community_id);

		foreach ($folder_row_array as $folder_row) {
			/* 空のインスタンスのみ取得 */
			$folder_obj = new ACSFolder($folder_row, '', '');

			array_push($all_community_folders_obj_array, $folder_obj);
		}

		/* フォルダインスタンスセット */
		$this->all_community_folders_obj_array = $all_community_folders_obj_array;
	}

	/**
	 * 表示対象コミュニティの全フォルダリストゲット
	 *
	 * @param none
	 */
	function get_all_community_folders_obj_array () {
		return $this->all_community_folders_obj_array;
	}

	
	/**
	 * 表示対象フォルダのパスフォルダリストセット
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
	 * 表示対象フォルダのパスフォルダリストゲット
	 */
	function get_path_folder_obj_array () {
		return $this->path_folder_obj_array;
	}

	/**
	 * ルートフォルダ取得
	 *
	 * @param $community_id
	 */
	function get_root_folder_row ($community_id) {
		/* ルートフォルダを取得する */
		$folder_row = ACSFolderModel::select_root_folder_row($community_id);

		/* ない場合、作成する */
		if ($folder_row == "") {
			$ret = ACSFolderModel::insert_root_folder($community_id);
			/* ない場合、エラー */
			if (!$ret) {
				print "ERROR: Create root folder failed.<br>\n";
				exit;
			} else {
				// もう一度取得する
				return $this->get_root_folder_row($community_id);
			}
		}

		return $folder_row;
	}

	/**
	 * フォルダの公開範囲セット
	 *
	 * @param none
	 */
	function set_folder_obj_open_level () {
		$open_level_code = "";
		$open_level_name = "";
		$trusted_community_row_array = array();

		// ルートフォルダの場合、フォルダ全体の公開範囲をセット
		if ($this->folder_obj->get_is_root_folder()) {
			$open_level_code = $this->get_contents_folder_open_level_code();
			$open_level_name = $this->get_contents_folder_open_level_name();
			$trusted_community_row_array = $this->get_contents_folder_trusted_community_row_array();

		} else {
			// 一階層目フォルダの公開範囲をセット
			$folder_obj = $this->get_first_level_folder_obj();
			if ($folder_obj->get_open_level_code() == "") {
				$folder_obj = $this;
			}
			$open_level_code = $folder_obj->get_open_level_code();
			$open_level_name = $folder_obj->get_open_level_name();
			$trusted_community_row_array = $folder_obj->get_trusted_community_row_array();
		}

		$this->folder_obj->set_open_level_code($open_level_code);
		$this->folder_obj->set_open_level_name($open_level_name);
		$this->folder_obj->set_trusted_community_row_array($trusted_community_row_array);
	}

	/**
	 * フォルダ情報セット
	 *
	 * @param $community_id
	 */
	function set_folder_info ($community_id) {
		$this->set_contents_folder_open_level($community_id);
	}

	/**
	 * フォルダ全体の公開範囲セット
	 * 継承するクラスオーバーライドすること
	 *
	 * @param $community_id
	 */
	function set_contents_folder_open_level ($community_id) {
		print "ERROR: not overridden (set_contents_folder_open_level)";
		exit;
	}

	/**
	 * フォルダ全体の公開範囲コードゲット
	 *
	 * @param none
	 */
	function get_contents_folder_open_level_code () {
		return $this->open_level_code;
	}

	/**
	 * フォルダ全体の公開範囲名ゲット
	 *
	 * @param none
	 */
	function get_contents_folder_open_level_name () {
		return $this->open_level_name;
	}

	/**
	 * フォルダ全体の公開範囲 閲覧許可コミュニティセット
	 *
	 * @param $trusted_community_row_array
	 */
	function set_contents_folder_trusted_community_row_array ($trusted_community_row_array) {
		$this->trusted_community_row_array = $trusted_community_row_array;
	}

	/**
	 * フォルダ全体の公開範囲 閲覧許可コミュニティゲット
	 *
	 * @param none
	 */
	function get_contents_folder_trusted_community_row_array () {
		return $this->trusted_community_row_array;
	}

	/**
	 * 一階層目フォルダ取得
	 *
	 * @param  none
	 * @return $folder_obj
	 */
	function get_first_level_folder_obj () {
		$path_folder_obj_array = $this->get_path_folder_obj_array();

		$path_count = count($path_folder_obj_array);
		if ($path_count >= 2) {
			$folder_obj = $path_folder_obj_array[1];
		}

		return $folder_obj;
	}

	/**
	 * コミュニティタイプ名ゲット
	 */
	function get_community_type_name () {
		return $this->community_type_name;
	}

	/**
	 * フォルダ構成ゲット
	 */
	function get_folder_tree () {

		// ルートフォルダ取得
		$root_folder_obj = ACSFolder::get_folder_instance($this->get_root_folder_row($this->get_community_id()));

		// サブフォルダを検索していく
		$root_folder_obj->set_sub_folder_obj_array($this->search_sub_folder_obj_array($root_folder_obj));

		return $root_folder_obj;
	}
	function search_sub_folder_obj_array($parent_folder_obj) {
		$all_community_folders_obj_array = $this->get_all_community_folders_obj_array();
		$sub_folder_obj_array = array();

		// サブフォルダ検索
		foreach ($all_community_folders_obj_array as $folder_obj) {
			if ($folder_obj->get_parent_folder_id() == $parent_folder_obj->get_folder_id()) {
				// さらにサブフォルダを検索（再帰）
				$folder_obj->set_sub_folder_obj_array($this->search_sub_folder_obj_array($folder_obj));
				array_push($sub_folder_obj_array, $folder_obj);
			}
		}

		return $sub_folder_obj_array;
	}

	/**
	 * 指定フォルダ配下のフォルダ構成ゲット
	 */
	function get_lower_folder_tree ($target_folder_obj) {

		// サブフォルダを検索していく
		$target_folder_obj->set_sub_folder_obj_array($this->search_sub_folder_obj_array($target_folder_obj));

		return $target_folder_obj;
	}
	/**
	 * フォルダ削除
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

		// フォルダ削除
		$ret = ACSFolderModel::delete_folder($folder_id_array);

		return $ret;
	}
	function get_lower_folder_obj_array ($target_folder_obj, &$_folder_id_array, $tree_level = 0) {
		// サブフォルダ取得
		$sub_folder_obj_array = $this->search_sub_folder_obj_array($target_folder_obj);

		foreach ($sub_folder_obj_array as $sub_folder_obj) {
			$tree_level++;

			array_push($_folder_id_array, $sub_folder_obj->get_folder_id());

			// さらにサブフォルダを検索（再帰）
			$this->get_lower_folder_obj_array($sub_folder_obj, $_folder_id_array, $tree_level);

			// 1階層上の検索に戻る
			$tree_level--;
		}
	}

	/**
	 * プット機能が使用できるフォルダかどうか
	 *
	 * @param true / false
	 */
	function is_put_available () {
		if ($this->folder_obj->get_is_root_folder()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 公開範囲設定機能が使用できるフォルダかどうか
	 *
	 * @param true / false
	 */
	function is_set_open_level_available () {
		if ($this->folder_obj->get_is_root_folder()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * フォルダにアクセス権があるか
	 * 継承するクラスでオーバーライドすること
	 */
	function has_privilege ($row) {
		return false;
	}


	/**
	 * 再帰的にフォルダIDを取得
	 *
	 */
	static function get_recursive_folder_id_array($folder_id_array) {
		$ret_folder_id_array = $folder_id_array;

		foreach ($folder_id_array as $folder_id) {
			$sub_folder_id_array = ACSGenericFolder::get_sub_folder_id_array($folder_id);
			if (count($sub_folder_id_array)) {
				// サブフォルダがあるとき
				$ret_folder_id_array = array_merge($ret_folder_id_array, ACSGenericFolder::get_recursive_folder_id_array($sub_folder_id_array));
			}
		}

		return $ret_folder_id_array;
	}

	/**
	 * サブフォルダのIDを取得する
	 *
	 */
	static function get_sub_folder_id_array($folder_id) {
		$folder_id = pg_escape_string($folder_id);

		$sql  = "SELECT folder_id";
		$sql .= " FROM folder";
		$sql .= " WHERE folder.parent_folder_id = '$folder_id'";
		$row_array = ACSDB::_get_row_array($sql);
		$folder_id_array = array();
		foreach ($row_array as $row) {
			array_push($folder_id_array, $row['folder_id']);
		} 

		return $folder_id_array;
	}

	/**
	 * $user_community_idの新着フォルダ情報を取得する
	 * ($commyunity_row_arrayに含まれるcommunity_idが所有するファイルを対象とする)
	 *
	 * @param $user_community_id ユーザコミュニティID (ダイアリーへのアクセス者となるユーザ)
	 * @param $csv_string 所有者条件csv文字列
	 * @return 新着フォルダ一覧 (連想配列の配列)
	 */
	function get_new_folder_row_array($user_community_id, $csv_string, $days=false, $offset=false) {
		
		$user_community_id = pg_escape_string($user_community_id);

		// フォルダの新着記事を最新順に取得する
		$sql = "SELECT *, acs_is_unread_file(" . 
							$user_community_id . ", fi.file_id) as is_unread " . 
				" FROM folder_file AS ff, folder AS fo, " .
				"	  file_info AS fi, community AS cm" .
				//" FROM   folder AS fo, folder_file AS ff, " . 
				//"		file_info AS fi, community AS cm" .
				" WHERE fi.file_id = ff.file_id" .
				"  AND ff.folder_id = fo.folder_id" .
				"  AND fi.owner_community_id = cm.community_id" .
				"  AND fi.owner_community_id IN (" . $csv_string . ")";

		//------ 2007.2 表示時間短縮対応
		// 日数指定がある場合
		if($days !== false){
			$sql = $sql . " AND " . ACSLib::get_sql_condition_from_today("fi.update_date", $days);
		}

		//$sql = $sql . " ORDER BY fi.update_date DESC";
		if($offset != false){
			// 表示件数制御 //
			$display_count = 
					ACSSystemConfig::get_keyword_value(ACSMsg::get_mst(
							'system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
			$sql = $sql . " AND open_level_code is not null";
			$sql = $sql . " ORDER BY fi.update_date DESC";
			$sql = $sql . " OFFSET 0 LIMIT ". $display_count;
		} else {
			$sql = $sql . " ORDER BY fi.update_date DESC";
		}
		$row_array = ACSDB::_get_row_array($sql);

		return $row_array;

	}

}
?>
