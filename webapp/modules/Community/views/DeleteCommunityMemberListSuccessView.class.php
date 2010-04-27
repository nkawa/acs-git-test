<?php
/**
 * コミュニティメンバ削除 メンバ一覧表示（確認）
 *
 * @author  kuwayama
 * @version $Revision: 1.4 $ $Date: 2006/02/20 07:06:10 $
 */
class DeleteCommunityMemberListSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$target_community_row = $request->getAttribute('target_community_row');
		$delete_user_info_row_array = $request->getAttribute('delete_user_info_row_array');


		// URL に付加する target_community
		$target_community_info = '&community_id=' . $target_community_row['community_id'];


		// コミュニティのURL
		$community_top_page_url  = $this->getControllerPath('Community', 'Index');
		$community_top_page_url .= $target_community_info;

		// 削除確認画面アクションURL
		$back_action_url  = $this->getControllerPath('Community', 'DeleteCommunityMemberList');
		$back_action_url .= $target_community_info;

		$delete_action_url  = $this->getControllerPath('Community',
														     'DeleteCommunityMember');
		$delete_action_url .= $target_community_info;

		// メンバリスト
		$community_member_info_row_array = array();
		foreach ($delete_user_info_row_array as $target_community_member_info_row) {
			$a_community_member_info_row = array();
			$top_page_url = "";

			$top_page_url  = $this->getControllerPath('User', DEFAULT_ACTION);
			$top_page_url .= "&id=" . $target_community_member_info_row['user_community_id'];

			$a_community_member_info_row['community_id'] = $target_community_member_info_row['user_community_id'];
			$a_community_member_info_row['name'] = $target_community_member_info_row['community_name'];
			$a_community_member_info_row['top_page_url'] = $top_page_url;
			$a_community_member_info_row['image_url'] = ACSUser::get_image_url($target_community_member_info_row['user_community_id'], 'thumb');

			array_push($community_member_info_row_array, $a_community_member_info_row);
		}


		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('target_community_name', $target_community_row['community_name']);

		// form のアクション先 URL
		$this->setAttribute('back_action_url', $back_action_url);
		$this->setAttribute('delete_action_url', $delete_action_url);

		$this->setAttribute('community_member_info_row_array', $community_member_info_row_array);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DeleteCommunityMemberList_confirm.tpl.php');

		return parent::execute();
	}
}
?>
