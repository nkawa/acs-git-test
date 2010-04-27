<?php
/**
 * ファイル詳細情報足跡　Actionクラス
 * 
 * FootprintFileDetailAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   w-ota					 @editor akitsu
 * @since	PHP 4.0
 */
// $Id: FootprintFileDetailAction.class.php,v 1.1 2007/03/28 02:26:52 w-ota Exp $

class FootprintFileDetailAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$target_user_community_id = $request->ACSgetParameter('id');
		$folder_id = $request->ACSgetParameter('folder_id');
		$file_id = $request->ACSgetParameter('file_id');

		$file_obj = ACSFile::get_file_info_instance($file_id);

		$contents_link_url = $this->getControllerPath('User', 'FileDetail') .
									"&id=" . $target_user_community_id . "&file_id=" . $file_id .
									"&folder_id=" . $folder_id;

		// 足跡登録
		$contents_type_name = ACSMsg::get_mst('contents_type_master','D33');
		$contents_type_arr = ACSDB::get_master_array(
										"contents_type",
										"contents_type_name='" . $contents_type_name . "'");

		$form['community_id'] = $target_user_community_id;
		$form['visitor_community_id'] = $acs_user_info_row['user_community_id'];
		$form['contents_type_code'] = array_search($contents_type_name, $contents_type_arr);
		$form['contents_title'] = $file_obj->get_display_file_name();
		$form['contents_link_url'] = $contents_link_url;
		$form['contents_date'] = $file_obj->get_update_date();
		$form['post_date'] = 'now';

		$ret = ACSUser::set_footprint($form);

		header("Location: $contents_link_url");
	}

	function isSecure () {
		return false;
	}

}
?>
