<?php
// $Id: SelectTrustedCommunityAction.class.php,v 1.4 2006/03/20 05:58:53 w-ota Exp $

class SelectTrustedCommunityAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
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

		$form_name = $request->ACSgetParameter('form_name');
		$prefix = $request->ACSgetParameter('prefix');

		// ������
		if ($form['search']) {
			// ���ߥ�˥ƥ��������������
			$community_row_array = ACSCommunity::search_community_row_array($acs_user_info_row['user_community_id'], $form);
		} else {
			$community_row_array = ACSCommunity::get_community_row_array($acs_user_info_row['user_community_id']);
		}

		// set
		$request->setAttribute('community_row_array', $community_row_array);
		$request->setAttribute('category_group_master_row_array', $category_group_master_row_array);
		$request->setAttribute('form', $form);
		$request->setAttribute('form_name', $form_name);
		$request->setAttribute('prefix', $prefix);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
}

?>
