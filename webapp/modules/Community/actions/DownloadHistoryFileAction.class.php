<?php
// コミュニティの履歴ファイルダウンロード
// $Id: DownloadHistoryFileAction.class.php,v 1.1 2006/05/26 08:44:02 w-ota Exp $

class DownloadHistoryFileAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$target_community_id = $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');
		$target_file_id = $request->getParameter('file_id');
		$file_history_id = $request->getParameter('file_history_id');
		$view_mode = $request->getParameter('mode');

		$target_community_row = ACSCommunity::get_community_row($target_community_id);

		// ファイルダウンロード処理
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
											 $acs_user_info_row,
											 $target_community_folder_id);
		$folder_obj = $community_folder_obj->get_folder_obj();

		// フォルダの公開範囲でアクセス制御
		if (!$community_folder_obj->has_privilege($target_community_row)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ルートフォルダ直下のファイルはコミュニティメンバ以外アクセス不可
		if ($folder_obj->get_is_root_folder() 
			&& $user->hasCredential('COMMUNITY_MEMBER')) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$file_obj = ACSFile::get_file_info_instance($target_file_id);
		if ($view_mode == 'thumb') {
			$ret = $file_obj->view_image($file_history_id, $view_mode);
		} else {
			$file_obj->download_history_file($file_history_id, $view_mode);
		}
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods () {
		return Request::GET;
	}
}
?>
