<?php
// $Id: IndexAction.class.php,v 1.3 2007/03/01 09:01:37 w-ota Exp $

class TestAction extends BaseAction
{
	// GET
	function execute() {

		return View::SUCCESS;
	}

	/**
	 * ǧ�ڥ����å���Ԥ���
	 * ����������¹Ԥ������ˡ�ǧ�ڥ����å���ɬ�פ����ꤹ��
	 * @access  public
	 * @return  boolean ǧ�ڥ����å�̵ͭ��true:ɬ�ס�false:���ס�
	 */
	public function isSecure()
	{
		return false;
	}
}

?>
