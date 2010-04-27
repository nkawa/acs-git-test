<?php
/**
 * ���ߥ�˥ƥ�������
 *
 * @author  kuwayama
 * @version $Revision: 1.3 $ $Date: 2006/11/20 08:44:12 $
 */
require_once(ACS_CLASS_DIR . 'ACSDone.class.php');
class LeaveCommunityAction extends BaseAction
{
	/**
	 * ��ǧ����ɽ��
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		/* ���ߥ�˥ƥ�������� */
		$target_community_id = $request->getParameter('community_id');
		$target_community_row = ACSCommunity::get_community_row($request->getParameter('community_id'));

		$request->setAttribute('target_community_row', $target_community_row);

		return View::SUCCESS;
	}

	/**
	 * ���ߥ�˥ƥ��������
	 */
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		/* �����������Ƥ���桼������ */
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		/* ���ߥ�˥ƥ�������� */
		$target_community_id = $request->getParameter('community_id');
		$target_community_row = ACSCommunity::get_community_row($request->getParameter('community_id'));

		/* ������� */
		ACSDB::_do_query("BEGIN");
		$ret = ACSCommunity::delete_community_member($target_community_id, array($acs_user_info_row['user_community_id']));
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			"ERROR : Delete community member failed.";
			exit;
		}
		ACSDB::_do_query("COMMIT");


		/* ��λ����ɽ�� */
		// �������å�
		$message = ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'LeaveCommunityAction.class.php', 'LEAVE_CM'),
				array("{COMMUNITY_NAME}" => $target_community_row['community_name']));

		// �ޥ��ڡ����ȥåץڡ���URL
		$top_page_url  = $this->getControllerPath('User', 'Index');
		$top_page_link_name = ACSMsg::get_msg('Community', 'LeaveCommunityAction.class.php', 'M001');

		// ���ߥ�˥ƥ�URL
		$community_top_page_url  = $this->getControllerPath('Community', 'Index');
		$community_top_page_url .= '&community_id=' . $target_community_row['community_id'];
		$community_top_page_link_name = ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'LeaveCommunityAction.class.php', 'BACK_TO_CM'),
				array("{COMMUNITY_NAME}" => $target_community_row['community_name']));

		$done_obj = new ACSDone();

		$done_obj->set_title(ACSMsg::get_msg('Community', 'LeaveCommunityAction.class.php', 'M002'));
		$done_obj->set_message($message);
		$done_obj->add_link($top_page_link_name, $top_page_url);
		$done_obj->add_link($community_top_page_link_name, $community_top_page_url);

		$request->setAttribute('done_obj', $done_obj);

		// ���̸ƤӽФ�
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_MEMBER');
	}


	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// ���ߥ�˥ƥ������Ԥ�NG
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return false;
		}
		// ���ߥ�˥ƥ����Ф�OK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}
		return false;
	}
}
?>
