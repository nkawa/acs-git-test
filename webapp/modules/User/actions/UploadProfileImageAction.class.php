<?php
/**
 * プロフィール写真アップロード
 * UploadImageAction.class.php
 *
 * @author  $Author: y-yuki $
 * @version $Revision: 1.7 $ $Date: 2008/03/24 07:00:36 $
 * @import  ACSFile.class.php
 * @import  ACSCommunityImageFileModel.class.php
 * @import  ACSFileInfoModel.class.php
 */
require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
require_once(ACS_CLASS_DIR . 'ACSCommunityImageFileModel.class.php');
require_once(ACS_CLASS_DIR . 'ACSFileInfoModel.class.php');	 //ver1.1

class UploadProfileImageAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$target_user_community_id			= $request->getParameter('id');
		$image_new_mode				 		= $request->getParameter('image_new_mode');
		$acs_user_info_row			   		= $user->getAttribute('acs_user_info_row');
		$file_id							= $request->getParameter('file_id');
		$open_level_code					= $request->getParameter('open_level_code');

		if ($_FILES['new_file']["name"] == "") {
			print "ERROR: This is not imagefile format.";
		}
		
		// ファイル情報取得
		$file_obj = ACSFile::get_upload_file_info_instance(
				$_FILES['new_file'],
				$target_user_community_id,
				$acs_user_info_row['user_community_id']);
		
		//ファイルの種類チェック
		$image_check = $file_obj->is_image_file();
		if (!$image_check) {
			print "ERROR: This is not imagefile format.";
		}

		 /* ファイルアップロード処理 */
		// ファイルの保存
		$ret = $file_obj->save_upload_file('PROFILE');

		ACSDB::_do_query("BEGIN");

		// 追加のみ
		$ret =  $file_obj->add_file();

		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			print "ERROR: Update image failed.:file_info";

		} else {
			// 上書きされるファイルIDを取得する
			$delete_file_id =  ACSCommunityImageFileModel::get_file_id_for_open_level($target_user_community_id, $open_level_code);
				
			// 一度削除する（空振りもOK）
			$ret = ACSCommunityImageFileModel::delete_community_image_with_open_level(
					$file_obj, $open_level_code);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				print "ERROR: Update image failed.:image_file";
			} else {

				//community_image_fileテーブルへの追加
				$ret = ACSCommunityImageFileModel::put_community_image_with_open_level(
						$file_obj, $open_level_code);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					print "ERROR: Update image failed.:image_file";
				} else {

					// 上書きされるファイルがある場合→削除
					if ($delete_file_id) {
						$delete_file_obj = ACSFile::get_file_info_instance($delete_file_id);
						$delete_file_obj->delete_file();
					}
					
					ACSDB::_do_query("COMMIT");	 //追加モードのコミット
				}
			}	
		}

		/* 表示アクション呼び出し */
		$image_change_url = $this->getControllerPath('User','EditProfileImage');
		$image_change_url .= '&id=' . $target_user_community_id;

		header("Location: $image_change_url");
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		return array('USER_PAGE_OWNER');
	}
}
?>
