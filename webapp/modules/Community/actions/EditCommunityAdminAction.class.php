<?php
// $Id: EditCommunityAdminAction.class.php,v 1.4 2006/11/20 08:44:12 w-ota Exp $

class EditCommunityAdminAction extends BaseAction
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

		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		// コミュニティメンバ一覧を取得する
		$community_member_user_info_row_array = ACSCommunity::get_community_member_user_info_row_array($community_id);

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('community_member_user_info_row_array', $community_member_user_info_row_array);

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

		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		$form = $request->ACSGetParameters();
		$form['community_id'] = $community_row['community_id'];

		// 更新
		ACSCommunity::update_community_admin($acs_user_info_row, $form);

		$done_obj = new ACSDone();
		$done_obj->set_title(ACSMsg::get_msg('Community', 'EditCommunityAdminAction.class.php', 'M001'));
		$done_obj->set_message(ACSMsg::get_msg('Community', 'EditCommunityAdminAction.class.php', 'M002'));
		$done_obj->add_link( ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'EditCommunityAdminAction.class.php', 'BACK_TO_CM'),
				array("{COMMUNITY_NAME}" => $community_row['community_name'])),
				$this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id']);

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
