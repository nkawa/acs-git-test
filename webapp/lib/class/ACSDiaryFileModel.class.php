<?php
/**
 * diary用ファイル情報 DBテーブル
 * ACSDiaryFileModel.class.php
 *
 * diary_fileテーブルモデル
 * @author  akitsu
 * @version $Revision: 1.1 $
 */
 
class ACSDiaryFileModel
{
	/* ファイルID
	* @type　BIG_INT */
	var $file_id;

	/* diary　ID
	* @type　BIG_INT */
	var $diary_id;

	/**
	 * コンストラクタ
	 * 必ず要素配列が渡される
	 * @param $diary_file_row
	 */
	function ACSdiaryFileModel ($diary_file_row) {
		$this->set_diary_file_info($diary_file_row);
	}
 
 	/**
	 * ファイル情報セット
	 * 必ず要素配列が渡される
	 * @param $diary_file_row
	 */
	function set_diary_file_info ($diary_file_row) {
		$this->set_file_id($diary_file_row['file_id']);
		$this->set_diary_id($diary_file_row['diary_id']);
	}

	/**
	 * インスタンス取得（ファイルID,diaryid指定）
	 *
	 * @param $file_id
	 */
	static function get_diary_file_info_instance ($file_id,$diary_id) {
		$diary_file_row['file_id'] = $file_id;
		$diary_file_row['diary_id'] = $diary_id;
		$file_obj = new ACSdiaryFileModel($diary_file_row);

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
	 * diary IDセット
	 *
	 * @param $diary_id
	 */
	function set_diary_id ($diary_id) {
		$this->file_id = $diary_id;
	}
	/**
	 * diary IDゲット
	 *
	 * @param none
	 */
	function get_diary_id () {
		return $this->diary_id;
	}
	
	
	
}
