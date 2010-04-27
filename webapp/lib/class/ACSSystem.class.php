<?php
// $Id: ACSSystem.class.php,v 2.0 2009/06/24 16:30:00 acs Exp $


/*
 * System���饹
 */
class ACSSystem {

	/**
	 * �ѥ���ɥե�����򹹿�����
	 *
	 * @param $new_user_id �����桼��ID
	 * @param $new_passwd �����ѥ����
	 * @return ����(true) / ����(false)
	 */
	function update_passwd($new_user_id, $new_passwd) {
		$ret = 1;
		$entry_array = array(); // �ѥ���ɥե����륨��ȥ��ݻ�����

		if (!is_writable(ACS_PASSWD_FILE)) {
			return 0;
		}

		// �ե������ɤ߹���
		$data_arr = file(ACS_PASSWD_FILE);
		foreach ($data_arr as $line) {
			list($user_id, $passwd) = explode(':', trim($line));
			// �桼������(user_info)��¸�ߤ���桼��ID�Τ���¸�оݤȤ���
			if (ACSUser::get_user_info_row_by_user_id($user_id)) {
				$entry_array[$user_id] = $passwd;
			}
		}

		// �������ѥ����
		if ($new_passwd != '') {
			$new_passwd = crypt($new_passwd);
		}
		$entry_array[$new_user_id] = $new_passwd;

		// �ե����륪���ץ�
		if (($fp = fopen(ACS_PASSWD_FILE, 'w')) === false) {
			return 0;
		}
		flock($fp, LOCK_EX);

		foreach ($entry_array as $user_id => $passwd) {
			// ���Υѥ���ɤϽ���
			if ($passwd != '') {
				fwrite($fp, "$user_id:$passwd\n");
			}
		}

		// �ե����륯����
		fclose($fp);
		return $ret;
	}

	/**
	 * �ѥ���ɥե����뤫�����Υ桼��ID�Υ���ȥ��������
	 *
	 * @param $delete_user_id �������桼��ID
	 * @return ����(true) / ����(false)
	 */
	function delete_passwd($delete_user_id) {
		$ret = 1;
		$entry_array = array(); // �ѥ���ɥե����륨��ȥ��ݻ�����

		if (!is_writable(ACS_PASSWD_FILE)) {
			return 0;
		}

		// �ե������ɤ߹���
		$data_arr = file(ACS_PASSWD_FILE);
		foreach ($data_arr as $line) {
			list($user_id, $passwd) = explode(':', trim($line));
			// �������桼��ID�ϥ����å�
			if ($user_id == $delete_user_id) {
				continue;
			}
			// �桼������(user_info)��¸�ߤ���桼��ID�Τ���¸�оݤȤ���
			if (ACSUser::get_user_info_row_by_user_id($user_id)) {
				$entry_array[$user_id] = $passwd;
			}
		}

		// �ե����륪���ץ�
		if (($fp = fopen(ACS_PASSWD_FILE, 'w')) === false) {
			return 0;
		}
		flock($fp, LOCK_EX);

		foreach ($entry_array as $user_id => $passwd) {
			// ���Υѥ���ɤϽ���
			if ($passwd != '') {
				fwrite($fp, "$user_id:$passwd\n");
			}
		}

		// �ե����륯����
		fclose($fp);
		return $ret;
	}

	/**
	 * �ѥ���ɥե�����(.htpasswd)��¸�ߤ���桼��ID���ɤ���
	 *
	 * @param $target_user_id �оݤΥ桼��ID
	 * @return ¸�ߤ���(true) / ¸�ߤ��ʤ�(false)
	 */
	function is_htpasswd_user($target_user_id) {

		$ret = false;

		// �ե������ɤ߹���
		$data_arr = file(ACS_PASSWD_FILE);
		foreach ($data_arr as $line) {
			list($user_id, $passwd) = explode(':', trim($line));
			if ($user_id != '' && $target_user_id == $user_id) {
				$ret = true;
				break;
			}
		}

		return $ret;
	}


	/**
	 * �ѥ���ɥե�����ǧ��
	 *
	 * @param $input_user_id ���ϥ桼��ID
	 * @param $input_passwd ���ϥѥ����
	 * @return ����(true) / ����(false)
	 */
	function check_passwd_by_htpasswd($input_user_id, $input_passwd) {

		// ���������׽���
		$filepassword = "";

		// �ե������ɤ߹���
		$data_arr = file(ACS_PASSWD_FILE);
		foreach ($data_arr as $line) {

			list($user_id, $passwd) = explode(':', trim($line));

			// �桼������(user_info)��¸�ߤ���桼��ID�Τ���¸�оݤȤ���
			if ($input_user_id == $user_id) {

				if(crypt($input_passwd, $passwd) == $passwd){
					// OK���ޥ��ڡ�����
					return 0;
				}

				// �Ź���������
				if (ACSSystem::verify_passwd_by_hash($input_passwd, $passwd) == 0) {
					return 0;
				}
			}
		}
		return -1;
	}

	/**
	 * �Ź沽�Ѥߥѥ���ɤ�ǧ�ڤ���(����)
	 *
	 * @param $input_passwd ���ϥѥ����
	 * @param $get_hash �ϥå���
	 * @return ǧ������(true) / ǧ�ڼ���(false)
	 */
	function verify_passwd_by_hash($input_passwd, $get_hash) {
	
		// SSHA���������
		if (ACSSystem::verify_passwd_by_ssha($input_passwd, $get_hash) == 0) {
			return 0;
		}

		// SHA���������
		if (ACSSystem::verify_passwd_by_sha($input_passwd, $get_hash) == 0) {
			return 0;
		}

		return -1;

	}

