<?php
/**
 * Diary写真表示
 *
 * @author  akitsu
 * @version $Revision: 1.2 $ $Date: 2007/03/28 02:51:45 $
 */

class DiaryImageAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$diary_file_id 		= $request->getParameter('id');
		$view_mode			= $request->getParameter('mode');
		$acs_user_info_row	= $user->getAttribute('acs_user_info_row');
		$is_permitted = false;

		/* アクセス権チェック */
		// 閲覧可能かチェックする
		// 削除フラグ、全体の公開範囲をチェック

		/* 写真表示 */
		// ファイル情報取得
		$image_file_id = $diary_file_id;
		if ($image_file_id) {
			$file_obj = ACSFile::get_file_info_instance($image_file_id);
			$ret = $file_obj->view_image($view_mode);
		}
	}

	function getRequestMethods () {
		return Request::GET;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		return array('EXECUTE');
	}

	function get_execute_privilege (&$controller, &$request, &$user) {

		// 公開範囲情報取得
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$diary_file_row = ACSDiaryFile::get_diary_file_row_by_file_id($request->ACSgetParameter('id'));
		$diary_row = ACSDiary::get_diary_row($diary_file_row['diary_id']);
		if (!$diary_row) {
			return false;
		}
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($diary_row['community_id']);
		if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
			$diary_row['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
		}

		// アクセス制御判定
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		$ret = ACSAccessControl::is_valid_user_for_user_community($acs_user_info_row, $role_array, $diary_row);

		return $ret;
	}
}
?>
