<?php
/**
 * ログアウト処理
 *
 * @author  y-yuki
 * @version $Revision: 1.1 $ $Date: 2008/03/24 07:09:27 $
 */
class LogoutAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();

		// タイムスタンプ更新
		$ret = ACSUser::upd_login_date($user);
		
		if (!$ret) {
			return View::ERROR;
		}
		
		// ログアウトユーザIDがなくなるとBaseActionで認証できなくなる
		$user->removeAttribute('login_user_id');
		$user->removeAttribute('getLogoutDateEverytime');
		
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
