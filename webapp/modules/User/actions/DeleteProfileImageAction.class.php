<?php
/**
 * プロフィール写真 削除処理　アクションクラス
 * DeleteProfileImageAction.class.php
 *
 * @author  $Author: y-yuki $
 * @version ver1.0 $  2006/02/16 $
 * @import  ACSFile.class.php
 * @import  ACSCommunityImageFileModel.class.php
 */
// $Id: DeleteProfileImageAction.class.php,v 1.7 2008/03/24 07:00:36 y-yuki Exp $


	require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
	require_once(ACS_CLASS_DIR . 'ACSCommunityImageFileModel.class.php');

class DeleteProfileImageAction extends BaseAction
{
	// GET
	function getDefaultView() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// set parameter
		$target_user_community_id       = $request->getParameter('id');

		$image_change_url = $this->getControllerPath('User','EditProfileImage');
		$image_change_url .= '&id=' . $target_user_community_id;
		
		$delete_image_url = $image_change_url;
		$back_url = $image_change_url;

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$request->setAttribute('delete_image_url', $delete_image_url);
		$request->setAttribute('back_url', $back_url);

		// 表示
		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		//削除処理を行う
		$target_user_community_id	= $request->getParameter('id');
		$acs_user_info_row		= $user->getAttribute('acs_user_info_row');
		$file_id			= $request->getParameter('file_id');
		$open_level_code	= $request->getParameter('open_level_code');

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ファイル情報取得
		$file_obj = ACSFile::get_file_info_instance($file_id);
		//ファイル情報テーブルのデータ削除
		ACSDB::_do_query("BEGIN");

		$ret = ACSCommunityImageFileModel::delete_community_image_with_open_level(
				$file_obj, $open_level_code);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			print "ERROR: Delete image failed. :image_file";
		} else {
			$row = ACSCommunityImageFileModel::get_file_id_with_open_level($file_obj->get_owner_community_id());
			if ($row == NULL || 
					($file_id != $row['file_id_ol05']
						&& $file_id != $row['file_id_ol02']
						&& $file_id != $row['file_id_ol01']
					)
				) {
					// ファイルごと削除
					$ret =  $file_obj->delete_file();
				}
			if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					print "ERROR: Delete image failed. :image_file";
			} else {
					ACSDB::_do_query("COMMIT");
			}
		}
		//表示
		$image_change_url = $this->getControllerPath('User','EditProfileImage');
		$image_change_url .= '&id=' . $target_user_community_id;
		header("Location: $image_change_url");
		return View::INPUT;
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

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 本人の場合はOK
		if ($user->hasCredential('USER_PAGE_OWNER')) {
			return true;
		}
		return false;
	}

}
?>
