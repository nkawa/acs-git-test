<?php
/**
 * ダイアリーRSS  Actionクラス
 */
// $Id: DiaryRSSAction.class.php,v 1.1 2006/12/13 09:51:46 w-ota Exp $

class DiaryRSSAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// 表示対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');

		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_profile_row($user_community_id);

		// 取得期間
		$term = intval($request->ACSgetParameter('term'));
		if (!$term) {
			// システム設定: マイページ: ダイアリーRSS取得期間
			$term = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'DIARY_RSS_TERM');
		}

		// 最新のダイアリーRSS
		$diary_row_array = ACSDiary::get_new_diary_rss_row_array($user_community_id, $term);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('diary_row_array', $diary_row_array);
		$request->setAttribute('term', $term);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
}

?>
