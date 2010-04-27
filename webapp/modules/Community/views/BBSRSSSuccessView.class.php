<?php
// $Id: BBSRSSSuccessView.class.php,v 1.20 2009/06/19 10:05:00 acs Exp $

class BBSRSSSuccessView extends BaseView
{
	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$bbs_row_array = $request->getAttribute('bbs_row_array');
		$term = $request->getAttribute('term');

		// �桼������
		$community_row['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION)
			 . '&community_id=' . $community_row['community_id'];
		
		$community_row['image_url'] = ACSCommunity::get_image_url($community_row['community_id']);

		if ($community_row['file_id'] != '') {
			$community_file_info_row = ACSFileInfoModel::select_file_info_row($community_row['file_id']);
			$community_row['image_title'] = $community_file_info_row['display_file_name'];
		} else {
			// ���ߥ�˥ƥ��̿��Ϥ���ޤ���
			$community_row['image_title'] = ACSMsg::get_msg('Community', ' BBSRSSSuccessView.class.php', 'M001');
		}

		// �ù�
		foreach ($bbs_row_array as $index => $bbs_row) {
			// �Ƶ�������Ƽ� �ȥåץڡ���URL
			$bbs_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION)
				 . '&community_id=' . $bbs_row['community_id'];
			// �ֿ�����URL
			$bbs_row_array[$index]['bbs_res_url'] = $this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
			// �ե�����β���URL
			if($bbs_row['file_id'] != ""){
				$bbs_row_array[$index]['file_url'] = ACSBBSFile::get_image_url($bbs_row['bbs_id'], 'rss');
			}
		}

		//---- ������������ ----//
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row);
		$bbs_row_array = ACSAccessControl::get_valid_row_array_for_community($acs_user_info_row, $role_array, $bbs_row_array);
		//----------------------//

		// ACSBBS::print_bbs_rss()�ǻ��Ѥ���ѥ�᡼���򥻥åȤ���
		$params = array();
		// �١���URL
		if ($acs_user_info_row['is_acs_user']) {
			$params['base_url'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_LOGIN_URL');
		} else {
			$params['base_url'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_URL');
		}

		// ���Ȥ�URL
		$params['rss_syndication_url'] = $params['base_url']
			 . $this->getControllerPath('Community', 'BBSRSS')
			 . '&id=' . $community_row['community_id']
			 . '&term=' . $term;

		// RSS������
		ACSBBS::print_bbs_rss($community_row, $bbs_row_array, $params);

		// ��λ
		exit;
	}
}

?>
