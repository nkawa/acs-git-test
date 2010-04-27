<?php
/**
 * ユーザのフォルダ ファイルダウンロード
 *
 * @author  $Author: w-ota $
 * @version $Revision: 1.5 $ $Date: 2006/12/18 07:42:15 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class DownloadFileAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($request->getParameter('id'));
		$target_user_community_id = $request->getParameter('id');
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_community_folder_id = $request->getParameter('folder_id');
		$view_mode = $request->getParameter('mode');

		// ファイルダウンロード処理
		$target_file_id = $request->getParameter('file_id');
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
		//$privilege_array = $user->getPrivileges();
		if ($folder_obj->get_is_root_folder() && !in_array('USER_PAGE_OWNER', $privilege_array)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ファイルアクセス履歴登録
		if ($acs_user_info_row['is_acs_user']) {
			ACSFile::set_file_access_history($acs_user_info_row['user_community_id'], $target_file_id);
		}
		
		if ($view_mode == 'thumb') {
			$file_obj = ACSFile::get_file_info_instance($target_file_id);
			$ret = $file_obj->view_image($view_mode);
		} else {
			$folder_obj->download_file($target_file_id);
		}
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods () {
		return Request::GET;
	}
	
	function getCredential() {
		return array('USER_PAGE_OWNER');
	}
}
?>
