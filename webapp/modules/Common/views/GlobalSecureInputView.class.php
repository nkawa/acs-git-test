<?php
/**
 * アクセス不可メッセージ表示
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/03/27 07:49:29 $
 */
class GlobalSecureSuccessView extends BaseView
{
	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// get
		// ユーザ情報一覧
		$user_info_row_array = $request->getAttribute('user_info_row_array');
		$form = $request->getAttribute('form');

		// set REQUEST_URI
		$request->setAttribute('request_uri', $_SERVER['REQUEST_URI']);

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'USER_SEARCH_RESULT_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $user_info_row_array, $display_count);

		// 加工
		if (is_array($user_info_row_array)) {
			foreach ($user_info_row_array as $index => $user_info_row) {
				$user_info_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $user_info_row['user_community_id'];
				$user_info_row_array[$index]['image_url'] = ACSUser::get_image_url($user_info_row['user_community_id'], 'thumb');
				$user_info_row_array[$index]['friends_row_array_num'] = ACSUser::get_friends_row_array_num($user_info_row['user_community_id']);
			}
		}

		// URL
		$action_url = $_SERVER['REQUEST_URI'];

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('LoginInput.tpl.php');

		// set
		$this->setAttribute('form', $form);
		$this->setAttribute('user_info_row_array', $user_info_row_array);
		$this->setAttribute('paging_info', $paging_info);

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('module', "User");
		$this->setAttribute('action', "Login");
		
		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		return parent::execute();
	}
}

?>
