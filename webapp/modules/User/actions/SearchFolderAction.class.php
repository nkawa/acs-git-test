<?php
// $Id: SearchFolderAction.class.php,v 1.3 2006/12/18 07:42:15 w-ota Exp $

class SearchFolderAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$user_community_id = $request->ACSgetParameter('id');
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($request->getParameter('id'));

		$form = $request->ACSgetParameters();

		// 検索時
		if ($form['search']) {
			$folder_row_array = array();
			$file_info_row_array = array();

			// フォルダ検索
			if ($form['target'] != 'file') {
				$folder_row_array = ACSUserFolder::search_folder_row_array($user_community_id, $form);
			}
			// ファイル検索
			if ($form['target'] != 'folder') {
				$file_info_row_array = ACSUserFolder::search_file_info_row_array($user_community_id, $form);
			}

			foreach ($folder_row_array as $index => $folder_row) {
				$target_folder_obj = new ACSUserFolder($user_community_id, $acs_user_info_row, $folder_row['folder_id']);
				$folder_row_array[$index]['update_date'] = $target_folder_obj->folder_obj->get_update_date_yyyymmddhmi();

				// 公開レベル
				$folder_row_array[$index]['open_level_code'] = $target_folder_obj->folder_obj->get_open_level_code();
				$folder_row_array[$index]['open_level_name'] = $target_folder_obj->folder_obj->get_open_level_name();
				$open_level_master_row = ACSAccessControl::get_open_level_master_row($folder_row_array[$index]['open_level_code']);
				$folder_row_array[$index] = array_merge($folder_row_array[$index], $open_level_master_row);
				$folder_row_array[$index]['trusted_community_row_array'] = $target_folder_obj->folder_obj->get_trusted_community_row_array();

				// パス
				$path_folder_obj_array = $target_folder_obj->get_path_folder_obj_array();
				$path_array = array();
				foreach ($path_folder_obj_array as $path_folder_obj_index => $path_folder_obj) {
					if ($path_folder_obj_index != 0) {
						array_push($path_array, $path_folder_obj->get_folder_name());
					}
				}
				$folder_row_array[$index]['path_array'] = $path_array;
			}

			foreach ($file_info_row_array as $index => $file_info_row) {

				$target_folder_obj = new ACSUserFolder($user_community_id, $acs_user_info_row, $file_info_row['folder_id']);
				$target_file_obj = new ACSFile($file_info_row);

				$file_info_row_array[$index]['file_size'] = $target_file_obj->get_file_size_kb();
				$file_info_row_array[$index]['update_date'] = $target_file_obj->get_update_date_yyyymmddhmi();
				$file_info_row_array[$index]['is_root_folder'] = $target_folder_obj->folder_obj->get_is_root_folder();

				// 公開レベル
				$file_info_row_array[$index]['open_level_code'] = $target_folder_obj->folder_obj->get_open_level_code();
				$file_info_row_array[$index]['open_level_name'] = $target_folder_obj->folder_obj->get_open_level_name();
				$open_level_master_row = ACSAccessControl::get_open_level_master_row($file_info_row_array[$index]['open_level_code']);
				$file_info_row_array[$index] = array_merge($file_info_row_array[$index], $open_level_master_row);
				$file_info_row_array[$index]['trusted_community_row_array'] = $target_folder_obj->folder_obj->get_trusted_community_row_array();

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
		}

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('form', $form);
		$request->setAttribute('folder_row_array', $folder_row_array);
		$request->setAttribute('file_info_row_array', $file_info_row_array);

		return View::INPUT;
	}

	function isSecure () {
		return false;
	}

}

?>
