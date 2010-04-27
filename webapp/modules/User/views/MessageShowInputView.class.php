<?php
/**
 * メッセージ詳細表示機能　Viewクラス
 * @package  acs/webapp/modules/User/views
 * MessageShowView::INPUT
 * @author   nakau  
 * @since	PHP 4.0
 */
// $Id: MessageShowView::INPUT.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $


class MessageShowInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$message_row = $request->getAttribute('message_row');

		// 加工
		//送信者のトップページURL
		$link_page_url['else_user_message_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Index') . '&id=' . $message_row['user_id'];

		// 投稿日時
		$message_row['post_date']  = ACSLib::convert_pg_date_to_str($message_row['post_date']);

		// メニュー部URL
		$menu['receiv_box_url'] = $this->getControllerPath('User', 'MessageBox') . '&id=' . $acs_user_info_row['user_community_id'];
		$menu['send_box_url'] = $this->getControllerPath('User', 'MessageBox') . '&id=' . $acs_user_info_row['user_community_id'] .'&move_id=2';
		
		// 返信ボタンURL
		$message_return_url = $this->getControllerPath('User', 'Message') . '&id=' . $message_row['user_id'].'&message_id='.$message_row['message_id'].'&move_id=4';

		// set
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('message_row', $message_row);
		$this->setAttribute('message_return_url', $message_return_url);
		$this->setAttribute('menu', $menu);
		$this->setAttribute('move_id', $request->getAttribute('move_id'));
		$this->setAttribute('link_page_url', $link_page_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('MessageShow.tpl.php');

		return parent::execute();
	}
}

?>
