<?php
/**
 * フォルダ 削除
 *
 * @author  kuwayama
 * @version $Revision: 1.2 $ $Date: 2006/03/20 10:45:19 $
 */
require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class DeleteFolderSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// get
		$target_community_info_row = $request->getAttribute('target_community_info_row');
		$community_folder_obj = $request->getAttribute('community_folder_obj');

		// フォルダの所有者
		$target_community_id   = $target_community_info_row['community_id'];

		$target_community_info = '&community_id=' . $target_community_id;
		$folder_info      = '&folder_id=' . $community_folder_obj->folder_obj->get_folder_id();

		$action_url = "";
		$action_url  = $this->getControllerPath('Community', 'DeleteFolder');
		$action_url .= $target_community_info;
		$action_url .= $folder_info;
		$action_url .= "&action_type=delete";

		$cancel_url = "";
		$cancel_url  = $this->getControllerPath('Community', 'Folder');
		$cancel_url .= $target_community_info;
		$cancel_url .= $folder_info;

		// 名前変更対象のフォルダ
		$folder_row_array = array();
		$selected_folder_obj_array = $request->getAttribute('selected_folder_obj_array');
		if ($selected_folder_obj_array) {
			foreach ($selected_folder_obj_array as $selected_folder_obj) {
				$_folder_row = array();

				$_folder_row['folder_name'] = $selected_folder_obj->get_folder_name();
				$_folder_row['folder_id'] = $selected_folder_obj->get_folder_id();
				array_push($folder_row_array, $_folder_row);
			}
		}

		// 名前変更対象のファイル
		$file_row_array = array();
		$selected_file_obj_array = $request->getAttribute('selected_file_obj_array');
		if ($selected_file_obj_array) {
			foreach ($selected_file_obj_array as $selected_file_obj) {
				$_file_row = array();

				$_file_row['file_name'] = $selected_file_obj->get_display_file_name();
				$_file_row['file_id'] = $selected_file_obj->get_file_id();
				array_push($file_row_array, $_file_row);
			}
		}

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DeleteFolder.tpl.php');

		// set
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('cancel_url', $cancel_url);

		// 名前変更対象
		$this->setAttribute('folder_row_array', $folder_row_array);
		$this->setAttribute('file_row_array', $file_row_array);

		return parent::execute();
	}
}
?>
