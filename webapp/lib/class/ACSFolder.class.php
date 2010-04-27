<?php
/**
 * ACS Folder
 *
 * @author  kuwayama
 * @version $Revision: 1.25 $ $Date: 2006/12/08 05:06:29 $
 */
require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
class ACSFolder
{
	/* フォルダID */
	var $folder_id;

	/* コミュニティID */
	var $community_id;

	/* フォルダ名 */
	var $folder_name;

	/* コメント */
	var $comment;

	/* 親フォルダID */
	var $parent_folder_id;

	/* 公開範囲コード */
	var $open_level_code;

	/* 公開範囲名 */
	var $open_level_name;

	/* 登録ユーザコミュニティID */
	var $entry_user_community_id;

	/* 登録ユーザコミュニティ名 */
	var $entry_user_community_name;

	/* 登録日 */
	var $entry_date;

	/* 更新ユーザコミュニティID */
	var $update_user_community_id;

	/* 更新ユーザコミュニティ名 */
	var $update_user_community_name;

	/* 更新日 */
	var $update_date;

	/* 閲覧許可コミュニティ */
	var $trusted_community_row_array = array();

	/* プット先コミュニティ */
	var $put_community_row_array = array();

	/* ルートフォルダフラグ */
	var $is_root_folder = false;

	/* フォルダリスト */
	var $folder_obj_array = array();

	/* ファイルリスト */
	var $file_obj_array = array();

	/* アクセスユーザ情報 */
	var $acs_user_info_row;

	/* サブフォルダ */
	var $sub_folder_obj_array = array();

	/**
	 * コンストラクタ
	 *
	 * @param $folder_row
	 * @param $target_folder_id_array フォルダ・ファイルのリスト取得対象となるフォルダID
	 */
	function ACSFolder ($folder_row, $target_folder_id_array, $acs_user_info_row) {
		/* フォルダ情報セット */
		$this->set_folder_info($folder_row);

		if (!is_array($target_folder_id_array)) {
			if ($target_folder_id_array) {
				$target_folder_id_array = array($target_folder_id_array);
			} else {
				// ない場合、インスタンスのみ返す
				return $this;
			}
		}

		/* フォルダリスト、ファイルリストセット */
		$this->set_folder_obj_array($target_folder_id_array);
		$this->set_file_obj_array($target_folder_id_array);

		/* アクセスユーザ情報セット */
		if (!$acs_user_info_row == "") {
			$this->set_acs_user_info_row($acs_user_info_row);
		}
	}

	/**
	 * フォルダインスタンス作成
	 *
	 * フォルダリスト、ファイルリストなしのフォルダ情報を保持する
	 * フォルダインスタンスを返す
	 *
	 * @param  $folder_row
	 * @return $folder_obj
	 */
	function get_folder_instance (&$folder_row) {
		/* 空のインスタンスのみ取得 */
		$folder_obj = new ACSFolder($folder_row, '', '');

		return $folder_obj;
	}

	/**
	 * フォルダリストセット
	 *
	 * @param $parent_folder_id_array
	 */
	function set_folder_obj_array (&$parent_folder_id_array) {
		$folder_obj_array = array();

		/* サブフォルダ情報取得 */
		$folder_row_array = ACSFolderModel::select_sub_folder_row_array($parent_folder_id_array);

		/* フォルダ情報セット */
		foreach ($folder_row_array as $folder_row) {
			$folder_id_array = array();

			$folder_obj = ACSFolder::get_folder_instance($folder_row);
			array_push($folder_obj_array, $folder_obj);
		}
		$this->folder_obj_array = $folder_obj_array;
	}

	/**
	 * フォルダリストゲット
	 *
	 * @param none
	 */
	function get_folder_obj_array () {
		return $this->folder_obj_array;
	}

	/**
	 * 指定フォルダIDのフォルダゲット
	 *
	 * @param  $search_folder_id
	 * @return $folder_obj
	 */
	function get_folder_obj ($search_folder_id) {
		$folder_obj_array = $this->get_folder_obj_array();
		foreach ($folder_obj_array as $folder_obj) {
			if ($folder_obj->get_folder_id() == $search_folder_id) {
				$ret_folder_obj = $folder_obj;
				break;
			}
		}

		return $ret_folder_obj;
	}

