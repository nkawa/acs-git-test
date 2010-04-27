<?php
// $Id: ACSLDAP.class.php,v 1.0 2009/06/24 10:30:00 y-yuki Exp $
// CAUTION::LDAPクラスは、各環境に合わせてコーディングの必要がある


/*
 * LDAPクラス
 */
class ACSLDAP {

	/**
	 * LDAPのパスワード認証処理を実施する
	 *
	 * @param $input_user_id ユーザID
	 * @param $input_passwd パスワード
	 * @return 連想配列1エントリ
	 */
	function check_passwd_by_ldap($input_user_id, $input_passwd) {

		$ldap_user_info_row = ACSLDAP::get_ldap_user_info_row($input_user_id);

		/* LDAPにデータが有る場合はパスワード認証 */
		if (count($ldap_user_info_row) > 1) {

			/* LDAPパスワードの認証 */
			$passwd = str_replace("{crypt}", "", $ldap_user_info_row['userpassword']);

			if(crypt($input_passwd, $passwd) == $passwd 
					|| ACSSystem::verify_passwd_by_hash($input_passwd, $passwd) == 0) {
				return $input_user_id;
			}
		}
		return null;
	}

	/**
	 * LDAPより、ユーザ情報を検索する
	 *
	 * @param $input_user_id ユーザID
	 * @return 連想配列1エントリ
	 */
	function ldap_search_user_info_ipdb($input_user_id) {

		// システム設定を取得
		$system_conf_row = ACSLDAP::set_system_conf();

		// フィルタ
		$filter = '(cn=' . $input_user_id . ')';

		// LDAP接続
		$conn = ACSLDAP::connect_ldap();
		if (!$conn) {
			return -1;
		}

		// search
		$res = @ldap_search($conn, $system_conf_row['ldap_base_dn'], $filter);

		// エントリ取得
		$row_arr = @ldap_get_entries($conn, $res);
		return $row_arr;
	}

	/**
	 * ユーザ情報を取得する
	 *
	 * @param $input_user_id 入力ユーザID
	 * @return ユーザ情報
	 */
	function get_ldap_user_info_row($input_user_id) {

		// まず取得してみる
		$ldap_user_info_row_array = ACSLDAP::ldap_search_user_info_ipdb($input_user_id);

		// 1件のユーザ情報
		$ldap_user_info_row = array();
		// ユーザID
		$ldap_user_info_row['user_id'] = $input_user_id;

		// 氏名
		$ldap_user_info_row['user_name'] = mb_convert_encoding(
				$ldap_user_info_row_array[0]['name'][0], mb_internal_encoding(), 'UTF-8');

		// メールアドレス
		$ldap_user_info_row['mail_addr'] = $ldap_user_info_row_array[0]['mail'][0];

		// 所属
		$ldap_user_info_row['belonging'] = '';

		// パスワード
		$ldap_user_info_row['userpassword'] = $ldap_user_info_row_array[0]['userpassword'][0];

		return $ldap_user_info_row;

	}

	/**
	 * LDAP接続チェック
	 *
	 * @return 接続成功(true) / 接続失敗(false)
	 */
	function check_connect_ldap_ipdb() {

		if (!ACSLDAP::connect_ldap()) {
			return -1;
		}
		return 0;

	}

	/**
	 * LDAPに接続する
	 *
	 * @param
	 * @return リソース
	 */
	function connect_ldap() {

		// システム設定を取得
		$system_conf_row = ACSLDAP::set_system_conf();

		// LDAP接続
		$conn = @ldap_connect($system_conf_row['ldap_server'], $system_conf_row['ldap_port']);
		if (!$conn) {
			// 失敗時はNULL
			return null;
		}

		// LDAPプロトコルバージョンセット (LDAPv3)
		@ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

		// バインド処理
		$bind = @ldap_bind($conn, $system_conf_row['ldap_bind_dn'], $system_conf_row['ldap_bind_passwd']);
		if (!$bind) {
			// 失敗時はNULL
			return null;
		}
		return $conn;
	}


	/**
	 * LDAP設定情報を取得する
	 *
	 * @return 設定情報(配列)
	 */
	function set_system_conf() {

		$system_conf_row = array();

		// ホスト
		$system_conf_row['ldap_server'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_SERVER');

		// ポート
		$system_conf_row['ldap_port'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_PORT');

		// BASE
		$system_conf_row['ldap_base_dn'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_BASE_DN');

		// BIND
		$system_conf_row['ldap_bind_dn'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_BIND_DN');

		// BINDパスワード
		$system_conf_row['ldap_bind_passwd'] = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'LDAP_BIND_PASSWD');

		return $system_conf_row;
	}


}
?>