<?php
// $Id: NewCommunityFolderView::SUCCESS.class.php,v 1.2 2007/03/01 09:01:46 w-ota Exp $

/**
 * マイコミュニティのフォルダ新着情報(success)
 *
 * @author  z-satosi
 * @version $Revision: 1.2 $
 */
class NewCommunityFolderSuccessView extends BaseView
{
	var $community_name_buffer;

	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$user_community_id = $request->getAttribute('user_community_id');
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$new_folder_row_array = $request->getAttribute('new_folder_row_array');
		$display_count = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D02'), 
				'NEW_INFO_LIST_DISPLAY_MAX_COUNT');

		// ページング設定
		$paging_info = $this->getPagingInfo($controller, $request, $new_folder_row_array, $display_count);

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('new_folder_row_array', $new_folder_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('get_days', $request->getAttribute('get_days'));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('NewCommunityFolder.tpl.php');

		return parent::execute();
	}
}

?>
