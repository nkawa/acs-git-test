<?php
/**
 * �������Ƚ���
 *
 * @author  y-yuki
 * @version $Revision: 1.1 $ $Date: 2008/03/24 07:09:27 $
 */
class LogoutAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();

		// �����ॹ����׹���
		$ret = ACSUser::upd_login_date($user);
		
		if (!$ret) {
			return View::ERROR;
		}
		
		// �������ȥ桼��ID���ʤ��ʤ��BaseAction��ǧ�ڤǤ��ʤ��ʤ�
		$user->removeAttribute('login_user_id');
		$user->removeAttribute('getLogoutDateEverytime');
		
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
