<?php
/**
 * ファイル詳細情報
 * $Id: FileDetailAction.class.php,v 1.5 2007/03/28 08:59:09 w-ota Exp $
 */

class FileDetailAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$target_community_id = $request->getParameter('community_id');
		// 対象となるフォルダIDを取得
		$target_community_folder_id = $request->getParameter('folder_id');
		// 詳細情報を表示するファイルIDを取得
		$file_id = $request->getParameter('file_id');

		// コミュニティ管理者か
		$is_community_admin = false;
		if(ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $target_community_id)){
			$is_community_admin = true;
		}

		// 表示するページの所有者情報取得
		$target_community_row = ACSCommunity::get_community_row($target_community_id);

		// フォルダ情報取得
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
													   $acs_user_info_row,
													   $target_community_folder_id);
		$folder_obj = $community_folder_obj->get_folder_obj();

		// フォルダの公開範囲でアクセス制御
		if (!$community_folder_obj->has_privilege($target_community_row)) {

			// 2010.03.24 未ログイン時の誘導
			// ログインユーザでない場合はログイン画面へ
			if ($user->hasCredential('PUBLIC_USER')) {
				$controller->forward("User", "Login");
				return;
			}

			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ルートフォルダ直下のファイルはコミュニティメンバ以外アクセス不可
		if ($folder_obj->get_is_root_folder() && $user->hasCredential('COMMUNITY_MEMBER')) {

			// 2010.03.24 未ログイン時の誘導
			// ログインユーザでない場合はログイン画面へ
			if ($user->hasCredential('PUBLIC_USER')) {
				$controller->forward("User", "Login");
				return;
			}

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


		// プットファイルでない場合
		if ($file_obj->get_owner_community_id() == $target_community_id) {
			// ファイルの公開情報
			$file_public_access_row = ACSFileDetailInfo::get_file_public_access_row($file_id);
		}

		// set
		$request->setAttribute('target_community_row', $target_community_row);
		$request->setAttribute('file_obj', $file_obj);
		$request->setAttribute('community_folder_obj', $community_folder_obj);
		$request->setAttribute('file_detail_info_row', $file_detail_info_row);
		$request->setAttribute('file_history_row_array', $file_history_row_array);
		$request->setAttribute('is_community_admin', $is_community_admin);
		$request->setAttribute('file_public_access_row', $file_public_access_row);

		return View::SUCCESS;
	}
	
	function isSecure () {
		return false;
	}
	
}

?>
