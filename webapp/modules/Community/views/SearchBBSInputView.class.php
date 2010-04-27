<?php
/**
 * 掲示板検索機能　Viewクラス
 * @package  acs/webapp/modules/User/views
 * SearchDiaryView::INPUT
 * @author  akitsu
 * @since	PHP 4.0
 */
// $Id: SearchBBSInputView.class.php,v 1.3 2006/03/23 01:37:36 kuwayama Exp $


class SearchBBSInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$community_id = $request->getParameter('community_id');
		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$open_level_master_row_array = $request->getAttribute('open_level_master_row_array');

			// トップページURL
			$link_page_url['top_page_url'] = $this->getControllerPath('Community', 'Index') . '&id=' . '&community_id=' . $community_id;
			// BBS URL
			$link_page_url['bbs_page_url'] = $this->getControllerPath('Community', 'BBS') . '&id=' . '&community_id=' . $community_id;
			//検索画面url
			$link_page_url['search_bbs_url'] = SCRIPT_PATH;
			// get でサブミットするための、遷移先情報
			$module = 'Community';
			$action = 'SearchResultBBS';
			$community_id = $community_id;
			$move_id = '1';

		// 本人のページかどうか
		if ($target_user_info_row['user_community_id'] == $acs_user_info_row['user_community_id']) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}

		// set
		$this->setAttribute('community_row', $target_user_info_row);
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('link_page_url', $link_page_url);
		$this->setAttribute('open_level_master_row_array', $open_level_master_row_array);

		$this->setAttribute('module',$module);
		$this->setAttribute('action',$action);
		$this->setAttribute('community_id',$community_id);
		$this->setAttribute('move_id',$move_id);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SearchBBS.tpl.php');


		return parent::execute();
	}
}

?>
