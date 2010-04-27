<?php
// $Id: EditCommunityAdminView::INPUT.class.php,v 1.1 2006/03/07 07:34:35 w-ota Exp $

class EditCommunityAdminInputView extends BaseView
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

		foreach ($community_member_user_info_row_array as $index => $user_info_row) {
			// 自分のユーザ情報は設定対象外
			if ($user_info_row['user_community_id'] == $acs_user_info_row['user_community_id']) {
				unset($community_member_user_info_row_array[$index]);
				continue;
			}
			$community_member_user_info_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $user_info_row['user_community_id'];
			$community_member_user_info_row_array[$index]['image_url'] = ACSUser::get_image_url($user_info_row['user_community_id'], 'thumb');
			$community_member_user_info_row_array[$index]['is_community_admin'] = ACSCommunity::is_community_admin($user_info_row['user_community_id'], $community_row['community_id']);
		}

		// アクセス者 コミュニティ管理者
		$acs_user_info_row['is_community_admin'] = ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $community_row['community_id']);
		$acs_user_community_row = ACSCommunity::get_community_row($acs_user_info_row['user_community_id']);
		$acs_user_info_row['community_name'] = $acs_user_community_row['community_name'];

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// action URL
		$action_url = $this->getControllerPath('Community', 'EditCommunityAdmin') . '&community_id=' . $community_row['community_id'];

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('EditCommunityAdmin.tpl.php');

		// set
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('community_member_user_info_row_array', $community_member_user_info_row_array);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('action_url', $action_url);

		return parent::execute();
	}
}

?>
