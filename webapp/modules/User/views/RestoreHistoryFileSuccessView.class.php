<?php
// $Id: RestoreHistoryFileView_confirm.class.php,v 1.1 2006/05/18 05:18:34 w-ota Exp $

class RestoreHistoryFileSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row    = $user->getAttribute('acs_user_info_row');
		$target_user_community_id = $request->getParameter('id');
		$target_user_community_folder_id = $request->getParameter('folder_id');
		$file_id = $request->getParameter('file_id');
		$file_info_row = $request->getAttribute('file_info_row');
		$file_history_row = $request->getAttribute('file_history_row');

		// 加工
		$file_history_row['display_file_name'] = $file_info_row['display_file_name'];
		$file_history_row['file_size_kb'] = number_format(ceil($file_history_row['file_size'] / 1024)) . " KB";
		$file_history_row['download_history_file_url'] = $this->getControllerPath('User', 'DownloadHistoryFile')
			 . '&id=' . $target_user_community_id . '&folder_id=' . $target_user_community_folder_id . '&file_id=' . $file_id . '&file_history_id=' . $file_history_row['file_history_id'];

		$action_url = $this->getControllerPath('User', 'RestoreHistoryFile')
			 . '&id=' . $target_user_community_id . '&folder_id=' . $target_user_community_folder_id . '&file_id=' . $file_id. '&file_history_id=' . $file_history_row['file_history_id'];

		// ファイル詳細情報URL
		$file_detail_url = $this->getControllerPath('User', 'FileDetail')
			 . '&id=' . $target_user_community_id . '&file_id=' . $file_id . '&folder_id=' . $target_user_community_folder_id;

		// set
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('file_detail_url', $file_detail_url);
		$this->setAttribute('file_history_row', $file_history_row);

		$this->setScreenId("0001");
		$this->setTemplate('RestoreHistoryFile.tpl.php');

		return parent::execute();
	}
}

?>
