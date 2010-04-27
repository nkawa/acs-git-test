<?php
/**
 * ファイル詳細情報
 * $Id: FileDetailAction.class.php,v 1.8 2007/03/29 01:55:17 w-ota Exp $
 */

class FileDetailAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_user_community_id = $request->getParameter('id');
		// 対象となるフォルダIDを取得
		$target_user_community_folder_id = $request->getParameter('folder_id');
		// 詳細情報を表示するファイルIDを取得
		$file_id = $request->getParameter('file_id');

		// 表示するページの所有者情報取得
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);

		// フォルダ情報取得
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
		//if ($folder_obj->get_is_root_folder() && !in_array('USER_PAGE_OWNER', $privilege_array)) {
		if ($folder_obj->get_is_root_folder() && !$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ファイル情報取得
		$file_obj = ACSFile::get_file_info_instance($file_id);

		// ファイルの詳細情報
		$file_detail_info_row = ACSFileDetailInfo::get_file_detail_info_row($file_id);

		// ファイルの履歴情報
		$file_history_row_array = ACSFileHistory::get_file_history_row_array($file_id);
		// ファイル履歴ごとのコメント
		foreach ($file_history_row_array as $index => $file_history_row) {
			$file_history_row_array[$index]['file_history_comment_row_array'] = ACSFileHistoryComment::get_file_history_comment_row_array($file_history_row['file_history_id']);
		}

		// ファイルアクセス履歴登録
		if ($acs_user_info_row['is_acs_user']) {
			ACSFile::set_file_access_history($acs_user_info_row['user_community_id'], $file_id);
		}

		// 足跡情報取得
		$footprint_url = $this->getControllerPath('User', 'FileDetail')
						. "&id=" . $target_user_community_id
						. "&file_id=" . $file_obj->get_file_id()
						. "&folder_id=" . $user_folder_obj->folder_obj->get_folder_id();
		$where  = "foot.contents_link_url = '" . $footprint_url . "'";
		$where .= " AND foot.visitor_community_id = '" . $acs_user_info_row['user_community_id'] . "'";
		$footprint_info = ACSUser::get_footprint_list($target_user_community_id, $where);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('file_obj', $file_obj);
		$request->setAttribute('user_folder_obj', $user_folder_obj);
		$request->setAttribute('file_detail_info_row', $file_detail_info_row);
		$request->setAttribute('file_history_row_array', $file_history_row_array);
		$request->setAttribute('footprint_info', $footprint_info);

		return View::SUCCESS;
	}
	
	function isSecure () {
		return false;
	}
	function getCredential () {
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 非ログインユーザ、本人以外はNG
		if ($user->hasCredential('PUBLIC_USER')
				 || !$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
	}
}

?>
