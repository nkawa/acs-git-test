<?php
// $Id: AddFriendsAction.class.php,v 1.11 2006/11/20 08:44:25 w-ota Exp $

class AddFriendsAction extends BaseAction
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

		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 表示対象となるユーザコミュニティIDを取得
		$user_community_id = $request->getParameter('id');

		$form = $request->ACSGetParameters();

		// マイフレンズ追加 承認待ち を登録する
		$waiting_id = ACSWaiting::set_waiting_for_add_friends($user_community_id, $acs_user_info_row['user_community_id'], $form['message']);

		// マイフレンズ追加 承認依頼通知メール
		ACSWaiting::send_admission_request_notify_mail($waiting_id);

		// forward
		$done_obj = new ACSDone();
		$done_obj->set_title(ACSMsg::get_msg('User', 'AddFriendsAction.class.php', 'M001'));
		$done_obj->set_message(ACSMsg::get_msg('User', 'AddFriendsAction.class.php', 'M002'));
		$done_obj->add_link(ACSMsg::get_msg('User', 'AddFriendsAction.class.php', 'M003'), './');

		$request->setAttribute('done_obj', $done_obj);
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		return array('NOT_FRIENDS');
	}
}

?>
