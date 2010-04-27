<?php
// $Id: DeleteFriendsAction.class.php,v 1.4 2006/11/20 08:44:25 w-ota Exp $

class DeleteFriendsAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		// 削除するユーザコミュニティIDを取得
		$delete_user_community_id = $request->ACSgetParameter('delete_user_community_id');
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// 削除するユーザ情報
		$delete_user_info_row = ACSUser::get_user_info_row_by_user_community_id($delete_user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('delete_user_info_row', $delete_user_info_row);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		$delete_user_community_id = $request->ACSgetParameter('delete_user_community_id');

		$form = $request->ACSGetParameters();

		// フレンズ削除
		ACSUser::delete_friends($user_community_id, $delete_user_community_id);

		$friends_list_top_page_url = $this->getControllerPath('User', 'FriendsList') . '&id=' . $user_community_id;

		// ACSDone
		$done_obj = new ACSDone();
		$done_obj->set_title(ACSMsg::get_msg('User', 'DeleteFriendsAction.class.php','M001'));
		$done_obj->set_message(ACSMsg::get_msg('User', 'DeleteFriendsAction.class.php', 'M002'));
		$done_obj->add_link(ACSMsg::get_msg('User', 'DeleteFriendsAction.class.php', 'M003'), $friends_list_top_page_url);
		$request->setAttribute('done_obj', $done_obj);
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods() {
		return Request::POST;
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
