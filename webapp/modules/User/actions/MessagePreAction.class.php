<?php
/**
 * メッセージ　登録・表示機能　actionクラス
 * メッセージ情報　確認・登録処理
 * @package  acs/webapp/modules/User/action
 * @author   nakau
 * @since    PHP 4.0
 * @version  $Revision: 1.1 $ $Date: 2008/03/06
 */
// $Id: MessagePreAction.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $


class MessagePreAction extends BaseAction
{
	//field
	var $form;

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		//mode　画面の遷移を取得する
		$move_id = $request->getParameter('move_id');
		// ユーザー情報
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// 対象となるUserIDを取得
		$user_community_id = $request->getParameter('id');
		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);
		/* 入力画面より */
		if($move_id==1){
		//☆☆　ここからほぼ同じ
			// 画面上のフォーム情報を取得する
			$form['subject'] = $request->getParameter('subject');		//件名：subject
			$form['body'] = $request->getParameter('body');				//内容：body
			$form['info_mail'] = $request->getParameter('info_mail');	//メール通知：info_mail
			$user->setAttribute('new_form_obj',$form);
			$request->setAttribute('target_user_info_row', $target_user_info_row);
		//☆☆　ここまでほぼ同じ
			return View::SUCCESS;

		/* 登録確定ボタン「はい」より */
		}else if($move_id==2){
			$acs_user_info_row = $user->getAttribute('acs_user_info_row');
			$user_community_id = $request->getParameter('id');
		//☆☆　ここからほぼ同じ
			// 画面上のフォーム情報を取得する
			$form = $user->getAttribute('new_form_obj');
			$new_file_obj = $form['file_obj'];
			$form['user_community_id'] = $user_community_id;
			$form['acs_user_info_id'] = $acs_user_info_row['user_community_id'];
		//☆☆　ここまでほぼ同じ
			// DBへの書き込み等
			ACSDB::_do_query("BEGIN");
			// Messageテーブル情報
			$ret = ACSMessage::set_message($form);
			if($ret){
				ACSDB::_do_query("COMMIT");
			}else{
				ACSDB::_do_query("ROLLBACK");
			}
		
			// 通知メール送信処理
			if ($form['info_mail'] == "on") {
				ACSMessage::send_info_mail($ret, $form['user_community_id'], $form['acs_user_info_id']);
			}

			// 書き込み後、GETの処理へ
			$action_url =  $this->getControllerPath('User', 'MessageBox') . '&id=' . $acs_user_info_row['user_community_id'].'&move_id=2';
			header("Location: $action_url");
		}
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$request = $context->getRequest();
		$move_id = $request->getParameter('move_id');

		// 入力画面からの場合のみ、入力チェックをする
		if ($move_id == 1) {
			/* 必須チェック */
			parent::regValidateName($validatorManager, 
					"subject", 
					true, 
					ACSMsg::get_msg('User', 'MessagePreAction.class.php', 'M001'));
		}
	}

	function handleError () {
		// 入力画面表示
		return $this->execute();
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('EXECUTE');
	}
	
	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 非ログインユーザ、本人以外はNG
		if ($user->hasCredential('PUBLIC_USER')
				 || !$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
	}
}

?>
