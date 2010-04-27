<?php
// $Id: EditFriendsGroupMemberAction.class.php,v 1.6 2006/11/20 08:44:25 w-ota Exp $

class EditFriendsGroupMemberAction extends BaseAction
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
		// 対象となるマイフレンズグループコミュニティIDを取得
		$friends_group_community_id = $request->ACSgetParameter('community_id');
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// get
		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);
		// マイフレンズ一覧
		$friends_row_array = ACSUser::get_friends_row_array($user_community_id);
		// 指定されたマイフレンズグループの情報
		$friends_group_row = ACSCommunity::get_community_row($friends_group_community_id);
		// 指定されたマイフレンズグループのメンバ一覧
		$friends_group_member_row_array = ACSCommunity::get_community_member_user_info_row_array($friends_group_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('friends_row_array', $friends_row_array);
		$request->setAttribute('friends_group_row', $friends_group_row);
		$request->setAttribute('friends_group_member_row_array', $friends_group_member_row_array);

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
		// 対象となるマイフレンズグループコミュニティIDを取得
		$friends_group_community_id = $request->ACSgetParameter('community_id');

		$form = $request->ACSGetParameters();
		$form['user_community_id'] = $user_community_id;

		// 更新
		ACSUser::update_friends_group_member($form);

		$friends_group_list_top_page_url = $this->getControllerPath('User', 'FriendsGroupList') . '&id=' . $user_community_id;
		header("Location: $friends_group_list_top_page_url");
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

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* 必須チェック */
		parent::regValidateName($validatorManager, 
				"community_name", 
				true, 
				ACSMsg::get_msg('User', 'EditFriendsGroupMemberAction.class.php', 'M001'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();

		// 入力値を set
		$form = $request->ACSGetParameters();
		$request->setAttribute('form', $form);

		// 入力画面表示
		return $this->getDefaultView();
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
