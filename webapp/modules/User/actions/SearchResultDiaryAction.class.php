<?php
/**
 * �������꡼������̡�Action���饹
 * 
 * SearchResultDiaryAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   akitsu
 * @since    PHP 4.0
 */
// $Id: SearchResultDiaryAction.class.php,v 1.5 2006/12/08 05:06:42 w-ota Exp $

class SearchResultDiaryAction extends BaseAction
{
	// POST 	�����ܥ���ν���
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ɽ���оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		//mode�����̤����ܤ��������
		$move_id = $request->getParameter('move_id');

		// ���̾�Υե����������������
		if ($move_id == 1) {
			$form = $request->getParameters('search_form_default');
		} else if($move_id == 2) {
			$form = $request->getParameters('search_form_new');
		}

		// form�ξ���˽���������Ԥ�
		if ($move_id > 0) {
			$err_str = "";
			/*	Array
			    [id] => 1000
			    [move_id] => 2
			    [q_text] => �������				//���ʢ�ɬ�ܡ�
			    [search_title] => title_in_serch	//��̾������
			    [search_all] => subject_in_serch	//��ʸ������
			    [open_level_code] => 00				//�����ϰϡ�00������ʤ��ˡʢ�ɬ�ܡ�
			    [search_all_about] => all_in_serch	//���٤Ƥ�����������
			*/
		// ------------ ��������μ�������ա��Х���ñ�̤ǽ�����
		for ($i = 1; $i < 3; $i++) {
			$str_where_create[$i] = ACSDiary::set_diary_where_list($form,$i);
			if ($str_where_create[$i]['err_str']) {
				$err_str = $str_where_create[$i]['err_str'];	//where��ΰ����������Ǥ��ʤ����ڥ졼���ߥ�
				if ($err_str != '') {
					break;
				}
			} else {
				$str_where[$i] = $str_where_create[$i]['like_sql'];
			}
		}
		//�����ϰϤ��̻���
		$str_open_level_code = '00';
		if ($form['open_level_code'] != '00') {		//�����ϰϤ����򤷤Ƥ�����
			$str_open_level_code =  $form['open_level_code'] ;
		}

		// ------------ 
		// db�������� and or ����Ԥ�
		if (!$err_str) {
			$diary_row_array_result = ACSDiary::get_diary_where_array($str_where[1],$str_open_level_code,array());
			//ʣ����Υ��ޥ�
			//and�������פ�ʤ�����������or��ʬ���פ򸡺�����
			if ($str_where_create[1]['str_count'] == 2 || $str_where_create[2]['str_count'] == 2) {
				$str_where_create['not_id'] = array();
				foreach ($diary_row_array_result as $index => $diary_row) {
					array_push($str_where_create['not_id'], $diary_row['diary_id']);
				}
				$diary_row_array_not = ACSDiary::get_diary_where_array(
					$str_where[2],$str_open_level_code,$str_where_create['not_id']);
				foreach ($diary_row_array_not as $index => $diary_row) {
					array_push($diary_row_array_result, $diary_row);
				}
			}
			if (!$diary_row_array_result) {
				//$err_str = "����������󤬤���ޤ���";
				$err_str = ACSMsg::get_msg('User', 'SearchResultDiaryAction.class.php' ,'M001');
			} else {
				// ����Ѥߥ��ߥ�˥ƥ�����
				foreach ($diary_row_array_result as $index => $diary_row) {
					if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
						$diary_row_array_result[$index]['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
					}
				}
			}
		}

		$request->setAttribute('diary_row_array_result',$diary_row_array_result);
		$request->setAttribute('err_str',$err_str);
		$request->setAttribute('form_pre',$form);
	}

	// �����ϰ�
	$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(
			ACSMsg::get_mst('community_type_master','D10'), 
			ACSMsg::get_mst('contents_type_master','D21'));

		// �ޥ��ե�󥺥��롼��
		$friends_group_row_array = ACSUser::get_friends_group_row_array($user_community_id);

		// set
		$user->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('diary_row_array', $diary_row_array);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$request->setAttribute('friends_group_row_array', $friends_group_row_array);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
	
}
?>
