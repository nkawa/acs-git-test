<?php
/**
 * コミュニティのフォルダ表示
 *
 * @author  kuwayama
 * @version $Revision: 1.20 $ $Date: 2006/12/08 05:06:37 $
 */
class FolderSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row    = $user->getAttribute('acs_user_info_row');
		$target_community_row = $request->getAttribute('target_community_row');
		$community_folder_obj = $request->getAttribute('community_folder_obj');
		$target_folder_obj    = $community_folder_obj->get_folder_obj();
		$folder_obj_array     = $target_folder_obj->get_folder_obj_array();
		$file_obj_array       = $target_folder_obj->get_file_obj_array();

		$mode = $request->getAttribute('mode');
		if ($mode == 'group') {
			$file_detail_info_row_array = $request->getAttribute('file_detail_info_row_array');
			$file_contents_type_master_row_array_array = $request->getAttribute('file_contents_type_master_row_array_array');
		}


		// URL に付加する target_community
		$target_community_info = '&community_id=' . $target_community_row['community_id'];
		// URL に付加する target_folder
		$target_folder_info = '&folder_id=' . $target_folder_obj->get_folder_id();

		// コミュニティのURL
		$community_top_page_url  = $this->getControllerPath('Community', 'Index');
		$community_top_page_url .= $target_community_info;

		// フォルダパス情報
		$path_folder_obj_array = $community_folder_obj->get_path_folder_obj_array();
		// 表示用に加工
		$path_folder_obj_row_array = $this->make_display_folder_row_array($path_folder_obj_array, $target_community_info, $target_folder_info, $target_community_row, $mode, $controller);

		// フォルダの公開範囲
		$target_folder_open_level_row['name'] = $target_folder_obj->get_open_level_name();
		$target_folder_open_level_row['trusted_community_row_array'] = $this->make_display_trusted_community_row_array($target_folder_obj->get_trusted_community_row_array(), &$controller);

		// コミュニティメンバかどうか
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'],
																 $target_community_row['community_id']);

		// ルートフォルダかどうか
		$is_root_folder = $target_folder_obj->get_is_root_folder();

		// プットフォルダかどうか
		$is_put_folder = $target_folder_obj->is_put_folder($target_community_row['community_id']);

		// ファイルアップロード URL
		$upload_file_url = $this->getControllerPath('Community',
														  'UploadFile');
		$upload_file_url .= $target_community_info;
		$upload_file_url .= $target_folder_info;

		/* フォルダ・ファイル一覧 */
		// フォルダ一覧
		$display_folder_obj_array = $target_folder_obj->get_display_folder_obj_array_for_community($acs_user_info_row, $target_community_row);
		$folder_row_array = $this->make_display_folder_row_array($display_folder_obj_array, $target_community_info, $target_folder_info, $target_community_row, $mode, $controller);


		if ($mode == 'group') {
			// グループ表示 //
			// ファイルカテゴリマスタ
			$file_category_master_array = ACSDB::get_master_array('file_category');

			// 指定なしのファイルカテゴリコード
			$default_file_category_code = array_search(ACSMsg::get_mst('file_category_master','D0000'), $file_category_master_array);

			$file_detail_info_row_array_array = array();
			foreach ($file_category_master_array as $file_category_code => $file_category_name) {
				$file_detail_info_row_array_array[$file_category_code] = array();
				$file_detail_info_row_array_array[$file_category_code]['file_category_code'] = $file_category_code;
				$file_detail_info_row_array_array[$file_category_code]['file_category_name'] = $file_category_name;
				$file_detail_info_row_array_array[$file_category_code]['file_detail_info_row_array'] = array();
			}


			// Actionで取得した$file_detail_info_rowの配列を、ファイル種類コードごとの配列に振り分ける
			foreach ($file_detail_info_row_array as $file_detail_info_row) {
				// ファイルカテゴリコード
				$file_category_code = $file_detail_info_row['file_category_code'];

				// リンクURL
				$file_detail_info_row['link_url'] = $this->getControllerPath('Community', 'DownloadFile')
					 . $target_community_info . '&file_id=' . $file_detail_info_row['file_id'] . $target_folder_info;
				// ファイル詳細情報URL
				$file_detail_info_row['file_detail_url'] = $this->getControllerPath('Community', 'FileDetail')
					 . $target_community_info . '&file_id=' . $file_detail_info_row['file_id'] . $target_folder_info;
				// サムネイル画像URL
				if ($file_category_master_array[$file_category_code] == ACSMsg::get_mst('file_category_master','D0003')) {
					$file_detail_info_row['image_url'] = $this->getControllerPath('Community', 'DownloadFile')
						 . $target_community_info . '&file_id=' . $file_detail_info_row['file_id'] . $target_folder_info . '&mode=thumb';
				}

				// push
				if ($file_category_code == '') {
					// ファイルカテゴリコードが存在しない場合はデフォルト(指定なし)扱い
					$file_category_code = $default_file_category_code;
				}
				array_push($file_detail_info_row_array_array[$file_category_code]['file_detail_info_row_array'], $file_detail_info_row);
			}

			// フォルダ 通常表示URL
			$folder_url  = $this->getControllerPath('Community', 'Folder');
			$folder_url .= $target_community_info;
			$folder_url .= $target_folder_info;

		} else {
			// ファイル一覧
			$file_row_array = array();
			foreach ($file_obj_array as $file_obj) {
				$a_file = array();
				$link_url = "";
				$update_user_community_link_url = "";

				$link_url  = $this->getControllerPath('Community',
															'DownloadFile');
				$link_url .= $target_community_info;
				$link_url .= "&file_id=" . $file_obj->get_file_id();
				$link_url .= $target_folder_info;

				$update_user_community_link_url  = $this->getControllerPath('User', 'Index');
				$update_user_community_link_url .= "&id=" . $file_obj->get_update_user_community_id();

				$a_file['name'] = $file_obj->get_display_file_name();
				$a_file['file_id'] = $file_obj->get_file_id();
				$a_file['link_url'] = $link_url;
				$a_file['file_size'] = $file_obj->get_file_size_kb();
				$a_file['update_user_community_name'] = $file_obj->get_update_user_community_name();
				$a_file['update_user_community_link_url'] = $update_user_community_link_url;
				$a_file['update_date'] = $file_obj->get_update_date_yyyymmddhmi();

				// ファイル詳細情報URL
				$a_file['detail_url']  = $this->getControllerPath('Community', 'FileDetail');
				$a_file['detail_url'] .= $target_community_info;
				$a_file['detail_url'] .= "&file_id=" . $file_obj->get_file_id();
				$a_file['detail_url'] .= $target_folder_info;

				// プットファイルかどうか
				if ($file_obj->get_owner_community_id() == $target_community_row['community_id']) {
					$a_file['is_put'] = false;
				} else {
					$a_file['is_put'] = true;
				}

				array_push($file_row_array, $a_file);
			}

			// フォルダ グループ表示URL
			$folder_group_mode_url  = $this->getControllerPath('Community', 'Folder');
			$folder_group_mode_url .= $target_community_info;
			$folder_group_mode_url .= $target_folder_info;
			$folder_group_mode_url .= '&mode=group';
		}

		// ルートフォルダのファイルは、非公開として扱う
		if ((!$is_community_member) && $is_root_folder) {
			$file_row_array = array();
			$file_detail_info_row_array_array = array();
		}

		// フォルダ作成 URL
		if (!$is_put_folder) {
			$edit_folder_url  = $this->getControllerPath('Community', 'EditFolder');
			$edit_folder_url .= $target_community_info;
			$edit_folder_url .= $target_folder_info;
		} else {
			$edit_folder_url = "";
		}

		// 名前変更URL
		$rename_folder_url = "";
		$rename_folder_url  = $this->getControllerPath('Community', 'RenameFolderList');
		$rename_folder_url .= $target_community_info;
		$rename_folder_url .= $target_folder_info;

		// 削除URL
		$delete_folder_url = "";
		$delete_folder_url  = $this->getControllerPath('Community', 'DeleteFolder');
		$delete_folder_url .= $target_community_info;
		$delete_folder_url .= $target_folder_info;
		$delete_folder_url .= "&action_type=confirm";  // 遷移する先の画面は確認画面

		// 移動URL
		$move_folder_url = "";
		$move_folder_url  = $this->getControllerPath('Community', 'MoveFolderList');
		$move_folder_url .= $target_community_info;
		$move_folder_url .= $target_folder_info;

		// コミュニティフォルダ検索URL
		$search_folder_url  = $this->getControllerPath('Community', 'SearchFolder');
		$search_folder_url .= $target_community_info;

		$this->setAttribute('target_folder_open_level_row', $target_folder_open_level_row);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('is_root_folder', $is_root_folder);
		$this->setAttribute('is_put_folder', $is_put_folder);
		$this->setAttribute('upload_file_url', $upload_file_url);
		$this->setAttribute('folder_put_community_url', $folder_put_community_url);

		$this->setAttribute('target_user_community_name', $target_community_row['community_name']);

		$this->setAttribute('path_folder_obj_row_array', $path_folder_obj_row_array);
		$this->setAttribute('folder_row_array', $folder_row_array);
		$this->setAttribute('file_row_array', $file_row_array);

		// 操作メニュー
		$this->setAttribute('edit_folder_url', $edit_folder_url);
		$this->setAttribute('rename_folder_url', $rename_folder_url);
		$this->setAttribute('delete_folder_url', $delete_folder_url);
		$this->setAttribute('move_folder_url', $move_folder_url);
		$this->setAttribute('search_folder_url', $search_folder_url);

		$this->setAttribute('mode', $mode);
		if ($mode == 'group') {
			$this->setAttribute('folder_url', $folder_url);
			$this->setAttribute('file_detail_info_row_array_array', $file_detail_info_row_array_array);
			$this->setAttribute('file_contents_type_master_row_array_array', $file_contents_type_master_row_array_array);
		} else {
			$this->setAttribute('folder_group_mode_url', $folder_group_mode_url);
		}

		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// テンプレート
		if ($mode == 'group') {
			$this->setScreenId("0001");
			$this->setTemplate('Folder_group.tpl.php');
		} else {
			$this->setScreenId("0001");
			$this->setTemplate('Folder.tpl.php');
		}

		return parent::execute();
	}

	function make_display_folder_row_array ($folder_obj_array,
											$target_community_info,
											$target_folder_info,
											$target_community_row,
											$mode,
											&$controller) {
		$folder_row_array = array();
		foreach ($folder_obj_array as $folder_obj) {
			$folder_row = array();
			$link_url = "";
			$name     = "";
			$update_user_community_link_url = "";

			$link_url  = $this->getControllerPath('Community', 'Folder');
			$link_url .= $target_community_info;
			$link_url .= "&folder_id=" . $folder_obj->get_folder_id();
			if ($mode == 'group') {
				$link_url .= '&mode=' . $mode;
			}


			$update_user_community_link_url  = $this->getControllerPath('User', 'Index');
			$update_user_community_link_url .= "&id=" . $folder_obj->get_update_user_community_id();

			// フォルダ詳細 URL
			$detail_url  = $this->getControllerPath('Community', 'FolderDetail');
			$detail_url .= $target_community_info;
			$detail_url .= $target_folder_info;
			$detail_url .= "&detail_folder_id=" . $folder_obj->get_folder_id();

			if ($folder_obj->get_is_root_folder()) {
				$name  = $target_community_row['community_name'];
				//$name .= "のフォルダ";
				$name = ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'FolderSuccessView.class.php', 'FOLDER_NM'),
					array("{COMMUNITY_NAME}" => $target_community_row['community_name']));
			} else {
				$name = $folder_obj->get_folder_name();
			}

			// 閲覧許可コミュニティ名作成
			$trusted_community_row_array = $folder_obj->get_trusted_community_row_array();
			$new_trusted_community_row_array = $this->make_display_trusted_community_row_array($trusted_community_row_array, &$controller);

			$folder_row['name'] = $name;
			$folder_row['folder_id'] = $folder_obj->get_folder_id();
			$folder_row['link_url'] = $link_url;
			$folder_row['open_level_name'] = $folder_obj->get_open_level_name();
			$folder_row['trusted_community_row_array'] = $new_trusted_community_row_array;
			$folder_row['update_user_community_name'] = $folder_obj->get_update_user_community_name();
			$folder_row['update_user_community_link_url'] = $update_user_community_link_url;
			$folder_row['update_date'] = $folder_obj->get_update_date_yyyymmddhmi();

			$folder_row['detail_url'] = $detail_url;

			// プットフォルダかどうか
			$folder_row['is_put'] = $folder_obj->is_put_folder($target_community_row['community_id']);

			array_push($folder_row_array, $folder_row);
		}
		return $folder_row_array;
	}

	function make_display_trusted_community_row_array ($trusted_community_row_array, &$controller) {
		$new_trusted_community_row_array = array();
		foreach ($trusted_community_row_array as $trusted_community_row) {
			$new_trusted_community_row = array();
			$community_top_page_url    = "";

			$community_top_page_url  = $this->getControllerPath('Community', 'Index');
			$community_top_page_url .= "&community_id=" . $trusted_community_row['community_id'];

			$new_trusted_community_row['community_name'] = $trusted_community_row['community_name'];
			$new_trusted_community_row['community_top_page_url'] = $community_top_page_url;

			array_push($new_trusted_community_row_array, $new_trusted_community_row);
		}

		return $new_trusted_community_row_array;
	}
}
?>
