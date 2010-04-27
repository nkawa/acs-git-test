<?php
// $Id: UserRankingAction.class.php,v 1.1 2006/03/10 11:45:11 w-ota Exp $

class UserRankingAction extends BaseAction {

	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �桼����󥭥󥰾���������������
		$ranking_user_info_row_array = ACSUser::get_ranking_user_info_row_array();

		// set
		$request->setAttribute('ranking_user_info_row_array', $ranking_user_info_row_array);

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
