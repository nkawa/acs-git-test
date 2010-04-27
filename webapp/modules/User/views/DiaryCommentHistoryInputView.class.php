<?php
// $Id: DiaryCommentHistoryView_inline.class.php,v 1.8 2007/03/01 09:01:46 w-ota Exp $

class DiaryCommentHistoryInputView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
	
		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$commented_diary_row_array = $request->getAttribute('commented_diary_row_array');

		// 加工
		foreach ($commented_diary_row_array as $index => $commented_diary_row) {
			$commented_diary_row_array[$index]['diary_comment_url'] = $this->getControllerPath(DEFAULT_MODULE, 'DiaryComment') . '&id=' . $commented_diary_row['community_id'] . '&diary_id=' . $commented_diary_row['diary_id'];
			$commented_diary_row_array[$index]['is_unread'] = ACSLib::get_boolean($commented_diary_row['is_unread']);
		}

		// 新着日記一覧URL
		$diary_comment_history_url = $this->getControllerPath(DEFAULT_MODULE, 'DiaryCommentHistory') . '&id=' . $target_user_info_row['user_community_id'];

		// 表示件数制御 //
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
		$commented_diary_row_array = array_slice($commented_diary_row_array, 0, $display_count);

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('commented_diary_row_array', $commented_diary_row_array);
		$this->setAttribute('diary_comment_history_url', $diary_comment_history_url);
		$this->setAttribute('get_days', $request->getAttribute('get_days'));

		// テンプレート
		$this->setTemplate('DiaryCommentHistory.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("DiaryCommentHistory", $this->render());

		return parent::execute();
	}
}

?>
