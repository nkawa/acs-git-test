<?php
// $Id: FileHistoryCommentAction.class.php,v 1.5 2006/12/08 05:06:42 w-ota Exp $

class FileHistoryCommentAction extends BaseAction
{
	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_user_community_id = $request->getParameter('id');
		$target_user_community_folder_id = $request->getParameter('folder_id');
		$file_id = $request->getParameter('file_id');
		$file_history_id = $request->getParameter('file_history_id');

		// form
		$form = $request->ACSGetParameters();


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
		}

		// ルートフォルダ直下のファイルは本人以外アクセス不可
		$privilege_array = $this->getCredential();
		if ($folder_obj->get_is_root_folder() && !in_array('USER_PAGE_OWNER', $privilege_array)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
		}


		// ファイル履歴コメントを登録する
		if ($form['comment'] != '') {
			// ファイル履歴が1件も登録されていない場合は"作成"を登録する
			$file_history_row_array = ACSFileHistory::get_file_history_row_array($file_id);
			if (count($file_history_row_array) == 0) {
				$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
				$file_history_id = ACSFileHistory::set_file_history($file_info_row, $file_info_row['entry_user_community_id'], '', ACSMsg::get_mst('file_history_operation_master','D0101'));
			}

			$ret = ACSFileHistoryComment::set_file_history_comment($file_history_id, $acs_user_info_row['user_community_id'], $form['comment']);
		}

		// ファイル詳細情報へ遷移
		$file_detail_url = $this->getControllerPath('User', 'FileDetail');
		$file_detail_url .= '&id=' . $target_user_community_id;
		$file_detail_url .= '&file_id=' . $file_id;
		$file_detail_url .= '&folder_id=' . $target_user_community_folder_id;
		header("Location: $file_detail_url");

	}

	function isSecure () {
		return false;
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* 必須チェック */
		parent::regValidateName($validatorManager, 
				"comment", 
				true, 
				ACSMsg::get_msg('User', 'FileHistoryCommentAction.class.php', 'M001'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		// 移動一覧アクション呼び出し
		$controller->forward('User', 'FileDetail');
	}
	
	function getCredential () {
		return array('USER_PAGE_OWNER');
	}
}

?>
