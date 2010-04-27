<?php
// $Id: NewBBSSuccessView.class.php,v 1.5 2007/03/01 09:01:46 w-ota Exp $

class NewBBSSuccessView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$new_bbs_row_array = $request->getAttribute('new_bbs_row_array');

		// 加工
		foreach ($new_bbs_row_array as $index => $new_bbs_row) {
			$new_bbs_row_array[$index]['bbs_res_url'] = $this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $new_bbs_row['community_id'] . '&bbs_id=' . $new_bbs_row['bbs_id'];
			$new_bbs_row_array[$index]['is_unread'] = ACSLib::get_boolean($new_bbs_row['is_unread']);
		}

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $new_bbs_row_array, $display_count);

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('new_bbs_row_array', $new_bbs_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('get_days', $request->getAttribute('get_days'));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('NewBBS.tpl.php');

		return parent::execute();
	}
}

?>
