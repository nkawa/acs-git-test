<?php
// $Id: NewDiaryAction.class.php,v 1.7 2008/04/24 16:00:00 y-yuki Exp $

class NewDiaryAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
	
	}

	function execute() {
		$context = &$this->getContext();
		$controller = $context->getController();
		$user = $context->getUser();
		$request = $context->getRequest();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		if ($user_community_id == null || $user_community_id == '') {
			$user_community_id = $request->getAttribute("id");
		}
		
		// 他ユーザのデータが見えないようチェック
		if (!$this->get_execute_privilege()
				&& $acs_user_info_row["user_community_id"] != $user_community_id) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		// インライン表示の場合: 1(true)
		$inline_mode = $request->ACSgetParameter('inline_mode');
		if ($inline_mode == null || $inline_mode == '') {
			$inline_mode = $request->getAttribute("inline_mode");
		}

		// 取得範囲の指定
		$get_days = ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D02'), 
					($inline_mode ? 'NEW_INFO_TOP_TERM' : 'NEW_INFO_LIST_TERM'));
		$request->setAttribute('get_days', $get_days);

		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		if ($inline_mode) {
			$new_diary_row_array = ACSDiary::get_new_diary_row_array($user_community_id, $get_days, true);
		} else {
			$new_diary_row_array = ACSDiary::get_new_diary_row_array($user_community_id, $get_days);
		}

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('new_diary_row_array', $new_diary_row_array);

		if ($inline_mode) {
			return View::INPUT;
		} else {
			return VIEW::SUCCESS;
		}
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 本人の場合はOK
		if (!$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
	}
}

?>
