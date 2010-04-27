<?php
// $Id: CommunityRankingAction.class.php,v 1.1 2006/03/10 11:45:11 w-ota Exp $

class CommunityRankingAction extends BaseAction {

	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ��������������꡼�������������
		$ranking_community_row_array = ACSCommunity::get_ranking_community_row_array();

		// set
		$request->setAttribute('ranking_community_row_array', $ranking_community_row_array);

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
