<?php
// $Id: NewPressReleaseAction.class.php,v 1.2 2006/05/29 00:36:07 w-ota Exp $

class NewPressReleaseAction extends BaseAction {

	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 新着パブリックリリース一覧を取得する
		$new_bbs_for_press_release_row_array = ACSBBS::get_new_bbs_for_press_release_row_array();

		// set
		$request->setAttribute('new_bbs_for_press_release_row_array', $new_bbs_for_press_release_row_array);

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
