<?php
// $Id: NewCommunityAction.class.php,v 1.2 2006/11/20 08:44:16 w-ota Exp $

class NewCommunityAction extends BaseAction {

	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 新着公開ダイアリー一覧を取得する
		$new_community_row_array = ACSCommunity::get_new_community_row_array();
		foreach ($new_community_row_array as $index => $new_community_row) {
			// 概要
			$new_community_row_array[$index]['contents_row_array']['community_profile'] = ACSCommunity::get_contents_row($new_community_row['community_id'], ACSMsg::get_mst('contents_type_master','D07'));
		}

		// set
		$request->setAttribute('new_community_row_array', $new_community_row_array);

		return View::INPUT;
	}
	
	/**
	 * 認証チェックを行うか
	 * アクションを実行する前に、認証チェックが必要か設定する
	 * @access  public
	 * @return  boolean 認証チェック有無（true:必要、false:不要）
	 */
	public function isSecure()
	{
		return false;
	}
}

?>
