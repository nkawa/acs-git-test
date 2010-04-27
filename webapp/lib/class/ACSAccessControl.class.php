<?php
// $Id: ACSAccessControl.class.php,v 1.17 2006/11/20 08:44:02 w-ota Exp $


/*
 * �����������楯�饹
 */
class ACSAccessControl {

	/**
	 * �����ϰϤ�������������
	 *
	 * @param ���ߥ�˥ƥ�����̾
	 * @param ����ƥ�ļ���̾
	 * @return �����ǽ�ʸ����ϰϤ����� (Ϣ�����������)
	 */
	static function get_open_level_master_row_array($community_type_name, $contents_type_name) {
		$community_type_name = pg_escape_string($community_type_name);
		$contents_type_name = pg_escape_string($contents_type_name);

		$sql  = "SELECT open_level_list.open_level_code, open_level_master.open_level_name, open_level_list.is_default";
		$sql .= " FROM open_level_list, open_level_master, community_type_master, contents_type_master";
		$sql .= " WHERE open_level_list.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND open_level_list.contents_type_code = contents_type_master.contents_type_code";
		$sql .= "  AND community_type_master.community_type_name = '$community_type_name'";
		$sql .= "  AND contents_type_master.contents_type_name = '$contents_type_name'";
		$sql .= "  AND open_level_list.open_level_code = open_level_master.open_level_code";
		$sql .= " ORDER BY open_level_list.display_order ASC";

		$row_array = ACSDB::_get_row_array($sql);

		// set true or false
		foreach ($row_array as $index => $row) {
			if ($row['is_default'] == 't') {
				$row_array[$index]['is_default'] = true;
			} else {
				$row_array[$index]['is_default'] = false;
			}
		}

		return $row_array;
	}

	/**
	 * ������٥�ޥ�����������������
	 *
	 * @param $open_level_code ������٥륳����
	 * @return ������٥�ޥ���������
	 */
	static function get_open_level_master_row($open_level_code) {
		$open_level_code = pg_escape_string($open_level_code);

		$sql  = "SELECT *";
		$sql .= " FROM open_level_master";
		$sql .= " WHERE open_level_master.open_level_code = '$open_level_code'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * �ǥե���Ȥ�open_level_code���������
	 *
	 * @param $community_type_code ���ߥ�˥ƥ����̥�����
	 * @param $contents_type_code ����ƥ�ļ��̥�����
	 * @return $open_level_code ������٥륳����
	 */
	static function get_default_open_level_code($community_type_name, $contents_type_name) {
		$community_type_name = pg_escape_string($community_type_name);
		$contents_type_name = pg_escape_string($contents_type_name);

		$sql  = "SELECT open_level_list.open_level_code";
		$sql .= " FROM open_level_list, community_type_master, contents_type_master";
		$sql .= " WHERE open_level_list.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '$community_type_name'";
		$sql .= "  AND open_level_list.contents_type_code = contents_type_master.contents_type_code";
		$sql .= "  AND contents_type_master.contents_type_name = '$contents_type_name'";
		$sql .= "  AND open_level_list.is_default = 't'";

		$value = ACSDB::_get_value($sql);
		return $value;
	}


	/**
	 * �ޥ��ڡ����⥳��ƥ�ĤΥ����������ˤ�����role_array���������
	 *
	 * @param $acs_user_info_row ���������ԤΥ桼������
	 * @param $target_user_info_row ���������оݤΥ桼������
	 * @return role_array (Ϣ������)
	 */
	static function get_user_community_role_array($acs_user_info_row, $target_user_info_row) {
		$role_array = array('public' => false, 'user' => false, 'member' => false, 'administrator' => false, 'system_administrator' => false);

		// (1) ���̥桼��(�����桼��)���ɤ���
		if (!$acs_user_info_row['is_acs_user']) {
			$role_array['public'] = true;

		} else {
			// (2) ������桼�����ɤ���
			$role_array['user'] = true;

			// (3) ͧ�ͤ��ɤ���
			if (ACSUser::is_in_friends_id_array($acs_user_info_row, $target_user_info_row['user_community_id'])) {
				$role_array['member'] = true;
			}

			// (4) �ܿͤ��ɤ���
			if ($acs_user_info_row['user_id'] == $target_user_info_row['user_id']) {
				$role_array['administrator'] = true;
			}

			// (5) �����ƥ�����Ԥ��ɤ���
			if (ACSAccessControl::is_system_administrator($acs_user_info_row)) {
				$role_array['system_administrator'] = true;
			}
		}

		return $role_array;
	}

	/**
	 * �ޥ��ڡ���(�桼�����ߥ�˥ƥ�)�Υ���ƥ�Ĥ˥���������ǽ���ɤ���
	 *
	 * @param $role_array ���������Ԥ�role_array
	 * @param $row ���������оݤȤʤ�ǡ���
	 * @return ����������(true)/���������Բ�(false)
	 */
	static function is_valid_user_for_user_community($acs_user_info_row, $role_array, $row) {
		$ret = false;

		foreach ($role_array as $role_key => $role_value) {
			if (ACSLib::get_boolean($row["open_for_{$role_key}"]) && $role_value) {
				if ($role_key == 'member') {
					// �ޥ��ե�� or �ޥ��ե�󥺥��롼�׸���
					$trusted_community_id_array = array();
					foreach ($row['trusted_community_row_array'] as $trusted_community_row) {
						if (ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $trusted_community_row['community_id'])) {
							$ret = true;
							break;
						}
					}
				} else {
					$ret = true;
					break;
				}
			}
		}

		return $ret;
	}

