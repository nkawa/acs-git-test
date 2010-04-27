<?php
// $Id: NewDiaryView_inline.class.php,v 1.9 2007/03/01 09:01:46 w-ota Exp $

class NewDiaryInputView extends BaseView
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

		// 新着日記一覧URL
		$new_diary_url = $this->getControllerPath(DEFAULT_MODULE, 'NewDiary') . '&id=' . $target_user_info_row['user_community_id'];

		// 表示件数制御
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');

		//---- アクセス制御 ----//
		$_new_diary_row_array = array();
		foreach ($new_diary_row_array as $index => $new_diary_row) {
			if (count($_new_diary_row_array) >= $display_count) {
				break;
			}

			// diary_trusted_community
			if ($new_diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
				$new_diary_row_array[$index]['trusted_community_row_array']
					 = $new_diary_row['trusted_community_row_array']
					 = ACSDiary::get_diary_trusted_community_row_array($new_diary_row['diary_id']);
			}

			$diary_target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($new_diary_row['community_id']);
			$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $diary_target_user_info_row);
			$new_diary_row = ACSAccessControl::get_valid_row_for_user_community($acs_user_info_row, $role_array, $new_diary_row);
			if ($new_diary_row) {
				array_push($_new_diary_row_array, $new_diary_row);
			}
		}
		$new_diary_row_array = $_new_diary_row_array;
		//----------------------//

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('new_diary_row_array', $new_diary_row_array);
		$this->setAttribute('new_diary_url', $new_diary_url);
		$this->setAttribute('get_days', $request->getAttribute('get_days'));

		// テンプレート
		$this->setTemplate('NewDiary.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("NewDiary", $this->render());

		return parent::execute();
	}
}

?>
