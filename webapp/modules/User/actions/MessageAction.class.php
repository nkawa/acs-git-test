<?php
/**
 * メッセージ　Actionクラス
 * 
 * MessageAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   nakau                    
 * @since    PHP 4.0
 */
// $Id: MessageAction.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $

class MessageAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		// 表示対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');

		// 他ユーザのデータが見えないようチェック
		if (!$this->get_execute_privilege()) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);

		return View::INPUT;
	}
	
	function execute() {
		
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$request = $context->getRequest();
		$user = $context->getUser();

		// 非ログインユーザはNG
		if ($user->hasCredential('PUBLIC_USER')) {
			return false;
		}

		// ユーザ情報を取得
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		if ($request->getParameter('move_id') == 4){
			$message_id = $request->getParameter('message_id');
			// 他ユーザの受信メッセージが見えないようチェック
			if (!ACSMessage::check_message_receiver($message_id, $acs_user_info_row["user_community_id"])) {
				return false;
			}
		}
		// 本人の場合はOK
		return true;
	}

}

?>
