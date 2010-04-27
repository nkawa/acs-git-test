<?php
// $Id: SetOpenLevelForProfileAction.class.php,v 1.3 2006/11/20 08:44:25 w-ota Exp $

class SetOpenLevelForProfileAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		// �оݤȤʤ�contents_key�����
		$contents_key = $request->ACSgetParameter('contents_key');
		// �оݤȤʤ륳��ƥ�ļ��̥����ɤ����
		$contents_type_code = $request->ACSgetParameter('contents_type_code');

		// ����ƥ�ļ��̥ޥ���
		$contents_type_master_array = ACSDB::get_master_array('contents_type');
		// �����ϰ�
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(
				ACSMsg::get_mst('community_type_master','D10'), $contents_type_master_array[$contents_type_code]);
		if ($acs_user_info_row['is_acs_user']) {
			// �ޥ��ե�󥺥��롼��
			$friends_group_row_array = ACSUser::get_friends_group_row_array($user_community_id);
		} else {
			$friends_group_row_array = array();
		}

		// set
		$request->setAttribute('contents_key', $contents_key);
		$request->setAttribute('contents_type_code', $contents_type_code);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$request->setAttribute('friends_group_row_array', $friends_group_row_array);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
}

?>
