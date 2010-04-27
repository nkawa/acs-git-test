<?php
/**
 * プロフィール写真表示
 *
 * @author  akitsu
 * @version $Revision: 1.3 $ $Date: 2007/03/28 10:20:22 $
 */
class BBSImageAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$bbs_id = $request->getParameter('id');
		$view_mode				= $request->getParameter('mode');
		$acs_user_info_row		= $user->getAttribute('acs_user_info_row');

		$bbs_row = ACSBBS::get_bbs_row($bbs_id);

		$is_permitted = false;

		/* アクセス権チェック */
		// 閲覧可能かチェックする
		// 削除フラグ、全体の公開範囲をチェック
		// 権限チェック
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		/* 写真表示 */
		// ファイル情報取得
		$image_file_id = $bbs_row['file_id'];
		if ($image_file_id) {
			$file_obj = ACSFile::get_file_info_instance($image_file_id);
			$ret = $file_obj->view_image($view_mode);
		}
	}

	function getRequestMethods () {
		return Request::GET;
	}

	// アクセス制御情報
	function get_access_control_info(&$controller, &$request, &$user) {
		return array();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$bbs_row = ACSBBS::get_bbs_row($request->ACSGetParameter('id'));
		// パブリックリリース
		if ($bbs_row['open_level_code'] == '06') {
			return array();
		}

		$community_id = $bbs_row['bbs_community_id'];

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// アクセス制御情報 //
		$bbs_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D41'));
		$bbs_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $bbs_contents_row['contents_type_code'], $bbs_contents_row['open_level_code']);
		$access_control_info = array(
									 'role_array' => ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row),
									 'contents_row_array' => array($bbs_contents_row)
									 );

		return $access_control_info;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('EXECUTE');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// 公開範囲情報取得
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$bbs_row = ACSBBS::get_bbs_row($request->ACSGetParameter('id'));
		if (!$bbs_row) {
			return false;
		}
		// パブリックリリース
		if ($bbs_row['open_level_code'] == '06') {
			return true;
		}
		$bbs_row['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);
		$target_community_row = ACSCommunity::get_community_profile_row($bbs_row['bbs_community_id']);

		// スレッドごとのアクセス制御判定
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $target_community_row);
		$ret = ACSAccessControl::is_valid_user_for_community($acs_user_info_row, $role_array, $bbs_row);

		return $ret;
	}
}
?>
