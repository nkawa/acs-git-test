<?php
/**
 * コミュニティ退会（確認）
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/03/02 08:48:33 $
 */
class LeaveCommunitySuccessView extends BaseView
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

		// 加工
		// コミュニティのURL
		$community_top_page_url  = $this->getControllerPath('Community', 'Index');
		$community_top_page_url .= $target_community_info;

		// キャンセルURL
		$cancel_action_url = $community_top_page_url;

		// 退会アクションURL
		$leave_action_url  = $this->getControllerPath('Community',
														     'LeaveCommunity');
		$leave_action_url .= $target_community_info;


		// set
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('target_community_name', $target_community_row['community_name']);

		// form のアクション先 URL
		$this->setAttribute('cancel_action_url', $cancel_action_url);
		$this->setAttribute('leave_action_url', $leave_action_url);


		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('LeaveCommunity_confirm.tpl.php');

		return parent::execute();
	}
}
?>
