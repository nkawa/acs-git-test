<?php
// $Id: ACSLDAP.class.php,v 1.0 2009/06/24 10:30:00 y-yuki Exp $
// CAUTION::LDAP�N���X�́A�e���ɍ��킹�ăR�[�f�B���O�̕K�v������


/*
 * LDAP�N���X
 */
class ACSLDAP {

	/**
	 * LDAP�̃p�X���[�h�F�؏��������{����
	 *
	 * @param $input_user_id ���[�UID
	 * @param $input_passwd �p�X���[�h
	 * @return �A�z�z��1�G���g��
	 */
	function check_passwd_by_ldap($input_user_id, $input_passwd) {

		$ldap_user_info_row = ACSLDAP::get_ldap_user_info_row($input_user_id);

		/* LDAP�Ƀf�[�^���L��ꍇ�̓p�X���[�h�F�� */
		if (count($ldap_user_info_row) > 1) {

			/* LDAP�p�X���[�h�̔F�� */
			$passwd = str_replace("{crypt}", "", $ldap_user_info_row['userpassword']);

			if(crypt($input_passwd, $passwd) == $passwd 
					|| ACSSystem::verify_passwd_by_hash($input_passwd, $passwd) == 0) {
				return $input_user_id;
			}
		}
		return null;
	}

	/**
	 * LDAP���A���[�U������������
	 *
	 * @param $input_user_id ���[�UID
	 * @return �A�z�z��1�G���g��
	 */
	function ldap_search_user_info_ipdb($input_user_id) {

		// �V�X�e���ݒ���擾
		$system_conf_row = ACSLDAP::set_system_conf();

		// �t�B���^
		$filter = '(cn=' . $input_user_id . ')';

		// LDAP�ڑ�
		$conn = ACSLDAP::connect_ldap();
		if (!$conn) {
			return -1;
		}

		// search
		$res = @ldap_search($conn, $system_conf_row['ldap_base_dn'], $filter);

		// �G���g���擾
		$row_arr = @ldap_get_entries($conn, $res);
		return $row_arr;
	}

	/**
	 * ���[�U�����擾����
	 *
	 * @param $input_user_id ���̓��[�UID
	 * @return ���[�U���
	 */
	function get_ldap_user_info_row($input_user_id) {

		// �܂��擾���Ă݂�
		$ldap_user_info_row_array = ACSLDAP::ldap_search_user_info_ipdb($input_user_id);

		// 1���̃��[�U���
		$ldap_user_info_row = array();
		// ���[�UID
		$ldap_user_info_row['user_id'] = $input_user_id;

		// ����
		$ldap_user_info_row['user_name'] = mb_convert_encoding(
				$ldap_user_info_row_array[0]['name'][0], mb_internal_encoding(), 'UTF-8');

		// ���[���A�h���X
		$ldap_user_info_row['mail_addr'] = $ldap_user_info_row_array[0]['mail'][0];

		// ����
		$ldap_user_info_row['belonging'] = '';

		// �p�X���[�h
		$ldap_user_info_row['userpassword'] = $ldap_user_info_row_array[0]['userpassword'][0];

		return $ldap_user_info_row;

	}

	/**
	 * LDAP�ڑ��`�F�b�N
	 *
	 * @return �ڑ�����(true) / �ڑ����s(false)
	 */
	function check_connect_ldap_ipdb() {

		if (!ACSLDAP::connect_ldap()) {
			return -1;
		}
		return 0;

	}

	/**
	 * LDAP�ɐڑ�����
	 *
	 * @param
	 * @return ���\�[�X
	 */
	function connect_ldap() {

		// �V�X�e���ݒ���擾
		$system_conf_row = ACSLDAP::set_system_conf();

		// LDAP�ڑ�
		$conn = @ldap_connect($system_conf_row['ldap_server'], $system_conf_row['ldap_port']);
		if (!$conn) {
			// ���s����NULL
			return null;
		}

		// LDAP�v���g�R���o�[�W�����Z�b�g (LDAPv3)
		@ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

		// �o�C���h����
		$bind = @ldap_bind($conn, $system_conf_row['ldap_bind_dn'], $system_conf_row['ldap_bind_passwd']);
		if (!$bind) {
			// ���s����NULL
			return null;
		}
		return $conn;
	}


	/**
	 * LDAP�ݒ�����擾����
	 *
	 * @return �ݒ���(�z��)
	 */
	function set_system_conf() {

		$system_conf_row = array();

		// �z�X�g
		$system_conf_row['ldap_server'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_SERVER');

		// �|�[�g
		$system_conf_row['ldap_port'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_PORT');

		// BASE
		$system_conf_row['ldap_base_dn'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_BASE_DN');

		// BIND
		$system_conf_row['ldap_bind_dn'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_BIND_DN');

		// BIND�p�X���[�h
		$system_conf_row['ldap_bind_passwd'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_BIND_PASSWD');

		return $system_conf_row;
	}


}
?>