<?php
// $Id: ACSSystem.class.php,v 2.0 2009/06/24 16:30:00 acs Exp $


/*
 * Systemクラス
 */
class ACSSystem {

	/**
	 * パスワードファイルを更新する
	 *
	 * @param $new_user_id 新規ユーザID
	 * @param $new_passwd 新規パスワード
	 * @return 成功(true) / 失敗(false)
	 */
	function update_passwd($new_user_id, $new_passwd) {
		$ret = 1;
		$entry_array = array(); // パスワードファイルエントリ保持配列

		if (!is_writable(ACS_PASSWD_FILE)) {
			return 0;
		}

		// ファイル読み込み
		$data_arr = file(ACS_PASSWD_FILE);
		foreach ($data_arr as $line) {
			list($user_id, $passwd) = explode(':', trim($line));
			// ユーザ情報(user_info)が存在するユーザIDのみ保存対象とする
			if (ACSUser::get_user_info_row_by_user_id($user_id)) {
				$entry_array[$user_id] = $passwd;
			}
		}

		// 新しいパスワード
		if ($new_passwd != '') {
			$new_passwd = crypt($new_passwd);
		}
		$entry_array[$new_user_id] = $new_passwd;

		// ファイルオープン
		if (($fp = fopen(ACS_PASSWD_FILE, 'w')) === false) {
			return 0;
		}
		flock($fp, LOCK_EX);

		foreach ($entry_array as $user_id => $passwd) {
			// 空のパスワードは除去
			if ($passwd != '') {
				fwrite($fp, "$user_id:$passwd\n");
			}
		}

		// ファイルクローズ
		fclose($fp);
		return $ret;
	}

	/**
	 * パスワードファイルから指定のユーザIDのエントリを削除する
	 *
	 * @param $delete_user_id 削除するユーザID
	 * @return 成功(true) / 失敗(false)
	 */
	function delete_passwd($delete_user_id) {
		$ret = 1;
		$entry_array = array(); // パスワードファイルエントリ保持配列

		if (!is_writable(ACS_PASSWD_FILE)) {
			return 0;
		}

		// ファイル読み込み
		$data_arr = file(ACS_PASSWD_FILE);
		foreach ($data_arr as $line) {
			list($user_id, $passwd) = explode(':', trim($line));
			// 削除するユーザIDはスキップ
			if ($user_id == $delete_user_id) {
				continue;
			}
			// ユーザ情報(user_info)が存在するユーザIDのみ保存対象とする
			if (ACSUser::get_user_info_row_by_user_id($user_id)) {
				$entry_array[$user_id] = $passwd;
			}
		}

		// ファイルオープン
		if (($fp = fopen(ACS_PASSWD_FILE, 'w')) === false) {
			return 0;
		}
		flock($fp, LOCK_EX);

		foreach ($entry_array as $user_id => $passwd) {
			// 空のパスワードは除去
			if ($passwd != '') {
				fwrite($fp, "$user_id:$passwd\n");
			}
		}

		// ファイルクローズ
		fclose($fp);
		return $ret;
	}

