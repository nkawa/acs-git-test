<?php
/**
 * bbs用ファイル情報 DBテーブル
 * ACSBBSFileModel.class.php
 *
 * bbs_fileテーブルモデル
 * @author  akitsu
 * @version $Revision: 1.1 $
 */
 
class ACSBBSFileModel
{
	/* ファイルID
	* @type　BIG_INT */
	var $file_id;

	/* BBS　ID
	* @type　BIG_INT */
	var $bbs_id;

	/**
	 * コンストラクタ
	 * 必ず要素配列が渡される
	 * @param $bbs_file_row
	 */
	function ACSBBSFileModel ($bbs_file_row) {
		$this->set_bbs_file_info($bbs_file_row);
	}
 
 	/**
	 * ファイル情報セット
	 * 必ず要素配列が渡される
	 * @param $bbs_file_row
	 */
	function set_bbs_file_info ($bbs_file_row) {
		$this->set_file_id($bbs_file_row['file_id']);
		$this->set_bbs_id($bbs_file_row['bbs_id']);
	}

	/**
	 * インスタンス取得（ファイルID,BBSid指定）
	 *
	 * @param $file_id
	 */
	static function get_bbs_file_info_instance ($file_id,$bbs_id) {
		$bbs_file_row['file_id'] = $file_id;
		$bbs_file_row['bbs_id'] = $bbs_id;
		$file_obj = new ACSBBSFileModel($bbs_file_row);

		return $file_obj;
	}


/* アクセッサ */
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
	 * BBS IDセット
	 *
	 * @param $bbs_id
	 */
	function set_bbs_id ($bbs_id) {
		$this->file_id = $bbs_id;
	}
	/**
	 * BBS IDゲット
	 *
	 * @param none
	 */
	function get_bbs_id () {
		return $this->bbs_id;
	}
	
	
	
}
