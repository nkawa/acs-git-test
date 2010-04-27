<?php
// $Id: StaticIndexAction.class.php,v 1,0 2007/03/01 09:01:37 w-ota Exp $

class StaticIndexAction extends BaseAction
{
	// GET
	function execute() {

		$context = &$this->getContext();
		$controller = $context->getController();
		$user = $context->getUser();
		$request = $context->getRequest();

		$pagefile = ACS_PAGES_DIR . "index.html." . ACSMsg::get_lang();
		$lockfile = $pagefile.".locked";

		// 静的ファイル書き換え中の場合(0.5秒待つ)
		if (is_readable($lockfile)) {
			usleep(500000);
		}

		// 書き換え中でなく、静的ファイルが存在する場合
		if (!is_readable($lockfile) && is_readable($pagefile)) {

			// 静的ファイル作成時間が有効時間範囲内の場合
			if ((time() - filemtime($pagefile)) <= ACS_PAGES_EFFECTIVE_SEC) {

				// 静的トップを標準出力
				mb_http_output('pass');
				readfile($pagefile);
				return;

			}
		}
		$request->setAttribute('force_realtime', 1);
		$controller->forward("Public", "Index");
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
