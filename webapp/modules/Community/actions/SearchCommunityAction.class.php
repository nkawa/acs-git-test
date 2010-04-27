<?php
// $Id: SearchCommunityAction.class.php,v 1.7 2006/11/20 08:44:12 w-ota Exp $

class SearchCommunityAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		// ���ϥե�����
		$form = $request->ACSgetParameters();

		// ���ƥ��ꥰ�롼�ץޥ���
		$category_group_master_row_array = ACSCommunity::get_category_group_master_row_array();
		foreach ($category_group_master_row_array as $index => $category_group_master_row) {
			// ���ƥ��ꥰ�롼�פ��ȤΥ��ƥ���ޥ���
			$category_group_master_row_array[$index]['category_master_row_array'] = ACSCommunity::get_category_master_row_array_by_category_group_code($category_group_master_row['category_group_code']);
		}

		// ���ƥ��ꤴ�ȤΥ��ߥ�˥ƥ���
		$category_code_community_num_array = ACSCommunity::get_category_code_community_num_array();

		// ������
		if ($form['search']) {
			// ���ߥ�˥ƥ��������������
			$community_row_array = ACSCommunity::search_community_row_array($acs_user_info_row['user_community_id'], $form);

			// ����
			foreach ($community_row_array as $index => $community_row) {
				$community_row_array[$index]['contents_row_array']['community_profile'] = ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D07'));
			}

			// set
			$request->setAttribute('community_row_array', $community_row_array);

		}

		// set
		$request->setAttribute('category_group_master_row_array', $category_group_master_row_array);
		$request->setAttribute('category_code_community_num_array', $category_code_community_num_array);
		$request->setAttribute('form', $form);

		return View::INPUT;
	}
	
	/**
	 * ǧ�ڥ����å���Ԥ���
	 * ����������¹Ԥ������ˡ�ǧ�ڥ����å���ɬ�פ����ꤹ��
	 * @access  public
	 * @return  boolean ǧ�ڥ����å�̵ͭ��true:ɬ�ס�false:���ס�
	 */
	function isSecure()
	{
		return false;
	}
	
}

?>
