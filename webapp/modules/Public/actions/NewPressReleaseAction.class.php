<?php
// $Id: NewPressReleaseAction.class.php,v 1.2 2006/05/29 00:36:07 w-ota Exp $

class NewPressReleaseAction extends BaseAction {

	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ����ѥ֥�å���꡼���������������
		$new_bbs_for_press_release_row_array = ACSBBS::get_new_bbs_for_press_release_row_array();

		// set
		$request->setAttribute('new_bbs_for_press_release_row_array', $new_bbs_for_press_release_row_array);

		return View::INPUT;
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
