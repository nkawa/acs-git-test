<?php
// $Id: CommunityListView::SUCCESS.class.php,v 1.9 2007/03/28 02:51:48 w-ota Exp $


class CommunityListSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$community_row_array = $request->getAttribute('community_row_array');
		$community_row_array_num = count($community_row_array);

		foreach ($community_row_array as $index => $community_row) {
			$community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];
			$community_row_array[$index]['image_url'] = ACSCommunity::get_image_url($community_row['community_id'], 'thumb');
			$community_row_array[$index]['community_member_num'] = ACSCommunity::get_community_member_num($community_row['community_id']);

			// メンバでない非公開コミュニティは表示対象にしない
			if ($community_row['contents_row_array']['self']['open_level_name'] == ACSMsg::get_mst('open_level_master','D03') && !$community_row['is_community_member']) {
				unset($community_row_array[$index]);
			}
		}

		// 本人のページかどうか
		if ($target_user_info_row['user_community_id'] == $acs_user_info_row['user_community_id']) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}

		//他人の日記を閲覧している場合のトップページURL
		$link_page_url['else_user_top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $target_user_info_row['community_id'];

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $community_row_array, $display_count);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('CommunityList.tpl.php');

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('community_row_array', $community_row_array);
		$this->setAttribute('community_row_array_num', $community_row_array_num);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('link_page_url', $link_page_url);

		return parent::execute();
	}
}

?>
