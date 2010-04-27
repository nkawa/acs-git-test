<?php
/**
 * ACS File
 *
 * @author  kuwayama
 * @version $Revision: 1.35 $ $Date: 2006/12/18 07:41:48 $
 */
require_once(ACS_CLASS_DIR . 'ACSFileInfoModel.class.php');
require_once(ACS_CLASS_DIR . 'ACSFileAccessHistoryModel.class.php');
class ACSFile
{
	/* ファイルID */
	var $file_id;

	/* オーナーコミュニティID */
	var $owner_community_id;

	/* 表示用ファイル名 */
	var $display_file_name;

	/* サーバファイル名 */
	var $server_file_name;

	/* サムネイルサーバファイル名 */
	var $thumbnail_server_file_name;

	/* RSSサーバファイル名 */
	var $rss_server_file_name;

	/* MIME TYPE */
	var $mime_type;

	/* ファイルサイズ */
	var $file_size;

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

	/* アップロードテンポラリファイル名 */
	var $upload_temp_file_name;

	/**
	 * コンストラクタ
	 *
	 * @param $file_info_row
	 */
	function ACSFile ($file_info_row) {
		$this->set_file_info($file_info_row);
	}

	/**
	 * ファイル情報セット
	 *
	 * @param $file_info_row
	 */
	function set_file_info ($file_info_row) {
		$this->set_file_id($file_info_row['file_id']);
		$this->set_owner_community_id($file_info_row['owner_community_id']);
		$this->set_display_file_name($file_info_row['display_file_name']);
		$this->set_server_file_name($file_info_row['server_file_name']);
		$this->set_thumbnail_server_file_name($file_info_row['thumbnail_server_file_name']);
		$this->set_rss_server_file_name($file_info_row['rss_server_file_name']);
		$this->set_mime_type($file_info_row['mime_type']);
		$this->set_file_size($file_info_row['file_size']);
		$this->set_entry_user_community_id($file_info_row['entry_user_community_id']);
		$this->set_entry_user_community_name($file_info_row['entry_user_community_name']);
		$this->set_entry_date($file_info_row['entry_date']);
		$this->set_update_user_community_id($file_info_row['update_user_community_id']);
		$this->set_update_user_community_name($file_info_row['update_user_community_name']);
		$this->set_update_date($file_info_row['update_date']);

		// アップロード時に必要
		$this->set_upload_temp_file_name($file_info_row['upload_temp_file_name']);
	}

	/**
	 * インスタンス取得（ファイルID指定）
	 *
	 * @param $file_id
	 */
	static function get_file_info_instance ($file_id) {
		$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
		$file_obj = new ACSFile($file_info_row);

		return $file_obj;
	}


	/**
	 * インスタンス取得（アップロード用）
	 *
	 * @param $upload_file_info_row  $_FILES['new_file']
	 * @param $owner_community_id	対象となるコミュニティID
	 * @param $acs_user_community_id アップロードしたユーザコミュニティID
	 * @param $file_id			   既存ファイルの更新時のファイルID
	 */
	static function get_upload_file_info_instance ($upload_file_info_row, $owner_community_id, $acs_user_community_id, $file_id = "") {
		$file_info_row = array();
		/* file_id 取得 */
		if($file_id == ""){
			$file_id = ACSFileInfoModel::get_next_file_id_seq();
		}

		/* display_file_name */
		if (!ini_get('mbstring.encoding_translation')) {
			$display_file_name = mb_convert_encoding($upload_file_info_row['name'], mb_internal_encoding(), mb_http_output());
		} else {
			$display_file_name = $upload_file_info_row['name'];
		}
		/* server_file_name 作成 */
		$server_file_name = ACSFile::get_upload_file_save_file_name($owner_community_id, $file_id);

		/* thumbnail_server_file_name 作成 */
		$thumbnail_server_file_name = ACSFile::get_thumbnail_save_file_name($owner_community_id, $file_id);
		/* 作成日 */
		$filemtime  = filemtime($upload_file_info_row['tmp_name']);
		
		// insert 用にフォーマットする
		$entry_date = ACSLib::convert_timestamp_to_pg_date($filemtime);

		/* 更新日 */
		$update_date = $entry_date;

		$file_info_row['file_id'] = $file_id;
		$file_info_row['owner_community_id'] = $owner_community_id;
		$file_info_row['display_file_name'] = $display_file_name;
		$file_info_row['server_file_name'] = $server_file_name;
		$file_info_row['thumbnail_server_file_name'] = $thumbnail_server_file_name;
		$file_info_row['mime_type'] = $upload_file_info_row['type'];
		$file_info_row['file_size'] = $upload_file_info_row['size'];
		$file_info_row['entry_user_community_id'] = $acs_user_community_id;
		$file_info_row['entry_date'] = $entry_date;
		$file_info_row['update_user_community_id'] = $acs_user_community_id;
		$file_info_row['update_date'] = $update_date;

		$file_info_row['upload_temp_file_name'] = $upload_file_info_row['tmp_name'];

		$file_obj = new ACSFile($file_info_row);
		return $file_obj;
	}

