<?php
// $Id: InviteToCommunityAction.class.php,v 1.4 2006/11/20 08:44:12 w-ota Exp $

class InviteToCommunityAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		// コミュニティメンバ一覧
		$community_member_user_info_row_array = ACSCommunity::get_community_member_user_info_row_array($community_id);

		// マイフレンズ一覧
		$friends_row_array = ACSUser::get_friends_row_array($acs_user_info_row['user_community_id']);

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('community_member_user_info_row_array', $community_member_user_info_row_array);
		$request->setAttribute('friends_row_array', $friends_row_array);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$form = $form = $request->ACSGetParameters();

		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);


		ACSDB::_do_query("BEGIN");

		// 生成されたwaiting_idの配列
		$waiting_id_array = array();

		foreach ($form['user_community_id_array'] as $invited_user_community_id) {
			// コミュニティ招待承認待ち登録
			$waiting_id = ACSWaiting::set_waiting_for_invite_to_community($invited_user_community_id, $community_id, $acs_user_info_row['user_community_id'], $form['message']);
			if (!$waiting_id) {
				ACSDB::_do_query("ROLLBACK");
				break;
			}
			// 生成されたwaiting情報を保持
			array_push($waiting_id_array, $waiting_id);
		}

		if ($waiting_id) {
			ACSDB::_do_query("COMMIT");
		}


		// 生成されたwaiting情報を元に複数メール送信
		foreach ($waiting_id_array as $waiting_id) {
			// コミュニティ招待承認依頼通知メール
			ACSWaiting::send_admission_request_notify_mail($waiting_id);
		}

		// forward
		$done_obj = new ACSDone();
		$done_obj->set_title(ACSMsg::get_msg('Community', 'InviteToCommunityAction.class.php', 'M001'));
		$done_obj->set_message(ACSMsg::get_msg('Community', 'InviteToCommunityAction.class.php', 'M002'));
		$done_obj->add_link( ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'InviteToCommunityAction.class.php', 'BACK_TO_CM'),
				array("{COMMUNITY_NAME}" => $community_row['community_name'])),
				$this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id']);

		$request->setAttribute('done_obj', $done_obj);
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* 必須チェック */
		parent::regValidateName($validatorManager, 
				"user_community_id_array", 
				true, 
				ACSMsg::get_msg('Community', 'InviteToCommunityAction.class.php', 'M003'));
		parent::regValidateName($validatorManager, 
				"message", 
				true, 
				ACSMsg::get_msg('Community', 'InviteToCommunityAction.class.php', 'M004'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		// 入力値を set
		$form = $request->ACSGetParameters();
		$request->setAttribute('form', $form);

		// 入力画面表示
		return $this->getDefaultView();
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_ADMIN');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// コミュニティ管理者はOK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		return false;
	}
}

?>
