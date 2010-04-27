<?php
// 外部RSS情報自動取込実行
// $Id: GetExternalRSSAction.class.php,v 1.1 2007/03/28 05:58:18 w-ota Exp $

class GetExternalRSSAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');
		$community_row = ACSCommunity::get_community_row($community_id);

		// 実行
		ACSExternalRSS::do_process($community_row);

		$bbs_url = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_id;
		header("Location: $bbs_url");
	}

	function isSecure() {
		return false;
	}

	function getPrivilege(&$controller, &$request, &$user) {
		return array('COMMUNITY_ADMIN');
	}
}

?>