	/**
	 * インスタンス取得（履歴更新アップロード用）
	 *
	 * @param $upload_file_info_row  $_FILES['new_file']
	 * @param $owner_community_id	対象となるコミュニティID
	 * @param $acs_user_community_id アップロードしたユーザコミュニティID
	 * @param $file_id			   既存ファイルの更新時のファイルID
	 */
	static function get_upload_file_info_instance_for_update($upload_file_info_row, $owner_community_id, $acs_user_community_id, $file_id) {
		$file_info_row = array();

		/* 新file_id取得 (server_file_name用) */
		$new_file_id = ACSFileInfoModel::get_next_file_id_seq();

		/* display_file_name */
		if (!ini_get('mbstring.encoding_translation')) {
			$display_file_name = mb_convert_encoding($upload_file_info_row['name'], mb_internal_encoding(), mb_http_output());
		} else {
			$display_file_name = $upload_file_info_row['name'];
		}
		/* server_file_name 作成 */
		$server_file_name = ACSFile::get_upload_file_save_file_name($owner_community_id, $new_file_id);

		/* thumbnail_server_file_name 作成 */
		$thumbnail_server_file_name = ACSFile::get_thumbnail_save_file_name($owner_community_id, $new_file_id);
		/* 作成日 */
		$filemtime  = filemtime($upload_file_info_row['tmp_name']);
		
		// insert 用にフォーマットする
		$entry_date = ACSLib::convert_timestamp_to_pg_date($filemtime);

		/* 更新日 */
		$update_date = $entry_date;

		$file_info_row['file_id'] = $file_id;
		$file_info_row['owner_community_id'] = $owner_community_id;
		$file_info_row['display_file_name'] = $display_file_name;
		$file_info_row['server_file_name'] = $server_file_name;
		$file_info_row['thumbnail_server_file_name'] = $thumbnail_server_file_name;
		$file_info_row['mime_type'] = $upload_file_info_row['type'];
		$file_info_row['file_size'] = $upload_file_info_row['size'];
		$file_info_row['entry_user_community_id'] = $acs_user_community_id;
		$file_info_row['entry_date'] = $entry_date;
		$file_info_row['update_user_community_id'] = $acs_user_community_id;
		$file_info_row['update_date'] = $update_date;

		$file_info_row['upload_temp_file_name'] = $upload_file_info_row['tmp_name'];

		$file_obj = new ACSFile($file_info_row);
		return $file_obj;
	}

	/**
	 * ファイルIDセット
	 *
	 * @param $file_id
	 */
	function set_file_id ($file_id) {
		$this->file_id = $file_id;
	}

	/**
	 * ファイルIDゲット
	 *
	 * @param none
	 */
	function get_file_id () {
		return $this->file_id;
	}

	/**
	 * オーナーコミュニティIDセット
	 *
	 * @param $owner_community_id
	 */
	function set_owner_community_id ($owner_community_id) {
		$this->owner_community_id = $owner_community_id;
	}

