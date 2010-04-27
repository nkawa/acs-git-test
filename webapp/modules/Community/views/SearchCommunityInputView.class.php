<?php
// $Id: SearchCommunityView::INPUT.class.php,v 1.12 2006/11/20 08:44:15 w-ota Exp $

class SearchCommunityInputView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$controller = $context->getController();

		// ACS�桼����������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row_array = $request->getAttribute('community_row_array');
		$category_group_master_row_array = $request->getAttribute('category_group_master_row_array');
		$category_code_community_num_array = $request->getAttribute('category_code_community_num_array');
		$form = $request->getAttribute('form');

		// �ڡ���������
		$display_count = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D03'), 'COMMUNITY_SEARCH_RESULT_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $community_row_array, $display_count);

		// ���ƥ���ޥ�������
		$category_master_row_array = array();
		array_push($category_master_row_array, 
				array('category_code' => 0, 'category_name' => ACSMsg::get_mst('file_category_master','D0000')));
		foreach ($category_group_master_row_array as $category_group_master_row) {
			foreach ($category_group_master_row['category_master_row_array'] as $category_master_row) {
				// ���ƥ��ꤴ�ȤΥ��ߥ�˥ƥ����򥻥å�
				$community_num = intval($category_code_community_num_array[$category_master_row['category_code']]);
				$category_master_row['community_num'] = $community_num;
				// push
				array_push($category_master_row_array, $category_master_row);
			}
		}

		// ���ߥ�˥ƥ�����
		if (is_array($community_row_array)) {
			foreach ($community_row_array as $index => $community_row) {
				$community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];
				$community_row_array[$index]['image_url'] = ACSCommunity::get_image_url($community_row['community_id'], 'thumb');
				$community_row_array[$index]['community_member_num'] = ACSCommunity::get_community_member_num($community_row['community_id']);

				// ���ߥ�˥ƥ������԰���
				$community_row_array[$index]['community_admin_user_info_row_array'] = ACSCommunity::get_community_admin_user_info_row_array($community_row['community_id']);
				foreach ($community_row_array[$index]['community_admin_user_info_row_array'] as $index2 => $community_admin_user_info_row) {
					$community_row_array[$index]['community_admin_user_info_row_array'][$index2]['top_page_url'] = 
						 $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $community_admin_user_info_row['user_community_id'];
				}
			}
		}
		
		if (!$form['search']) {
			/*----------------------------------------------*/
			// ���ɽ����
			// ���ߤΥ������⡼�ɤ����
			$renderMode = $controller->getRenderMode();

			//�������⡼�ɤ��� �ʲ��̽��Ϥ򥪥դˤ��Ƥ��
			$controller->setRenderMode(View::RENDER_VAR);
			$this->inlineFlg = true;

			// �ե����¦��Ƚ�Ǥ���
			$request->setAttribute("inline_mode", "1");

			// ���女�ߥ�˥ƥ�
			$controller->forward("Public", "NewCommunity");
			$this->setAttribute("NewCommunity", $request->getAttribute("NewCommunity"));

			// �������⡼�ɤ򸵤��᤹
			$controller->setRenderMode($renderMode); 
			$this->inlineFlg = false;

			/*----------------------------------------------*/
		}
		

		// URL
		$action_url = $this->getControllerPath();

		// RSS���ϥڡ�����URL
		$PressRelease_community_url = $this->getControllerPath("Community", 'PressReleaseAllRSS') ;

		// ���ߥ�˥ƥ�������URL
		$create_community_url = "";
		if ($acs_user_info_row['is_acs_user']) {
			$create_community_url = $this->getControllerPath("Community", 'CreateCommunity');
		}

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('SearchCommunity.tpl.php');

		// set
		$this->setAttribute('category_master_row_array', $category_master_row_array);

		$this->setAttribute('form', $form);
		$this->setAttribute('community_row_array', $community_row_array);
		$this->setAttribute('paging_info', $paging_info);
		
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('module', $form["module"]);
		$this->setAttribute('action', $form["action"]);

		$this->setAttribute('create_community_url', $create_community_url);
		$this->setAttribute('PressRelease_community_url', $PressRelease_community_url);

		$this->setAttribute('new_community_action_chain_html', $request->getAttribute('new_community_action_chain_html'));

		return parent::execute();
	}
}

?>
