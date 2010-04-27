<?php
/**
 * �ޥ��ڡ�����ǽ��action���饹
 * �ǥ���������
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

		// �����оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $acs_user_info_row['user_community_id'];

		// ����ǥ��쥯�ȥ�κ���(¸�ߤ��ʤ����)
		$work_dir = ACS_CONTENTS_BACKUP_DIR;
		ACSLib::make_dir($work_dir);

		$work_dir .= $user_community_id . '/';
		ACSLib::make_dir($work_dir);

		$work_dir .= ACS_BACKUP_ZIP_DIR_NAME;
		ACSLib::make_dir($work_dir);


		// �Хå����å���ZIP���饹������
		$zip = new ACSZip($work_dir);

		// Folder, Diary �ǥ��쥯�ȥ��������Ƥ���(0���б�)
		ACSLib::make_dir($work_dir . '/' . ACS_BACKUP_MYFOLDER_SUBDIR_NAME);
		ACSLib::make_dir($work_dir . '/' . ACS_BACKUP_MYDIARY_SUBDIR_NAME);

		// ----- �ޥ��ե�����Хå����åץ���ƥ�Ĥ�����
		// �ե���������Ѥ����������
		$form = array('q'=>'', 'order'=>'name');

		// �ե�����μ���
		$folder_row_array = ACSUserFolder::search_folder_row_array($user_community_id, $form);

		// �ѥ����������
		foreach ($folder_row_array as $index => $folder_row) {
			$target_folder_obj = new ACSUserFolder(
					$user_community_id, $acs_user_info_row, $folder_row['folder_id']);
			// �ѥ�
			$path_folder_obj_array = $target_folder_obj->get_path_folder_obj_array();
			$path_array = array();
			foreach ($path_folder_obj_array as $path_folder_obj_index => $path_folder_obj) {
				if ($path_folder_obj_index != 0) {
					array_push($path_array, $path_folder_obj->get_folder_name());
				}
			}
			// �ǥ��쥯�ȥ�����(���ǥ��쥯�ȥ��б�)
			$zip->make_dir(ACS_BACKUP_MYFOLDER_SUBDIR_NAME . '/' .
					implode("/",$path_array), ACS_BACKUP_NAME_ENCODING);
		}

		// �ե�����μ���
		$file_info_row_array = ACSUserFolder::search_file_info_row_array($user_community_id, $form);

		// �ѥ����������
		foreach ($file_info_row_array as $index => $file_info_row) {

			$target_folder_obj = new ACSUserFolder(
					$user_community_id, $acs_user_info_row, $file_info_row['folder_id']);
			// �ѥ�
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

		// �ޥ��ե�����Υե���������ǥե����������
		$dest_path_array = array();
		foreach ($file_info_row_array as $file_info_row) {
			$from_path = ACS_FOLDER_DIR . $file_info_row['server_file_name'];
			$dest_path = ACS_BACKUP_MYFOLDER_SUBDIR_NAME .'/'. implode("/", $file_info_row['path_array']);

			// Ʊ��̾�ե��������Ϣ���ղ��б�
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

		// ----- �ޥ���������Хå����åץ���ƥ�Ĥ�����
		$diary_backup = new ACSDiaryBackup($user_community_id, $work_dir . '/' . ACS_BACKUP_MYDIARY_SUBDIR_NAME );
		$diary_backup->make_contents(ACS_BACKUP_NAME_ENCODING);

		// ��������ɻ�ZIP�ե�����̾������
		$download_filename = 'ACSBackup_' . date('Ymd', time()) . '.zip';

		// �Хå����å�zip���������֤κ���(zip���̤μ¹�)
		$zip->commpress();
		$zip->download($download_filename);

		// ��ɬ�פʥ���ե�����κ��
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