	/**
	 * �Ź沽�Ѥߥѥ���ɤ�ǧ�ڤ���(SSHA)
	 *
	 * @param $input_passwd ���ϥѥ����
	 * @param $ssha_hash �ϥå���(SSHA)
	 * @return ����(true) / ����(false)
	 */
	function verify_passwd_by_ssha($input_passwd, $ssha_hash) {

		// Verify SSHA hash
		$rep_hash = ereg_replace("{SSHA}", "", $ssha_hash);

		// base64_encode
		$ohash = base64_decode($rep_hash); 
		$osalt = substr($ohash, 20);
		$ohash = substr($ohash, 0, 20);

		// PHP�ΥС������ˤ��ʬ��
		if(function_exists('sha1')) {
			$nhash = pack("H*", sha1($input_passwd . $osalt));
		} else if(function_exists('mHash')) {
			$nhash = mHash(MHASH_SHA1, $input_passwd . $osalt);
		} else {
			return -1;
		}

		// �ϥå���Ʊ�Τ����פ��뤫
		if ($ohash == $nhash) {
			return 0;
		} else {
			return -1;
		}
	}

	/**
	 * �Ź沽�Ѥߥѥ���ɤ�ǧ�ڤ���(SHA)
	 *
	 * @param $input_passwd ���ϥѥ����
	 * @param $sha_hash �ϥå���
	 * @return ����(true) / ����(false)
	 */
	function verify_passwd_by_sha($input_passwd, $sha_hash) {

		// Verify SHA hash
		$rep_hash = ereg_replace("{SHA}", "", $sha_hash);

		// PHP�ΥС������ˤ��ʬ��
		// base64_encode
		if(function_exists('sha1')) {
			$nhash = base64_encode(pack("H*", sha1($input_passwd)));
		} else if(function_exists('mHash')) {
			$nhash = base64_encode(mHash(MHASH_SHA1, $input_passwd));
		} else {
			return -1;
		}

		// �ϥå���Ʊ�Τ����פ��뤫
		if ($rep_hash == $nhash) {
			return 0;
		} else {
			return -1;
		}
	}

	/**
	 * �ѥ���ɥե�����Υ桼��ID�������ؤ���
	 *
	 * @param $new_user_id �����桼��ID
	 * @param $old_user_id ��桼��ID
	 * @return ��������(true) / ��������(false)
	 */
	function update_passwd_with_userid($new_user_id, $old_user_id) {
		$ret = 1;
		$entry_array = array(); // �ѥ���ɥե����륨��ȥ��ݻ�����

		// �񤭹��߲�ǽ�����å�
		if (!is_writable(ACS_PASSWD_FILE)) {
			return 0;
		}

		// �ե������ɤ߹���
		$data_arr = file(ACS_PASSWD_FILE);
		foreach ($data_arr as $line) {
			list($user_id, $passwd) = explode(':', trim($line));
			$entry_array[$user_id] = $passwd;
		}

		// �ե����륪���ץ�
		if (($fp = fopen(ACS_PASSWD_FILE, 'w')) === false) {
			return 0;
		}
		flock($fp, LOCK_EX);
		foreach ($entry_array as $user_id => $passwd) {
			// ���Υѥ���ɤϽ���
			if ($passwd != '') {
				if ($old_user_id == $user_id) {
					// �Ť��桼��ID�򿷤����桼��ID���ѹ�
					fwrite($fp, "$new_user_id:$passwd\n");
				} else {
					// �оݥ桼���ʳ��Ͻ�ľ������
					fwrite($fp, "$user_id:$passwd\n");
				}
			}
		}

		// �ե����륯����
		fclose($fp);
		return $ret;
	}

	/**
	 * �ѥ���ɥե������ǧ�ڤ���
	 *
	 * @param $input_user_id ���ϥ桼��ID
	 * @param $input_passwd ���ϥѥ����
	 * @return �ޥå�����桼��ID / NULL
	 */
	function check_passwd($input_user_id, $input_passwd) {
		// ���������׽���
		$input_user_id = trim($input_user_id);
		$input_passwd = trim($input_passwd);
		$filepassword = "";
		
		/* LDAP�ؤ�ǧ��(LDAP����Ѥ�����) */
		if (USE_LDAP_SYSTEM == "1") {
			$ret_id = ACSLDAP::check_passwd_by_ldap($input_user_id, $input_passwd);
			if ($ret_id != null) {
				return $ret_id;
			}
		}

		/* �ѥ���ɥե�����Ȥ�ǧ�� */
		$ret = ACSSystem::check_passwd_by_htpasswd($input_user_id, $input_passwd);
		if ($ret == 0) {
			// ���ϥ桼��ID���ֵ�
			return $input_user_id;
		}

		// NULL���ֵ�
		return NULL;
	}

	/**
	 * ���������ƥ�Ȥ���³�����å�
	 * LDAP�ʤɳ��������ƥफ��桼���������������硢
	 * ��³�����å���Ԥ�
	 *
	 * @return ��³����(true) / ��³����(false)
	 */
	function check_connect_outside() {

		if (USE_LDAP_SYSTEM != "1") {
			// ���������ƥ����³���ʤ���������ʤ�
			return 0;
		}

		// LDAP����³������ͤξ��
		if (!ACSLDAP::connect_ldap()) {
			return -1;
		}
		return 0;

	}

}
?>