	/**
	 * role_array�˱�����row_array��������� (�桼�����ߥ�˥ƥ�)
	 *
	 * @param $acs_user_info_row ���������ԤΥ桼������
	 * @param $role_array ���������Ԥ�role_array
	 * @param $row_array ���������оݤȤʤ�ǡ��� (Ϣ�����������)
	 * @return row_array
	 */
	static function get_valid_row_array_for_user_community($acs_user_info_row, $role_array, $row_array) {
		$new_row_array = array();
		foreach ($row_array as $row) {
			if (ACSAccessControl::is_valid_user_for_user_community($acs_user_info_row, $role_array, $row)) {
				array_push($new_row_array, $row);
			}
		}
		return $new_row_array;
	}

	/**
	 * role_array�˱�����obj_row_array��������� (�桼�����ߥ�˥ƥ�)
	 *
	 * @param  $acs_user_info_row ���������ԤΥ桼������
	 * @param  $role_array        ���������Ԥ�role_array
	 * @param  $obj_row_array     ���������оݤȤʤ�ǡ��� (���֥������Ȥ�����)
	 * @return ����������ǽ�ʥǡ���(���֥������Ȥ�����)
	 */
	static function get_valid_obj_row_array_for_user_community($acs_user_info_row, $role_array, $obj_array) {
		$new_obj_array = array();

		/* �����ϰϥޥ������� */
		$open_level_master_row_array = ACSAccessControl::get_all_open_level_master_row_array();

		foreach ($obj_array as $obj) {
			$open_level_code = $obj->get_open_level_code();

			// obj -> row ���Ѵ�
			$row['open_level_code'] = $open_level_code;
			$row['open_for_public'] = $open_level_master_row_array[$open_level_code]['open_for_public'];
			$row['open_for_user'] = $open_level_master_row_array[$open_level_code]['open_for_user'];
			$row['open_for_member'] = $open_level_master_row_array[$open_level_code]['open_for_member'];
			$row['open_for_administrator'] = $open_level_master_row_array[$open_level_code]['open_for_administrator'];
			$row['open_for_system_administrator'] = $open_level_master_row_array[$open_level_code]['open_for_system_administrator'];
			$row['trusted_community_row_array'] = $obj->get_trusted_community_row_array();

			if (ACSAccessControl::is_valid_user_for_user_community($acs_user_info_row, $role_array, $row)) {
				array_push($new_obj_array, $obj);
			}
		}
		return $new_obj_array;
	}

