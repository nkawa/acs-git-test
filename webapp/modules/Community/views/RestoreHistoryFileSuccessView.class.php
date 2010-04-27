<?php
// $Id: RestoreHistoryFileConfirmView.class.php,v 1.1 2006/05/26 08:44:04 w-ota Exp $

class RestoreHistoryFileSuccessView extends BaseView
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
		$file_info_row = $request->getAttribute('file_info_row');
		$file_history_row = $request->getAttribute('file_history_row');

		// コミュニティ情報 //
		$target_community_row = ACSCommunity::get_community_row($target_community_id);
		$target_community_row['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $target_community_row['community_id'];

		// 加工
		$file_history_row['display_file_name'] = $file_info_row['display_file_name'];
		$file_history_row['file_size_kb'] = number_format(ceil($file_history_row['file_size'] / 1024)) . " KB";
		$file_history_row['download_history_file_url'] = $this->getControllerPath('Community', 'DownloadHistoryFile')
			 . '&community_id=' . $target_community_id . '&folder_id=' . $target_community_folder_id . '&file_id=' . $file_id . '&file_history_id=' . $file_history_row['file_history_id'];

		$action_url = $this->getControllerPath('Community', 'RestoreHistoryFile')
			 . '&community_id=' . $target_community_id . '&folder_id=' . $target_community_folder_id . '&file_id=' . $file_id. '&file_history_id=' . $file_history_row['file_history_id'];

		// ファイル詳細情報URL
		$file_detail_url = $this->getControllerPath('Community', 'FileDetail')
			 . '&community_id=' . $target_community_id . '&file_id=' . $file_id . '&folder_id=' . $target_community_folder_id;

		// set
		$this->setAttribute('target_community_row', $target_community_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('file_detail_url', $file_detail_url);
		$this->setAttribute('file_history_row', $file_history_row);

		$this->setScreenId("0001");
		$this->setTemplate('RestoreHistoryFile.tpl.php');

		return parent::execute();
	}
}

?>
