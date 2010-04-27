<?php
/**
 * ファイル詳細情報
 * $Id: EditFileDetailAction.class.php,v 1.2 2006/06/16 07:52:32 w-ota Exp $
 */

class EditFileDetailAction extends BaseAction
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

		// 対象となるコミュニティIDを取得
		$target_community_id = $request->getParameter('community_id');
		// 対象となるフォルダIDを取得
		$target_community_folder_id = $request->getParameter('folder_id');
		// 詳細情報を表示するファイルIDを取得
		$file_id = $request->getParameter('file_id');

		// 表示するページの所有者情報取得
		$target_community_row = ACSCommunity::get_community_row($target_community_id);


		// フォルダ情報取得
		$community_folder_obj = new ACSUserFolder($target_community_id,
											 $acs_user_info_row,
											 $target_community_folder_id);

		// ファイル情報取得
		$file_obj = ACSFile::get_file_info_instance($file_id);

		// ファイルの詳細情報
		$file_detail_info_row = ACSFileDetailInfo::get_file_detail_info_row($file_id);


		// マスタ
		$file_category_master_array = ACSDB::get_master_array('file_category');
		$file_contents_type_master_array = ACSDB::get_master_array('file_contents_type');


		// ファイルカテゴリコードごとのファイルコンテンツ種別の連想配列を取得する
		$file_contents_type_master_row_array_array = ACSFileDetailInfo::get_file_contents_type_master_row_array_array();


		// アクセス制御 // プットフォルダ、ファイルはNG
		if ($file_obj->get_owner_community_id() != $target_community_id) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}


		// set
		$request->setAttribute('target_community_row', $target_community_row);
		$request->setAttribute('file_obj', $file_obj);
		$request->setAttribute('community_folder_obj', $community_folder_obj);
		$request->setAttribute('file_detail_info_row', $file_detail_info_row);
		$request->setAttribute('file_contents_type_master_row_array_array', $file_contents_type_master_row_array_array);

		$request->setAttribute('file_category_master_array', $file_category_master_array);
		$request->setAttribute('file_contents_type_master_array', $file_contents_type_master_array);

		return View::SUCCESS;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

				if (!$this->get_execute_privilege()) {
						$controller->forward(SECURE_MODULE, SECURE_ACTION);
						return;
				}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_community_id = $request->getParameter('community_id');
		// 対象となるフォルダIDを取得
		$target_community_folder_id = $request->getParameter('folder_id');
		// 詳細情報を表示するファイルIDを取得
		$file_id = $request->getParameter('file_id');


		// アクセス制御 // プットフォルダ、ファイルはNG
		$file_obj = ACSFile::get_file_info_instance($file_id);
		if ($file_obj->get_owner_community_id() != $target_community_id) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// form
		$form = $request->ACSGetParameters();

		$file_contents_type_list_row_array = ACSFileDetailInfo::get_file_contents_type_list_row_array($form['file_category_code']);
		$file_contents_form_array = array();
		foreach ($file_contents_type_list_row_array as $file_contents_type_list_row) {
			$file_contents_form = array(
										'file_id' => $file_id,
										'file_contents_type_code' => $file_contents_type_list_row['file_contents_type_code'],
										'file_contents_value' => $form['file_contents_array'][$file_contents_type_list_row['file_contents_type_code']]
										);
			array_push($file_contents_form_array, $file_contents_form);
		}

		$ret = ACSFileDetailInfo::set_file_detail_info($file_id, $form['file_category_code'], $file_contents_form_array);

		if ($ret) {
			$file_detail_url  = $this->getControllerPath('Community', 'FileDetail');
			$file_detail_url .= '&community_id=' . $target_community_id;
			$file_detail_url .= '&folder_id=' . $target_community_folder_id;
			$file_detail_url .= '&file_id=' . $file_id;
			// ファイル詳細情報URLへ
			header("Location: $file_detail_url");
		}
	}

	function getRequestMethods() {
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
