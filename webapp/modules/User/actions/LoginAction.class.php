<?php
// $Id: LoginAction.class.php,v 1.8 2008/04/24 16:00:00 y-yuki Exp $

class LoginAction extends BaseAction
{
	function execute() {
				
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$user_id = $user->getAttribute('login_user_id');
		if ($user_id == null || $user_id == "") {
			if (ACSSystem::check_connect_outside() != "0") {
				// LDAP接続エラーの場合、処理終了
				$this->setError($controller, $request, $user, 'login_input', 
							ACSMsg::get_msg('User', 'LoginAction.class.php' ,'M002'));
			}
			else if ($_POST['userid'] != NULL && $_POST['userid'] != "") {
				// エラーの場合、処理終了
				$this->setError($controller, $request, $user, 'login_input', 
							ACSMsg::get_msg('User', 'LoginAction.class.php' ,'M001'));
			}

			return View::INPUT;
		}

		if ($acs_user_info_row['is_acs_user']) {
			// ログ登録: ログイン
			ACSLog::set_log($acs_user_info_row, 'Login', true, "[UserID:{$acs_user_info_row['user_id']}]");

			// ラストログイン登録
			ACSUser::set_last_login($acs_user_info_row);
			header("Location: ./");
		}
		return View::NONE;
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