	/**
	 * フォルダリストゲット
	 * アクセス者がアクセス可能なフォルダのみ返す
	 *
	 * @param $acs_user_info_row
	 * @param $target_user_info_row
	 */
	function get_display_folder_obj_array ($acs_user_info_row, $target_user_info_row) {
		$ret_folder_obj_array = array();

		/* role_array 取得 */
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);

		if ($this->get_is_root_folder()) {
			/* 表示可能オブジェクト取得 */
			$all_folder_obj_array = $this->get_folder_obj_array();
			$ret_folder_obj_array = ACSAccessControl::get_valid_obj_row_array_for_user_community(
														$acs_user_info_row,
														$role_array,
														$all_folder_obj_array);
		} else {
			$ret_folder_obj_array = $this->get_folder_obj_array();
		}

		// アクセス可能なフォルダのみ返す
		//return $this->folder_obj_array;
		return $ret_folder_obj_array;
	}

	/**
	 * フォルダリストゲット (コミュニティ用)
	 * アクセス者がアクセス可能なフォルダのみ返す
	 *
	 * @param $acs_user_info_row
	 * @param $target_community_row
	 */
	function get_display_folder_obj_array_for_community ($acs_user_info_row, $target_community_row) {
		$ret_folder_obj_array = array();

		/* role_array 取得 */
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $target_community_row);

		if ($this->get_is_root_folder()) {
			/* 表示可能オブジェクト取得 */
			$all_folder_obj_array = $this->get_folder_obj_array();
			$ret_folder_obj_array = ACSAccessControl::get_valid_obj_row_array_for_community(
														$acs_user_info_row,
														$role_array,
														$all_folder_obj_array);
		} else {
			$ret_folder_obj_array = $this->get_folder_obj_array();
		}

		// アクセス可能なフォルダのみ返す
		//return $this->folder_obj_array;
		return $ret_folder_obj_array;
	}

	/**
	 * ファイルリストセット
	 *
	 * @param $folder_id_array
	 */
	function set_file_obj_array (&$folder_id_array) {
		$file_obj_array = array();

		/* ファイル情報取得 */
		$file_info_row_array = ACSFolderModel::select_folder_file_info_row_array($folder_id_array);
		if (count($file_info_row_array) <= 0) {
			return;
		}

		/* ファイル情報セット */
		foreach ($file_info_row_array as $file_info_row) {
		//_debug($file_info_row);
			$file_obj = new ACSFile($file_info_row);

			array_push($file_obj_array, $file_obj);
			//array_push($this->file_obj_array, $file_obj);
		}
		$this->file_obj_array = $file_obj_array;
	}

	/**
	 * ファイルリストゲット
	 *
	 * @param none
	 */
	function get_file_obj_array () {
		return $this->file_obj_array;
	}

	/**
	 * 指定IDのファイルゲット
	 *
	 * @param  $search_file_id
	 * @return $file_obj
	 */
	function get_file_obj ($search_file_id) {
		$file_obj_array = $this->get_file_obj_array();
		foreach ($file_obj_array as $file_obj) {
			if ($file_obj->get_file_id() == $search_file_id) {
				$ret_file_obj = $file_obj;
				break;
			}
		}

		return $ret_file_obj;
	}

	/**
	 * フォルダ情報セット
	 *
	 * @param $folder_row
	 */
	function set_folder_info (&$folder_row) {
		$this->set_folder_id($folder_row['folder_id']);
		$this->set_community_id($folder_row['community_id']);

		if ($folder_row['parent_folder_id']) {
			$this->set_folder_name($folder_row['folder_name']);
		} else {
			// ルートフォルダの場合
			$this->set_folder_name(ACSMsg::get_mdmsg(__FILE__,'M001'));
		}

		$this->set_comment($folder_row['comment']);
		$this->set_parent_folder_id($folder_row['parent_folder_id']);
		$this->set_open_level_code($folder_row['open_level_code']);
		$this->set_open_level_name($folder_row['open_level_name']);
		$this->set_trusted_community_row_array($folder_row['trusted_community_row_array']);
		$this->set_put_community_row_array($folder_row['put_community_row_array']);

		$this->set_entry_user_community_id($folder_row['entry_user_community_id']);
		$this->set_entry_user_community_name($folder_row['entry_user_community_name']);
		$this->set_entry_date($folder_row['entry_date']);
		$this->set_update_user_community_id($folder_row['update_user_community_id']);
		$this->set_update_user_community_name($folder_row['update_user_community_name']);
		$this->set_update_date($folder_row['update_date']);
	}

	/**
	 * フォルダIDセット
	 *
	 * @param $folder_id
	 */
	function set_folder_id ($folder_id) {
		$this->folder_id = $folder_id;
	}

	/**
	 * フォルダIDゲット
	 *
	 * @param none
	 */
	function get_folder_id () {
		return $this->folder_id;
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
	 * @param none
	 */
	function get_community_id () {
		return $this->community_id;
	}

	/**
	 * フォルダ名セット
	 *
	 * @param $folder_name
	 */
	function set_folder_name ($folder_name) {
		$this->folder_name = $folder_name;
	}

	/**
	 * フォルダ名ゲット
	 *
	 * @param none
	 */
	function get_folder_name () {
		return $this->folder_name;
	}

	/**
	 * コメントセット
	 *
	 * @param $comment
	 */
	function set_comment ($comment) {
		$this->comment = $comment;
	}

	/**
	 * コメントゲット
	 *
	 * @param none
	 */
	function get_comment () {
		return $this->comment;
	}

	/**
	 * 親フォルダIDセット
	 *
	 * @param $parent_folder_id
	 */
	function set_parent_folder_id ($parent_folder_id) {
		$this->parent_folder_id = $parent_folder_id;

		// ルートフォルダフラグセット
		$this->set_is_root_folder($parent_folder_id);
	}

	/**
	 * 親フォルダIDゲット
	 *
	 * @param none
	 */
	function get_parent_folder_id () {
		return $this->parent_folder_id;
	}

	/**
	 * 公開範囲コードセット
	 *
	 * @param $open_level_code
	 */
	function set_open_level_code ($open_level_code) {
		$this->open_level_code = $open_level_code;
	}

	/**
	 * 公開範囲コードゲット
	 *
	 * @param none
	 */
	function get_open_level_code () {
		return $this->open_level_code;
	}

	/**
	 * 公開範囲名セット
	 *
	 * @param $open_level_name
	 */
	function set_open_level_name ($open_level_name) {
		$this->open_level_name = $open_level_name;
	}

	/**
	 * 公開範囲名ゲット
	 *
	 * @param none
	 */
	function get_open_level_name () {
		return $this->open_level_name;
	}

	/**
	 * 登録者ユーザコミュニティIDセット
	 *
	 * @param $entry_user_community_id
	 */
	function set_entry_user_community_id ($entry_user_community_id) {
		$this->entry_user_community_id = $entry_user_community_id;
	}

	/**
	 * 登録者ユーザコミュニティIDゲット
	 *
	 * @param none
	 */
	function get_entry_user_community_id () {
		return $this->entry_user_community_id;
	}

	/**
	 * 登録者ユーザコミュニティ名セット
	 *
	 * @param $entry_user_community_name
	 */
	function set_entry_user_community_name ($entry_user_community_name) {
		$this->entry_user_community_name = $entry_user_community_name;
	}

	/**
	 * 登録者ユーザコミュニティ名ゲット
	 *
	 * @param none
	 */
	function get_entry_user_community_name () {
		return $this->entry_user_community_name;
	}

	/**
	 * 登録日セット
	 *
	 * @param $entry_date
	 */
	function set_entry_date ($entry_date) {
		$this->entry_date = $entry_date;
	}

	/**
	 * 登録日ゲット
	 *
	 * @param none
	 */
	function get_entry_date () {
		return $this->entry_date;
	}

	/**
	 * 登録日ゲット (yyyymmddhmi)
	 *
	 * @param none
	 */
	function get_entry_date_yyyymmddhmi () {
		$date_yyyymmddhmi = ACSLib::convert_pg_date_to_str($this->entry_date);
		return $date_yyyymmddhmi;
	}

	/**
	 * 更新者ユーザコミュニティIDセット
	 *
	 * @param $update_user_community_id
	 */
	function set_update_user_community_id ($update_user_community_id) {
		$this->update_user_community_id = $update_user_community_id;
	}

	/**
	 * 更新者ユーザコミュニティIDゲット
	 *
	 * @param none
	 */
	function get_update_user_community_id () {
		return $this->update_user_community_id;
	}

	/**
	 * 更新者ユーザコミュニティ名セット
	 *
	 * @param $update_user_community_name
	 */
	function set_update_user_community_name ($update_user_community_name) {
		$this->update_user_community_name = $update_user_community_name;
	}

	/**
	 * 更新者ユーザコミュニティ名ゲット
	 *
	 * @param none
	 */
	function get_update_user_community_name () {
		return $this->update_user_community_name;
	}

	/**
	 * 更新日セット
	 *
	 * @param $update_date
	 */
	function set_update_date ($update_date) {
		$this->update_date = $update_date;
	}

	/**
	 * 更新日ゲット
	 *
	 * @param none
	 */
	function get_update_date () {
		return $this->update_date;
	}

	/**
	 * 更新日ゲット (yyyymmddhmi)
	 *
	 * @param none
	 */
	function get_update_date_yyyymmddhmi () {
		$date_yyyymmddhmi = ACSLib::convert_pg_date_to_str($this->update_date);
		return $date_yyyymmddhmi;
	}

	/**
	 * ルートフォルダフラグセット
	 *
	 * @param $is_root_folder
	 */
	function set_is_root_folder ($parent_folder_id) {
		if ($parent_folder_id == "") {
			$this->is_root_folder = true;
		} else {
			$this->is_root_folder = false;
		}
	}

	/**
	 * ルートフォルダフラグゲット
	 *
	 * @param none
	 */
	function get_is_root_folder () {
		return $this->is_root_folder;
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
	 * @param none
	 */
	function get_acs_user_info_row () {
		return $this->acs_user_info_row;
	}

	/**
	 * 閲覧許可コミュニティセット
	 *
	 * @param $trusted_community_row_array
	 */
	function set_trusted_community_row_array ($trusted_community_row_array) {
		if ($trusted_community_row_array == "") {
			$trusted_community_row_array = array();
		}
		$this->trusted_community_row_array = $trusted_community_row_array;
	}

	/**
	 * 閲覧許可コミュニティゲット
	 *
	 * @param none
	 */
	function get_trusted_community_row_array () {
		return $this->trusted_community_row_array;
	}

	/**
	 * プット先コミュニティセット
	 *
	 * @param $put_community_row_array
	 */
	function set_put_community_row_array ($put_community_row_array) {
		if ($put_community_row_array == "") {
			$put_community_row_array = array();
		}
		$this->put_community_row_array = $put_community_row_array;
	}

	/**
	 * プット先コミュニティゲット
	 *
	 * @param none
	 */
	function get_put_community_row_array () {
		return $this->put_community_row_array;
	}

	/**
	 * ファイル追加
	 *
	 * @param $file_obj
	 *
	 * @return $ret
	 */
	function add_file ($file_obj) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		ACSDB::_do_query("BEGIN");

		/* テーブルに追加 */
		// file_info
		$ret = $file_obj->add_file();
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// folder_file
		$ret = ACSFolderModel::insert_folder_file($this->get_folder_id(), $file_obj->get_file_id());
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* フォルダの更新日を更新 */
		$ret = ACSFolderModel::update_folder_update_date($this->get_folder_id(),
														 $acs_user_community_id,
														 $file_obj->get_update_date());
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* ファイルを移動 */
		$ret = $file_obj->save_upload_file('FOLDER');
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* ファイルリストに追加 */
		array_push($this->file_obj_array, $file_obj);

		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * ファイル更新 (履歴管理)
	 *
	 * @param $file_obj
	 *
	 * @return $ret
	 */
	function update_file($file_obj) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		ACSDB::_do_query("BEGIN");

		/* テーブル更新 */
		// file_info
		$row = array();
		$row['server_file_name'] = $file_obj->get_server_file_name();
		$row['thumbnail_server_file_name'] = $file_obj->get_thumbnail_server_file_name();
		$row['rss_server_file_name'] = $file_obj->get_rss_server_file_name();
		$row['mime_type'] = $file_obj->get_mime_type();
		$row['file_size'] = $file_obj->get_file_size();
		$row['update_user_community_id'] = $file_obj->get_update_user_community_id();
		$row['update_date'] = $file_obj->get_update_date();
		$ret = ACSFileInfoModel::update_file_info($file_obj->get_file_id(), $row); 
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* フォルダの更新日を更新 */
		$ret = ACSFolderModel::update_folder_update_date($this->get_folder_id(),
														 $acs_user_community_id,
														 $file_obj->get_update_date());
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* ファイルを移動(保存) */
		$ret = $file_obj->save_upload_file('FOLDER');
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * ファイル復活 (履歴管理)
	 *
	 * @param $file_obj
	 *
	 * @return $ret
	 */
	function restore_history_file($file_info_row, $file_history_row) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		ACSDB::_do_query("BEGIN");

		// file_info_row更新
		$row = array();
		$row['display_file_name'] = $file_history_row['display_file_name'];
		$row['server_file_name'] = $file_history_row['server_file_name'];
		$row['thumbnail_server_file_name'] = $file_history_row['thumbnail_server_file_name'];
		$row['mime_type'] = $file_history_row['mime_type'];
		$row['file_size'] = $file_history_row['file_size'];
		$row['update_user_community_id'] = $acs_user_community_id;
		$row['update_date'] = 'now';
		$ret = ACSFileInfoModel::update_file_info($file_info_row['file_id'], $row); 
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* フォルダの更新日を更新 */
		$file_obj = ACSFile::get_file_info_instance($file_info_row['file_id']);
		$ret = ACSFolderModel::update_folder_update_date($this->get_folder_id(),
														 $acs_user_community_id,
														 $file_obj->get_update_date());
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * ファイル移動
	 *
	 * @param  $file_obj 移動対象のファイルオブジェクト
	 * @param  $new_folder_id 移動先のフォルダID
	 * @return $ret
	 */
	function move_file ($file_obj, $new_folder_id) {
		$ret = ACSFolderModel::update_folder_file_folder_id($this->get_folder_id(), $file_obj->get_file_id(), $new_folder_id);
		return $ret;
	}

	/**
	 * フォルダ作成
	 *
	 * @param $input_folder_row
	 *
	 * @return $ret
	 */
	function create_folder ($input_folder_row) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		// フォルダの値セット
		$folder_row  = array();
		$timestamp_pg_date = ACSLib::convert_timestamp_to_pg_date();
		$folder_id   = ACSDB::get_next_seq('folder_id_seq');
		if ($input_folder_row['entry_date']) {
			$entry_date  = $input_folder_row['entry_date'];
		} else {
			$entry_date  = $timestamp_pg_date;
		}
		$update_date = $entry_date;

		$folder_row['folder_id']                   = $folder_id;
		$folder_row['community_id']                = $this->get_community_id();
		$folder_row['folder_name']                 = $input_folder_row['folder_name'];
		$folder_row['comment']                     = $input_folder_row['comment'];
		$folder_row['parent_folder_id']            = $this->get_folder_id();
		$folder_row['entry_user_community_id']     = $acs_user_community_id;
		$folder_row['entry_date']                  = $entry_date;
		$folder_row['update_user_community_id']    = $acs_user_community_id;
		$folder_row['update_date']                 = $update_date;
		$folder_row['open_level_code']             = $input_folder_row['open_level_code'];

		$trusted_community_id_array = $input_folder_row['trusted_community_id_array'];


		/* フォルダ作成 */
		$ret = ACSFolderModel::insert_folder($folder_row, $trusted_community_id_array);
		if (!$ret) {
			return $ret;
		}

		/* 親フォルダの更新日を更新 */
		$ret = ACSFolderModel::update_folder_update_date($this->get_folder_id(),
														 $acs_user_community_id,
														 $update_date);
		if (!$ret) {
			return $ret;
		}

		/* フォルダリストに追加 */
		$folder_obj = ACSFolder::get_folder_instance($folder_row);
		array_push($this->folder_obj_array, $folder_obj);

		return $ret;
	}

	/**
	 * フォルダ情報変更
	 *
	 * @param $input_folder_row
	 *
	 * @return $ret
	 */
	function update_folder ($input_folder_row) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		$folder_row['folder_name']                 = $input_folder_row['folder_name'];
		$folder_row['comment']                     = $input_folder_row['comment'];
		$folder_row['update_user_community_id']    = $acs_user_community_id;
		$folder_row['update_date']                 = ACSLib::convert_timestamp_to_pg_date();
		$folder_row['open_level_code']             = $input_folder_row['open_level_code'];

		$trusted_community_id_array  = $input_folder_row['trusted_community_id_array'];

		$ret = ACSFolderModel::update_folder_info($this->get_folder_id(), $folder_row, $trusted_community_id_array);
		return $ret;
	}

	/**
	 * ファイルダウンロード
	 *
	 * @param $file_id
	 *
	 * @return none
	 */
	function download_file ($file_id) {
		$file_obj = $this->get_file_obj($file_id);
		$file_obj->download_file();
	}

	/**
	 * プットフォルダかどうか
	 *
	 * @param  $community_id 表示しているコミュニティ
	 * @return true/false
	 */
	function is_put_folder ($community_id) {
		if ($this->get_community_id() == $community_id) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * プット先コミュニティがあるかどうか
	 */
	function has_put_community () {
		if (count($this->get_put_community_row_array()) > 0) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * プット先コミュニティのフォルダID取得
	 *
	 * @param $community_id
	 */
	function get_put_community_folder_id ($community_id) {
		$ret_put_community_folder_id = "";
		$put_community_row_array = $this->get_put_community_row_array();

		foreach ($put_community_row_array as $put_community_row) {
			if ($put_community_row['community_id'] == $community_id) {
				$ret_put_community_folder_id = $put_community_row['put_community_folder_id'];
				break;
			}
		}

		return $ret_put_community_folder_id;
	}

	/**
	 * プット先コミュニティ更新
	 */
	function update_put_community ($folder_id, $put_community_array) {
		ACSDB::_do_query("BEGIN");

		foreach ($put_community_array as $put_community) {
			// delete
			$ret = ACSFolderModel::delete_put_community($folder_id, $put_community['put_community_id']);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return false;
			}

			// プット先フォルダIDの指定がある場合、insert
			if ($put_community['put_community_folder_id']) {
				// insert
				$ret = ACSFolderModel::insert_put_community($folder_id, $put_community['put_community_id'], $put_community['put_community_folder_id']);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return false;
				}
			}
		}

		ACSDB::_do_query("COMMIT");

		return true;
	}

	/**
	 * サブフォルダセット
	 *
	 * @param $sub_folder_obj_array
	 */
	function set_sub_folder_obj_array ($sub_folder_obj_array) {
		$this->sub_folder_obj_array = $sub_folder_obj_array;
	}

	/**
	 * サブフォルダゲット
	 */
	function get_sub_folder_obj_array () {
		return $this->sub_folder_obj_array;
	}

	/**
	 * フォルダ名更新
	 *
	 * @param  $new_folder_name
	 * @return $ret
	 */
	function rename_folder_name ($new_folder_name) {
		$ret = ACSFolderModel::update_folder_name($this->get_folder_id(), $new_folder_name);
		return $ret;
	}

	/**
	 * フォルダ移動
	 *
	 * @param  $new_parent_folder_id
	 * @return $ret
	 */
	function move_folder ($new_parent_folder_id) {
		$ret = ACSFolderModel::update_parent_folder_id($this->get_folder_id(), $new_parent_folder_id);
		return $ret;
	}

	/**
	 * 公開範囲更新
	 *
	 * @param  $new_open_level_code
	 * @param  $new_trusted_community_row_array
	 * @return $ret
	 */
	function update_open_level_code ($new_open_level_code, $new_trusted_community_row_array) {
		$ret = ACSFolderModel::update_folder_open_level_code($this->get_folder_id(), $new_open_level_code, $new_trusted_community_row_array);
		return $ret;
	}
}
?>
