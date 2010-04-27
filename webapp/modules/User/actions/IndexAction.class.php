<?php
// $Id: IndexAction.class.php,v 1.21 2008/03/24 07:00:36 y-yuki Exp $

class IndexAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ɽ���оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		if (empty($user_community_id)) {
			$user_community_id = $acs_user_info_row['user_community_id'];
		}

		// ɽ���оݤΥ桼����¸�ߤ��ʤ����ϡ����顼��ɽ��
		if (!ACSUser::get_user_info_row_by_user_community_id($user_community_id)) {
			// ̤������λ���Public/Index������
			header("Location: ./index.php?module=Public&action=Index");
//			return View::ERROR;
		}

		// ���ȤΥڡ�����
		if ($acs_user_info_row['user_community_id'] == $user_community_id) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}

		// �ץ�ե�����
		$target_user_info_row = ACSUser::get_user_profile_row($user_community_id);

		// �ޥ��ե��
		$friends_row_array = ACSUser::get_simple_friends_row_array($user_community_id);

		// �ޥ����ߥ�˥ƥ�
		$community_row_array = ACSUser::get_community_row_array($user_community_id);

		// �ǽ�������
		// ������Ѥߤξ��
		if($acs_user_info_row['is_login_user']){
			$last_login_row = ACSCommunity::get_contents_row($user_community_id, 
										ACSMsg::get_mst('contents_type_master','D52'));
		}

		// �ޥ��ڡ����ǥ�������������
		$selection_css_row = ACSCommunity::get_contents_row($user_community_id, 
										ACSMsg::get_mst('contents_type_master','D53'));
		$selection_css = $selection_css_row['contents_value'] == '' ? 
				ACS_DEFAULT_SELECTION_CSS_FILE : $selection_css_row['contents_value'];

		$waiting_for_join_community_row_array_array = array();
		$waiting_for_parent_community_link_row_array_array = array();
		$waiting_for_sub_community_link_row_array_array = array();

		foreach ($community_row_array as $index => $community_row) {
			$community_row_array[$index]['contents_row_array']['self'] = ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D00'));
			$community_row_array[$index]['is_community_admin'] = ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $community_row['community_id']);
			$community_row_array[$index]['is_community_member'] = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_row['community_id']);
			if ($is_self_page) {

				// �Ե�: ���ߥ�˥ƥ����� ��ǧ�Ԥ� (��ʬ�Υޥ����ߥ�˥ƥ�)
				if ($community_row['is_community_admin'] && $waiting_row_array = ACSWaiting::get_waiting_row_array($community_row['community_id'], ACSMsg::get_mst('waiting_type_master','D20'), ACSMsg::get_mst('waiting_status_master','D10'))) {
					$waiting_for_join_community_row_array['waiting_row_array'] = $waiting_row_array;
					$waiting_for_join_community_row_array['community_row'] = $community_row;
					array_push($waiting_for_join_community_row_array_array, $waiting_for_join_community_row_array);
				}

				// �Ե�: �ƥ��ߥ�˥ƥ��ɲ� ��ǧ�Ԥ�
				if ($community_row['is_community_admin'] && $waiting_row_array = ACSWaiting::get_waiting_row_array($community_row['community_id'], ACSMsg::get_mst('waiting_type_master','D40'), ACSMsg::get_mst('waiting_status_master','D10'))) {
					$waiting_for_parent_community_link_row_array['waiting_row_array'] = $waiting_row_array;
					$waiting_for_parent_community_link_row_array['community_row'] = $community_row;
					array_push($waiting_for_parent_community_link_row_array_array, $waiting_for_parent_community_link_row_array);
				}

				// �Ե�: ���֥��ߥ�˥ƥ��ɲ� ��ǧ�Ԥ�
				if ($community_row['is_community_admin'] && $waiting_row_array = ACSWaiting::get_waiting_row_array($community_row['community_id'], ACSMsg::get_mst('waiting_type_master','D50'), ACSMsg::get_mst('waiting_status_master','D10'))) {
					$waiting_for_sub_community_link_row_array['waiting_row_array'] = $waiting_row_array;
					$waiting_for_sub_community_link_row_array['community_row'] = $community_row;
					array_push($waiting_for_sub_community_link_row_array_array, $waiting_for_sub_community_link_row_array);
				}
			}
		}


		if ($is_self_page) {
			// �Ե�: �ޥ��ե���ɲ� ��ǧ�Ԥ�
			$waiting_for_add_friends_row_array = ACSWaiting::get_waiting_row_array($user_community_id, ACSMsg::get_mst('waiting_type_master','D10'), ACSMsg::get_mst('waiting_status_master','D10'));

			// �Ե�: ���ߥ�˥ƥ����� ��ǧ�Ԥ�
			$waiting_for_invite_to_community_row_array = ACSWaiting::get_waiting_row_array($user_community_id, ACSMsg::get_mst('waiting_type_master','D30'), ACSMsg::get_mst('waiting_status_master','D10'));

			// �ޥ��������꡼�ο��女����
			$new_comment_diary_row_array = ACSDiary::get_new_comment_diary_row_array($user_community_id);
			
			// �����å�����
			$new_message_row_array = ACSMessage::get_new_message_row_array($user_community_id);

			// �����ƥफ��Τ��Τ餻
			$system_announce_row_array = ACSSystemAnnounce::get_valid_system_announce_row_array();

		}

		// set
		$request->setAttribute('user_community_id', $user_community_id);
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('is_self_page', $is_self_page);
		$request->setAttribute('friends_row_array', $friends_row_array);
		$request->setAttribute('community_row_array', $community_row_array);
		$request->setAttribute('waiting_for_add_friends_row_array', $waiting_for_add_friends_row_array);
		$request->setAttribute('waiting_for_join_community_row_array_array', $waiting_for_join_community_row_array_array);
		$request->setAttribute('waiting_for_parent_community_link_row_array_array', $waiting_for_parent_community_link_row_array_array);
		$request->setAttribute('waiting_for_sub_community_link_row_array_array', $waiting_for_sub_community_link_row_array_array);
		$request->setAttribute('waiting_for_invite_to_community_row_array', $waiting_for_invite_to_community_row_array);
		$request->setAttribute('new_comment_diary_row_array', $new_comment_diary_row_array);
		$request->setAttribute('system_announce_row_array', $system_announce_row_array);
		$request->setAttribute('last_login', $last_login_row['contents_value']);
		$request->setAttribute('selection_css', $selection_css);
		$request->setAttribute('new_message_row_array', $new_message_row_array);

		return View::SUCCESS;
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
