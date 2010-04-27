<?php
// $Id: InviteToCommunityView::INPUT.class.php,v 1.3 2006/05/22 07:28:05 w-ota Exp $

class InviteToCommunityInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$community_member_user_info_row_array = $request->getAttribute('community_member_user_info_row_array');
		$friends_row_array = $request->getAttribute('friends_row_array');

		// コミュニティメンバは招待対象外
		foreach ($friends_row_array as $index => $user_info_row) {
			$friends_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $user_info_row['user_community_id'];
			$friends_row_array[$index]['image_url'] = ACSUser::get_image_url($user_info_row['user_community_id']);
			$friends_row_array[$index]['is_community_member'] = 0;
			foreach ($community_member_user_info_row_array as $community_member_user_info_row) {
				if ($user_info_row['user_community_id'] == $community_member_user_info_row['user_community_id']) {
					$friends_row_array[$index]['is_community_member'] = 1;
					break;
				}
			}
		}

		// URL
		$action_url = $this->getControllerPath('Community', 'InviteToCommunity') . '&community_id=' . $community_row['community_id'];

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('InviteToCommunity.tpl.php');

		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// set
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('friends_row_array', $friends_row_array);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('form', $request->getAttribute('form'));

		return parent::execute();
	}
}

?>
