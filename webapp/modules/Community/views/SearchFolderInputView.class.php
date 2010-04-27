<?php
// $Id: SearchFolderView::INPUT.class.php,v 1.3 2006/12/18 07:42:13 w-ota Exp $

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
		$community_row = $request->getAttribute('community_row');
		$form = $request->getAttribute('form');
		$folder_row_array = $request->getAttribute('folder_row_array');
		$put_folder_row_array = $request->getAttribute('put_folder_row_array');
		$file_info_row_array = $request->getAttribute('file_info_row_array');
		$put_file_info_row_array = $request->getAttribute('put_file_info_row_array');

		// 加工
		if (is_array($folder_row_array)) {
			foreach ($folder_row_array as $index => $folder_row) {
				$folder_row_array[$index]['path'] = '/' . implode('/', $folder_row['path_array']);
				$folder_row_array[$index]['folder_url'] = $this->getControllerPath('Community', 'Folder')
					 . '&community_id=' . $community_row['community_id']
					 . '&folder_id=' . $folder_row['folder_id'];
			}
		}

		if (is_array($put_folder_row_array)) {
			foreach ($put_folder_row_array as $index => $folder_row) {
				$put_folder_row_array[$index]['path'] = '/' . implode('/', $folder_row['path_array']);
				$put_folder_row_array[$index]['folder_url'] = $this->getControllerPath('Community', 'Folder')
					 . '&community_id=' . $community_row['community_id']
					 . '&folder_id=' . $folder_row['folder_id'];
			}
		}

		if (is_array($file_info_row_array)) {
			foreach ($file_info_row_array as $index => $file_info_row) {
				$file_info_row_array[$index]['path'] = '/' . implode('/', $file_info_row['path_array']);
				$file_info_row_array[$index]['download_file_url'] = $this->getControllerPath('Community', 'DownloadFile')
					 . '&community_id=' . $community_row['community_id']
					 . '&file_id=' . $file_info_row['file_id']
					 . '&folder_id=' . $file_info_row['folder_id'];
			}
		}

		if (is_array($put_file_info_row_array)) {
			foreach ($put_file_info_row_array as $index => $file_info_row) {
				$put_file_info_row_array[$index]['path'] = '/' . implode('/', $file_info_row['path_array']);
				$put_file_info_row_array[$index]['download_file_url'] = $this->getControllerPath('Community', 'DownloadFile')
					 . '&community_id=' . $community_row['community_id']
					 . '&file_id=' . $file_info_row['file_id']
					 . '&folder_id=' . $file_info_row['folder_id'];
			}
		}

		// コミュニティメンバかどうか
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_row['community_id']);

		// URL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];
		$folder_url = $this->getControllerPath('Community', 'Folder') . '&community_id=' . $community_row['community_id'];
		$action_url = $this->getControllerPath();


		//---- アクセス制御 ----//
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row);
		if (is_array($folder_row_array)) {
			$folder_row_array = ACSAccessControl::get_valid_row_array_for_community($acs_user_info_row, $role_array, $folder_row_array);
		}
		if (is_array($put_folder_row_array)) {
			$put_folder_row_array = ACSAccessControl::get_valid_row_array_for_community($acs_user_info_row, $role_array, $put_folder_row_array);
		}
		if (is_array($file_info_row_array)) {
			$file_info_row_array = ACSAccessControl::get_valid_row_array_for_community($acs_user_info_row, $role_array, $file_info_row_array);

			// 本人以外はis_root_folderのファイルを閲覧できない
			$_file_info_row_array = array();
			foreach ($file_info_row_array as $index => $file_info_row) {
				if (!$is_community_member && $file_info_row['is_root_folder']) {
					continue;
				} else {
					array_push($_file_info_row_array, $file_info_row);
				}
			}
			$file_info_row_array = $_file_info_row_array;
		}
		if (is_array($put_file_info_row_array)) {
			$put_file_info_row_array = ACSAccessControl::get_valid_row_array_for_community($acs_user_info_row, $role_array, $put_file_info_row_array);
		}

		//----------------------//

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SearchFolder.tpl.php');

		// set
		$this->setAttribute('community_row', $community_row);

		$this->setAttribute('form', $form);
		$this->setAttribute('folder_row_array', $folder_row_array);
		$this->setAttribute('put_folder_row_array', $put_folder_row_array);
		$this->setAttribute('file_info_row_array', $file_info_row_array);
		$this->setAttribute('put_file_info_row_array', $put_file_info_row_array);

		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('folder_url', $folder_url);

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('module', 'Community');
		$this->setAttribute('action', 'SearchFolder');

		return parent::execute();
	}
}

?>


