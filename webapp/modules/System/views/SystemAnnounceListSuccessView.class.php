<?php
// $Id: SystemAnnounceListView_success.class.php,v 1.3 2006/11/20 08:44:22 w-ota Exp $

class SystemAnnounceListSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$system_announce_row_array = $request->getAttribute('system_announce_row_array');

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $system_announce_row_array, $display_count);

		foreach ($system_announce_row_array as $index => $system_announce_row) {
			$system_announce_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $system_announce_row['community_id'];
			$system_announce_row_array[$index]['post_date'] = ACSLib::convert_pg_date_to_str($system_announce_row['post_date'], true, true);
			$system_announce_row_array[$index]['expire_date'] = ACSLib::convert_pg_date_to_str($system_announce_row['expire_date'], false, false, false);
			$system_announce_row_array[$index]['is_expire'] = ACSLib::get_boolean($system_announce_row['is_expire']);
			$system_announce_row_array[$index]['system_announce_delete_flag'] = ACSLib::get_boolean($system_announce_row['system_announce_delete_flag']);
			$system_announce_row_array[$index]['delete_system_announce_url'] = $this->getControllerPath('System', 'DeleteSystemAnnounce')
				 . '&system_announce_id=' . $system_announce_row['system_announce_id'];
		}


		// システムアナウンス(システムからのお知らせ)作成URL
		$create_system_announce_url = $this->getControllerPath('System', 'CreateSystemAnnounce');

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SystemAnnounceList.tpl.php');

		// set
		$this->setAttribute('system_announce_row_array', $system_announce_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('create_system_announce_url', $create_system_announce_url);

		return parent::execute();
	}
}

?>
