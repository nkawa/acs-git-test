<?php

class NewOpenDiaryAction extends BaseAction {

	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 新着公開ダイアリー一覧を取得する
		$new_open_diary_row_array = ACSDiary::get_new_open_diary_row_array();

		// set
		$request->setAttribute('new_open_diary_row_array', $new_open_diary_row_array);

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
