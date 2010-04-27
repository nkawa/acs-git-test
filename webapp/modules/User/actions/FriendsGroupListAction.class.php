<?php
// $Id: FriendsGroupListAction.class.php,v 1.4 2006/03/28 04:38:13 kuwayama Exp $

class FriendsGroupListAction extends BaseAction
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
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// マイフレンズグループ
		$friends_group_row_array = ACSUser::get_friends_group_row_array($user_community_id);
		foreach ($friends_group_row_array as $index => $friends_group_row) {
			$friends_group_row_array[$index]['friends_row_array'] = ACSCommunity::get_community_member_user_info_row_array($friends_group_row['community_id']);
		}

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('friends_group_row_array', $friends_group_row_array);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 本人で、LDAP認証以外の場合はOK
		if ($user->hasCredential('USER_PAGE_OWNER')) {
			return true;
		}
		return false;
	}
}

?>
