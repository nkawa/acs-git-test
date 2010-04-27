<?php
// $Id: UploadFileView::INPUT.class.php,v 1.1 2006/05/11 04:44:17 w-ota Exp $

class UploadFileInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row    = $user->getAttribute('acs_user_info_row');
		$target_user_community_id = $request->getParameter('id');
		$target_user_community_folder_id = $request->getParameter('folder_id');


		$action_url = $this->getControllerPath('User', 'UploadFile')
			 . '&id=' . $target_user_community_id . '&folder_id=' . $target_user_community_folder_id;

		// フォルダURL 通常表示
		$folder_url = $this->getControllerPath('User', 'Folder')
			 . '&id=' . $target_user_community_id . '&folder_id=' . $target_user_community_folder_id;

		// フォルダURL グループ表示
		$folder_group_mode_url = $this->getControllerPath('User', 'Folder')
			 . '&id=' . $target_user_community_id . '&folder_id=' . $target_user_community_folder_id . '&mode=group';

		// set
		$this->setAttribute('file_contents_type_master_row_array_array', $request->getAttribute('file_contents_type_master_row_array_array'));
		$this->setAttribute('file_category_master_array', $request->getAttribute('file_category_master_array'));
		$this->setAttribute('file_contents_type_master_array', $request->getAttribute('file_contents_type_master_array'));

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('folder_url', $folder_url);
		$this->setAttribute('folder_group_mode_url', $folder_group_mode_url);

		$this->setScreenId("0001");
		$this->setTemplate('UploadFile.tpl.php');

		return parent::execute();
	}
}

?>
