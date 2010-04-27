<?php
// $Id: BBSThreadListAction.class.php,v 1.3 2006/11/20 08:44:12 w-ota Exp $

class BBSThreadListAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$community_id = $request->getParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// BBS記事一覧
		$bbs_row_array = ACSBBS::get_bbs_row_array($community_id);

		foreach ($bbs_row_array as $index => $bbs_row) {
			// 信頼済みコミュニティ一覧
			$bbs_row_array[$index]['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('bbs_row_array', $bbs_row_array);

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
		$bbs_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D41'));
		$bbs_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $bbs_contents_row['contents_type_code'], $bbs_contents_row['open_level_code']);
		$access_control_info = array(
				 'role_array' => ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row),
				 'contents_row_array' => array($bbs_contents_row)
		);

		return $access_control_info;
	}
}

?>
