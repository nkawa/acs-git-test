<?php
/**
 * システム　ユーザ管理　ログイン情報画面 Viewクラス
 * @package  acs/webapp/modules/System/views
 * LoginInfoView_succes
 * @author   nakau v 1.0 2008/03/13 10:03:42
 * @since	PHP 4.0
 */
// $Id: LoginInfoView_success.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $


class LoginInfoSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$login_info_row_array = $request->getAttribute('login_info_row_array');

		$top_page_url = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $target_user_info_row['community_id'];
		
		// 加工
		foreach ($login_info_row_array as $index => $login_info_row) {
			$login_info_row_array[$index]['login_date'] = ACSLib::convert_pg_date_to_str($login_info_row['login_date']);
			$login_info_row_array[$index]['logout_date'] = ACSLib::convert_pg_date_to_str($login_info_row['logout_date']);
		}

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'USER_SEARCH_RESULT_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $login_info_row_array, $display_count);


		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('LoginInfo.tpl.php');

		// set
		//$this->setAttribute('form', $form);
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('login_info_row_array', $login_info_row_array);
		$this->setAttribute('top_page_url', $top_page_url);
		$this->setAttribute('paging_info', $paging_info);


		$this->setAttribute('module', 'System');
		$this->setAttribute('action', 'LoginInfo');

		return parent::execute();
	}
}

?>
