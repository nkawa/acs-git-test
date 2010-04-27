<?php
// $Id: BBSResAction.class.php,v 1.10 2006/11/20 08:44:12 w-ota Exp $


class BBSResAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->getParameter('community_id');
		// �оݤȤʤ�bbs_id�����
		$bbs_id = $request->getParameter('bbs_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_row($community_id);

		// BBS��������
		$bbs_row = ACSBBS::get_bbs_row($bbs_id);

		// ����Ѥߥ��ߥ�˥ƥ�����
		$bbs_row['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);
		// �ֿ�����
		$bbs_row['bbs_res_row_array'] = ACSBBS::get_bbs_res_row_array($bbs_row['bbs_id']);

		// �����ϰ�
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D42'));

		// �Ǽ��ĥ�����������
		if ($acs_user_info_row['is_acs_user']) {
			ACSBBS::set_bbs_access_history($acs_user_info_row['user_community_id'], $bbs_row['bbs_id']);
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('bbs_row', $bbs_row);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();  

		//����󥻥����äƤ����Ȥ��Τߤν���
		$move_id = $request->getParameter('move_id');
		if($move_id == 3){
			$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		
		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
			$community_id = $request->ACSGetParameter('community_id');
		// �оݤȤʤ�bbs_id�����
			$bbs_id = $request->getParameter('bbs_id');
		
		// ���Υե����������������
			$form = $user->getAttribute('new_form_obj');//��̾��subject ���ơ�body
			$form['community_id'] = $community_id;
			$form['user_community_id'] = $acs_user_info_row['user_community_id']; // ��Ƽ�

			$user->setAttribute('new_form_obj',$form);
			// GET�ν�����
			return $this->getDefaultView();
		}
	}

	function getRequestMethods() {
		return Request::POST;
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

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('EXECUTE');
	}

	function get_execute_privilege (&$controller, &$request, &$user) {

		// �����ϰϾ������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_community_row = ACSCommunity::get_community_profile_row($request->getParameter('community_id'));
		$bbs_row = ACSBBS::get_bbs_row($request->getParameter('bbs_id'));
		if (!$bbs_row) {
			return false;
		}
		$bbs_row['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);

		// ������������Ƚ��
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $target_community_row);
		$ret = ACSAccessControl::is_valid_user_for_community($acs_user_info_row, $role_array, $bbs_row);

		return $ret;
	}
}

?>
