<?php
/**
 * ファイル公開設定
 * $Id: PublicAccessFileDetailAction.class.php,v 1.1 2007/03/28 08:59:09 w-ota Exp $
 */

class PublicAccessFileDetailAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser(); 
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$target_community_id = $request->getParameter('community_id');
		// 対象となるフォルダIDを取得
		$target_community_folder_id = $request->getParameter('folder_id');
		// 詳細情報を表示するファイルIDを取得
		$file_id = $request->getParameter('file_id');

		// コミュニティ管理者か
		$is_community_admin = false;
		if(ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $target_community_id)){
			$is_community_admin = true;
		}

		// 表示するページの所有者情報取得
		$target_community_row = ACSCommunity::get_community_row($target_community_id);

		// フォルダ情報取得
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
													   $acs_user_info_row,
													   $target_community_folder_id);
		$folder_obj = $community_folder_obj->get_folder_obj();

		// ファイル情報取得
		$file_obj = ACSFile::get_file_info_instance($file_id);

		// ファイル公開設定
		$submit_kind = $request->getParameter('submit_kind');

		// プットファイルでない場合
		if ($file_obj->get_owner_community_id() == $target_community_id) {
			if($submit_kind != "" && $is_community_admin){
				// ファイル公開URL作成
				if($submit_kind == "insert"){
					$form['folder_id'] = $target_community_folder_id;
					$form['community_id'] = $target_community_id;
					ACSFileDetailInfo::insert_file_public_access($file_id, $form);

				// ファイル公開URL削除
				}else if($submit_kind == "delete"){
					ACSFileDetailInfo::delete_file_public_access($file_id);

				// ファイル公開アクセス数リセット
				}else if($submit_kind == "reset"){
					$form['access_count'] = 0;
					$form['access_start_date'] = "'now'";
					ACSFileDetailInfo::update_file_public_access($file_id, $form);
				}
			}
		}

		$contents_link_url = 
						$this->getControllerPath('Community', 'FileDetail') . 
						"&community_id=" . $target_community_id . 
						"&file_id=" . $file_obj->get_file_id() . 
						"&folder_id=" . $community_folder_obj->folder_obj->get_folder_id();

		header("Location: $contents_link_url");

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
}

?>