	/**
	 * オーナーコミュニティIDゲット
	 *
	 * @param none
	 */
	function get_owner_community_id () {
		return $this->owner_community_id;
	}

	/**
	 * 表示用ファイル名セット
	 *
	 * @param $file_name
	 */
	function set_display_file_name ($file_name) {
		$this->display_file_name = $file_name;
	}

	/**
	 * 表示用ファイル名ゲット
	 *
	 * @param none
	 */
	function get_display_file_name () {
		return $this->display_file_name;
	}

	/**
	 * サーバファイル名セット
	 *
	 * @param $server_file_name
	 */
	function set_server_file_name ($server_file_name) {
		$this->server_file_name = $server_file_name;
	}

	/**
	 * サーバファイル名ゲット
	 *
	 * @param none
	 */
	function get_server_file_name () {
		return $this->server_file_name;
	}

	/**
	 * サムネイルサーバファイル名セット
	 *
	 * @param $thumbnail_server_file_name
	 */
	function set_thumbnail_server_file_name ($thumbnail_server_file_name) {
		$this->thumbnail_server_file_name = $thumbnail_server_file_name;
	}

	/**
	 * サムネイルサーバファイル名ゲット
	 *
	 * @param none
	 */
	function get_thumbnail_server_file_name () {
		return $this->thumbnail_server_file_name;
	}

	/**
	 * RSSサーバファイル名セット
	 *
	 * @param $rss_server_file_name
	 */
	function set_rss_server_file_name ($rss_server_file_name) {
		$this->rss_server_file_name = $rss_server_file_name;
	}

	/**
	 * RSSサーバファイル名ゲット
	 *
	 * @param none
	 */
	function get_rss_server_file_name () {
		return $this->rss_server_file_name;
	}

	/**
	 * MIME TYPE セット
	 *
	 * @param $mime_type
	 */
	function set_mime_type ($mime_type) {
		$this->mime_type = $mime_type;
	}

	/**
	 * MIME TYPE ゲット
	 *
	 * @param none
	 */
	function get_mime_type () {
		return $this->mime_type;
	}

	/**
	 * ファイルサイズセット
	 *
	 * @param $file_size
	 */
	function set_file_size ($file_size) {
		$this->file_size = $file_size;
	}

	/**
	 * ファイルサイズゲット
	 *
	 * @param none
	 */
	function get_file_size () {
		return $this->file_size;
	}

	/**
	 * ファイルサイズゲット (KB)
	 *
	 * @param none
	 */
	function get_file_size_kb () {
		$size = $this->file_size / 1024;
		return number_format(ceil($size)) . " KB";
	}

	/**
	 * アップロードテンポラリファイル名セット
	 *
	 * @param $upload_temp_file_name
	 */
	function set_upload_temp_file_name ($upload_temp_file_name) {
		$this->upload_temp_file_name = $upload_temp_file_name;
	}

