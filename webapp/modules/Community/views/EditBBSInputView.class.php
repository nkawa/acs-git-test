<?php
// $Id: EditBBSView::INPUT.class.php,v 1.1 2006/06/08 05:53:03 w-ota Exp $

class EditBBSInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$bbs_row = $request->getAttribute('bbs_row');
		$form = $request->getAttribute('form');

		// 入力エラー時のデータ復元
		if (is_array($form)) {
			$bbs_row['subject'] = $form['subject'];
			$bbs_row['body'] = $form['body'];
		}

		// get
		if ($bbs_row['file_id'] != '') {
			$bbs_row['file_url'] = ACSBBSFile::get_image_url($bbs_row['bbs_id'], 'thumb');
		}

		// form action 確認画面への遷移
		$action_url  = $this->getControllerPath('Community', 'EditBBS') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];
		// BBS URL
		$bbs_url = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_row['community_id'];

		// set
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('community_row', $request->getAttribute('community_row'));
		$this->setAttribute('bbs_row', $bbs_row);
		$this->setAttribute('action_url', $action_url);

		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('bbs_url', $bbs_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('EditBBS.tpl.php');
		
		return parent::execute();
	}
}

?>
