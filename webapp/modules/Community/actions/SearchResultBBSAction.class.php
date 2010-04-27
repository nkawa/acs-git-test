<?php
/**
 * �Ǽ��ġ�������̡�Action���饹
 * 
 * SearchResultBBSAction.class.php
 * @package  acs/webapp/module/Community/Action
 * @author   akitsu
 * @since    PHP 4.0
 */
// $Id: SearchResultBBSAction.class.php,v 1.4 2006/11/20 08:44:12 w-ota Exp $

class SearchResultBBSAction extends BaseAction
{
	// POST 	�����ܥ���ν���
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ɽ���оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->ACSgetParameter('community_id');
		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_profile_row($community_id);

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
			    [q_text] => �������					//���ʢ�ɬ�ܡ�
			    [search_title] => title_in_serch		//��̾������
			    [search_all] => subject_in_serch		//��ʸ������
			    [open_level_code] => 00					//�����ϰϡ�00������ʤ��ˡʢ�ɬ�ܡ�
			    [search_all_about] => all_in_serch		//���٤Ƥ�BBS������
			*/

			// ------------ ��������μ�������ա��Х���ñ�̤ǽ�����
			for ($i = 1; $i < 3; $i++) {
				$str_where_create[$i] = ACSBBS::set_bbs_where_list($form,$i);
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
			if ($form['open_level_code'] != '00') {	//�����ϰϤ����򤷤Ƥ�����
					$str_open_level_code =  $form['open_level_code'] ;
			}

			// ------------ 
			//db�������� and or ����Ԥ�
			if (!$err_str) {
				$bbs_row_array_result = ACSBBS::get_bbs_where_array($str_where[1],$str_open_level_code,array());
				//ʣ����Υ��ޥ�
				//and�������פ�ʤ�����������or��ʬ���פ򸡺�����
				if ($str_where_create[1]['str_count'] == 2 || $str_where_create[2]['str_count'] == 2) {
					$str_where_create['not_id'] = array();
					foreach ($bbs_row_array_result as $index => $bbs_row) {
						array_push($str_where_create['not_id'], $bbs_row['bbs_id']);
					}
					$bbs_row_array_not = ACSBBS::get_bbs_where_array($str_where[2],$str_open_level_code,$str_where_create['not_id']);
					foreach ($bbs_row_array_not as $index => $bbs_row) {
						array_push($bbs_row_array_result, $bbs_row);
					}
				}
				if (!$bbs_row_array_result) {
					$err_str = ACSMsg::get_msg('Community', 'SearchResultBBSAction.class.php', 'M001');
				} else {
					// ����Ѥߥ��ߥ�˥ƥ�����
					foreach ($bbs_row_array_result as $index => $bbs_row) {
						// ����Ѥߥ��ߥ�˥ƥ�����
						$bbs_row_array[$index]['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);
					}
				}
			}
		}

		$request->setAttribute('bbs_row_array_result',$bbs_row_array_result);
		$request->setAttribute('err_str',$err_str);
		$request->setAttribute('form_pre',$form);

		// �����ϰ�
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(
				ACSMsg::get_mst('community_type_master','D40'), 
				ACSMsg::get_mst('contents_type_master','D42'));

		// set
		$user->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('bbs_row_array', $bbs_row_array);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
//		$request->setAttribute('friends_group_row_array', $friends_group_row_array);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
	
	// ���������������
	function get_access_control_info(&$controller, &$request, &$user) {
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->getParameter('community_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// ��������������� //
		$bbs_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D41'));
		$bbs_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $bbs_contents_row['contents_type_code'], $bbs_contents_row['open_level_code']);
		$access_control_info = array(
				 'role_array' => ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row),
				 'contents_row_array' => array($bbs_contents_row)
		);

		return $access_control_info;
	}
}
?>
