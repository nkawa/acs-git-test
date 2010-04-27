<?php
// $Id: DiaryRSSView::SUCCESS.class.php,v 1.20 2009/06/19 10:10:00 acs Exp $

class DiaryRSSSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$diary_row_array = $request->getAttribute('diary_row_array');
		$term = $request->getAttribute('term');

		// �桼������
		$target_user_info_row['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION)
			 . '&id=' . $target_user_info_row['user_community_id'];
		
		$target_user_info_row['image_url'] = ACSUser::get_image_url($target_user_info_row['user_community_id']);

		if ($target_user_info_row['file_id'] != '') {
			$user_file_info_row = ACSFileInfoModel::select_file_info_row($target_user_info_row['file_id']);
			$target_user_info_row['image_title'] = $user_file_info_row['display_file_name'];
		} else {
			// �̿��Ϥ���ޤ���
			$target_user_info_row['image_title'] = ACSMsg::get_msg('User', 'DiaryRSSSuccessView.class.php' ,'M001');
		}


		// ����Ѥߥ��ߥ�˥ƥ�����
		foreach ($diary_row_array as $index => $diary_row) {
			// ͧ�ͤ˸���
			if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
				$diary_row_array[$index]['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
			}

			// �������꡼������URL
			$diary_row_array[$index]['diary_comment_url'] = $this->getControllerPath('User', 'DiaryComment')
				 . '&id=' . $target_user_info_row['community_id'] . '&diary_id=' . $diary_row['diary_id'];

			// �ե�����β���URL
			if ($diary_row['file_id'] != '') {
				$diary_row_array[$index]['file_url'] = ACSDiaryFile::get_image_url($diary_row['file_id']);
			}
		}

		// ACSDiary::print_diary_rss()�ǻ��Ѥ���ѥ�᡼���򥻥åȤ���
		$params = array();
		// �١���URL
		if ($acs_user_info_row['is_acs_user']) {
			$params['base_url'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_LOGIN_URL');
		} else {
			$params['base_url'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_URL');
		}

		// ���Ȥ�URL
		$params['rss_syndication_url'] = $params['base_url']
			 . $this->getControllerPath('User', 'DiaryRSS')
			 . '&id=' . $target_user_info_row['user_community_id']
			 . '&term=' . $term;

		// <description>
		if ($acs_user_info_row['is_acs_user']) {
			if (ACSUser::is_friends($acs_user_info_row['user_community_id'], $target_user_info_row['user_community_id'])) {
				// ͧ�͸���
				$params['description'] = $target_user_info_row['contents_row_array']['community_profile_friend']['contents_value'];
			} else {
				// ������桼������
				$params['description'] = $target_user_info_row['contents_row_array']['community_profile_login']['contents_value'];
			}
		} else {
			// ���̸���
			$params['description'] = $target_user_info_row['contents_row_array']['community_profile']['contents_value'];
		}


		//---- ������������ ----//
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		$diary_row_array = ACSAccessControl::get_valid_row_array_for_user_community($acs_user_info_row, $role_array, $diary_row_array);
		//----------------------//

		// RSS������
		ACSDiary::print_diary_rss($target_user_info_row, $diary_row_array, $params);

		// ��λ
		exit;
	}
}

?>
