<?php
/**
 * マイページ機能　actionクラス
 * デザイン選択
 * @package  acs/webapp/modules/User/action
 * SelectDesignAction
 * @author   teramoto
 * @since	PHP 4.0
 */
// $Id: SelectDesignAction.class.php,v 1.1 2007/03/27 02:12:41 w-ota Exp $

class SelectDesignAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 表示対象となるユーザコミュニティIDを取得
		$user_community_id = $acs_user_info_row['user_community_id'];
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// デザイン情報取得
		$select_design_row_array = 
				ACSCss::get_style_selection_list_array(
				ACSMsg::get_lang(), ACS_SELECTION_CSS_DIR );

		//$style_url = ACSSystemConfig::get_keyword_value(
		//		ACSMsg::get_mst('system_config_group','D01'), 'DESIGN_STYLE_CSS_URL');
		$style_url = ACS_SELECTION_CSS_DIR;

		// 設定済のマイページデザインを取得する
		$selection_css_row = ACSCommunity::get_contents_row($user_community_id, 
										ACSMsg::get_mst('contents_type_master','D53'));
		$selection_css = $selection_css_row['contents_value'] == '' ? 
				ACS_DEFAULT_SELECTION_CSS_FILE : $selection_css_row['contents_value'];

		// set
		$request->setAttribute('style_url', $style_url);
		$request->setAttributeByRef('acs_user_info_row', $acs_user_info_row);
		$request->setAttributeByRef('select_design_row_array', $select_design_row_array);
		$request->setAttributeByRef('user_community_id', $user_community_id);
		$request->setAttribute('selection_css', $selection_css);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 更新対象となるユーザコミュニティIDを取得
		$user_community_id = $acs_user_info_row['user_community_id'];

		$form = $request->ACSGetParameters();

		// デザインの登録実行
		$ret = ACSUser::set_design($acs_user_info_row,$form['css_file']);

		if ($ret) {
			// 書き込み後、GETの処理へ
			header("Location: ./");
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

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 本人で、LDAP認証以外の場合はOK
		if ($user->hasCredential('USER_PAGE_OWNER')) {
			return true;
		}
		return false;
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* 必須チェック */
		parent::regValidateName($validatorManager, 
				"css_file", 
				true, 
				ACSMsg::get_msg('User', 'SelectDesignAction.class.php', 'M001'));
	}

	function handleError () {
		// 入力画面表示
		return $this->getDefaultView();
	}
}
?>
