<?php
/**
 * ダイアリー検索機能　Viewクラス
 * @package  acs/webapp/modules/User/views
 * SearchDiaryView::INPUT
 * @author  akitsu
 * @since	PHP 4.0
 */
// $Id: SearchDiaryView::INPUT.class.php,v 1.4 2006/03/23 01:37:38 kuwayama Exp $


class SearchDiaryInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$open_level_master_row_array = $request->getAttribute('open_level_master_row_array');

			// トップページURL
			$link_page_url['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Diary') . '&id=' . $acs_user_info_row['user_community_id'];
			//他人の日記を閲覧している場合のトップページURL
			$link_page_url['else_user_top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Index') . '&id=' . $target_user_info_row['community_id'];
			//他人の日記を閲覧している場合の日記ページURL
			$link_page_url['else_user_diary_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Diary') . '&id=' . $target_user_info_row['community_id'];

			//検索画面url
			$link_page_url['search_diary_url'] = SCRIPT_PATH;
			// get でサブミットするための、遷移先情報
			$module = 'User';
			$action = 'SearchResultDiary';
			$id = $target_user_info_row['community_id'];
			$move_id = '1';

		// 本人のページかどうか
		if ($target_user_info_row['user_community_id'] == $acs_user_info_row['user_community_id']) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}

		//---- アクセス制御 ----//
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		//----------------------//


		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('link_page_url', $link_page_url);
		$this->setAttribute('open_level_master_row_array', $open_level_master_row_array);

		$this->setAttribute('module',$module);
		$this->setAttribute('action',$action);
		$this->setAttribute('id',$id);
		$this->setAttribute('move_id',$move_id);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SearchDiary.tpl.php');


		return parent::execute();
	}
}

?>
