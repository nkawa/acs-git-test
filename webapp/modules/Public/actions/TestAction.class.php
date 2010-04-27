<?php
// $Id: IndexAction.class.php,v 1.3 2007/03/01 09:01:37 w-ota Exp $

class TestAction extends BaseAction
{
	// GET
	function execute() {

		return View::SUCCESS;
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
