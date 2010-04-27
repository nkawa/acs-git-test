<?php
// $Id: FileDetailView::SUCCESS.class.php,v 1.10 2007/03/28 09:24:55 w-ota Exp $

class FileDetailSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$file_obj = $request->getAttribute('file_obj');
		$user_folder_obj = $request->getAttribute('user_folder_obj');
		$file_detail_info_row = $request->getAttribute('file_detail_info_row');
		$file_history_row_array = $request->getAttribute('file_history_row_array');

		$target_folder_obj = $user_folder_obj->get_folder_obj();
		$target_user_community_id = $target_user_info_row['user_community_id'];

		$footprint_info = $request->getAttribute('footprint_info');

		// URL付加情報（表示するユーザ情報）
		$target_community_info = '&id=' . $target_user_community_id;
		$target_folder_info = '&folder_id=' . $user_folder_obj->folder_obj->get_folder_id();

		// 本人かどうか
		if ($target_user_community_id == $acs_user_info_row['user_community_id']) {
			$is_self_page = true;
		} else {
			$is_self_page = false;
		}

		// ルートフォルダかどうか
		$is_root_folder = $target_folder_obj->get_is_root_folder();

		// ファイル情報 //
		$file_info_row = array();
		$file_info_row['file_id'] = $file_obj->get_file_id();
		$file_info_row['display_file_name'] = $file_obj->get_display_file_name();
		$file_info_row['mime_type'] = $file_obj->get_mime_type();
		$file_info_row['file_size_kb'] = $file_obj->get_file_size_kb();
		$file_info_row['file_size'] = $file_obj->get_file_size();
		$file_info_row['entry_user_community_name'] = $file_obj->get_entry_user_community_name();
		$file_info_row['entry_date'] = $file_obj->get_entry_date_yyyymmddhmi();
		$file_info_row['update_date'] = $file_obj->get_update_date_yyyymmddhmi();
		$file_info_row['update_user_community_name'] = $file_obj->get_update_user_community_name();
		$file_info_row['link_url'] = $this->getControllerPath('User', 'DownloadFile')
			 . $target_community_info . "&file_id=" . $file_obj->get_file_id() . $target_folder_info;
		// 登録者
		$file_info_row['entry_user_community_name']      = $file_obj->get_entry_user_community_name();
		$file_info_row['entry_user_community_link_url']  = $this->getControllerPath('User', DEFAULT_ACTION);
		$file_info_row['entry_user_community_link_url'] .= '&id=' . $file_obj->get_entry_user_community_id();;
		$file_info_row['entry_date']                     = $file_obj->get_entry_date_yyyymmddhmi();
		// 更新者
		$file_info_row['update_user_community_name']      = $file_obj->get_update_user_community_name();
		$file_info_row['update_user_community_link_url']  = $this->getControllerPath('User', DEFAULT_ACTION);
		$file_info_row['update_user_community_link_url'] .= '&id=' . $file_obj->get_update_user_community_id();;
		$file_info_row['update_date']                     = $file_obj->get_update_date_yyyymmddhmi();
		
		// フォルダパス情報 //
		$path_folder_obj_array = $user_folder_obj->get_path_folder_obj_array();
		$path_folder_row_array = array();
		foreach ($path_folder_obj_array as $path_folder_obj) {
			$path_folder_row = array();

			// フォルダ名
			if ($path_folder_obj->get_is_root_folder()) {
				// $folder_name  = $target_user_info_row['community_name'];
				// $folder_name .= "さんのフォルダ";
				$folder_name = ACSMsg::get_tag_replace(ACSMsg::get_msg('User', 'FileDetailSuccessView.class.php' ,'FOLDER_NM'), array(
						"{COMMUNITY_NAME}" => $target_user_info_row['community_name']));

			} else {
				$folder_name = $path_folder_obj->get_folder_name();
			}

			// フォルダURL
			$link_url  = $this->getControllerPath('User', 'Folder');
			$link_url .= $target_community_info;
			$link_url .= "&folder_id=" . $path_folder_obj->get_folder_id();

			// set
			$path_folder_row['folder_name'] = $folder_name;
			$path_folder_row['link_url']    = $link_url;

			array_push($path_folder_row_array, $path_folder_row);
		}


		// フォルダ詳細情報 //
		$detail_folder_obj = $user_folder_obj->get_folder_obj();
		$detail_folder_row = array();
		$detail_folder_row['folder_name'] = $detail_folder_obj->get_folder_name();
		$detail_folder_row['comment']     = $detail_folder_obj->get_comment();
		$detail_folder_row['open_level_name'] = $detail_folder_obj->get_open_level_name();
		// 閲覧許可コミュニティ名作成
		$detail_folder_row['trusted_community_row_array'] = array();
		$trusted_community_row_array = $detail_folder_obj->get_trusted_community_row_array();
		if ($is_self_page) {
			foreach ($trusted_community_row_array as $trusted_community_row) {
				$_trusted_community_row = array();
				$_trusted_community_row['community_name'] = $trusted_community_row['community_name'];

				array_push($detail_folder_row['trusted_community_row_array'], $_trusted_community_row);
			}
		}
		// ルートフォルダのファイルの場合は非公開
		if ($is_root_folder) {
			$detail_folder_row['open_level_name'] = ACSMsg::get_mst('open_level_master','D03');
			$detail_folder_row['trusted_community_row_array'] = array();
		}
		// link url
		$detail_folder_row['link_url'] = $this->getControllerPath('User', 'Folder')
			 . $target_community_info . "&folder_id=" . $detail_folder_obj->get_folder_id();


		// ファイル履歴情報 //
		foreach ($file_history_row_array as $index => $file_history_row) {
			$file_history_row_array[$index]['update_date'] = ACSLib::convert_pg_date_to_str($file_history_row['update_date'], 1, 1);
			$file_history_row_array[$index]['link_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $file_history_row['community_id'];
			$file_history_row_array[$index]['download_history_file_url'] = $this->getControllerPath('User', 'DownloadHistoryFile')
				 . $target_community_info . "&folder_id=" . $detail_folder_obj->get_folder_id() . '&file_id=' . $file_history_row['file_id'] . '&file_history_id=' . $file_history_row['file_history_id'];
			if ($index != 0) {
				$file_history_row_array[$index]['restore_history_file_url'] = $this->getControllerPath('User', 'RestoreHistoryFile')
					 . $target_community_info . "&folder_id=" . $detail_folder_obj->get_folder_id() . '&file_id=' . $file_history_row['file_id'] . '&file_history_id=' . $file_history_row['file_history_id'];
			}
			foreach ($file_history_row['file_history_comment_row_array'] as $index2 => $file_history_comment_row) {
				$file_history_row_array[$index]['file_history_comment_row_array'][$index2]['post_date'] =
					 ACSLib::convert_pg_date_to_str($file_history_comment_row['post_date'], false, true, true);
				$file_history_row_array[$index]['file_history_comment_row_array'][$index2]['link_url'] =
					 $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $file_history_comment_row['community_id'];
			}
		}

		// フォルダURL
		$folder_url = $this->getControllerPath('User', 'Folder') . $target_community_info;

		// 足跡登録URL(自画面) //
		$footprint_url = "";
		if($is_self_page === false && $acs_user_info_row['is_login_user'])	{
			$footprint_url = $this->getControllerPath('User', 'FootprintFileDetail')
			 			. $target_community_info . $target_folder_info . '&file_id=' . $file_info_row['file_id'];
		}

		// ファイル履歴コメントURL //
		$file_history_comment_url = $this->getControllerPath('User', 'FileHistoryComment')
			 . $target_community_info . $target_folder_info . '&file_id=' . $file_info_row['file_id']
			 . '&file_history_id=' . $file_history_row_array[0]['file_history_id'];


		// メニュー //
		if ($is_self_page) {
			// ファイル更新URL
			$menu['update_file_url'] = $this->getControllerPath('User', 'UpdateFile')
				 . $target_community_info . $target_folder_info . '&file_id=' . $file_info_row['file_id'];

			// 名前変更URL
			$menu['rename_folder_list_url'] = $this->getControllerPath('User', 'RenameFolderList')
				 . $target_community_info . $target_folder_info . '&selected_file[]=' . $file_info_row['file_id'];

			// 移動URL
			$menu['move_folder_list_url'] = $this->getControllerPath('User', 'MoveFolderList')
				 . $target_community_info . $target_folder_info . '&selected_file[]=' . $file_info_row['file_id'];

			// 削除URL
			$menu['delete_folder_url'] = $this->getControllerPath('User', 'DeleteFolder')
				 . $target_community_info . $target_folder_info . '&action_type=confirm' . '&selected_file[]=' . $file_info_row['file_id'];

			// 詳細情報編集URL
			$menu['edit_file_detail_url'] = $this->getControllerPath('User', 'EditFileDetail')
				 . $target_community_info . $target_folder_info . '&file_id=' . $file_info_row['file_id'];
		}


		// 戻り先URL（フォルダ一覧）//
		$back_url = "";
		$back_url  = $this->getControllerPath('User', 'Folder');
		$back_url .= $target_community_info;
		$back_url .= $target_folder_info;


		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $file_history_row_array, $display_count);

		// set
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('file_info_row', $file_info_row);
		$this->setAttribute('path_folder_row_array', $path_folder_row_array);
		$this->setAttribute('detail_folder_row', $detail_folder_row);
		$this->setAttribute('file_detail_info_row', $file_detail_info_row);
		$this->setAttribute('file_history_row_array', $file_history_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('menu', $menu);
		$this->setAttribute('folder_url', $folder_url);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('file_history_comment_url', $file_history_comment_url);
		$this->setAttribute('footprint_url', $footprint_url);
		$this->setAttribute('footprint_info', $footprint_info);

		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('FileDetail.tpl.php');

		return parent::execute();
	}
}

?>
