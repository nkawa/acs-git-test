<?php
// $Id: AddCommunityLinkView::INPUT.class.php,v 1.3 2006/03/23 12:36:46 kuwayama Exp $

class AddCommunityLinkInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser(); 
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$input_form    = $request->getAttribute('form');

		// エラー時の入力値を取得
		$form = array();
		if ($input_form) {
			// 選択されたコミュニティ情報取得
			$trusted_community_row_array = array();
			if ($input_form['trusted_community_id_array']) {
				foreach ($input_form['trusted_community_id_array'] as $trusted_community_id) {
					// 初期化
					$_community_row = array();
					$trusted_community_row = array();

					$_community_row = ACSCommunity::get_community_row($trusted_community_id);
					$trusted_community_row['community_id']   = $_community_row['community_id'];
					$trusted_community_row['community_name'] = $_community_row['community_name'];
					$trusted_community_row['top_page_url']   = $this->getControllerPath('Community', DEFAULT_ACTION);
					$trusted_community_row['top_page_url']  .= '&community_id=' . $_community_row['community_id'];

					array_push($trusted_community_row_array, $trusted_community_row);
				}
			}

			$form['link_type'] = $input_form['link_type'];
			$form['trusted_community_row_array'] = $trusted_community_row_array;
			$form['message'] = $input_form['message'];

		}

		// URL
		$action_url = $this->getControllerPath('Community', 'AddCommunityLink') . '&community_id=' . $community_row['community_id'];
		$select_trusted_community_url = $this->getControllerPath('Community', 'SelectTrustedCommunity');

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// コミュニティリンク設定URL
		$community_link_url = $this->getControllerPath('Community', 'CommunityLink') . '&community_id=' . $community_row['community_id'];

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('AddCommunityLink.tpl.php');

		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// set
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('select_trusted_community_url', $select_trusted_community_url);
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('community_link_url', $community_link_url);
		$this->setAttribute('form', $form);

		return parent::execute();
	}
}

?>
