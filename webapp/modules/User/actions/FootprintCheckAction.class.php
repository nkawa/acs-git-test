<?php
/**
 * マイページ機能　actionクラス
 * 足跡確認
 * @package  acs/webapp/modules/User/action
 * FootprintCheckAction
 * @author   teramoto
 * @since	PHP 4.0
 */
// $Id: FootprintCheckAction.class.php,v 1.1 2007/03/27 02:12:41 w-ota Exp $

class FootprintCheckAction extends BaseAction
{
	// GET
	function execute() {
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

		// 足跡
		$get_days = ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D02'), FOOTPRINT_LIST_TERM);
		$footprint_info_row_array = ACSUser::get_footprint_row($user_community_id, $get_days);

		// set
		$request->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('user_community_id', $user_community_id);
		$request->setAttribute('footprint_info_row_array', $footprint_info_row_array);

		return View::SUCCESS;
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
}
?>
