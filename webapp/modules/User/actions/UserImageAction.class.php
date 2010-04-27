<?php
/**
 * プロフィール写真表示
 *
 * @author  kuwayama
 * @version $Revision: 1.3 $ $Date: 2008/03/24 07:00:36 $
 */
require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
class UserImageAction extends BaseAction
{
	function getDefaultView() {
		$this->execute();
	}
	
	
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$target_user_community_id	= $request->getParameter('id');
		$view_mode					= $request->getParameter('mode');
		$acs_user_info_row			= $user->getAttribute('acs_user_info_row');

		$target_user_info_row = ACSUser::get_user_profile_row($target_user_community_id);
		$is_permitted = false;

		/* アクセス権チェック */
		// マイページが閲覧可能かチェックする
		// 削除フラグ、全体の公開範囲をチェック

		/* 写真表示 */
		// ファイル情報取得
		// (1) 一般ユーザ(外部ユーザ)かどうか
		if (!$acs_user_info_row['is_acs_user']) {

			$image_file_id = $target_user_info_row['file_id_ol01'];

		} else {
			// (2) ログインユーザかどうか
				$image_file_id = $target_user_info_row['file_id_ol02'];

			// (3) 友人かどうか
			if (ACSUser::is_in_friends_id_array($acs_user_info_row, $target_user_info_row['user_community_id'])) {
				$image_file_id = $target_user_info_row['file_id_ol05'];

			}

			// (4) 本人かどうか
			if ($acs_user_info_row['user_id'] == $target_user_info_row['user_id']) {
				$image_file_id = $target_user_info_row['file_id_ol05'];

			}

			// (5) システム管理者かどうか
			if (ACSAccessControl::is_system_administrator($acs_user_info_row)) {
				$image_file_id = $target_user_info_row['file_id_ol05'];

			}
		}

		if ($image_file_id) {
			$file_obj = ACSFile::get_file_info_instance($image_file_id);
			$ret = $file_obj->view_image($view_mode);
		} else {
			$image_url = ACSUser::get_default_image_url($view_mode);
			header("Location: $image_url");
		}
	}

	function isSecure () {
		return false;
	}
	

	function getRequestMethods () {
		return REQ_GET;
	}
}
?>
