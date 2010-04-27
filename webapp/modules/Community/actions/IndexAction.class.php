<?php
// $Id: IndexAction.class.php,v 1.12 2006/12/08 05:06:34 w-ota Exp $

class IndexAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$controller = $context->getController();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		
		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->ACSGetParameter('community_id');

		// ���ߥ�˥ƥ�¸�ߥ����å�
		$community_row = ACSCommunity::get_community_row($community_id);
		if (!$community_row || $community_row['community_type_name'] != ACSMsg::get_mst('community_type_master','D40')) {
			return View::ERROR;
		}

		// ���¥����å�
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// ���֥��ߥ�˥ƥ�����ΰ���
		$sub_community_row_array = ACSCommunity::get_sub_community_row_array($community_id);

		// �ƥ��ߥ�˥ƥ�����ΰ���
		$parent_community_row_array = ACSCommunity::get_parent_community_row_array($community_id);

		// ���ߥ�˥ƥ�����, ���ߥ�˥ƥ�������
		$community_member_user_info_row_array = ACSCommunity::get_community_member_user_info_row_array($community_id);
		$community_admin_user_info_row_array = ACSCommunity::get_community_admin_user_info_row_array($community_id);

		// �Ե�: ���ߥ�˥ƥ����� ��ǧ�Ԥ�
		$waiting_for_join_community_row_array = ACSWaiting::get_waiting_row_array($community_id, ACSMsg::get_mst('waiting_type_master','D20'), ACSMsg::get_mst('waiting_status_master','D10'));

		// �Ե�: �ƥ��ߥ�˥ƥ��ɲ�, ���֥��ߥ�˥ƥ��ɲ�
		$waiting_for_parent_community_link_row_array = ACSWaiting::get_waiting_row_array($community_id, ACSMsg::get_mst('waiting_type_master','D40'), ACSMsg::get_mst('waiting_status_master','D10'));
		$waiting_for_sub_community_link_row_array = ACSWaiting::get_waiting_row_array($community_id, ACSMsg::get_mst('waiting_type_master','D50'), ACSMsg::get_mst('waiting_status_master','D10'));

		// �ǿ�����: BBS
		// BBS��������
		$bbs_row_array = ACSBBS::get_bbs_row_array($community_id);
		foreach ($bbs_row_array as $index => $bbs_row) {
			// ����Ѥߥ��ߥ�˥ƥ�����
			$bbs_row_array[$index]['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('sub_community_row_array', $sub_community_row_array);
		$request->setAttribute('parent_community_row_array', $parent_community_row_array);
		$request->setAttribute('community_member_user_info_row_array', $community_member_user_info_row_array);
		$request->setAttribute('community_admin_user_info_row_array', $community_admin_user_info_row_array);
		$request->setAttribute('waiting_for_join_community_row_array', $waiting_for_join_community_row_array);
		$request->setAttribute('waiting_for_parent_community_link_row_array', $waiting_for_parent_community_link_row_array);
		$request->setAttribute('waiting_for_sub_community_link_row_array', $waiting_for_sub_community_link_row_array);
		$request->setAttribute('bbs_row_array', $bbs_row_array);

		return View::SUCCESS;
	}

	/**
	 * ǧ�ڥ����å���Ԥ���
	 * ����������¹Ԥ������ˡ�ǧ�ڥ����å���ɬ�פ����ꤹ��
	 * @access  public
	 * @return  boolean ǧ�ڥ����å�̵ͭ��true:ɬ�ס�false:���ס�
	 */
	function isSecure()
	{
		return false;
	}

	function getCredential () {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();

		// ��������ߥ�˥ƥ��ϥ��ФΤߥ���������ǽ
		$community_self_info_row = ACSCommunity::get_contents_row($request->getParameter('community_id'), ACSMsg::get_mst('contents_type_master','D00'));
		if ($community_self_info_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
			return array('COMMUNITY_MEMBER');
		}
		return array();
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// ���ߥ�˥ƥ����Ф�OK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}

		// ��������ߥ�˥ƥ��ϥ��ߥ�˥ƥ����Ф��Բ�ǽ
		$community_self_info_row = ACSCommunity::get_contents_row($request->getParameter('community_id'), ACSMsg::get_mst('contents_type_master','D00'));
		if ($community_self_info_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
			return false;
		}
		return true;
	}
}

?>
