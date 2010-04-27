<?php
/**
 * ユーザのフォルダ表示
 *
 * @author  $Author: w-ota $
 * @version $Revision: 1.10 $ $Date: 2006/05/26 08:44:05 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class FolderAction extends BaseAction
{
	/**
	 * 初期画面
	 * GETメソッドの場合、呼ばれる
	 */
	function getDefaultView () {

		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		/* エラーを取得 */
		//$error_row = $user->getAttribute('error_row');
		//$user->removeAttribute('error_row');
		
		$target_user_community_id = $request->getParameter('id');
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_community_folder_id = $request->getParameter('folder_id');
		$mode = $request->getParameter('mode'); // 表示モード

		$user_folder_obj = new ACSUserFolder($target_user_community_id,
											 $acs_user_info_row,
											 $target_user_community_folder_id);

		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($request->getParameter('id'));


		// グループ表示
		$file_detail_info_row_array = array();
		if ($mode == 'group') {
			// ファイルオブジェクトの配列
			$target_folder_obj	= $user_folder_obj->get_folder_obj();
			$file_obj_array	   = $target_folder_obj->get_file_obj_array();

			foreach ($file_obj_array as $file_obj) {
				$file_detail_info_row = ACSFileDetailInfo::get_file_detail_info_row($file_obj->get_file_id());
				if (!$file_detail_info_row['file_id']) {
					// ファイル詳細情報が設定されてない場合
					$file_detail_info_row['file_id'] = $file_obj->get_file_id();
				}
				$file_detail_info_row['display_file_name'] = $file_obj->get_display_file_name();
				$file_detail_info_row['thumbnail_server_file_name'] = $file_obj->get_thumbnail_server_file_name();
				array_push($file_detail_info_row_array, $file_detail_info_row);
			}

			// ファイルカテゴリコードごとのファイルコンテンツ種別の連想配列を取得する
			$file_contents_type_master_row_array_array = ACSFileDetailInfo::get_file_contents_type_master_row_array_array();
		}


		// フォルダの公開範囲でアクセス制御
		if (!$user_folder_obj->has_privilege($target_user_info_row)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);
		$request->setAttribute('error_row', $error_row);

		$request->setAttribute('mode', $mode);
		if ($mode == 'group') {
			$request->setAttribute('file_detail_info_row_array', $file_detail_info_row_array);
			$request->setAttribute('file_contents_type_master_row_array_array', $file_contents_type_master_row_array_array);
		}

		return View::SUCCESS;
	}

	function execute() {
		return $this->getDefaultView();
	}
	
	function isSecure () {
		return false;
	}

	function getRequestMethods () {
		return Request::POST | Request::GET;
	}
}
?>
