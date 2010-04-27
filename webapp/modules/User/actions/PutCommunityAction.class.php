<?php
/**
 * ユーザフォルダのプット先コミュニティ表示
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/02/23 01:25:36 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class PutCommunityAction extends BaseAction
{
	/**
	 * 初期画面
	 * GETメソッドの場合、呼ばれる
	 */
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_user_community_id = $request->getParameter('id');
		// 対象となるフォルダIDを取得
		$target_user_community_folder_id = $request->getParameter('folder_id');

		// 表示するページの所有者情報取得
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// フォルダ情報取得
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
											 $acs_user_info_row,
											 $target_user_community_folder_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
	
	function getRequestMethods () {
		return Request::GET;
	}
}
?>
