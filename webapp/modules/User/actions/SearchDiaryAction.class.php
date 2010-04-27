<?php
/**
 * �������꡼������Action���饹
 * 
 * SearchDiaryAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   akitsu
 * @since    PHP 4.0
 */
// $Id: SearchDiaryAction.class.php,v 1.3 2006/11/20 08:44:25 w-ota Exp $

class SearchDiaryAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// ɽ���оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);
		// �����ϰϤΥꥹ�ȥǡ���
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D21'));
		
		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		
		return View::INPUT;
	}

	function isSecure () {
		return false;
	}

}

?>
