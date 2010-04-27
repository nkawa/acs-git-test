<?php
/**
 * メッセージ　詳細　Actionクラス
 * 
 * MessageShowAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   nakau					 
 * @since	PHP 4.0
 */
// $Id: MessageShowAction.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $

class MessageShowAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// 対象のmessage_idを取得
		$message_id = $request->ACSgetParameter('message_id');

		// ユーザ情報
		$user_community_id = $request->ACSgetParameter('id');
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);
		
		// 他ユーザのデータが見えないようチェック
		if ($this->get_execute_privilege() == 2
				&& $acs_user_info_row["user_community_id"] != $user_community_id) {
			// ログイン画面に遷移
			$controller->forward("User", "Login");
			return;
		}
		if ($this->get_execute_privilege() == 1
				&& $acs_user_info_row["user_community_id"] != $user_community_id) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		//送信済画面の処理
		$move_id = $request->getParameter('move_id');
		if($move_id == 2){
			// 送信済メッセージ詳細
			$message_row = ACSMessage::get_send_message_row($message_id);
		} else {
			// 受信メッセージ詳細
			$message_row = ACSMessage::get_receive_message_row($message_id);
			// 未読・既読判定
			if ($message_row['read_flag'] == "f") {
				// 未読時、DBのフラグ変更
				$message_receiver_id = $message_row['message_receiver_id'];
				ACSDB::_do_query("BEGIN");
				// message_receiverテーブル情報変更
				$ret = ACSMessage::read_message($message_receiver_id);
				if($ret){
					ACSDB::_do_query("COMMIT");
				}else{
					ACSDB::_do_query("ROLLBACK");
				}
			}
		}
		
		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('message_row', $message_row);
		$request->setAttribute('move_id', $move_id);

		return View::INPUT;
	}
	
	function getRequestMethods() {
		return Request::POST;
	}
	
	function execute() {
		
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

		// 非ログインユーザ、本人以外はNG
		if ($user->hasCredential('PUBLIC_USER')){
			return 2;
		}
                if (!$user->hasCredential('USER_PAGE_OWNER')) {
			return 1;
                }
		return 0;
/*
		if ($user->hasCredential('PUBLIC_USER')
				 || !$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
*/
	}

}
?>
