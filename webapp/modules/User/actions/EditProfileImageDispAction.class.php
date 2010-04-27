<?php
/**
 * プロフィール編集画面限定での写真表示
 *
 * @author  y-yuki
 * @version $Revision: 1.1 $ $Date: 2008/03/24 07:09:27 $
 */
require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
class EditProfileImageDispAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$target_user_community_id = $request->getParameter('id');
		$view_mode                = $request->getParameter('mode');
		$open_level_code          = $request->getParameter('open_level_code');
		$acs_user_info_row        = $user->getAttribute('acs_user_info_row');
		$target_user_info_row = ACSUser::get_user_profile_row($target_user_community_id);
		
		$is_permitted = false;

		/* 写真表示 */
		// ファイル情報取得
		$image_file_id = $target_user_info_row['file_id_ol'. $open_level_code];
		if ($image_file_id) {
			$file_obj = ACSFile::get_file_info_instance($image_file_id);
			$ret = $file_obj->view_image($view_mode);
		} else {
			$image_url = ACSUser::get_default_image_url($view_mode);
			header("Location: $image_url");
		}

	}

	function getRequestMethods () {
		return Request::GET;
	}

	function isSecure () {
		return false;
	}
}
?>
