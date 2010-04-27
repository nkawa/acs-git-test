	<?php
/**
 * プロフィール写真 削除処理　アクションクラス
 * DeleteProfileImageAction.class.php
 *
 * @author  $Author: w-ota $
 * @version ver1.0 $  2006/02/16 $
 * @import  ACSFile.class.php
 * @import  ACSCommunityImageFileModel.class.php
 */
//	require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
//	require_once(ACS_CLASS_DIR . 'ACSCommunityImageFileModel.class.php');

class DeleteProfileImageAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

				if (!$this->get_execute_privilege()) {
						$controller->forward(SECURE_MODULE, SECURE_ACTION);
						return;
				}

		// set parameter
		$target_user_community_id	   = $request->getParameter('community_id');

		$image_change_url = $this->getControllerPath('Community','EditProfileImage');
		$image_change_url .= '&community_id=' . $target_user_community_id;
		
		$delete_image_url = $image_change_url;
		$back_url = $image_change_url;

		$request->setAttribute('delete_image_url', $delete_image_url);
		$request->setAttribute('back_url', $back_url);

		// 表示
		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();


		//削除処理を行う
		$target_user_community_id	= $request->getParameter('community_id');
		$acs_user_info_row		= $user->getAttribute('acs_user_info_row');
		$file_id			= $request->getParameter('file_id');

		// ファイル情報取得
		$file_obj = ACSFile::get_file_info_instance($file_id);
		//ファイル情報テーブルのデータ削除
		ACSDB::_do_query("BEGIN");

		$ret =  $file_obj->delete_file();
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			print "ERROR: Remove imagefile failed.:file_info";
		}else{
			//community_image_fileテーブルのデータ削除
			$ret = ACSCommunityImageFileModel::delete_community_image($file_obj);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				print "ERROR: Remove imagefile failed.:image_file";
			}else{
				ACSDB::_do_query("COMMIT");
			}
		}
		//表示
		$image_change_url = $this->getControllerPath('Community','EditProfileImage');
				$image_change_url .= '&community_id=' . $target_user_community_id;
		header("Location: $image_change_url");
		return View::INPUT;
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
		$request = $context->getRequest();
		$user = $context->getUser();
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
