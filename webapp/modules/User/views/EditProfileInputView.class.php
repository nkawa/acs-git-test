<?php
// $Id: EditProfileView::INPUT.class.php,v 1.10 2006/11/20 08:44:28 w-ota Exp $

class EditProfileInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$is_new_ldap_user = $request->getAttribute('is_new_ldap_user');
		$form = $request->getAttribute('form');
		
		// �ù�

		// ����ƥ��̾������
		$contents_name_array = array(
									 'user_name' => ACSMsg::get_mst('contents_type_master','D01'),
									 'mail_addr' => ACSMsg::get_mst('contents_type_master','D02'),
									 'belonging' => ACSMsg::get_mst('contents_type_master','D03'),
									 'speciality' => ACSMsg::get_mst('contents_type_master','D04'),
									 'birthplace' => ACSMsg::get_mst('contents_type_master','D05'),
									 'birthday' => ACSMsg::get_mst('contents_type_master','D06'),
									 'community_profile' => ACSMsg::get_mst('contents_type_master','D07'),
									 'community_profile_login' => ACSMsg::get_mst('contents_type_master','D08'),
									 'community_profile_friend' => ACSMsg::get_mst('contents_type_master','D09'),
									 'friends_list' => ACSMsg::get_mst('contents_type_master','D11'),
									 'mail_lang' => ACSMsg::get_mst('contents_type_master','D51'),
									 );

		// ���ϥ��顼������������
		if (is_array($form)) {
			// ����ƥ�ļ��̥ޥ���
			$contents_type_master_array = ACSDB::get_master_array('contents_type');
			// �����ϰϥޥ���
			$open_level_master_array = ACSDB::get_master_array('open_level');

			// $target_user_info_row[contents_row_array]������
			// �˥å��͡���
			$target_user_info_row['community_name'] = $form['community_name'];
			// ¾
			foreach ($contents_name_array as $contents_key => $contents_name) {
				$target_user_info_row['contents_row_array'][$contents_key]['contents_type_code'] = array_search($contents_name, $contents_type_master_array);
				$target_user_info_row['contents_row_array'][$contents_key]['contents_type_name'] = $contents_name;
				if ($contents_key != 'user_name') {
					$target_user_info_row['contents_row_array'][$contents_key]['contents_value'] = $form[$contents_key];
				}
				$target_user_info_row['contents_row_array'][$contents_key]['open_level_code'] = $form['open_level_code_array'][$contents_key];
				$target_user_info_row['contents_row_array'][$contents_key]['open_level_name'] = $open_level_master_array[$form['open_level_code_array'][$contents_key]];

				// ����Ѥߥޥ��ե�󥺥��롼�ץ��ߥ�˥ƥ�
				if ($form['trusted_community_id_csv_array'][$contents_key] != '') {
					$target_user_info_row['contents_row_array'][$contents_key]['trusted_community_row_array'] = array();
					$trusted_community_id_array = explode(',', $form['trusted_community_id_csv_array'][$contents_key]);
					foreach ($trusted_community_id_array as $trusted_community_id) {
						// �ޥ��ե�󥺥��롼�ץ��ߥ�˥ƥ���������
						$friends_group_community_row = ACSCommunity::get_community_row($trusted_community_id);
						array_push($target_user_info_row['contents_row_array'][$contents_key]['trusted_community_row_array'],
								   $friends_group_community_row);
					}
					$target_user_info_row['contents_row_array'][$contents_key]['trusted_community_flag'] = 1;
					$target_user_info_row['contents_row_array'][$contents_key]['trusted_community_id_csv'] = $form['trusted_community_id_csv_array'][$contents_key];
				} else {
					unset($target_user_info_row['contents_row_array'][$contents_key]['trusted_community_row_array']);
					unset($target_user_info_row['contents_row_array'][$contents_key]['trusted_community_flag']);
					unset($target_user_info_row['contents_row_array'][$contents_key]['trusted_community_id_csv']);
				}
			}

		} else {
			// �ǡ�����¸�ߤ��ʤ��Ȥ��ϥǥե���Ȥ��ͤ��������
			foreach ($contents_name_array as $contents_key => $contents_name) {
				if (!$target_user_info_row['contents_row_array'][$contents_key]) {
					$target_user_info_row['contents_row_array'][$contents_key] = ACSCommunity::get_empty_contents_row(ACSMsg::get_mst('community_type_master','D10'), $contents_name);
				}
			}
		}

		// URL
		$action_url = $this->getControllerPath('User', 'EditProfile') . '&id=' . $target_user_info_row['user_community_id'];
		$back_url = $this->getControllerPath('User', 'Index') . '&id=' . $target_user_info_row['user_community_id'];
		// ����Ѥߥ��ߥ�˥ƥ�����URL
		$set_open_level_for_profile_url = $this->getControllerPath('User', 'SetOpenLevelForProfile') . '&id=' . $target_user_info_row['user_community_id'];


		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('EditProfile.tpl.php');
		
		// set
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('is_new_ldap_user', $is_new_ldap_user);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('set_open_level_for_profile_url', $set_open_level_for_profile_url);

		return parent::execute();
	}
}

?>
