<?php
// $Id: SearchFolderAction.class.php,v 1.5 2006/12/18 07:42:11 w-ota Exp $

class SearchFolderAction extends BaseAction
{
	function execute() {
	    $context = $this->getContext();
	    $controller = $context->getController();
	    $request =  $context->getRequest();
	    $user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_id = $request->ACSgetParameter('community_id');
		$community_row = ACSCommunity::get_community_row($community_id);

		$form = $request->ACSgetParameters();

		// 検索時
		if ($form['search']) {
			$folder_row_array = array();
			$put_folder_row_array = array();
			$file_info_row_array = array();
			$put_file_info_row_array = array();

			// フォルダ検索
			if ($form['target'] != 'file') {
				$folder_row_array = ACSCommunityFolder::search_folder_row_array($community_id, $form);
				$put_folder_row_array = ACSCommunityFolder::search_put_folder_row_array($community_id, $form);
			}
			// ファイル検索
			if ($form['target'] != 'folder') {
				$file_info_row_array = ACSCommunityFolder::search_file_info_row_array($community_id, $form);
				$put_file_info_row_array = ACSCommunityFolder::search_put_file_info_row_array($community_id, $form);
			}

			// フォルダ
			foreach ($folder_row_array as $index => $folder_row) {
				$target_folder_obj = new ACSCommunityFolder($community_id, $acs_user_info_row, $folder_row['folder_id']);
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

			// プットフォルダ
			foreach ($put_folder_row_array as $index => $folder_row) {
				$target_folder_obj = new ACSCommunityFolder($community_id, $acs_user_info_row, $folder_row['folder_id']);
				$put_folder_row_array[$index]['update_date'] = $target_folder_obj->folder_obj->get_update_date_yyyymmddhmi();

				// 公開レベル
				$put_folder_row_array[$index]['open_level_code'] = $target_folder_obj->folder_obj->get_open_level_code();
				$put_folder_row_array[$index]['open_level_name'] = $target_folder_obj->folder_obj->get_open_level_name();
				$open_level_master_row = ACSAccessControl::get_open_level_master_row($put_folder_row_array[$index]['open_level_code']);
				$put_folder_row_array[$index] = array_merge($put_folder_row_array[$index], $open_level_master_row);
				$put_folder_row_array[$index]['trusted_community_row_array'] = $target_folder_obj->folder_obj->get_trusted_community_row_array();

				// パス
				$path_folder_obj_array = $target_folder_obj->get_path_folder_obj_array();
				$path_array = array();
				foreach ($path_folder_obj_array as $path_folder_obj_index => $path_folder_obj) {
					if ($path_folder_obj_index != 0) {
						array_push($path_array, $path_folder_obj->get_folder_name());
					}
				}
				$put_folder_row_array[$index]['path_array'] = $path_array;
			}

			// ファイル
			foreach ($file_info_row_array as $index => $file_info_row) {
				$target_folder_obj = new ACSCommunityFolder($community_id, $acs_user_info_row, $file_info_row['folder_id']);
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
			// プットファイル
			foreach ($put_file_info_row_array as $index => $file_info_row) {
				// プットされたフォルダの直下に位置するプットファイルのfolder_idは
				// プットされたフォルダのfolder_idに変換する
				if ($file_info_row['put_community_folder_id'] != '') {
					$file_info_row['folder_id'] = $file_info_row['put_community_folder_id'];
					$put_file_info_row_array[$index] = $file_info_row;
				}
				$target_folder_obj = new ACSCommunityFolder($community_id, $acs_user_info_row, $file_info_row['folder_id']);
				$target_file_obj = new ACSFile($file_info_row);

				$put_file_info_row_array[$index]['file_size'] = $target_file_obj->get_file_size_kb();
				$put_file_info_row_array[$index]['update_date'] = $target_file_obj->get_update_date_yyyymmddhmi();
				$put_file_info_row_array[$index]['is_root_folder'] = $target_folder_obj->folder_obj->get_is_root_folder();

				// 公開レベル
				$put_file_info_row_array[$index]['open_level_code'] = $target_folder_obj->folder_obj->get_open_level_code();
				$put_file_info_row_array[$index]['open_level_name'] = $target_folder_obj->folder_obj->get_open_level_name();
				$open_level_master_row = ACSAccessControl::get_open_level_master_row($put_file_info_row_array[$index]['open_level_code']);
				$put_file_info_row_array[$index] = array_merge($put_file_info_row_array[$index], $open_level_master_row);
				$put_file_info_row_array[$index]['trusted_community_row_array'] = $target_folder_obj->folder_obj->get_trusted_community_row_array();

				// パス
				$path_folder_obj_array = $target_folder_obj->get_path_folder_obj_array();
				$path_array = array();
				foreach ($path_folder_obj_array as $path_folder_obj_index => $path_folder_obj) {
					if ($path_folder_obj_index != 0) {
						array_push($path_array, $path_folder_obj->get_folder_name());
					}
				}
				array_push($path_array, $file_info_row['display_file_name']);
				$put_file_info_row_array[$index]['path_array'] = $path_array;
			}
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('form', $form);
		$request->setAttribute('folder_row_array', $folder_row_array);
		$request->setAttribute('put_folder_row_array', $put_folder_row_array);
		$request->setAttribute('file_info_row_array', $file_info_row_array);
		$request->setAttribute('put_file_info_row_array', $put_file_info_row_array);

		return View::INPUT;
	}

    function isSecure () {
        return false;
    }

	// アクセス制御情報
	function get_access_control_info(&$controller, &$request, &$user) {
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$community_id = $request->getParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// アクセス制御情報 //
		$folder_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D31'));
		$folder_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $folder_contents_row['contents_type_code'], $folder_contents_row['open_level_code']);
		$access_control_info = array(
									 'role_array' => ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row),
									 'contents_row_array' => array($folder_contents_row)
									 );

		return $access_control_info;
	}
}

?>
