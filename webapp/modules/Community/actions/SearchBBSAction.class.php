<?php
/**
 * �Ǽ��ĸ�����Action���饹
 * 
 * SearchDiaryAction.class.php
 * @package  acs/webapp/module/Community/Action
 * @author   akitsu
 * @since	PHP 4.0
 */
// $Id: SearchBBSAction.class.php,v 1.4 2006/11/20 08:44:12 w-ota Exp $

class SearchBBSAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// ɽ���оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('community_id');
		// �桼������
		$target_user_info_row = ACSCommunity::get_community_profile_row($user_community_id);
		// �����ϰϤΥꥹ�ȥǡ���
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D42'));

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		
		return View::INPUT;
	}

	function isSecure () {
		return false;
	}
	
	// ���������������
	function get_access_control_info(&$controller, &$request, &$user) {
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->getParameter('community_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// ��������������� //
		$bbs_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D41'));
		$bbs_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $bbs_contents_row['contents_type_code'], $bbs_contents_row['open_level_code']);
		$access_control_info = array(
									 'role_array' => ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row),
									 'contents_row_array' => array($bbs_contents_row)
									 );

		return $access_control_info;
	}
}

?>