	/**
	 * パスワードファイル(.htpasswd)に存在するユーザIDかどうか
	 *
	 * @param $target_user_id 対象のユーザID
	 * @return 存在する(true) / 存在しない(false)
	 */
	function is_htpasswd_user($target_user_id) {

		$ret = false;

		// ファイル読み込み
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
	 * パスワードファイル認証
	 *
	 * @param $input_user_id 入力ユーザID
	 * @param $input_passwd 入力パスワード
	 * @return 成功(true) / 失敗(false)
	 */
	function check_passwd_by_htpasswd($input_user_id, $input_passwd) {

		// エスケープ処理
		$filepassword = "";

		// ファイル読み込み
		$data_arr = file(ACS_PASSWD_FILE);
		foreach ($data_arr as $line) {

			list($user_id, $passwd) = explode(':', trim($line));

			// ユーザ情報(user_info)が存在するユーザIDのみ保存対象とする
			if ($input_user_id == $user_id) {

				if(crypt($input_passwd, $passwd) == $passwd){
					// OK→マイページへ
					return 0;
				}

				// 暗号形式の比較
				if (ACSSystem::verify_passwd_by_hash($input_passwd, $passwd) == 0) {
					return 0;
				}
			}
		}
		return -1;
	}

	/**
	 * 暗号化済みパスワードを認証する(制御)
	 *
	 * @param $input_passwd 入力パスワード
	 * @param $get_hash ハッシュ
	 * @return 認証成功(true) / 認証失敗(false)
	 */
	function verify_passwd_by_hash($input_passwd, $get_hash) {
	
		// SSHA形式の比較
		if (ACSSystem::verify_passwd_by_ssha($input_passwd, $get_hash) == 0) {
			return 0;
		}

		// SHA形式の比較
		if (ACSSystem::verify_passwd_by_sha($input_passwd, $get_hash) == 0) {
			return 0;
		}

		return -1;

	}

	/**
	 * 暗号化済みパスワードを認証する(SSHA)
	 *
	 * @param $input_passwd 入力パスワード
	 * @param $ssha_hash ハッシュ(SSHA)
	 * @return 成功(true) / 失敗(false)
	 */
	function verify_passwd_by_ssha($input_passwd, $ssha_hash) {

		// Verify SSHA hash
		$rep_hash = ereg_replace("{SSHA}", "", $ssha_hash);

		// base64_encode
		$ohash = base64_decode($rep_hash); 
		$osalt = substr($ohash, 20);
		$ohash = substr($ohash, 0, 20);

		// PHPのバージョンにより分岐
		if(function_exists('sha1')) {
			$nhash = pack("H*", sha1($input_passwd . $osalt));
		} else if(function_exists('mHash')) {
			$nhash = mHash(MHASH_SHA1, $input_passwd . $osalt);
		} else {
			return -1;
		}

		// ハッシュ同士が合致するか
		if ($ohash == $nhash) {
			return 0;
		} else {
			return -1;
		}
	}

	/**
	 * 暗号化済みパスワードを認証する(SHA)
	 *
	 * @param $input_passwd 入力パスワード
	 * @param $sha_hash ハッシュ
	 * @return 成功(true) / 失敗(false)
	 */
	function verify_passwd_by_sha($input_passwd, $sha_hash) {

		// Verify SHA hash
		$rep_hash = ereg_replace("{SHA}", "", $sha_hash);

		// PHPのバージョンにより分岐
		// base64_encode
		if(function_exists('sha1')) {
			$nhash = base64_encode(pack("H*", sha1($input_passwd)));
		} else if(function_exists('mHash')) {
			$nhash = base64_encode(mHash(MHASH_SHA1, $input_passwd));
		} else {
			return -1;
		}

		// ハッシュ同士が合致するか
		if ($rep_hash == $nhash) {
			return 0;
		} else {
			return -1;
		}
	}

	/**
	 * パスワードファイルのユーザIDを入れ替える
	 *
	 * @param $new_user_id 新規ユーザID
	 * @param $old_user_id 旧ユーザID
	 * @return 処理成功(true) / 処理失敗(false)
	 */
	function update_passwd_with_userid($new_user_id, $old_user_id) {
		$ret = 1;
		$entry_array = array(); // パスワードファイルエントリ保持配列

		// 書き込み可能チェック
		if (!is_writable(ACS_PASSWD_FILE)) {
			return 0;
		}

		// ファイル読み込み
		$data_arr = file(ACS_PASSWD_FILE);
		foreach ($data_arr as $line) {
			list($user_id, $passwd) = explode(':', trim($line));
			$entry_array[$user_id] = $passwd;
		}

		// ファイルオープン
		if (($fp = fopen(ACS_PASSWD_FILE, 'w')) === false) {
			return 0;
		}
		flock($fp, LOCK_EX);
		foreach ($entry_array as $user_id => $passwd) {
			// 空のパスワードは除去
			if ($passwd != '') {
				if ($old_user_id == $user_id) {
					// 古いユーザIDを新しいユーザIDに変更
					fwrite($fp, "$new_user_id:$passwd\n");
				} else {
					// 対象ユーザ以外は書き直すだけ
					fwrite($fp, "$user_id:$passwd\n");
				}
			}
		}

		// ファイルクローズ
		fclose($fp);
		return $ret;
	}

	/**
	 * パスワードファイルを認証する
	 *
	 * @param $input_user_id 入力ユーザID
	 * @param $input_passwd 入力パスワード
	 * @return マッチするユーザID / NULL
	 */
	function check_passwd($input_user_id, $input_passwd) {
		// エスケープ処理
		$input_user_id = trim($input_user_id);
		$input_passwd = trim($input_passwd);
		$filepassword = "";
		
		/* LDAPへの認証(LDAPを使用する場合) */
		if (USE_LDAP_SYSTEM == "1") {
			$ret_id = ACSLDAP::check_passwd_by_ldap($input_user_id, $input_passwd);
			if ($ret_id != null) {
				return $ret_id;
			}
		}

		/* パスワードファイルとの認証 */
		$ret = ACSSystem::check_passwd_by_htpasswd($input_user_id, $input_passwd);
		if ($ret == 0) {
			// 入力ユーザIDを返却
			return $input_user_id;
		}

		// NULLを返却
		return NULL;
	}

	/**
	 * 外部システムとの接続チェック
	 * LDAPなど外部システムからユーザ情報を取得する場合、
	 * 接続チェックを行う
	 *
	 * @return 接続成功(true) / 接続失敗(false)
	 */
	function check_connect_outside() {

		if (USE_LDAP_SYSTEM != "1") {
			// 外部システムに接続しない場合は問題ない
			return 0;
		}

		// LDAPに接続する仕様の場合
		if (!ACSLDAP::connect_ldap()) {
			return -1;
		}
		return 0;

	}

}
?>
