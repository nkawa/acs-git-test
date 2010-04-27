<?php
/**
 * �������꡼��Action���饹
 * 
 * DiaryCommentAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   w-ota                     @editor akitsu
 * @since    PHP 4.0
 */
// $Id: DiaryAction.class.php,v 1.13 2007/03/27 02:12:41 w-ota Exp $

class DiaryAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// ɽ���оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');

		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		$year = $request->ACSgetParameter('year');   // ǯ
		$month = $request->ACSgetParameter('month'); // ��
		$day = $request->ACSgetParameter('day');     // ��

		if (checkdate($month, $day, $year)) {
			// ǯ��������
			$diary_row_array = ACSDiary::get_diary_row_array_by_year_month_day($user_community_id, $year, $month, $day);
		} elseif (checkdate($month, 1, $year)) {
			// ǯ�����
			$diary_row_array = ACSDiary::get_diary_row_array_by_year_month($user_community_id, $year, $month);
			unset($day);
		} else {
			// ���ƤΥ������꡼
			$diary_row_array = ACSDiary::get_diary_row_array($user_community_id);
			unset($year);
			unset($month);
			unset($day);
		}

		// �����ϰϤ�ǽ���Ͽ��Ʊ����Τ�ɽ�����Ƥ���
		if($diary_row_array){
			$last_open_level_code = $diary_row_array[0]['open_level_code'];
		}
		
		// ����Ѥߥ��ߥ�˥ƥ�����
		foreach ($diary_row_array as $index => $diary_row) {
			if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
				$diary_row_array[$index]['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
			}
		}

		// �����ϰ�
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D21'));

		// �ޥ��ե�󥺥��롼��
		$friends_group_row_array = ACSUser::get_friends_group_row_array($user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('diary_row_array', $diary_row_array);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$request->setAttribute('friends_group_row_array', $friends_group_row_array);
		$request->setAttribute('last_open_level_code', $last_open_level_code);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();

		//����󥻥����äƤ����Ȥ��Τߤν���
		$move_id = $request->getParameter('move_id');
		if($move_id == 3){
			$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		
			// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
			$user_id = $request->ACSGetParameter('id');

			// ���Υե����������������
			$form = $user->getAttribute('new_form_obj');//��̾��subject ���ơ�body �����ϰϡ�open_level_code ������new_file
			$form['user_community_id'] = $acs_user_info_row['user_community_id']; // ��Ͽ��

			$user->setAttribute('new_form_obj',$form);
		}

		// GET�ν�����
		return $this->getDefaultView();
	}

	function getRequestMethods() {
		return REQ_POST;
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
