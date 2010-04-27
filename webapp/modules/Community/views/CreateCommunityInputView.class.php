<?php
// $Id: CreateCommunityInputView.class.php,v 1.4 2006/12/28 07:36:16 w-ota Exp $

class CreateCommunityInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$category_group_master_row_array = $request->getAttribute('category_group_master_row_array');

		// 加工

		// カテゴリマスタ一覧
		$category_master_row_array = array();
		foreach ($category_group_master_row_array as $category_group_master_row) {
			foreach ($category_group_master_row['category_master_row_array'] as $category_master_row) {
				array_push($category_master_row_array, $category_master_row);
			}
		}

		// URL
		$action_url = $this->getControllerPath('Community', 'CreateCommunity');
		$select_trusted_community_url = $this->getControllerPath('Community', 'SelectTrustedCommunity');

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('CreateCommunity.tpl.php');

		// set
		$this->setAttribute('community_row', $request->getAttribute('community_row'));
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('select_trusted_community_url', $select_trusted_community_url);
		$this->setAttribute('category_master_row_array', $category_master_row_array);
		$this->setAttribute('bbs_open_level_master_row_array', $request->getAttribute('bbs_open_level_master_row_array'));
		$this->setAttribute('community_folder_open_level_master_row_array', $request->getAttribute('community_folder_open_level_master_row_array'));
		$this->setAttribute('self_open_level_master_row_array', $request->getAttribute('self_open_level_master_row_array'));
		$this->setAttribute('edit_community_ml_address', 
				$request->getAttribute('edit_community_ml_address'));

		// エラー時のメッセージ表示
		$this->setAttribute('error_message', 
				$this->getErrorMessage($controller, $request, $user));

		return parent::execute();
	}
}

?>
