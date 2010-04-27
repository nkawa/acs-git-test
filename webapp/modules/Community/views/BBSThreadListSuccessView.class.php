<?php
// $Id: BBSThreadListSuccessView.class.php,v 1.1 2006/03/07 07:29:59 w-ota Exp $

class BBSThreadListSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$bbs_row_array = $request->getAttribute('bbs_row_array');

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// BBS URL
		$bbs_top_page_url = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_row['community_id'];

		// 加工
		foreach ($bbs_row_array as $index => $bbs_row) {
			// 親記事の投稿者 トップページURL
			$bbs_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $bbs_row['user_community_id'];
			// 投稿日時
			$bbs_row_array[$index]['post_date'] = ACSLib::convert_pg_date_to_str($bbs_row['post_date']);
			// 返信画面URL
			$bbs_row_array[$index]['bbs_res_url'] = $this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
		}


		//---- アクセス制御 ----//
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row);
		$bbs_row_array = ACSAccessControl::get_valid_row_array_for_community($acs_user_info_row, $role_array, $bbs_row_array);
		//----------------------//


		// set
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('bbs_top_page_url', $bbs_top_page_url);

		$this->setAttribute('community_row', $request->getAttribute('community_row'));
		$this->setAttribute('bbs_row_array', $bbs_row_array);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('BBSThreadList.tpl.php');

		return parent::execute();
	}
}

?>
