<?php
// $Id: NewDiaryView::SUCCESS.class.php,v 1.6 2007/03/01 09:01:46 w-ota Exp $

class NewDiarySuccessView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$new_diary_row_array = $request->getAttribute('new_diary_row_array');

		// 加工
		foreach ($new_diary_row_array as $index => $new_diary_row) {
			$new_diary_row_array[$index]['diary_comment_url'] = $this->getControllerPath(DEFAULT_MODULE, 'DiaryComment') . '&id=' . $new_diary_row['community_id'] . '&diary_id=' . $new_diary_row['diary_id'];
			$new_diary_row_array[$index]['is_unread'] = ACSLib::get_boolean($new_diary_row['is_unread']);
		}

		//---- アクセス制御 ----//
		foreach ($new_diary_row_array as $index => $new_diary_row) {
			// diary_trusted_community
			if ($new_diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
				$new_diary_row_array[$index]['trusted_community_row_array']
					 = $new_diary_row['trusted_community_row_array']
					 = ACSDiary::get_diary_trusted_community_row_array($new_diary_row['diary_id']);
			}

			// 簡易処理:
			$diary_target_user_info_row['user_community_id'] = $new_diary_row['community_id'];
			$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $diary_target_user_info_row);
			$new_diary_row = ACSAccessControl::get_valid_row_for_user_community($acs_user_info_row, $role_array, $new_diary_row);
			if (!$new_diary_row) {
				unset($new_diary_row_array[$index]);
			}
		}
		//----------------------//

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $new_diary_row_array, $display_count);

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('new_diary_row_array', $new_diary_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('get_days', $request->getAttribute('get_days'));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('NewDiary.tpl.php');

		return parent::execute();
	}
}

?>
