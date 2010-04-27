<?php
// $Id: IndexAction.class.php,v 1.3 2007/03/01 09:01:37 w-ota Exp $

class IndexAction extends BaseAction
{
	// GET
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$controller = $context->getController();

//		$controller = $context->getController();
//		return View::SUCCESS;


		// 動的トップページの場合
		if ($_SERVER['HTTP_ACS_AGENT'] == 'create_statictop'
			|| array_key_exists('realtime',$_REQUEST)
			|| $request->getAttribute('force_realtime') 
			|| $acs_user_info_row['is_login_user']
			) {

			if ($_SERVER['HTTP_ACS_AGENT'] == 'create_statictop') {
				ini_set("url_rewriter.tags",''); // urlパラメータ上のPHPSESSIDを省く
			}

			$controller = $context->getController();
			return View::SUCCESS;

		} else {

			$controller->forward('Public','StaticIndex');
			//return VIEW_NONE;
		}


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
