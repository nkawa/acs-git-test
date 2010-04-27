<?php
/**
 * コミュニティメンバ削除 メンバ一覧表示（選択）
 *
 * @author  kuwayama
 * @version $Revision: 1.8 $ $Date: 2006/03/30 08:21:11 $
 */
class DeleteCommunityMemberListInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row    = $user->getAttribute('acs_user_info_row');
		$target_community_row = $request->getAttribute('target_community_row');
		$target_community_member_info_row_array = $request->getAttribute('target_community_member_info_row_array');

		// 選択されているユーザ
		$delete_user_community_id_array = $request->getParameter('delete_user_community_id_array');


		// URL に付加する target_community
		$target_community_info = '&community_id=' . $target_community_row['community_id'];


		// コミュニティのURL
		$community_top_page_url  = $this->getControllerPath('Community', 'Index');
		$community_top_page_url .= $target_community_info;

		// コミュニティメンバかどうか
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'],
$target_community_row['community_id']);

		// 削除確認画面アクションURL
		$confirm_action_url  = $this->getControllerPath('Community', 'DeleteCommunityMemberList');
		$confirm_action_url .= $target_community_info;

		// メンバリスト
		$community_member_info_row_array = array();
		foreach ($target_community_member_info_row_array as $target_community_member_info_row) {
			$a_community_member_info_row = array();
			$top_page_url = "";
			$is_selected  = false;
			$is_disabled  = false;

			$top_page_url  = $this->getControllerPath('User', DEFAULT_ACTION);
			$top_page_url .= "&id=" . $target_community_member_info_row['user_community_id'];

			if ($delete_user_community_id_array) {
				$is_selected = in_array($target_community_member_info_row['user_community_id'], $delete_user_community_id_array);
			}

			// 本人の場合は、選択不可
			if ($target_community_member_info_row['user_community_id'] == $acs_user_info_row['user_community_id']) {
				$is_disabled = true;
			}

			$a_community_member_info_row['community_id'] = $target_community_member_info_row['user_community_id'];
			$a_community_member_info_row['name'] = $target_community_member_info_row['community_name'];
			$a_community_member_info_row['top_page_url'] = $top_page_url;
			$a_community_member_info_row['image_url'] = ACSUser::get_image_url($target_community_member_info_row['user_community_id'], 'thumb');
			$a_community_member_info_row['is_selected'] = $is_selected;
			$a_community_member_info_row['is_disabled'] = $is_disabled;

			array_push($community_member_info_row_array, $a_community_member_info_row);
		}

		// エラーメッセージ設定
		$error_msg_array = array();
		$error_row = $request->getErrors();
		if ($error_row) {
			foreach ($error_row as $key => $msg) {
				array_push($error_msg_array, $msg);
			}
		}

		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('target_community_name', $target_community_row['community_name']);
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('confirm_action_url', $confirm_action_url);

		$this->setAttribute('community_member_info_row_array', $community_member_info_row_array);

		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DeleteCommunityMemberList_input.tpl.php');

		return parent::execute();
	}
}
?>
