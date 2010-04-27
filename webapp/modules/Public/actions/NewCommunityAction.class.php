<?php
// $Id: NewCommunityAction.class.php,v 1.2 2006/11/20 08:44:16 w-ota Exp $

class NewCommunityAction extends BaseAction {

	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ��������������꡼�������������
		$new_community_row_array = ACSCommunity::get_new_community_row_array();
		foreach ($new_community_row_array as $index => $new_community_row) {
			// ����
			$new_community_row_array[$index]['contents_row_array']['community_profile'] = ACSCommunity::get_contents_row($new_community_row['community_id'], ACSMsg::get_mst('contents_type_master','D07'));
		}

		// set
		$request->setAttribute('new_community_row_array', $new_community_row_array);

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
