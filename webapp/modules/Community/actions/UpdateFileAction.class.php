<?php
// ファイル更新
// $Id: UpdateFileAction.class.php,v 1.4 2006/12/18 07:42:11 w-ota Exp $

class UpdateFileAction extends BaseAction
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

		// アクセス制御 // プットフォルダ、ファイルはNG
		$file_obj = ACSFile::get_file_info_instance($file_id);
		if ($file_obj->get_owner_community_id() != $target_community_id) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// マスタ
		$file_category_master_array = ACSDB::get_master_array('file_category');
		$file_contents_type_master_array = ACSDB::get_master_array('file_contents_type');

		// ファイルカテゴリコードごとのファイルコンテンツ種別の連想配列を取得する
		$file_contents_type_master_row_array_array = ACSFileDetailInfo::get_file_contents_type_master_row_array_array();

		// set
		$request->setAttribute('file_contents_type_master_row_array_array', $file_contents_type_master_row_array_array);
		$request->setAttribute('file_category_master_array', $file_category_master_array);
		$request->setAttribute('file_contents_type_master_array', $file_contents_type_master_array);

		return View::INPUT;
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

		// アクセス制御 // プットフォルダ、ファイルはNG
		$file_obj = ACSFile::get_file_info_instance($file_id);
		if ($file_obj->get_owner_community_id() != $target_community_id) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// form
		$form = $request->ACSGetParameters();

		$ret = false;
		// ファイル更新処理
		if ($_FILES['new_file']['tmp_name'] != '') {
			// ファイルobj
			$file_obj = ACSFile::get_upload_file_info_instance_for_update($_FILES['new_file'],
					$target_community_id,
					$acs_user_info_row['user_community_id'],
					$file_id
			);

			// フォルダobj
			$community_folder_obj = new ACSCommunityFolder($target_community_id,
					$acs_user_info_row,
					$target_community_folder_id);
			$folder_obj = $community_folder_obj->get_folder_obj();

			// ファイル履歴が1件も登録されていない場合は"作成"を登録する
			$file_history_row_array = ACSFileHistory::get_file_history_row_array($file_id);
			if (count($file_history_row_array) == 0) {
				$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
				$file_history_id = ACSFileHistory::set_file_history($file_info_row, $file_info_row['entry_user_community_id'], '', ACSMsg::get_mst('file_history_operation_master','D0101'));
			}

			// file_info更新, ファイル保存
			$ret = $folder_obj->update_file($file_obj);
		}

		if (!$ret) {
			print "ERROR: ファイルアップロードに失敗しました";
		}

		// ファイル履歴情報登録
		if ($ret) {
			$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
			$ret = ACSFileHistory::set_file_history(
					$file_info_row, 
					$acs_user_info_row['user_community_id'], 
					$form['comment'], 
					ACSMsg::get_mst('file_history_operation_master','D0201'));

			// 2007.12 追加
			// ML通知チェックがあればMLにメール送信する
			// コミュニティ情報の取得
			$send_announce_mail		= $request->getParameter('send_announce_mail');
			if($send_announce_mail == "t"){
				ACSCommunityMail::send_fileupload_mail(
							$target_community_id, $acs_user_info_row, $folder_obj, $file_obj);
			}

		}

		// ファイル詳細情報へ遷移
		$file_detail_url = $this->getControllerPath('Community', 'FileDetail');
		$file_detail_url .= '&community_id=' . $target_community_id;
		$file_detail_url .= '&file_id=' . $file_id;
		$file_detail_url .= '&folder_id=' . $target_community_folder_id;
		header("Location: $file_detail_url");
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
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
