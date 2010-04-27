<?php
/**
 * システム　ユーザ管理　ユーザ情報変更画面 Viewクラス
 * @package  acs/webapp/modules/System/views
 * UserListView_succes
 * @author   w-ota v 1.2 2006/01/19 10:03:42
 * @alter	akitsu  
 * @since	PHP 4.0
 */
// $Id: UserListView_success.class.php,v 1.6 2008/03/24 07:00:36 y-yuki Exp $


class UserListSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$form = $request->getAttribute('form');

		// get
		$user_info_row_array = $request->getAttribute('user_info_row_array');
		foreach ($user_info_row_array as $index => $user_info_row) {
			$user_info_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $user_info_row['user_community_id'];
			$user_info_row_array[$index]['edit_page_url'] = $this->getControllerPath('System', 'EditUser') . '&id=' . $user_info_row['user_community_id'];
			$user_info_row_array[$index]['delete_page_url'] = $this->getControllerPath('System', 'DeleteUser') . '&id=' . $user_info_row['user_community_id'];
			$user_info_row_array[$index]['login_info_url'] = $this->getControllerPath('System', 'LoginInfo') . '&id=' . $user_info_row['user_community_id'];
		}

		$add_user_url = $this->getControllerPath('System', 'AddUser');

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'USER_SEARCH_RESULT_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $user_info_row_array, $display_count);

		// URL
		$action_url = $this->getControllerPath();

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('UserList.tpl.php');

		// set
		$this->setAttribute('form', $form);
		$this->setAttribute('user_info_row_array', $user_info_row_array);
		$this->setAttribute('paging_info', $paging_info);

		$this->setAttribute('add_user_url', $add_user_url);

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('module', 'System');
		$this->setAttribute('action', 'UserList');

		return parent::execute();
	}
}

?>