	/**
	 * アップロードテンポラリファイル名ゲット
	 *
	 * @param none
	 */
	function get_upload_temp_file_name () {
		return $this->upload_temp_file_name;
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
	 * アップロードファイル保存先パス
	 *
	 * @param $owner_community_id
	 * @param $file_id
	 */
	function get_upload_file_save_file_name ($owner_community_id, $file_id) {
		//$upload_file_save_file_name  = ACS_FOLDER_DIR;
		//$upload_file_save_file_name .= '/';
		//$upload_file_save_file_name .= $owner_community_id;
		//$upload_file_save_file_name .= '/';

		$upload_file_save_file_name  = ACSFile::get_upload_file_save_path($owner_community_id);
		$upload_file_save_file_name .= $file_id;

		return $upload_file_save_file_name;
	}

	/**
	 * アップロードファイル保存先ディレクトリ
	 * ファイル格納先ルートパス / ユーザコミュニティID / ファイル名 (file_id)
	 *
	 * @param $owner_community_id
	 */
	static function get_upload_file_save_path ($owner_community_id) {
		//$upload_file_save_path  = ACS_FOLDER_DIR;
		//$upload_file_save_path .= '/';
		$upload_file_save_path = $owner_community_id;
		$upload_file_save_path .= '/';

		return $upload_file_save_path;
	}

	/**
	 * サムネイル画像ファイル名
	 * ファイル格納先ルートパス / ユーザコミュニティID / ファイル名 (file_id.thumb)
	 *
	 * @param $owner_community_id
	 * @param $file_id
	 */
	static function get_thumbnail_save_file_name ($owner_community_id, $file_id) {
		$thumnail_save_file_name  = ACSFile::get_upload_file_save_path($owner_community_id);
		$thumnail_save_file_name .= $file_id;
		$thumnail_save_file_name .= '.thumb';
		return $thumnail_save_file_name;
	}

	/**
	 * RSSサムネイル画像ファイル名
	 * ファイル格納先ルートパス / ユーザコミュニティID / ファイル名 (file_id.rss)
	 *
	 * @param $owner_community_id
	 * @param $file_id
	 */
	function get_rss_save_file_name ($owner_community_id, $file_id) {
		$rss_save_file_name  = ACSFile::get_upload_file_save_path($owner_community_id);
		$rss_save_file_name .= $file_id;
		$rss_save_file_name .= '.rss';
		return $rss_save_file_name;
	}

	/**
	 * ファイル追加
	 *
	 * @param none
	 */
	function add_file () {
		$ret = ACSFileInfoModel::insert_file_info($this);

		return $ret;
	}

	/**
	 * アップロードファイル保存
	 * テンポラリファイルをフォルダの格納場所へ移動する
	 *
	 * @param $save_mode  保存するモード ('PROFILE' or 'DIARY' or 'BBS' or 'FOLDER')
	 */
	function save_upload_file ($save_mode) {
		// 0バイトファイルの場合は、エラー
		if ($this->get_file_size() <= 0) {
			return false;
		}

		/* ディレクトリ存在チェック */
		// ない場合は作成する
		$to_dir  = ACS_FOLDER_DIR . "/";
		$to_dir .= $this->get_upload_file_save_path($this->get_owner_community_id());
		if(!file_exists($to_dir)) {mkdir($to_dir); chmod($to_dir, 0777);}

		$from = $this->get_upload_temp_file_name();
		$to   = ACS_FOLDER_DIR . "/" . $this->get_server_file_name();
		if(is_uploaded_file($from)){
			$ret = move_uploaded_file($from, $to);
			if (!$ret) {
				return $ret;
			}
		}else{
			//直接のアップロードファイルではない場合、移動する
			$ret = rename($from, $to);
		}
		/* 画像の場合、サムネイルを作成 */
		if ($this->is_image_file()) {
			$ret = $this->make_thumbnail($to, $save_mode);
		}
		return $ret;
	}

	/**
	 * サムネイル画像作成
	 *
	 * @param $target_file サムネイル作成元 画像ファイルパス
	 * @param $save_mode  保存するモード ('PROFILE' or 'DIARY' or 'BBS' or 'FOLDER')
	 */
	function make_thumbnail ($target_file, $save_mode) {
		// システム設定グループ名
		//$system_config_group = '画像ファイル';
		$system_config_group = ACSMsg::get_mst('system_config_group','D04');

		// 画像サイズを取得
		$default_width_max  = "";
		$default_height_max = "";
		$thumb_width_max	= "";
		$thumb_height_max   = "";
		$rss_width_max	  = "";
		$rss_height_max	 = "";

		switch ($save_mode) {
			case 'PROFILE':
				$default_width_max  = ACSSystemConfig::get_keyword_value($system_config_group, 'PROFILE_IMAGE_WIDTH_MAX');
				$default_height_max = ACSSystemConfig::get_keyword_value($system_config_group, 'PROFILE_IMAGE_HEIGHT_MAX');
				$thumb_width_max	= ACSSystemConfig::get_keyword_value($system_config_group, 'PROFILE_IMAGE_THUMB_WIDTH_MAX');
				$thumb_height_max   = ACSSystemConfig::get_keyword_value($system_config_group, 'PROFILE_IMAGE_THUMB_HEIGHT_MAX');
				break;

			case 'DIARY':
				$default_width_max  = ACSSystemConfig::get_keyword_value($system_config_group, 'DIARY_IMAGE_WIDTH_MAX');
				$default_height_max = ACSSystemConfig::get_keyword_value($system_config_group, 'DIARY_IMAGE_HEIGHT_MAX');
				$thumb_width_max	= ACSSystemConfig::get_keyword_value($system_config_group, 'DIARY_IMAGE_THUMB_WIDTH_MAX');
				$thumb_height_max   = ACSSystemConfig::get_keyword_value($system_config_group, 'DIARY_IMAGE_THUMB_HEIGHT_MAX');
				break;

			case 'BBS':
				$default_width_max  = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_WIDTH_MAX');
				$default_height_max = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_HEIGHT_MAX');
				$thumb_width_max	= ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_THUMB_WIDTH_MAX');
				$thumb_height_max   = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_THUMB_HEIGHT_MAX');
				$rss_width_max	  = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_RSS_WIDTH_MAX');
				$rss_height_max	 = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_RSS_HEIGHT_MAX');
				break;

			case 'FOLDER':
				$thumb_width_max	= ACSSystemConfig::get_keyword_value($system_config_group, 'FOLDER_IMAGE_THUMB_WIDTH_MAX');
				$thumb_height_max   = ACSSystemConfig::get_keyword_value($system_config_group, 'FOLDER_IMAGE_THUMB_HEIGHT_MAX');
				break;

			default:
				return false;
		}

		// サムネイル画像作成
		// ImageMagick
		$image_obj = new ACSImageMagickWrapper($target_file);
		if (!$image_obj) {
			return false;
		}

		// 最大サイズより大きい場合は縮小する
		//	フォルダの場合は制限なし
		if ($save_mode != 'FOLDER') {
			$new_image_name = $image_obj->reduce_image($default_width_max, $default_height_max);
		}

		// サムネイル作成
		$new_thumb_name = $image_obj->make_jpg_thumbnail(ACS_FOLDER_DIR . $this->get_thumbnail_server_file_name(),
					$thumb_width_max, $thumb_height_max);

		// RSS フィード用
		//	BBS の場合は、RSS用ファイルを作成
		if ($save_mode == 'BBS') {
			$rss_server_file_name = $this->get_rss_save_file_name($this->get_owner_community_id(), $this->get_file_id());
			$new_thumb_name = $image_obj->make_jpg_thumbnail(ACS_FOLDER_DIR . $rss_server_file_name,
					$rss_width_max, $rss_height_max);
			$this->set_rss_server_file_name($rss_server_file_name);
		}

		return true;
	}

	/**
	 * ファイルダウンロード
	 *
	 * @param none
	 *
	 * @return none
	 */
	function download_file ($mode = '') {
		// ファイルパス
		if ($mode == 'thumb') {
			$file_path = $this->get_thumbnail_server_file_name();
		} else if ($mode == 'rss') {
			$file_path = $this->get_rss_server_file_name();
		} else {
			$file_path = $this->get_server_file_name();
		}

		// ファイルが読み込みできない場合
		if (!is_readable(ACS_FOLDER_DIR . $file_path)) {
			header("Cache-Control: public, max-age=0");
			header("Pragma:");
			echo "Not Found";
			return;
		}

		// ダウンロードファイル名
		//$download_file_name = mb_convert_encoding($this->get_display_file_name(), mb_http_output());
		$download_file_name = $this->get_display_file_name();

		// Content-type
		$content_type = $this->get_mime_type();
		if ($content_type == '') {
			$content_type = 'application/octet-stream';
		}

		// charset (textの場合)
		if (preg_match('/text/', $content_type)) {
			$str = implode('', file(ACS_FOLDER_DIR . $file_path));
			$encoding = mb_detect_encoding($str, 'auto');
			if ($encoding == 'ASCII' && mb_http_output() != 'pass') {
				$content_type .= "; charset=" . mb_preferred_mime_name(mb_http_output());
			} else {
				$content_type .= "; charset=" . mb_preferred_mime_name($encoding);

			}
		}

		// action: inline(ブラウザ内表示), attachment(ダウンロードダイアログ)
		//if (preg_match('/text|image/', $content_type)) {
		if ($this->is_image_file() or $this->is_text_file()) {
			$action = 'inline';
		} else {
			$action = 'attachment';
		}

		// output_bufferingを無効にする
		mb_http_output('pass');
		if ($mode == 'thumb') {
			header("Cache-Control: public, max-age=1800");
			header("Pragma:");
/*
header("Pragma: no-cache");
header("Cache-Control: no-store");
header("Cache-Control: no-cache");
header("Expires: -1");
//header("Expires: 0");
*/
		} else {
			header("Cache-Control: public, max-age=0");
			header("Pragma:");
/*
//header("Pragma: no-store");
header("Pragma: no-cache");
header("Cache-Control: no-store");
header("Cache-Control: no-cache");
//header("Expires: 0");
header("Expires: -1");
*/
		}

		// HTTPヘッダを吐く (action: inline, attachment)
		if ($this->is_image_file()) {
			header("Content-type: $content_type");
			header("Content-disposition: $action; filename=\"$download_file_name\"");
		} else {
			header("Content-type: $content_type");
			header("Content-disposition: $action; attachment; filename=\"" . ACSFile::get_download_name( $download_file_name ) . '"' );

		}		

		// ファイルを読み出す
		readfile(ACS_FOLDER_DIR . $file_path);
	}

	/**
	 * 履歴ファイルダウンロード
	 *
	 * @param none
	 *
	 * @return none
	 */
	function download_history_file($file_history_id, $mode = '') {
		$file_history_row = ACSFileHistory::get_file_history_row($file_history_id);

		// ファイルパス
		if ($mode == 'thumb') {
			$file_path = $file_history_row['thumbnail_server_file_name'];
		} else {
			$file_path = $file_history_row['server_file_name'];
		}

		// ファイルが読み込みできない場合
		if (!is_readable(ACS_FOLDER_DIR . $file_path)) {
			header("Cache-Control: public, max-age=0");
			header("Pragma:");
			echo "Not Found";
			return;
		}

		// ダウンロードファイル名
//		$download_file_name = mb_convert_encoding($this->get_display_file_name(), mb_http_output());
		$download_file_name = $this->get_display_file_name();

		// Content-type
		$content_type = $file_history_row['mime_type'];
		if ($content_type == '') {
			$content_type = 'application/octet-stream';
		}

		// charset (textの場合)
		if (preg_match('/text/', $content_type)) {
			$str = implode('', file(ACS_FOLDER_DIR . $file_path));
			$encoding = mb_detect_encoding($str, 'auto');
			if ($encoding == 'ASCII' && mb_http_output() != 'pass') {
				$content_type .= "; charset=" . mb_preferred_mime_name(mb_http_output());
			} else {
				$content_type .= "; charset=" . mb_preferred_mime_name($encoding);
			}
		}

		// action: inline(ブラウザ内表示), attachment(ダウンロードダイアログ)
		//if (preg_match('/text|image/', $content_type)) {
		if (preg_match('/image/', $file_history_row['mime_type']) || preg_match('/text/', $file_history_row['mime_type'])) {
			$action = 'inline';
		} else {
			$action = 'attachment';
		}

		// HTTPヘッダを吐く (action: inline, attachment)
//		header("Content-disposition: $action; filename=\"$download_file_name\"");
		header("Content-disposition: $action; attachment; filename=\"" . ACSFile::get_download_name( $download_file_name ) . '"' );
		header("Content-type: $content_type");

		// output_bufferingを無効にする
		mb_http_output('pass');
		if ($mode == 'thumb') {
			header("Cache-Control: public, max-age=1800");
			header("Pragma:");
		} else {
			header("Cache-Control: public, max-age=0");
			header("Pragma:");
		}

		// ファイルを読み出す
		readfile(ACS_FOLDER_DIR . $file_path);
	}

	/**
	 * 画像ファイル表示
	 *
	 * @param $mode	   :NULL, thumb, rss
	 *
	 * @return true/false
	 */
	function view_image ($mode) {
		$mime_type = $this->get_mime_type();

		if ($this->is_image_file()) {
			// image ファイル出力
			$this->download_file($mode);
			return true;

		} else {
			return false;
		}
	}

	/**
	 * 画像ファイルかどうか
	 */
	function is_image_file () {
		if (preg_match('/image/', $this->get_mime_type())) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * テキストファイルかどうか
	 */
	function is_text_file () {
		if (preg_match('/text/', $this->get_mime_type())) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 表示用ファイル名変更
	 *
	 * @param  $new_display_file_name
	 * @return $ret
	 */
	function rename_display_file_name ($new_display_file_name) {
		$ret = ACSFileInfoModel::update_display_file_name($this->get_file_id(), $new_display_file_name);
		return $ret;
	}

	/**
	 * ファイル削除
	 *
	 * @return $ret
	 */
	function delete_file () {
		$ret = ACSFileInfoModel::delete_file_info($this);
		return $ret;
	}

	/**
	 * ファイルのアクセス履歴情報を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @param $file_id ファイルID
	 * @return ファイルのアクセス履歴情報 (連想配列)
	 */
	static function get_file_access_history_row($user_community_id, $file_id) {
		$user_community_id = pg_escape_string($user_community_id);
		$file_id = pg_escape_string($file_id);

		$sql  = "SELECT *" . 
				" FROM file_access_history" .
				" WHERE user_community_id = '" . $user_community_id . "'" . 
				"  AND file_id = '" . $file_id . "'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * ファイルのアクセス履歴を登録する
	 *
	 * @param $file_id
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_file_access_history($user_community_id, $file_id) {
		// ファイルアクセス履歴
		$file_access_history_row = ACSFile::get_file_access_history_row($user_community_id, $file_id);

		$file_access_history_form = array(
										'user_community_id' => $user_community_id,
										'file_id' 			=> $file_id,
										'access_date' 		=> 'now'
		);

		// レコードが存在する場合はUPDATE
		if ($file_access_history_row) {
			ACSFileAccessHistoryModel::update_file_access_history($file_access_history_form);
		// レコードが存在しない場合はINSERT
		} else {
			ACSFileAccessHistoryModel::insert_file_access_history($file_access_history_form);
		}
	}

	/**
	 * ファイルダウンロード機能用：エンコーディングを設定する
	 *
	 * @return 成功(true) / 失敗(false)
	 */
	function get_current_mb_encoding() {
		// エンコーディングを固定する場合
		return ACSFile::_get_mb_encoding( 'EUC-JP' ) ;
		//return ACSFile::_get_mb_encoding( lang_get( 'charset' ) );
	}

	/**
	 * ファイルダウンロード機能用：エンコードを行う
	 *
	 * @return 成功(エンコード後) / 失敗(null)
	 */
	function _get_mb_encoding( $p_charset ) {

		$mb_encoding_array = array(
			'Shift_JIS' => 'SJIS-win',
			'EUC-JP' => 'EUC-JP',
			'UTF-8'  => 'UTF-8'
		);

		if ( isset( $mb_encoding_array[$p_charset] ) ) {
			return $mb_encoding_array[$p_charset];
		}

		return null;
	}

	/**
	 * ファイルダウンロード機能用：ダウンロードファイル名を取得する
	 *
	 * @return ファイル名
	 */
	function get_download_name( $p_filename ) {

		$encoding = ACSFile::get_current_mb_encoding();
		if ( $encoding === null ) {
			return $p_filename;
		}

		$ua = $_SERVER['HTTP_USER_AGENT'];

		// ユーザーエージェントによってファイル名を変換
		if ( strstr( $ua, 'MSIE' ) && !strstr( $ua, 'Opera' ) ) {
			$t_filename = mb_convert_encoding( $p_filename, 'SJIS-win', $encoding );

		} elseif (strstr( $ua, 'Safari') ) {
			// Safari対応
			$t_filename = "";

		} else {
			$t_filename = mb_convert_encoding( $p_filename, 'UTF-8', $encoding );
		}

		return $t_filename;
	}

}
?>
