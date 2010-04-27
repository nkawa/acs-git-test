<?php
/**
 * メッセージ機能　Viewクラス
 * @package  acs/webapp/modules/User/views
 * MessageView::INPUT
 * @author   nakau
 * @since	PHP 4.0
 */
// $Id: MessageView::INPUT.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $


class MessageInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');

		//他人のトップページURL
		$link_page_url['else_user_Message_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Index') . '&id=' . $target_user_info_row['community_id'];
		
		//確認画面ボタンで確認画面を表示
		$action_url = $this->getControllerPath('User', 'MessagePre') . '&id=' . $target_user_info_row['user_community_id'] ."&move_id=1";
		
		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('link_page_url', $link_page_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('Message.tpl.php');
		
		// 確認画面からキャンセルボタンで戻ってきたときのみの処理
		if($request->getParameter('move_id') == 3){
			//ユーザ入力情報
			$form = $user->getAttribute('new_form_obj');

			$this->setAttribute('form', $form);
			$this->setAttribute('move_id', $request->getParameter('move_id'));
		}
		// メッセージ返信ボタン押下時の処理
		if($request->getParameter('move_id') == 4){
			$message_id = $request->getParameter('message_id');
			//引用メッセージ取得
			$message_row = ACSMessage::get_message_row($message_id);
			$form['subject'] = $message_row['subject'];
			$form['body'] = $message_row['body'];
			
			$this->setAttribute('form', $form);
			$this->setAttribute('move_id', $request->getParameter('move_id'));
		}

		return parent::execute();
	}
}

?>
