<?php
/**
 * アクセス不可メッセージ表示
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/03/27 07:49:29 $
 */
class GlobalSecureSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// テンプレート
		$this->setTemplate('GlobalSecure.tpl.php');
		$this->setScreenId("0001");

		return parent::execute();
	}
}

?>
