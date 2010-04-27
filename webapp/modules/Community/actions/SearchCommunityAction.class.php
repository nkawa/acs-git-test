<?php
// $Id: SearchCommunityAction.class.php,v 1.7 2006/11/20 08:44:12 w-ota Exp $

class SearchCommunityAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		// 入力フォーム
		$form = $request->ACSgetParameters();

		// カテゴリグループマスタ
		$category_group_master_row_array = ACSCommunity::get_category_group_master_row_array();
		foreach ($category_group_master_row_array as $index => $category_group_master_row) {
			// カテゴリグループごとのカテゴリマスタ
			$category_group_master_row_array[$index]['category_master_row_array'] = ACSCommunity::get_category_master_row_array_by_category_group_code($category_group_master_row['category_group_code']);
		}

		// カテゴリごとのコミュニティ数
		$category_code_community_num_array = ACSCommunity::get_category_code_community_num_array();

		// 検索時
		if ($form['search']) {
			// コミュニティ一覧を取得する
			$community_row_array = ACSCommunity::search_community_row_array($acs_user_info_row['user_community_id'], $form);

			// 概要
			foreach ($community_row_array as $index => $community_row) {
				$community_row_array[$index]['contents_row_array']['community_profile'] = ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D07'));
			}

			// set
			$request->setAttribute('community_row_array', $community_row_array);

		}

		// set
		$request->setAttribute('category_group_master_row_array', $category_group_master_row_array);
		$request->setAttribute('category_code_community_num_array', $category_code_community_num_array);
		$request->setAttribute('form', $form);

		return View::INPUT;
	}
	
	/**
	 * 認証チェックを行うか
	 * アクションを実行する前に、認証チェックが必要か設定する
	 * @access  public
	 * @return  boolean 認証チェック有無（true:必要、false:不要）
	 */
	function isSecure()
	{
		return false;
	}
	
}

?>
