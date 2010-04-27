<?php
/**
 * コミュニティメンバ削除処理
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2006/11/20 08:44:12 $
 */
require_once(ACS_CLASS_DIR . 'ACSDone.class.php');
class DeleteCommunityMemberAction extends BaseAction
{

	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		/* コミュニティ情報取得 */
		$target_community_id = $request->getParameter('community_id');
		$target_community_row = ACSCommunity::get_community_row($request->getParameter('community_id'));

		/* 削除処理 */
		$delete_user_community_id_array = $request->getParameter('delete_user_community_id_array');

		$ret = ACSCommunity::delete_community_member($target_community_id, $delete_user_community_id_array);
		if (!$ret) {
			"ERROR : delete community member failed";
			exit;
		}

		/* 完了画面表示 */
		// 引数セット
		$community_top_url  = $this->getControllerPath('Community','');
		$community_top_url .= '&community_id=' . $target_community_row['community_id'];
		$community_top_link_name = ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'DeleteCommunityMemberAction.class.php', 'BACK_TO_CM'),
				array("{COMMUNITY_NAME}" => $target_community_row['community_name']));

		$done_obj = new ACSDone();

		$done_obj->set_title(ACSMsg::get_msg('Community', 'DeleteCommunityMemberAction.class.php', 'M001'));
		$done_obj->set_message(ACSMsg::get_msg('Community', 'DeleteCommunityMemberAction.class.php', 'M002'));
		$done_obj->add_link($community_top_link_name, $community_top_url);

		$request->setAttribute('done_obj', $done_obj);


		// 画面呼び出し
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods () {
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
