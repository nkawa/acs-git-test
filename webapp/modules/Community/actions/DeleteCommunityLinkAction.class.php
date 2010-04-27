<?php
// $Id: DeleteCommunityLinkAction.class.php,v 1.4 2006/11/20 08:44:12 w-ota Exp $

class DeleteCommunityLinkAction extends BaseAction
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

		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');
		// 削除するコミュニティリンクのコミュニティID
		$delete_community_id = $request->ACSgetParameter('delete_community_id');
		// mode
		$mode = $request->ACSgetParameter('mode');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		// 削除するコミュニティ情報
		if ($mode == 'parent') {
			$delete_community_row = ACSCommunity::get_parent_community_row($delete_community_id, $community_id);
		} elseif ($mode == 'sub') {
			$delete_community_row = ACSCommunity::get_sub_community_row($community_id, $delete_community_id);
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('delete_community_row', $delete_community_row);
		$request->setAttribute('mode', $mode);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser(); 
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');

		$form = $request->ACSGetParameters();

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		if ($form['mode'] == 'parent') {
			$parent_community_id = $form['delete_community_id'];
			$sub_community_id = $community_id;
		} elseif ($form['mode'] == 'sub') {
			$parent_community_id = $community_id;
			$sub_community_id = $form['delete_community_id'];
		}

		// 削除
		ACSCommunity::delete_community_link($parent_community_id, $sub_community_id);

		// forward
		$done_obj = new ACSDone();
		$done_obj->set_title(ACSMsg::get_msg('Community', 'DeleteCommunityLinkAction.class.php', 'M001'));
		$done_obj->set_message(ACSMsg::get_msg('Community', 'DeleteCommunityLinkAction.class.php', 'M002'));
		$done_obj->add_link(ACSMsg::get_msg('Community', 'DeleteCommunityLinkAction.class.php', 'M003'), $this->getControllerPath('Community', 'CommunityLink') . '&community_id=' . $community_row['community_id']);

		$done_obj->add_link(
				ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'DeleteCommunityLinkAction.class.php', 'BACK_TO_CM'),
					array("{COMMUNITY_NAME}" => $community_row['community_name'])),
				$this->getControllerPath('Community', DEFAULT_ACTION) 
					. '&community_id=' . $community_row['community_id']);

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
		return array('COMMUNITY_ADMIN');
	}


	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// コミュニティ管理者はOK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		return false;
	}
}

?>
