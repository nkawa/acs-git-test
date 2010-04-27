<?php
// $Id: SetOpenLevelForProfileView::SUCCESS.class.php,v 1.1 2005/12/22 08:53:45 w-ota Exp $

class SetOpenLevelForProfileSuccessView extends SimpleBaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$contents_key = $request->getAttribute('contents_key');
		$contents_type_code = $request->getAttribute('contents_type_code');
		$open_level_master_row_array = $request->getAttribute('open_level_master_row_array');
		$friends_group_row_array = $request->getAttribute('friends_group_row_array');

		// コンテンツ種別マスタ
		$contents_type_master_array = ACSDB::get_master_array('contents_type');
		$contents_type_name = $contents_type_master_array[$contents_type_code];


		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SetOpenLevelForProfileView.tpl.php');

		// set
		$this->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$this->setAttribute('friends_group_row_array', $friends_group_row_array);
		$this->setAttribute('contents_key', $contents_key);
		$this->setAttribute('contents_type_name', $contents_type_name);

		return parent::execute();
	}
}

?>
