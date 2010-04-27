<?php
/**
 * フォルダ詳細情報
 *
 * @author  kuwayama
 * @version $Revision: 1.3 $ $Date: 2006/12/08 05:06:34 $
 */
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class FolderDetailAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_community_id = $request->getParameter('community_id');
		// 対象となるフォルダIDを取得
		$target_community_folder_id = $request->getParameter('folder_id');
		// 詳細情報を表示するフォルダIDを取得
		$detail_community_folder_id = $request->getParameter('detail_folder_id');

		// 表示するページの所有者情報取得
		$target_community_info_row = ACSCommunity::get_community_row($target_community_id);
		// フォルダ情報取得
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
													   $acs_user_info_row,
													   $target_community_folder_id);

		$detail_community_folder_obj = new ACSCommunityFolder($target_community_id,
															  $acs_user_info_row,
															  $detail_community_folder_id);

		// set
		$request->setAttribute('target_community_info_row', $target_community_info_row);
		$request->setAttribute('community_folder_obj', $community_folder_obj);
		$request->setAttribute('detail_community_folder_obj', $detail_community_folder_obj);

		return View::SUCCESS;
	}
	function isSecure () {
		return false;
	}
	

	// アクセス制御情報
	function get_access_control_info(&$controller, &$request, &$user) {
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$community_id = $request->getParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// アクセス制御情報 //
		$folder_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D31'));
		$folder_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $folder_contents_row['contents_type_code'], $folder_contents_row['open_level_code']);
		$access_control_info = array(
				 'role_array' => ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row),
				 'contents_row_array' => array($folder_contents_row)
		);

		return $access_control_info;
	}
}
?>
