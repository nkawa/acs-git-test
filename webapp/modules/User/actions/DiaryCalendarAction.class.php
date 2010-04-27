<?php
/**
 * ��������������Action���饹
 * 
 * DiaryCalendarAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   akitsu
 * @since    PHP 4.0
 */
// $Id: DiaryCalendarAction.class.php,v 1.6 2006/11/20 08:44:25 w-ota Exp $

class DiaryCalendarAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
			// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		if ($user_community_id == null || $user_community_id == '') {
			$user_community_id = $request->getAttribute("id");
		}

		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		$top_page_url = $this->getControllerPath("User", 'Diary') . '&id=' .$user_community_id;
		
		$year = $request->ACSgetParameter('year');   // ǯ
		$month = $request->ACSgetParameter('month'); // ��

		if (!checkdate($month, 1, $year)) {
			$date_array = getdate(); // ���ߤ����վ���
			$year = $date_array["year"];
			$month = $date_array["mon"];
		}

		//ɽ����($year/$month)�˳�������ǡ����򸡺��˹Ԥ�
		$diary_row_array = ACSDiary::get_diary_row_array_by_year_month($user_community_id, $year, $month);
		// ����Ѥߥ��ߥ�˥ƥ�������ɲ�
		foreach ($diary_row_array as $index => $diary_row) {
			if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
				$diary_row_array[$index]['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
			}
		}
		//---- ������������ ----//
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		foreach ($diary_row_array as $index => $diary_row) {
			// ����Ѥߥ��ߥ�˥ƥ�(�ޥ��ե�󥺥��롼��)���������Ƥ��뤫
			if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
				if (count($diary_row['trusted_community_row_array'])
					&& $diary_row['trusted_community_row_array'][0]['community_type_name'] == ACSMsg::get_mst('community_type_master','D20')) {
					$diary_row_array[$index]['trusted_community_flag'] = 0;
				} else {
					$diary_row_array[$index]['trusted_community_flag'] = 1;
				}
			}
		}
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		$diary_row_array = ACSAccessControl::get_valid_row_array_for_user_community($acs_user_info_row, $role_array, $diary_row_array);

		//----------------------//
		// ���饹������
		// calendar(��������, ����ʳ������դ�ɽ�����뤫�ɤ���) �η��ǻ��ꤷ�ޤ���
		// ������������0-���� ���� 6-���ˡˡ�����ʳ������դ�ɽ����0-No, 1-Yes��
		$calendar_obj = new calendar(0, 0);	// ɽ����ʳ������դ�ɽ�����ʤ����ȤǶ��̤�����

		// ��󥯤�����
		$calendar_obj->clear_link();
		if($diary_row_array){
			// ��󥯤Τ���֡������֤�
			foreach ($diary_row_array as $index => $diary_row) {
				if($diary_row){
					$link_date = substr($diary_row['post_date'],8,2);
					if(substr($link_date,0,1) == '0'){
						$link_date = substr($link_date,1,1);
					}
					$calendar_obj->set_link($link_date, $top_page_url ."&year=$year&month=$month&day=$link_date", ACSMsg::get_msg('User', 'DiaryCalendarAction.class.php' ,'M001'));
				}
			}
		}

		// �����Υ�����������
		// show_calendar(ǯ, ��, ��) �η��ǻ��ꤷ�ޤ���
		// �����Ͼ�ά��ǽ�Ǥ������ꤹ��Ȥ�������������ɽ������ޤ���
		$calendar_obj->set_str_url($top_page_url);
		$new_calendar_html = $calendar_obj->show_calendar($year, $month); // ǯ�ȷ����ꤷ�ƥ���������ɽ��

		$request->setAttribute('new_calendar_html', $new_calendar_html);

		return View::INPUT;
	}
	
	function isSecure () {
		return false;
	}
	
	function getCredential () {
		return array('USER_PAGE_OWNER');
	}
}

?>
