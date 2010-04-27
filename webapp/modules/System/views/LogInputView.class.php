<?php
// $Id: LogView_input.class.php,v 1.2 2006/11/20 08:44:22 w-ota Exp $

class LogInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		// get
		// ユーザ情報一覧
		$log_row_array = $request->getAttribute('log_row_array');
		$form = $request->getAttribute('form');

		// 加工
		foreach ($log_row_array as $index => $log_row) {
			$log_row_array[$index]['log_date'] = ACSLib::convert_pg_date_to_str($log_row['log_date'], true, true, true);
			$log_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $log_row['user_community_id'];
			if (ACSLib::get_boolean($log_row_array[$index]['administrator_flag'])) {
				// システム管理者
				$log_row_array[$index]['user_level_name'] = ACSMsg::get_msg('System', 'LogInputView.class.php', 'M001');
			} else {
				// ログインユーザ
				$log_row_array[$index]['user_level_name'] = ACSMsg::get_msg('System', 'LogInputView.class.php', 'M002');
			}
			if (ACSLib::get_boolean($log_row_array[$index]['operation_result'])) {
				// 成功
				$log_row_array[$index]['operation_result_name'] = ACSMsg::get_msg('System', 'LogInputView.class.php', 'M003');
			} else {
				// 失敗
				$log_row_array[$index]['operation_result_name'] = ACSMsg::get_msg('System', 'LogInputView.class.php', 'M004');
			}
		}

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D07'), 'LOG_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $log_row_array, $display_count);

		// URL
		$action_url = $this->getControllerPath();

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('Log.tpl.php');

		// set
		$this->setAttribute('form', $form);
		$this->setAttribute('log_row_array', $log_row_array);
		$this->setAttribute('paging_info', $paging_info);

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('module', 'System');
		$this->setAttribute('action', 'Log');

		return parent::execute();
	}
}

?>