	/**
	 * �����ϰϥޥ�������
	 *
	 * @return open_level_code �򥭡��ˤ�������
	 */
	static function get_all_open_level_master_row_array () {
		$sql  = "SELECT *";
		$sql .= " FROM open_level_master";

		$row_array = ACSDB::_get_row_array($sql);

		// set true or false
		$role_array = array('public', 'user', 'member', 'administrator');
		foreach ($row_array as $index => $row) {
			$open_level_code = $row['open_level_code'];
			$new_row_array[$open_level_code]['open_level_name'] = $row['open_level_name'];
			foreach ($role_array as $role_key) {
				$new_row_array[$open_level_code]["open_for_{$role_key}"] = $row["open_for_{$role_key}"];
			}
		}
		return $new_row_array;
	}

	/**
	 * role_array�˱�����row��������� (�桼�����ߥ�˥ƥ�)
	 *
	 * @param $acs_user_info_row ���������ԤΥ桼������
	 * @param $role_array ���������Ԥ�role_array
	 * @param $row ���������оݤȤʤ�ǡ��� (Ϣ������)
	 * @return row
	 */
	static function get_valid_row_for_user_community($acs_user_info_row, $role_array, $row) {
		$new_row = array();
		if (count($row)) {
			$new_row = null;
			if (ACSAccessControl::is_valid_user_for_user_community($acs_user_info_row, $role_array, $row)) {
				$new_row = $row;
			}
		}
		return $new_row;
	}


	/**
	 * ���ߥ�˥ƥ��⥳��ƥ�ĤΥ����������ˤ�����role_array���������
	 *
	 * @param $acs_user_info_row ���������ԤΥ桼������
	 * @param $target_community_row ���������оݤΥ��ߥ�˥ƥ�����
	 * @return role_array (Ϣ������)
	 */
	static function get_community_role_array($acs_user_info_row, $target_community_row) {
		$role_array = array('public' => false, 'user' => false, 'member' => false, 'administrator' => false, 'system_administrator' => false);

		// (1) ���̥桼��(�����桼��)���ɤ���
		if (!$acs_user_info_row['is_acs_user']) {
			$role_array['public'] = true;

		} else {
			// (2) ������桼�����ɤ���
			$role_array['user'] = true;

			// (3) ���ߥ�˥ƥ����Ф��ɤ���
			if (ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $target_community_row['community_id'])) {
				$role_array['member'] = true;
			}

			// (4) ���ߥ�˥ƥ������Ԥ��ɤ���
			if (ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $target_community_row['community_id'])) {
				$role_array['administrator'] = true;
			}

