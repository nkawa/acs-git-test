<?php
// $Id: NewBBSAction.class.php,v 1.4 2008/04/24 16:00:00 y-yuki Exp $

class NewBBSAction extends BaseAction
{
	function execute() {
		$context = &$this->getContext();
		$controller = $context->getController();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		if ($user_community_id == null || $user_community_id == '') {
			$user_community_id = $request->getAttribute("id");
		}

		// ¾�桼���Υǡ����������ʤ��褦�����å�
		if (!$this->get_execute_privilege()
				&& $acs_user_info_row["user_community_id"] != $user_community_id) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ����饤��ɽ���ξ��: 1(true)
		$inline_mode = $request->ACSgetParameter('inline_mode');
		if ($inline_mode == null || $inline_mode == '') {
			$inline_mode = $request->getAttribute("inline_mode");
		}

		// �����ϰϤλ���
		$get_days = ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D02'), 
					($inline_mode ? 'NEW_INFO_TOP_TERM' : 'NEW_INFO_LIST_TERM'));
		$request->setAttribute('get_days', $get_days);

		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// �ޥ����ߥ�˥ƥ��ο���Ǽ��ĵ����������������
		if ($inline_mode) {
			$new_bbs_row_array = ACSBBS::get_new_bbs_row_array($user_community_id, $get_days, true);
		} else {
			$new_bbs_row_array = ACSBBS::get_new_bbs_row_array($user_community_id, $get_days);
		}
		
		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('new_bbs_row_array', $new_bbs_row_array);
		
		if ($inline_mode) {
			return View::INPUT;
		} else {
			return View::SUCCESS;
		}
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �ܿͤξ���OK
		if (!$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
	}
}

?>
