<?php
// $Id: EditExternalRSSView::INPUT.class.php,v 1.1 2007/03/28 05:58:21 w-ota Exp $

class EditExternalRSSInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$community_admin_user_info_row_array = $request->getAttribute('community_admin_user_info_row_array');

		// 信頼済みコミュニティ情報
		if (is_array($community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'])) {
			foreach ($community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'] as $index => $trusted_community_row) {
				$trusted_community_row = ACSCommunity::get_community_row($trusted_community_row['community_id']);
				$trusted_community_row['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $trusted_community_row['community_id'];
				$community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'][$index] = $trusted_community_row;
			}
		}

		// URL
		$action_url = $this->getControllerPath('Community', 'EditExternalRSS') . '&community_id=' . $community_row['community_id'];
		// 信頼済みコミュニティ選択
		$select_trusted_community_url = $this->getControllerPath('Community', 'SelectTrustedCommunity');

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('EditExternalRSS.tpl.php');

		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// set
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('community_admin_user_info_row_array', $community_admin_user_info_row_array);

		$this->setAttribute('external_rss_url_open_level_master_row_array', $request->getAttribute('external_rss_url_open_level_master_row_array'));

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('select_trusted_community_url', $select_trusted_community_url);
		$this->setAttribute('community_top_page_url', $community_top_page_url);

		return parent::execute();
	}
}

?>
