<?php
// ユーザの履歴ファイルダウンロード
// $Id: DownloadHistoryFileAction.class.php,v 1.1 2006/05/18 05:18:20 w-ota Exp $

class DownloadHistoryFileAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($request->getParameter('id'));
		$target_user_community_id = $request->getParameter('id');
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_community_folder_id = $request->getParameter('folder_id');

		$target_file_id = $request->getParameter('file_id');
		$file_history_id = $request->getParameter('file_history_id');
		$view_mode = $request->getParameter('mode');

		// ファイルダウンロード処理
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
											 $acs_user_info_row,
											 $target_user_community_folder_id);
		$folder_obj = $user_folder_obj->get_folder_obj();

		// フォルダの公開範囲でアクセス制御
		if (!$user_folder_obj->has_privilege($target_user_info_row)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ルートフォルダ直下のファイルは本人以外アクセス不可
		$privilege_array = $this->getCredential();

		if ($folder_obj->get_is_root_folder() && !in_array('USER_PAGE_OWNER', $privilege_array)) {
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

	function getRequestMethods () {
		return Request::GET;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('USER_PAGE_OWNER');
	}
}
?>
