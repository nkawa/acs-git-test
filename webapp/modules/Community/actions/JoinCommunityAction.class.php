<?php
// $Id: JoinCommunityAction.class.php,v 1.7 2006/11/20 08:44:12 w-ota Exp $

class JoinCommunityAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->ACSgetParameter('community_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_row($community_id);

		// ��ǧ��ɬ�פ�
		$is_admission_required = ACSCommunity::is_admission_required_for_join_community($acs_user_info_row['user_community_id'], $community_id);

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('is_admission_required', $is_admission_required);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$form = $form = $request->ACSGetParameters();

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->ACSgetParameter('community_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// ��ǧ��ɬ�פ�
		$is_admission_required = ACSCommunity::is_admission_required_for_join_community($acs_user_info_row['user_community_id'], $community_id);


		// forward
		$done_obj = new ACSDone();
		$done_obj->set_message(ACSMsg::get_msg('Community', 'JoinCommunityAction.class.php', 'M003'));
		$done_obj->add_link($community_row['community_name'] . ' ' . 
				ACSMsg::get_msg('Community', 'JoinCommunityAction.class.php', 'M004'), $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id']);

		if ($is_admission_required) {
			// ���ߥ�˥ƥ����þ�ǧ�Ԥ���Ͽ
			$waiting_id = ACSWaiting::set_waiting_for_join_community($community_id, $acs_user_info_row['user_community_id'], $form['message']);

			// ���þ�ǧ�������Υ᡼��
			ACSWaiting::send_admission_request_notify_mail($waiting_id);

			$done_obj->set_title(ACSMsg::get_msg('Community', 'JoinCommunityAction.class.php', 'M001'));

		} else {
			// ���ߥ�˥ƥ�������Ͽ
			$community_member_form = array();
			$community_member_form['community_id'] = $community_id;
			$community_member_form['user_community_id'] = $acs_user_info_row['user_community_id'];
			ACSCommunity::set_community_member($community_member_form);

			$done_obj->set_title(ACSMsg::get_msg('Community', 'JoinCommunityAction.class.php', 'M002'));
		}

		$request->setAttribute('done_obj', $done_obj);
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		return array('NOT_COMMUNITY_MEMBER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// ��������NG
		if ($user->hasCredential('PUBLIC_USER')) {
			return false;
		}

		// ���ߥ�˥ƥ����Ф�NG
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return false;
		}
		return true;
	}
}

?>
