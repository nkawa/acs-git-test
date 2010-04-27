<?php
require_once(ACS_CLASS_DIR . 'ACSCommunityMail.class.php');

/**
 * コミュニティのフォルダ ファイルアップロード
 *
 * @author  kuwayama
 * @version $Revision: 1.7 $ $Date: 2006/12/18 07:42:11 $
 */
//require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class UploadFileAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_community_id = $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// アクセス制御 // プットフォルダはNG
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
													   $acs_user_info_row,
													   $target_community_folder_id);
		$target_folder_obj = $community_folder_obj->get_folder_obj();
		$is_put_folder = $target_folder_obj->is_put_folder($target_community_id);
		if ($is_put_folder) {
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

		$target_community_id        = $request->getParameter('community_id');
		$acs_user_info_row          = $user->getAttribute('acs_user_info_row');
		$target_community_folder_id = $request->getParameter('folder_id');

		// form
		$form = $request->ACSGetParameters();

		/* ファイルアップロード処理 */
		$ret = 0;
		if ($_FILES['new_file']['tmp_name'] != '') {
			// ファイル情報取得
			$file_obj = ACSFile::get_upload_file_info_instance($_FILES['new_file'],
					$target_community_id,
					$acs_user_info_row['user_community_id']);

			// フォルダにファイル追加処理
			$user_folder_obj = new ACSCommunityFolder($target_community_id,
					$acs_user_info_row,
					$target_community_folder_id);
			$folder_obj = $user_folder_obj->get_folder_obj();
			$ret = $folder_obj->add_file($file_obj);

		}

		if (!$ret) {
			print "ERROR: Upload file failed.";
		}

		if ($ret) {
			// 新規登録したファイルID
			$file_id = $file_obj->get_file_id();

			// 2007.12 追加
			// ML通知チェックがあればMLにメール送信する
			$send_announce_mail        = $request->getParameter('send_announce_mail');
			if($send_announce_mail == "t"){
				ACSCommunityMail::send_fileupload_mail(
							$target_community_id, $acs_user_info_row, $folder_obj, $file_obj);
			}
		}

		// ファイル履歴情報登録
		if ($ret) {
			$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
			$ret = ACSFileHistory::set_file_history($file_info_row, $acs_user_info_row['user_community_id'], $form['comment'], ACSMsg::get_mst('file_history_operation_master','D0101'));
		}

		// ファイル詳細情報登録
		if ($form['file_category_code'] != '' && $ret) {
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
		}

		// フォルダ表示アクション呼び出し
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$folder_action = $this->getControllerPath('Community', 'Folder');
		$folder_action .= '&community_id=' . $target_community_id;
		$folder_action .= '&folder_id=' . $target_community_folder_id;

		header("Location: $folder_action");
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
