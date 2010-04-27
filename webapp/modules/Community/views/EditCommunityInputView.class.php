<?php
/**
 * ���ߥ�˥ƥ���ǽ��View���饹
 * ���ߥ�˥ƥ������ѹ�����
 * @package  acs/webapp/modules/Community/views
 * @author   akitsu
 * @since    PHP 4.0
 * @version  $Revision: 1.4 $ $Date: 2006/03/09
 */
// $Id: EditCommunityView::INPUT.class.php,v 1.4 2006/12/18 07:42:13 w-ota Exp $

class EditCommunityInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$category_group_master_row_array = $request->getAttribute('category_group_master_row_array');
		$community_row = $request->getAttribute('community_row');
		$sub_community_row_array = $request->getAttribute('sub_community_row_array');
		$parent_community_row_array = $request->getAttribute('parent_community_row_array');

		// �ù�
		$community_row['register_date'] = ACSLib::convert_pg_date_to_str($community_row['register_date'], 0, 0, 0); // ��Ͽ��
		
		// �Ǽ��ġ����ߥ�˥ƥ��ե�����θ����ϰϡʵ������
		foreach (array('bbs', 'community_folder') as $key) {
			foreach($community_row['contents_row_array'][$key]['trusted_community_row_array'] as $index => $trusted_community_row) {
				$community_row['contents_row_array'][$key]['trusted_community_row_array'][$index]['top_page_url'] = 
					 $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $trusted_community_row['community_id'];
			}
		}

		// ���ƥ���ޥ�������
		$category_master_row_array = array();
		foreach ($category_group_master_row_array as $category_group_master_row) {
			foreach ($category_group_master_row['category_master_row_array'] as $category_master_row) {
				array_push($category_master_row_array, $category_master_row);
			}
		}

		// URL
		$action_url = $this->getControllerPath('Community', 'EditCommunity'). '&community_id=' . $community_row['community_id'];
		$select_trusted_community_url = $this->getControllerPath('Community', 'SelectTrustedCommunity');
		// ���ߥ�˥ƥ��ȥåץڡ�����URL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('EditCommunity.tpl.php');

		// set
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('select_trusted_community_url', $select_trusted_community_url);
		$this->setAttribute('community_top_page_url', $community_top_page_url);

		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('category_master_row_array', $category_master_row_array);
		$this->setAttribute('bbs_open_level_master_row_array', $request->getAttribute('bbs_open_level_master_row_array'));
		$this->setAttribute('community_folder_open_level_master_row_array', $request->getAttribute('community_folder_open_level_master_row_array'));
		$this->setAttribute('self_open_level_master_row_array', $request->getAttribute('self_open_level_master_row_array'));
		$this->setAttribute('edit_community_ml_address', 
				$request->getAttribute('edit_community_ml_address'));

		// ���顼���Υ�å�����ɽ��
		$this->setAttribute('error_message', 
				$this->getErrorMessage($controller, $request, $user));

		return parent::execute();
	}
}

?>
