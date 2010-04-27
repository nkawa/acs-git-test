<?php
// $Id: LoginInputAction.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $

class LoginInputAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 表示対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		if (empty($user_community_id)) {
			$user_community_id = $acs_user_info_row['user_community_id'];
		}
	
		$user_id = $user->getAttribute('login_user_id');
		if ($user_id == null || $user_id == "") {
			return View::INPUT;
		}
		return View::ERROR;
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
