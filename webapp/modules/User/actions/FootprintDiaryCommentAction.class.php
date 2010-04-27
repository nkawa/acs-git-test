<?php
/**
 * ダイアリー　コメント　Actionクラス
 * 
 * FootprintDiaryCommentAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   w-ota					 @editor akitsu
 * @since	PHP 4.0
 */
// $Id: FootprintDiaryCommentAction.class.php,v 1.1 2007/03/28 02:26:52 w-ota Exp $

class FootprintDiaryCommentAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象のdiary_idを取得
		$diary_id = $request->ACSgetParameter('diary_id');
		$diary_row = ACSDiary::get_diary_row($diary_id);
		if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
			$diary_row['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
		}

		// ユーザ情報
		$user_community_id = $diary_row['user_community_id'];

		$contents_link_url = $this->getControllerPath('User', 'DiaryComment') . 
										"&diary_id=" . $diary_row['diary_id'];

		// 足跡登録
		$contents_type_name = ACSMsg::get_mst('contents_type_master','D21');
		$contents_type_arr = ACSDB::get_master_array(
									   	"contents_type",
									   	"contents_type_name='" . $contents_type_name . "'");

		$form['community_id'] = $user_community_id;
		$form['visitor_community_id'] = $acs_user_info_row['user_community_id'];
		$form['contents_type_code'] = array_search($contents_type_name, $contents_type_arr);
		$form['contents_title'] = $diary_row['subject'];
		$form['contents_link_url'] = $contents_link_url;
		$form['contents_date'] = $diary_row['post_date'];
		$form['post_date'] = 'now';

		$ret = ACSUser::set_footprint($form);

		header("Location: $contents_link_url");
	}

	function isSecure () {
		return false;
	}

}
?>
