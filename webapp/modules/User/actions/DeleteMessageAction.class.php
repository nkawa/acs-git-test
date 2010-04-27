<?php
/**
 * メッセージ 削除
 *
 * @author  nakau
 * @version $Revision: 1.1 $ $Date: 2008/03/24 07:09:27 $
 */
class DeleteMessageAction extends BaseAction
{
	
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		// 必須チェック
		//    Validator でできないチェックはここで行う
		if (!$request->getParameter('selected_message')) {
			// エラーの場合、処理終了
			return $this->setError($controller, $request, $user, 'selected_message', 
					ACSMsg::get_msg('User', 'DeleteMessageAction.class.php', 'M001'));
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_user_community_id = $request->getParameter('id');
		// 対象となるメッセージIDを取得
		$target_message_id = $request->getParameter('selected_message');

		// 表示するページの所有者情報取得
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('target_message_id', $target_message_id);
		$request->setAttribute('move_id', $request->getParameter('move_id'));

		/* ------------ */
		/* 確認画面表示 */
		/* ------------ */
		if ($request->getParameter('action_type') == 'confirm') {
			return View::SUCCESS;
		}

		/* -------- */
		/* 削除処理 */
		/* -------- */
		elseif ($request->getParameter('action_type') == 'delete') {
			$move_id = $request->getParameter('move_id');
			ACSDB::_do_query("BEGIN");
			// フォルダ
			$folder_row_array = array();
			$delete_message_id_array = $request->getParameter('selected_message');
			if ($delete_message_id_array) {
				if ($move_id == 2) {
					foreach ($delete_message_id_array as $message_id) {
						// 削除処理
						$ret =ACSMessage::delete_send_message($message_id);
						if (!$ret) {
							ACSDB::_do_query("ROLLBACK;");
							print "ERROR: Delete message failed.";
							exit;
						}
					}
				} else {
					foreach ($delete_message_id_array as $message_id) {
						// 削除処理
						$ret =ACSMessage::delete_receive_message($message_id);
						if (!$ret) {
							ACSDB::_do_query("ROLLBACK;");
							print "ERROR: Delete message failed.";
							exit;
						}
					}
					
				}
			}

			ACSDB::_do_query("COMMIT;");

			// フォルダ表示アクション呼び出し
			$message_action  = $this->getControllerPath('User', 'MessageBox');
			$message_action .= '&id=' . $target_user_community_id;
			if($move_id == 2){
				$message_action .= '&move_id=2';
			}

			header("Location: $message_action");
		}
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		/* エラーメッセージをセッションにセット */
		$this->sendError($controller, $request, $user);

		// メッセージ表示アクション呼び出し
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_community_id = $request->getParameter('id');
		$move_id = $request->getParameter('move_id');

		$message_action = $this->getControllerPath('User', 'MessageBox');
		$message_action .= '&id=' . $target_user_community_id;
		if($move_id == 2){
				$message_action .= '&move_id=2';
			}
		header("Location: $message_action");
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('USER_PAGE_OWNER');
	}
}
?>