			// (5) �����ƥ�����Ԥ��ɤ���
			if (ACSAccessControl::is_system_administrator($acs_user_info_row)) {
				$role_array['administrator'] = true;
			}
		}

		return $role_array;
	}

	/**
	 * ���ߥ�˥ƥ��Υ���ƥ�Ĥ˥���������ǽ���ɤ���
	 *
	 * @param $acs_user_info_row ���������ԤΥ桼������
	 * @param $role_array ���������Ԥ�role_array
	 * @param $row ���������оݤȤʤ�ǡ��� (Ϣ������)
	 * @return ����������(true)/���������Բ�(false)
	 */
	static function is_valid_user_for_community($acs_user_info_row, $role_array, $row) {
		$ret = false;

		// ���ߥ�˥ƥ����С������ƥ�����԰ʳ��ξ��
		// ���ߥ�˥ƥ����Τθ����ϰϤ�����å�
		if (!ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $row['community_id']) && !ACSAccessControl::is_system_administrator($acs_user_info_row)) {
			$community_self_info_row = ACSCommunity::get_contents_row($row['community_id'], ACSMsg::get_mst('contents_type_master','D00'));
			if ($community_self_info_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
				return false;
			}
		}

		foreach ($role_array as $role_key => $role_value) {
			if (ACSLib::get_boolean($row["open_for_{$role_key}"]) && $role_value) {
				$ret = true;
				break;
			} elseif ($role_key == 'member') {
				// �������Ĥ�Ϳ���륳�ߥ�˥ƥ������ꤵ��Ƥ�����
				if(count($row['trusted_community_row_array']) > 0){
					foreach ($row['trusted_community_row_array'] as $trusted_community_row) {
						if (ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $trusted_community_row['community_id'])) {
							$ret = true;
							break;
						}
					}
				}
				if ($ret) {
					break;
				}
			}
		}

		return $ret;
	}

	/**
	 * role_array�˱�����row_array��������� (���ߥ�˥ƥ�)
	 *
	 * @param $acs_user_info_row ���������ԤΥ桼������
	 * @param $role_array ���������Ԥ�role_array
	 * @param $row ���������оݤȤʤ�ǡ��� (Ϣ�����������)
	 * @return row_array
	 */
	static function get_valid_row_array_for_community($acs_user_info_row, $role_array, $row_array) {
		$new_row_array = array();
		foreach ($row_array as $row) {
			if (ACSAccessControl::is_valid_user_for_community($acs_user_info_row, $role_array, $row)) {
				array_push($new_row_array, $row);
			}
		}
		return $new_row_array;
	}

	/**
	 * role_array�˱�����obj_row_array��������� (���ߥ�˥ƥ�)
	 *
	 * @param  $acs_user_info_row ���������ԤΥ桼������
	 * @param  $role_array        ���������Ԥ�role_array
	 * @param  $obj_row_array     ���������оݤȤʤ�ǡ��� (���֥������Ȥ�����)
	 * @return ����������ǽ�ʥǡ���(���֥������Ȥ�����)
	 */
	static function get_valid_obj_row_array_for_community($acs_user_info_row, $role_array, $obj_array) {
		$new_obj_array = array();

		/* �����ϰϥޥ������� */
		$open_level_master_row_array = ACSAccessControl::get_all_open_level_master_row_array();

		foreach ($obj_array as $obj) {
			$open_level_code = $obj->get_open_level_code();

			// obj -> row ���Ѵ�
			$row['community_id'] = $obj->get_community_id();
			$row['open_level_code'] = $open_level_code;
			$row['open_for_public'] = $open_level_master_row_array[$open_level_code]['open_for_public'];
			$row['open_for_user'] = $open_level_master_row_array[$open_level_code]['open_for_user'];
			$row['open_for_member'] = $open_level_master_row_array[$open_level_code]['open_for_member'];
			$row['open_for_administrator'] = $open_level_master_row_array[$open_level_code]['open_for_administrator'];
			$row['open_for_system_administrator'] = $open_level_master_row_array[$open_level_code]['open_for_system_administrator'];
			$row['trusted_community_row_array'] = $obj->get_trusted_community_row_array();

			if (ACSAccessControl::is_valid_user_for_community($acs_user_info_row, $role_array, $row)) {
				array_push($new_obj_array, $obj);
			}
		}
		return $new_obj_array;
	}


	/**
	 * �����ƥ�����Ԥ��ɤ���
	 *
	 * @param $acs_user_info_row �桼�����������
	 * @return true / false
	 */
	static function is_system_administrator($acs_user_info_row) {
		if (ACSLib::get_boolean($acs_user_info_row['administrator_flag']) || $acs_user_info_row['user_id'] == ACS_ADMINISTRATOR_USER_ID) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * �����������˱������Ѳ�����ɽ����������ꤹ��
	 *
	 * @param $profile_row �ץ�ե��������Σ���
	 * @param $view_at     ɽ���Υ���������
	 *
	 * @return $profile_row
	 */
	static function set_not_open($profile_row,$view_at){
		$profile_row['not_open'] = false;
		for($i = 0; $i < count($view_at); $i++){
			if($profile_row['open_level_code'] == $view_at[$i]){
				$profile_row['not_open'] = true;
				break;
			}
		}	
		return $profile_row;
	}
}

?>
