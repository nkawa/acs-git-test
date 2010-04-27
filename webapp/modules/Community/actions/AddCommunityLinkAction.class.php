<?php
// $Id: AddCommunityLinkAction.class.php,v 1.8 2006/11/20 08:44:12 w-ota Exp $

class AddCommunityLinkAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
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

		// set
		$request->setAttribute('community_row', $community_row);

		return View::INPUT;
	}

	// POST
	function execute() {
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

		$form = $request->ACSGetParameters();

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// forward
		$done_obj = new ACSDone();

		ACSDB::_do_query("BEGIN");

		// �������줿waiting_id������
		$waiting_id_array = array();
		$ret = 1; // 1:���� / 0:����

		foreach ($form['trusted_community_id_array'] as $link_community_id) {
			if (ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $link_community_id)) {
				// ��󥯰����襳�ߥ�˥ƥ��δ����ԤǤ�����Ͼ�ǧ�Ԥ���Ͽ�����ˡ�¨�¤˥�󥯤��ɲä���
				if ($form['link_type'] == 'parent') {
					$ret = ACSCommunity::set_community_link($link_community_id, $community_id);
				} elseif ($form['link_type'] == 'sub') {
					$ret = ACSCommunity::set_community_link($community_id, $link_community_id);
				}

			} else {
				if ($form['link_type'] == 'parent') {
					// �ƥ��ߥ�˥ƥ��ɲþ�ǧ�Ԥ���Ͽ
					$waiting_id = ACSWaiting::set_waiting_for_parent_community_link($link_community_id, $community_id, $acs_user_info_row['user_community_id'], $form['message']);
				} elseif ($form['link_type'] == 'sub') {
					// ���֥��ߥ�˥ƥ��ɲþ�ǧ�Ԥ���Ͽ
					$waiting_id = ACSWaiting::set_waiting_for_sub_community_link($link_community_id, $community_id, $acs_user_info_row['user_community_id'], $form['message']);
				}

				if ($waiting_id) {
					// �������줿waiting������ݻ�
					array_push($waiting_id_array, $waiting_id);
				} else {
					$ret = 0;
				}
			}

			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				break;
			}
		}

		if ($ret) {
			// COMMIT
			ACSDB::_do_query("COMMIT");

			// �������줿waiting����򸵤�ʣ���᡼������
			foreach ($waiting_id_array as $waiting_id) {
				// ���ߥ�˥ƥ��֥���ɲð������Υ᡼��
				ACSWaiting::send_admission_request_notify_mail($waiting_id);
			}

			$done_obj->set_title(ACSMsg::get_msg('Community', 'AddCommunityLinkAction.class.php', 'M001'));
			$done_obj->set_message(ACSMsg::get_msg('Community', 'AddCommunityLinkAction.class.php', 'M002'));
			$done_obj->add_link(ACSMsg::get_msg('Community', 'AddCommunityLinkAction.class.php', 'M003'), $this->getControllerPath('Community', 'CommunityLink') . '&community_id=' . $community_row['community_id']);
			$done_obj->add_link($community_row['community_name'] . ' '. ACSMsg::get_msg('Community', 'AddCommunityLinkAction.class.php', 'M004'), $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id']);
		} else {
			$done_obj->set_message('���Ԥ��ޤ�����');
		}


		$request->setAttribute('done_obj', $done_obj);
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* ɬ�ܥ����å� */
		parent::regValidateName($validatorManager, 
				"link_type", 
				true, 
				ACSMsg::get_msg('Community', 'AddCommunityLinkAction.class.php', 'M006'));

		parent::regValidateName($validatorManager, 
				"trusted_community_id_array", 
				true, 
				ACSMsg::get_msg('Community', 'AddCommunityLinkAction.class.php', 'M007'));

		parent::regValidateName($validatorManager, 
				"message", 
				true, 
				ACSMsg::get_msg('Community', 'AddCommunityLinkAction.class.php', 'M008'));
	}

	function handleError () {
		$context = $this->getContext();
		$request =  $context->getRequest();

		// �����ͤ� set
		$form = $request->ACSGetParameters();
		$request->setAttribute('form', $form);

		// ���ϲ���ɽ��
		return $this->getDefaultView();
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_ADMIN');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// ���ߥ�˥ƥ������Ԥ�OK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		return false;
	}
}

?>
