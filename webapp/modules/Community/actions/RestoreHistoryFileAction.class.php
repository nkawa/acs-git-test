<?php
// 履歴ファイル復活
// $Id: RestoreHistoryFileAction.class.php,v 1.2 2006/11/20 08:44:12 w-ota Exp $

class RestoreHistoryFileAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

				if (!$this->get_execute_privilege()) {
						$controller->forward(SECURE_MODULE, SECURE_ACTION);
						return;
				}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_community_id = $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');
		$file_id = $request->getParameter('file_id');
		$file_history_id = $request->getParameter('file_history_id');
		
		// アクセス制御 // プットフォルダ、ファイルはNG
		$file_obj = ACSFile::get_file_info_instance($file_id);
		if ($file_obj->get_owner_community_id() != $target_community_id) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
		$file_history_row = ACSFileHistory::get_file_history_row($file_history_id);

		// set
		$request->setAttribute('file_info_row', $file_info_row);
		$request->setAttribute('file_history_row', $file_history_row);

		return View::SUCCESS;
	}

	// POST
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

				if (!$this->get_execute_privilege()) {
						$controller->forward(SECURE_MODULE, SECURE_ACTION);
						return;
				}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_community_id = $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');
		$file_id = $request->getParameter('file_id');
		$file_history_id = $request->getParameter('file_history_id');
		
		// アクセス制御 // プットフォルダ、ファイルはNG
		$file_obj = ACSFile::get_file_info_instance($file_id);
		if ($file_obj->get_owner_community_id() != $target_community_id) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
			//return VIEW_NONE;
		}

		$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
		$file_history_row = ACSFileHistory::get_file_history_row($file_history_id);

		// form
		$form = $request->ACSGetParameters();

		// ファイル復活処理

		// フォルダobj
		$community_folder_obj = new ACSUserFolder($target_community_id,
											 $acs_user_info_row,
											 $target_community_folder_id);
		$folder_obj = $community_folder_obj->get_folder_obj();

		// file_info更新
		$ret = $folder_obj->restore_history_file($file_info_row, $file_history_row);

		if (!$ret) {
			print "ERROR: Restore file failed.";
		}

		// ファイル履歴情報登録
		if ($ret) {
			$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
			$ret = ACSFileHistory::set_file_history($file_info_row, $acs_user_info_row['user_community_id'], $form['comment'], ACSMsg::get_msg('Community', '', 'M001'));
		}

		// ファイル詳細情報へ遷移
		$file_detail_url = $this->getControllerPath('Community', 'FileDetail');
		$file_detail_url .= '&community_id=' . $target_community_id;
		$file_detail_url .= '&file_id=' . $file_id;
		$file_detail_url .= '&folder_id=' . $target_community_folder_id;
		header("Location: $file_detail_url");

		//return VIEW_NONE;
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		return array('COMMUNITY_MEMBER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// コミュニティメンバはOK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}
		return false;
	}

}

?>
