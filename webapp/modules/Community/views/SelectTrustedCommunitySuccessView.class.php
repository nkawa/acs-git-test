<?php
// $Id: SelectTrustedCommunityView::SUCCESS.class.php,v 1.6 2006/11/20 08:44:15 w-ota Exp $


class SelectTrustedCommunitySuccessView extends SimpleBaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$category_group_master_row_array = $request->getAttribute('category_group_master_row_array');
		$community_row_array = $request->getAttribute('community_row_array');
		$form = $request->getAttribute('form');

		// カテゴリマスタ一覧
		$category_master_row_array = array();
		array_push($category_master_row_array, array('category_code' => 0, 'category_name' => ACSMsg::get_mst('file_category_master','D0000')));
		foreach ($category_group_master_row_array as $category_group_master_row) {
			foreach ($category_group_master_row['category_master_row_array'] as $category_master_row) {
				array_push($category_master_row_array, $category_master_row);
			}
		}

		// コミュニティ一覧
		foreach ($community_row_array as $index => $community_row) {
			$community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];
		}

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D03'), 'COMMUNITY_SEARCH_RESULT_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $community_row_array, $display_count);

		// URL
		$action_url = $this->getControllerPath();

		// set
		$this->setAttribute('category_master_row_array', $category_master_row_array);

		$this->setAttribute('form', $form);
		$this->setAttribute('community_row_array', $community_row_array);
		$this->setAttribute('paging_info', $paging_info);

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('module', 'Community');
		$this->setAttribute('action', 'SelectTrustedCommunity');

		$this->setAttribute('form_name', $request->getAttribute('form_name'));
		$this->setAttribute('prefix', $request->getAttribute('prefix'));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SelectTrustedCommunity.tpl.php');

		return parent::execute();
	}
}

?>
