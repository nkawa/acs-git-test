<?php
/**
 * �������꡼��ǽ��View���饹
 * @package  acs/webapp/modules/User/views
 * DiaryView::INPUT
 * @author   ota  update akitsu
 * @since	PHP 4.0
 */
// $Id: DiaryView::INPUT.class.php,v 1.18 2006/12/13 09:51:49 w-ota Exp $

class DiaryInputView extends BaseView
{
	function execute() {
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$controller = $context->getController();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$diary_row_array = $request->getAttribute('diary_row_array');
		$diary_comment_row_array = $request->getAttribute('diary_comment_row_array');
		$open_level_master_row_array = $request->getAttribute('open_level_master_row_array');
		$friends_group_row_array = $request->getAttribute('friends_group_row_array');
		$last_open_level_code = $request->getAttribute('last_open_level_code');

		// �ȥåץڡ���URL
		$link_page_url['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Diary') . '&id=' . $acs_user_info_row['user_community_id'];
		// ¾�ͤ�������������Ƥ�����Υȥåץڡ���URL
		$link_page_url['else_user_diary_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Index') . '&id=' . $target_user_info_row['community_id'];
		// ��������URL
		$link_page_url['search_diary_url'] = $this->getControllerPath(DEFAULT_MODULE, 'SearchDiary') . '&id=' . $target_user_info_row['community_id'];
		// �������꡼RSS URL
		$term = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'DIARY_RSS_TERM');
		$link_page_url['diary_rss_url'] = $this->getControllerPath(DEFAULT_MODULE, 'DiaryRSS')
			 . '&id=' . $target_user_info_row['community_id']
			 . "&term=$term";

		// �ù�
		foreach ($diary_row_array as $index => $diary_row) {
			// ����URL
			$diary_row_array[$index]['image_url'] = ACSUser::get_image_url($diary_row['community_id'], 'thumb');
			// �������
			$diary_row_array[$index]['post_date'] = ACSLib::convert_pg_date_to_str($diary_row['post_date']);
			// ������� (��ά��: M/D)
			$diary_row_array[$index]['short_post_date'] = gmdate("n/j", strtotime($diary_row['post_date']) + 9*60*60);
			// �����ȥڡ���URL
			$diary_row_array[$index]['diary_comment_url'] = $this->getControllerPath(DEFAULT_MODULE, 'DiaryComment') . '&id=' . $target_user_info_row['community_id'] . '&diary_id=' . $diary_row['diary_id'];

			// ����Ѥߥ��ߥ�˥ƥ�(�ޥ��ե�󥺥��롼��)���������Ƥ��뤫
			if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
				if (count($diary_row['trusted_community_row_array'])
					&& $diary_row['trusted_community_row_array'][0]['community_type_name'] == ACSMsg::get_mst('community_type_master','D20')) {
					$diary_row_array[$index]['trusted_community_flag'] = 0;
				} else {
					$diary_row_array[$index]['trusted_community_flag'] = 1;
				}
			}
			// �������URL
			$diary_row_array[$index]['diary_delete_url'] = $this->getControllerPath(DEFAULT_MODULE, 'DeleteDiary') . '&id=' . $target_user_info_row['user_community_id'] . '&diary_id=' . $diary_row['diary_id'];
			// �ե�����β���URL
			$diary_row_array[$index]['file_url'] = "";
			if($diary_row['file_id'] != ""){
				$diary_row_array[$index]['file_url'] = ACSDiaryFile::get_image_url($diary_row['file_id'],'thumb');		//�����ɽ����
				$diary_row_array[$index]['file_url_alink'] = ACSDiaryFile::get_image_url($diary_row['file_id'],'');	//�ݥåץ��å���
			}
		}

		// �ܿͤΥڡ������ɤ���
		if ($target_user_info_row['user_community_id'] == $acs_user_info_row['user_community_id']) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}
		// �񤭹��ߥܥ���ǳ�ǧ���̤�ɽ��
		$action_url = $this->getControllerPath(DEFAULT_MODULE, 'DiaryPre') . '&id=' . $target_user_info_row['user_community_id'] ."&move_id=1";

		//---- ������������ ----//
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		$diary_row_array = ACSAccessControl::get_valid_row_array_for_user_community($acs_user_info_row, $role_array, $diary_row_array);
		//----------------------//

		// �ڡ���������
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $diary_row_array, $display_count);

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('diary_row_array', $diary_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('link_page_url', $link_page_url);
		$this->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$this->setAttribute('friends_group_row_array', $friends_group_row_array);
		$this->setAttribute('last_open_level_code', $last_open_level_code);

		// ����饤��ɽ��(��������) ����ͤ�����
//		$this->setAttribute('new_calendar_action_chain', $request->getAttribute('new_calendar_action_chain_html'));
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
		$controller->forward("User", "DiaryCalendar");
		$this->setAttribute("DiaryCalendar", $request->getAttribute("DiaryCalendar"));

		// �������⡼�ɤ򸵤��᤹
		$controller->setRenderMode($renderMode); 
		$this->inlineFlg = false;

		/*----------------------------------------------*/
		
		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('Diary.tpl.php');
		
		// ��ǧ���̤��饭��󥻥�ܥ������äƤ����Ȥ��Τߤν���
		if($request->getParameter('move_id') == 3){
			//�桼�����Ͼ���
			$form = $user->getAttribute('new_form_obj');
			$this->setAttribute('form', $form);
			$this->setAttribute('move_id', $request->getParameter('move_id'));
		}

		return parent::execute();
	}
}

?>
