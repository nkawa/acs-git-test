<?php
/**
 * �������ȡʥ��顼�˽���
 *
 * @author  y-yuki
 * @version $Revision: 1.1 $ $Date: 2008/03/24 07:09:27 $
 */
class LogoutErrorView extends BaseView
{
	function execute() {

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('Logout.tpl.php');

		return parent::execute();
	}
}

?>
