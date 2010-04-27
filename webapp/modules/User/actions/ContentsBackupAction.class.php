<?php
/**
 * マイページ機能　actionクラス
 * デザイン選択
 * @package  acs/webapp/modules/User/action
 * SelectDesignAction
 * @author   teramoto
 * @since	PHP 4.0
 */
// $Id: ContentsBackupAction.class.php,v 1.2 2007/03/28 02:26:52 w-ota Exp $

class ContentsBackupAction extends BaseAction
{
	// GET/POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 更新対象となるユーザコミュニティIDを取得
		$user_community_id = $acs_user_info_row['user_community_id'];

		// ワークディレクトリの作成(存在しない場合)
		$work_dir = ACS_CONTENTS_BACKUP_DIR;
		ACSLib::make_dir($work_dir);

		$work_dir .= $user_community_id . '/';
		ACSLib::make_dir($work_dir);

		$work_dir .= ACS_BACKUP_ZIP_DIR_NAME;
		ACSLib::make_dir($work_dir);


		// バックアップ用ZIPクラスの生成
		$zip = new ACSZip($work_dir);

		// Folder, Diary ディレクトリを作成しておく(0件対応)
		ACSLib::make_dir($work_dir . '/' . ACS_BACKUP_MYFOLDER_SUBDIR_NAME);
		ACSLib::make_dir($work_dir . '/' . ACS_BACKUP_MYDIARY_SUBDIR_NAME);

		// ----- マイフォルダバックアップコンテンツの生成
		// フォルダ取得用の配列を設定
		$form = array('q'=>'', 'order'=>'name');

		// フォルダの取得
		$folder_row_array = ACSUserFolder::search_folder_row_array($user_community_id, $form);

		// パス情報の設定
		foreach ($folder_row_array as $index => $folder_row) {
			$target_folder_obj = new ACSUserFolder(
					$user_community_id, $acs_user_info_row, $folder_row['folder_id']);
			// パス
			$path_folder_obj_array = $target_folder_obj->get_path_folder_obj_array();
			$path_array = array();
			foreach ($path_folder_obj_array as $path_folder_obj_index => $path_folder_obj) {
				if ($path_folder_obj_index != 0) {
					array_push($path_array, $path_folder_obj->get_folder_name());
				}
			}
			// ディレクトリを作成(空ディレクトリ対応)
			$zip->make_dir(ACS_BACKUP_MYFOLDER_SUBDIR_NAME . '/' .
					implode("/",$path_array), ACS_BACKUP_NAME_ENCODING);
		}

		// ファイルの取得
		$file_info_row_array = ACSUserFolder::search_file_info_row_array($user_community_id, $form);

		// パス情報の設定
		foreach ($file_info_row_array as $index => $file_info_row) {

			$target_folder_obj = new ACSUserFolder(
					$user_community_id, $acs_user_info_row, $file_info_row['folder_id']);
			// パス
			$path_folder_obj_array = $target_folder_obj->get_path_folder_obj_array();
			$path_array = array();
			foreach ($path_folder_obj_array as $path_folder_obj_index => $path_folder_obj) {
				if ($path_folder_obj_index != 0) {
					array_push($path_array, $path_folder_obj->get_folder_name());
				}
			}
			array_push($path_array, $file_info_row['display_file_name']);
			$file_info_row_array[$index]['path_array'] = $path_array;
		}

		// マイフォルダのフォルダ構成でファイルを配置
		$dest_path_array = array();
		foreach ($file_info_row_array as $file_info_row) {
			$from_path = ACS_FOLDER_DIR . $file_info_row['server_file_name'];
			$dest_path = ACS_BACKUP_MYFOLDER_SUBDIR_NAME .'/'. implode("/", $file_info_row['path_array']);

			// 同一名ファイル時の連番付加対応
			$dest_path_array[$dest_path]++;
			if ($dest_path_array[$dest_path]>1) {
				$count = $dest_path_array[$dest_path];
				mb_ereg('.*(\.[^\.\/]*$)', $dest_path, $matches);
				$ext = $matches[1];
				if ($ext) {
					$dest_path = mb_ereg_replace('\.[^\.\/]*$', '', $dest_path);
				}
				$dest_path .= '_' . ($count-1) . $ext;
			}

			$zip->entry($from_path, $dest_path, ACS_BACKUP_NAME_ENCODING);
		}

		// ----- マイダイアリバックアップコンテンツの生成
		$diary_backup = new ACSDiaryBackup($user_community_id, $work_dir . '/' . ACS_BACKUP_MYDIARY_SUBDIR_NAME );
		$diary_backup->make_contents(ACS_BACKUP_NAME_ENCODING);

		// ダウンロード時ZIPファイル名の生成
		$download_filename = 'ACSBackup_' . date('Ymd', time()) . '.zip';

		// バックアップzipアーカイブの作成(zip圧縮の実行)
		$zip->commpress();
		$zip->download($download_filename);

		// 不必要なワークファイルの削除
		$zip->clear_work_dir_and_files();
	}

	function getRequestMethods() {
		return Request::GET|Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('USER_PAGE_OWNER');
	}
}
?>
