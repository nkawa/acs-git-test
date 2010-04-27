<?php
// $Id: UpdateFileView::INPUT.class.php,v 1.1 2006/05/26 08:44:04 w-ota Exp $

class UpdateFileInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row    = $user->getAttribute('acs_user_info_row');
		$target_community_id = $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');
		$file_id = $request->getParameter('file_id');

		// コミュニティ情報 //
		$target_community_row = ACSCommunity::get_community_row($target_community_id);
		$target_community_row['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $target_community_row['community_id'];


		$action_url = $this->getControllerPath('Community', 'UpdateFile')
			 . '&community_id=' . $target_community_id . '&folder_id=' . $target_community_folder_id . '&file_id=' . $file_id;

		// ファイル詳細情報URL
		$file_detail_url = $this->getControllerPath('Community', 'FileDetail')
			 . '&community_id=' . $target_community_id . '&file_id=' . $file_id . '&folder_id=' . $target_community_folder_id;

		// set
		$this->setAttribute('file_contents_type_master_row_array_array', $request->getAttribute('file_contents_type_master_row_array_array'));
		$this->setAttribute('file_category_master_array', $request->getAttribute('file_category_master_array'));
		$this->setAttribute('file_contents_type_master_array', $request->getAttribute('file_contents_type_master_array'));

		$this->setAttribute('target_community_row', $target_community_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('file_detail_url', $file_detail_url);

		$this->setScreenId("0001");
		$this->setTemplate('UpdateFile.tpl.php');

		return parent::execute();
	}
}

?>
