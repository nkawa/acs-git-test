<?php
/**
 * ログアウト（エラー）処理
 *
 * @author  y-yuki
 * @version $Revision: 1.1 $ $Date: 2008/03/24 07:09:27 $
 */
class LogoutErrorView extends BaseView
{
	function execute() {

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('Logout.tpl.php');

		return parent::execute();
	}
}

?>
