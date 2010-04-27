<?php
// $Id: CommunityListAction.class.php,v 1.5 2006/11/20 08:44:25 w-ota Exp $


class CommunityListAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');

		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// マイコミュニティ
		$community_row_array = ACSUser::get_community_row_array($user_community_id);

		// コミュニティ全体の公開範囲をセットする
		foreach ($community_row_array as $index => $community_row) {
			$community_row_array[$index]['contents_row_array']['self'] = ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D00'));
			$community_row_array[$index]['is_community_member'] = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_row['community_id']);
		}

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('community_row_array', $community_row_array);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
}

?>
