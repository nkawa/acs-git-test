<?php
/**
 * ログアウト処理
 *
 * @author  y-yuki
 * @version $Revision: 1.1 $ $Date: 2008/03/24 07:09:27 $
 */
class LogoutSuccessView extends BaseView
{
	function execute() {

		$this->setAttribute('logout_complete', "1");
		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('Logout.tpl.php');

		return parent::execute();
	}
}

?>
