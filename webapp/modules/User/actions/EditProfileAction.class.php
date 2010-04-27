<?php
// $Id: EditProfileAction.class.php,v 2.0 2008/04/24 16:00:00 y-yuki Exp $

class EditProfileAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$open_level_master_array = ACSDB::get_master_array('open_level');

		if ($acs_user_info_row['is_acs_user'] && $acs_user_info_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
			// マイページ全体が非公開のユーザ //

			// 非公開ユーザコミュニティの情報を取得
			$target_user_info_row = ACSUser::get_user_profile_row($acs_user_info_row['user_community_id'], 'include_private');

		} elseif (!$acs_user_info_row['is_acs_user'] && $acs_user_info_row['is_ldap_user']) {
			// システムに未登録のLDAPユーザ //

			$ldap_user_info_row = ACSLDAP::get_ldap_user_info_row($acs_user_info_row['user_id']);

			$target_user_info_row['contents_row_array'] = array();

			// ニックネームのデフォルト値を氏名とする
			$target_user_info_row['community_name'] = $ldap_user_info_row['user_name'];

			$target_user_info_row['contents_row_array']['user_name'] = ACSCommunity::get_empty_contents_row(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D01'));
			$target_user_info_row['contents_row_array']['user_name']['contents_value'] = $ldap_user_info_row['user_name'];
			$target_user_info_row['contents_row_array']['mail_addr'] = ACSCommunity::get_empty_contents_row(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D02'));
			$target_user_info_row['contents_row_array']['mail_addr']['contents_value'] = $ldap_user_info_row['mail_addr'];
			$target_user_info_row['contents_row_array']['belonging'] = ACSCommunity::get_empty_contents_row(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D03'));
			$target_user_info_row['contents_row_array']['belonging']['contents_value'] = $ldap_user_info_row['belonging'];
			$target_user_info_row['contents_row_array']['friends_list'] = ACSCommunity::get_empty_contents_row(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D11'));
			$target_user_info_row['contents_row_array']['mail_lang'] = ACSCommunity::get_empty_contents_row(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D51'));

			// 新規LDAPユーザである
			$is_new_ldap_user = true;

		} else {
			// 対象となるユーザコミュニティIDを取得
			$user_community_id = $request->ACSgetParameter('id');
			// プロフィール
			$target_user_info_row = ACSUser::get_user_profile_row($user_community_id);
		}

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('is_new_ldap_user', $is_new_ldap_user);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');

		$form = $request->ACSGetParameters();

		if ($acs_user_info_row['is_acs_user']) {
			// 氏名は変更しない
			$form['user_name'] = $acs_user_info_row['user_name'];

		} else {
			$form['user_id'] = $acs_user_info_row['user_id'];
			// LDAPから氏名取得
			$ldap_user_info_row = ACSLDAP::get_ldap_user_info_row($acs_user_info_row['user_id']);
			$form['user_name'] = $ldap_user_info_row['user_name'];
		}

		// INSERT
		$ret = ACSUser::set_user_profile($acs_user_info_row, $form);

		// 言語の切替
		ACSMsg::set_lang($form['mail_lang']);
		ACSMsg::set_lang_cookie($form['mail_lang']);

		if ($ret) {
			// 書き込み後、GETの処理へ
			header("Location: ./");
		} else {
			exit;
		}
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
				"mail_addr", 
				true, 
				ACSMsg::get_msg('User', 'EditProfileAction.class.php', 'M001'));
		parent::regValidateName($validatorManager, 
				"community_name", 
				true, 
				ACSMsg::get_msg('User', 'EditProfileAction.class.php', 'M002'));
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
