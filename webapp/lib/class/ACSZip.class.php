<?php

define('_ACSZIP_ZIP_CMD', 'cd %s;/usr/bin/zip -r %s %s;cd -');

/**
 * ACS Zip
 *
 * @author  z-satosi
 * @version $Revision: 1.2 $ $Date: 2007/03/28 02:26:48 $
 */
class ACSZip
{
	/* zipファイル */
	var $zip_file;

	/* zipファイル作成時のワークディレクトリ */
	var $zip_work_dir;

	/**
	 * コンストラクタ
	 *
	 * @param string $zip_work_dir zip作成用ワークディレクトリ
	 * @param string $zip_file zipファイル名
	 */
	function ACSZip ($zip_work_dir, $zip_file = "") {
		$this->zip_work_dir = mb_ereg_replace('/$','',$zip_work_dir);
		$this->zip_file = $zip_file == "" ? 
				$this->zip_work_dir.'.zip' : dirname($this->zip_work_dir).'/'.$zip_file;
		$this->initialize();
	}

	/**
	 * ワークディレクトリの初期化
	 */
	function initialize () {
		$this->clear_zip_file ();
		$this->clear_work_dir_and_files ();
		ACSLib::make_dir($this->zip_work_dir);
	}

	/**
	 * ワークディレクトリ・ファイルの消去
	 */
	function clear_work_dir_and_files () {
		ACSLib::remove_dir($this->zip_work_dir);
	}

	/**
	 * zipファイルの消去
	 */
	function clear_zip_file () {
		@unlink($this->zip_file);
	}

	/**
	 * ディレクトリの作成(途中のディレクトリも自動生成)
	 *
	 * @param string $dir ディレクトリ
	 * @param string $name_encoding 日本語ファイル・フォルダ名のエンコーディング
	 * @return mixed 正常時...ファイル内容/エラー時...FALSE
	 */
	function make_dir ($dir, $name_encoding = '') {
		// フォルダが存在しない場合、ディレクトリの作成
		$dir_array = explode("/",  $dir);
		$dest_dir = $this->zip_work_dir;
		foreach ($dir_array as $dir) {
			if ($dir != '') {
				$dir = $name_encoding != '' ?
						mb_convert_encoding($dir, $name_encoding) : $dir;
				$dest_dir .= '/' . $dir;
				ACSLib::make_dir($dest_dir);
			}
		}
	}

	/**
	 * ファイルの配置(ディレクトリの自動作成)
	 *
	 * @param string $from_file コピー元ファイルのパス
	 * @param string $dest_file コピー先ファイルのパス(zip_work_dir からのパス)
	 * @param string $name_encoding 日本語ファイル・フォルダ名のエンコーディング
	 * @return mixed 正常時...ファイル内容/エラー時...FALSE
	 */
	function entry ($from_file, $dest_file, $name_encoding = '') {

		$dest_file = mb_ereg_replace( '^/', '', $dest_file);

		// コピー先のフォルダが存在しない場合、ディレクトリの作成
		$this->make_dir(dirname($dest_file), $name_encoding);

		if ($name_encoding != '') {
			$dest_file = mb_convert_encoding($dest_file, $name_encoding);
		}
		return @copy($from_file, $this->zip_work_dir . '/' . $dest_file);
	}

	/**
	 * ワークディレクトリの圧縮を実行し、zipファイルを作成
	 */
	function commpress () {
		$commpress = $this->zip_work_dir;
		$commpress_cmd = sprintf(_ACSZIP_ZIP_CMD, 
				dirname($this->zip_work_dir),
				basename($this->zip_file),
				basename($this->zip_work_dir) . '/');

		return exec($commpress_cmd);
	}

	/**
	 * zipファイルの取得
	 * 
	 * @param string $attachement_file_name ダウンロード時のファイル名を指定する場合
	 */
	function download ($attachement_file_name = '') {

		$file_name = $attachement_file_name=='' ? 
				basename($this->zip_file) : $attachement_file_name;

		// HTTPヘッダ出力
		mb_http_output('pass');
		header("Cache-Control: public, max-age=0");
		header("Pragma:");
		header('Content-disposition: attachment; filename="'.$file_name.'"');
		header("Content-type: application/zip");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 

		// ファイルを読み出す
		readfile($this->zip_file);
	}
}

?>
