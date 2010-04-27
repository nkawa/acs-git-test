<?php
// $Id: NewFriendsFolderView::SUCCESS.class.php,v 1.2 2007/03/01 09:01:46 w-ota Exp $

/**
 * マイフレンズのフォルダ新着情報(success)
 *
 * @author  z-satosi
 * @version $Revision: 1.2 $
 */
class NewFriendsFolderSuccessView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$user_community_id = $request->getAttribute('user_community_id');
		$new_file_row_array = $request->getAttribute('new_file_row_array');
		$display_count = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D02'), 
				'NEW_INFO_LIST_DISPLAY_MAX_COUNT');

		// ページング設定
		$paging_info = $this->getPagingInfo($controller, $request, $new_file_row_array, $display_count);

		// set
		$this->setAttribute('new_file_row_array', $new_file_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('get_days', $request->getAttribute('get_days'));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('NewFriendsFolder.tpl.php');

		return parent::execute();
	}
}

?>
