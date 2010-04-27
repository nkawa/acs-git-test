<?php
// $Id: SearchFolderView::INPUT.class.php,v 1.2 2006/03/21 08:25:10 w-ota Exp $

class SearchFolderInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		// ユーザ情報一覧
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$form = $request->getAttribute('form');
		$folder_row_array = $request->getAttribute('folder_row_array');
		$file_info_row_array = $request->getAttribute('file_info_row_array');

		// 加工
		if (is_array($folder_row_array)) {
			foreach ($folder_row_array as $index => $folder_row) {
				$folder_row_array[$index]['path'] = '/' . implode('/', $folder_row['path_array']);
				$folder_row_array[$index]['folder_url'] = $this->getControllerPath('User', 'Folder')
					 . '&id=' . $target_user_info_row['user_community_id']
					 . '&folder_id=' . $folder_row['folder_id'];
			}
		}

		if (is_array($file_info_row_array)) {
			foreach ($file_info_row_array as $index => $file_info_row) {
				$file_info_row_array[$index]['path'] = '/' . implode('/', $file_info_row['path_array']);
				$file_info_row_array[$index]['download_file_url'] = $this->getControllerPath('User', 'DownloadFile')
					 . '&id=' . $target_user_info_row['user_community_id']
					 . '&file_id=' . $file_info_row['file_id']
					 . '&folder_id=' . $file_info_row['folder_id'];
			}
		}

		// 本人かどうか
		$is_self_page = ($target_user_info_row['user_community_id'] == $acs_user_info_row['user_community_id'])
			 ? true : false;

		// URL
		$folder_url = $this->getControllerPath('User', 'Folder') . '&id=' . $target_user_info_row['user_community_id'];
		$action_url = $this->getControllerPath();


		//---- アクセス制御 ----//
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		if (is_array($folder_row_array)) {
			$folder_row_array = ACSAccessControl::get_valid_row_array_for_user_community($acs_user_info_row, $role_array, $folder_row_array);
		}
		if (is_array($file_info_row_array)) {
			$file_info_row_array = ACSAccessControl::get_valid_row_array_for_user_community($acs_user_info_row, $role_array, $file_info_row_array);
			$_file_info_row_array = array();

			// 本人以外はis_root_folderのファイルを閲覧できない
			foreach ($file_info_row_array as $index => $file_info_row) {
				if (!$is_self_page && $file_info_row['is_root_folder']) {
					continue;
				} else {
					array_push($_file_info_row_array, $file_info_row);
				}
			}
			$file_info_row_array = $_file_info_row_array;
		}
		//----------------------//


		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SearchFolder.tpl.php');

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);

		$this->setAttribute('form', $form);
		$this->setAttribute('folder_row_array', $folder_row_array);
		$this->setAttribute('file_info_row_array', $file_info_row_array);

		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('folder_url', $folder_url);

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('module', 'User');
		$this->setAttribute('action', 'SearchFolder');

		return parent::execute();
	}
}

?>


