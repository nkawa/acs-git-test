<?php
/**
 * プロフィール写真アップロード
 * UploadImageAction.class.php
 *
 * @author  $Author: w-ota $
 * @version $Revision: 1.6 $
 * @import  ACSFile.class.php
 * @import  ACSCommunityImageFileModel.class.php
 * @import  ACSFileInfoModel.class.php
 */
//require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
//require_once(ACS_CLASS_DIR . 'ACSCommunityImageFileModel.class.php');
//require_once(ACS_CLASS_DIR . 'ACSFileInfoModel.class.php');

class UploadProfileImageAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$target_user_community_id	= $request->getParameter('community_id');
		$image_new_mode				= $request->getParameter('image_new_mode');
		$acs_user_info_row			= $user->getAttribute('acs_user_info_row');
		$file_id					= $request->getParameter('file_id');

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		//追加と更新の分岐 ver1.1
		// $image_new_modeフラグ
		// file_infoテーブルへの追加trueまたは更新false
		if($image_new_mode){
			// ファイル情報取得(新規の場合)
			$file_obj = ACSFile::get_upload_file_info_instance($_FILES['new_file'],$target_user_community_id,$acs_user_info_row['user_community_id']);
		} else {
			// ファイル情報取得(更新の場合)
			$file_obj = ACSFile::get_upload_file_info_instance(
				$_FILES['new_file'],$target_user_community_id,$acs_user_info_row['user_community_id'],
				$file_id);
		}

		//ファイルの種類チェック
		$image_check = $file_obj->is_image_file();
		if (!$image_check) {
			print "ERROR: This file is not image-format.";
		}

		/* ファイルアップロード処理 */

		// ファイルの保存
		$ret = $file_obj->save_upload_file('PROFILE');

		ACSDB::_do_query("BEGIN");
		if($image_new_mode){
			$ret =  $file_obj->add_file();
		} else {
			$ret =  ACSFileInfoModel::update_all_file_info($file_obj);
		}

		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			print "ERROR: Upload image-file failed.:file_info";
		} else if($image_new_mode) {
			//community_image_fileテーブルへの追加
			$ret = ACSCommunityImageFileModel::insert_community_image($file_obj);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				print "ERROR: Upload image-file failed.:image_file";
			} else {
				ACSDB::_do_query("COMMIT");	 //追加モードのコミット
			}
		} else {
			ACSDB::_do_query("COMMIT");	 //更新モードのコミット
		}

		/* 表示アクション呼び出し */
		$image_change_url = $this->getControllerPath('Community','EditProfileImage');
		$image_change_url .= '&community_id=' . $target_user_community_id;

		header("Location: $image_change_url");
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_ADMIN');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// コミュニティ管理者はOK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		return false;
	}

}
?>
