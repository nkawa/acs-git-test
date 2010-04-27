<?php
// $Id: ACSUser.class.php,v 1.47 2008/04/24 16:00:00 y-yuki Exp $

/*
 * マイページ共通クラス
 */
class ACSUser {

	/**
	 * ユーザIDを指定して、ユーザ情報を取得する
	 *
	 * @param ユーザID
	 * @return ユーザ情報 (連想配列)
	 */
	static function get_user_info_row_by_user_id($user_id) {

		static $cache_rows;

		$user_id = pg_escape_string($user_id);

		if (is_array($cache_rows[$user_id])) {
			return $cache_rows[$user_id];
		}

		$sql  = "SELECT user_info.user_id, user_info.user_community_id, USER_COMMUNITY.community_name,";
		$sql .= "	   USER_NAME_C.contents_value as user_name, MAIL_ADDR_C.contents_value as mail_addr,";
		$sql .= "	   SELF_C.contents_type_code, SELF_CTM.contents_type_name,";
		$sql .= "	   SELF_C.open_level_code, SELF_OLM.open_level_name,";
		$sql .= "	   user_info.administrator_flag";
		$sql .= " FROM user_info, community as USER_COMMUNITY,"; 
		$sql .= "  contents as USER_NAME_C, contents_type_master as USER_NAME_CTM,"; // 氏名
		$sql .= "  contents as MAIL_ADDR_C, contents_type_master as MAIL_ADDR_CTM,"; // メールアドレス
		$sql .= "  contents as SELF_C,	  contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // マイページ全体
		$sql .= " WHERE user_info.user_id = '$user_id'";
		$sql .= "  AND user_info.user_community_id = USER_COMMUNITY.community_id";
		// 氏名
		$sql .= "  AND USER_COMMUNITY.community_id = USER_NAME_C.community_id";
		$sql .= "  AND USER_NAME_C.contents_type_code = USER_NAME_CTM.contents_type_code";
		$sql .= "  AND USER_NAME_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D01')."'";
		// メールアドレス
		$sql .= "  AND USER_COMMUNITY.community_id = MAIL_ADDR_C.community_id";
		$sql .= "  AND MAIL_ADDR_C.contents_type_code = MAIL_ADDR_CTM.contents_type_code";
		$sql .= "  AND MAIL_ADDR_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D02')."'";
		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";

		// 全体=非公開を除く
		$sql .= "  AND USER_COMMUNITY.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		//$sql .= "  AND SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";

		$row = ACSDB::_get_row($sql);

		$cache_rows[$user_id] = $row;

		return $row;
	}

	/**
	 * ユーザコミュニティIDを指定して、ユーザ情報を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @param $include_private_flag 非公開ユーザコミュニティも含める
	 * @return ユーザ情報 (連想配列)
	 */
	static function get_user_info_row_by_user_community_id($user_community_id, $include_private_flag = false) {

		static $cache_rows;

		$user_community_id = pg_escape_string($user_community_id);

		$cache_key = $user_community_id ."_". ($include_private_flag===true ? "T" : "F");
		if (is_array($cache_rows[$cache_key])) {
			return $cache_rows[$cache_key];
		}

		$sql  = "SELECT *";
		$sql .= " FROM user_info,";
		$sql .= "  (community LEFT OUTER JOIN community_image_file USING(community_id)) as USER_COMMUNITY, community_type_master,";
		$sql .= "  contents as USER_NAME_C, contents_type_master as USER_NAME_CTM,"; // 氏名
		$sql .= "  contents as SELF_C,	  contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // マイページ全体
		$sql .= " WHERE user_info.user_community_id = '$user_community_id'";
		$sql .= "  AND user_info.user_community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D10')."'";
		// 氏名
		$sql .= "  AND USER_COMMUNITY.community_id = USER_NAME_C.community_id";
		$sql .= "  AND USER_NAME_C.contents_type_code = USER_NAME_CTM.contents_type_code";
		$sql .= "  AND USER_NAME_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D01')."'";
		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";
		// 全体=非公開を除く
		$sql .= "  AND USER_COMMUNITY.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		if (!$include_private_flag) {
			$sql .= "  AND SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";
		}

		$row = ACSDB::_get_row($sql);

		$cache_rows[$cache_key] = $row;

		return $row;
	}

	/**
	 * ユーザコミュニティIDを指定して、ユーザ情報(プロフィール)を取得する
	 *
	 * @param ユーザコミュニティID
	 * @param $include_private_flag 非公開ユーザコミュニティも含める
	 * @return ユーザ情報 (連想配列)
	 */
	static function get_user_profile_row($user_community_id, $include_private_flag = false) {

		$user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id, $include_private_flag);
		if (!$user_info_row) {
			return;
		}

		// プロフィール(contents)
		$user_info_row['contents_row_array'] = array();
		$user_info_row['contents_row_array']['user_name'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D01'));
		$user_info_row['contents_row_array']['mail_addr'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D02'));
		$user_info_row['contents_row_array']['belonging'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D03'));
		$user_info_row['contents_row_array']['speciality'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D04'));
		$user_info_row['contents_row_array']['birthplace'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D05'));
		$user_info_row['contents_row_array']['birthday'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D06'));
		$user_info_row['contents_row_array']['community_profile'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D07'));
		$user_info_row['contents_row_array']['community_profile_login'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D08'));
		$user_info_row['contents_row_array']['community_profile_friend'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D09'));
		$user_info_row['contents_row_array']['self'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D00'));
		$user_info_row['contents_row_array']['friends_list'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D11'));

		$user_info_row['contents_row_array']['mail_lang'] = ACSCommunity::get_contents_row($user_community_id, ACSMsg::get_mst('contents_type_master','D51'));

		// データ無しの場合は空のarrayを入れる
		foreach ($user_info_row['contents_row_array'] as $contents_key => $contents_row) {
			if (!$contents_row) {
				$user_info_row['contents_row_array'][$contents_key] = array();
			}
		}

		// 信頼済みコミュニティ
		// birthplace
		if ($user_info_row['contents_row_array']['birthplace']['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
			// trusted_community_flag
			$user_info_row['contents_row_array']['birthplace']['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($user_community_id, $user_info_row['contents_row_array']['birthplace']['contents_type_code'], $user_info_row['contents_row_array']['birthplace']['open_level_code']);
			if (count($user_info_row['contents_row_array']['birthplace']['trusted_community_row_array'])
				&& $user_info_row['contents_row_array']['birthplace']['trusted_community_row_array'][0]['community_type_name'] == ACSMsg::get_mst('community_type_master','D20')) {
				$user_info_row['contents_row_array']['birthplace']['trusted_community_flag'] = 0;
			} else {
				$user_info_row['contents_row_array']['birthplace']['trusted_community_flag'] = 1;
			}
			// trusted_community_id_csv
			$trusted_community_id_array = array();
			foreach ($user_info_row['contents_row_array']['birthplace']['trusted_community_row_array'] as $trusted_community_row) {
				array_push($trusted_community_id_array, $trusted_community_row['community_id']);
			}
			$user_info_row['contents_row_array']['birthplace']['trusted_community_id_csv'] = implode(',', $trusted_community_id_array);
		}

		// birthday
		if ($user_info_row['contents_row_array']['birthday']['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
			// trusted_community_flag
			$user_info_row['contents_row_array']['birthday']['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($user_community_id, $user_info_row['contents_row_array']['birthday']['contents_type_code'], $user_info_row['contents_row_array']['birthday']['open_level_code']);
			if (count($user_info_row['contents_row_array']['birthday']['trusted_community_row_array'])
				&& $user_info_row['contents_row_array']['birthday']['trusted_community_row_array'][0]['community_type_name'] == ACSMsg::get_mst('community_type_master','D20')) {
				$user_info_row['contents_row_array']['birthday']['trusted_community_flag'] = 0;
			} else {
				$user_info_row['contents_row_array']['birthday']['trusted_community_flag'] = 1;
			}
			// trusted_community_id_csv
			$trusted_community_id_array = array();
			foreach ($user_info_row['contents_row_array']['birthday']['trusted_community_row_array'] as $trusted_community_row) {
				array_push($trusted_community_id_array, $trusted_community_row['community_id']);
			}
			$user_info_row['contents_row_array']['birthday']['trusted_community_id_csv'] = implode(',', $trusted_community_id_array);
		}

		// flat
		foreach ($user_info_row['contents_row_array'] as $contents_key => $contents_row) {
			$user_info_row[$contents_key] = $contents_row['contents_value'];
		}

		return $user_info_row;
	}

	/**
	 * ユーザIDを指定して、メール言語を取得する
	 *
	 * @param ユーザID
	 * @return メール言語
	 */
	static function get_user_mail_lang($user_id) {

		$user_id = pg_escape_string($user_id);

		$sql  = "SELECT contents.contents_value ";
		$sql .= " FROM user_info, contents ";
		$sql .= " WHERE user_info.user_id = '" . $user_id . "'";
		$sql .= "  AND user_info.user_community_id = contents.community_id";
		$sql .= "  AND contents.contents_type_code = '" . ACS_MAIL_LANG_CONTENTS_TYPE_CODE ."'";

		$row = ACSDB::_get_row($sql);
		return $row['contents_value'];
	}

	/**
	 * コミュニティIDを指定して、メール言語を取得する
	 *
	 * @param コミュニティID
	 * @return メール言語
	 */
	function get_community_mail_lang($community_id) {

		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT contents_value FROM contents ";
		$sql .= " WHERE community_id = '" . $community_id . "'";
		$sql .= "  AND contents_type_code = '" . ACS_MAIL_LANG_CONTENTS_TYPE_CODE ."'";

		$row = ACSDB::_get_row($sql);
		return $row['contents_value'];
	}


	/**
	 * ユーザ情報(プロフィール)をセットする
	 *
	 * @param $acs_user_info_row
	 * @param ユーザ情報(プロフィール)
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_user_profile($acs_user_info_row, $form) {
		$user_community_id = $form['id'];

		$contents_type_master_array = ACSDB::get_master_array('contents_type');
		$contents_type_code_array['self'] = array_search(ACSMsg::get_mst('contents_type_master','D00'), $contents_type_master_array);
		$contents_type_code_array['user_name'] = array_search(ACSMsg::get_mst('contents_type_master','D01'), $contents_type_master_array);
		$contents_type_code_array['mail_addr'] = array_search(ACSMsg::get_mst('contents_type_master','D02'), $contents_type_master_array);
		$contents_type_code_array['belonging'] = array_search(ACSMsg::get_mst('contents_type_master','D03'), $contents_type_master_array);
		$contents_type_code_array['speciality'] = array_search(ACSMsg::get_mst('contents_type_master','D04'), $contents_type_master_array);
		$contents_type_code_array['birthplace'] = array_search(ACSMsg::get_mst('contents_type_master','D05'), $contents_type_master_array);
		$contents_type_code_array['birthday'] = array_search(ACSMsg::get_mst('contents_type_master','D06'), $contents_type_master_array);
		$contents_type_code_array['community_profile'] = array_search(ACSMsg::get_mst('contents_type_master','D07'), $contents_type_master_array);
		$contents_type_code_array['community_profile_login'] = array_search(ACSMsg::get_mst('contents_type_master','D08'), $contents_type_master_array);
		$contents_type_code_array['community_profile_friend'] = array_search(ACSMsg::get_mst('contents_type_master','D09'), $contents_type_master_array);
		$contents_type_code_array['friends_list'] = array_search(ACSMsg::get_mst('contents_type_master','D11'), $contents_type_master_array);
		$contents_type_code_array['mail_lang'] = array_search(ACSMsg::get_mst('contents_type_master','D51'), $contents_type_master_array);

		// コミュニティ種別マスタ
		$community_type_master_array = ACSDB::get_master_array('community_type');
		// コミュニティ種別コード
		$user_community_type_code = array_search(ACSMsg::get_mst('community_type_master','D10'), $community_type_master_array);
		$friends_community_type_code = array_search(ACSMsg::get_mst('community_type_master','D20'), $community_type_master_array);
		// 公開レベルマスタ
		$open_level_master_array = ACSDB::get_master_array('open_level');


		// BEGIN
		ACSDB::_do_query("BEGIN");


		// user_infoが未登録のLDAPユーザの場合は基本情報を登録する
		if (!$acs_user_info_row['is_acs_user'] && $acs_user_info_row['is_ldap_user']) {
			// 新コミュニティID
			$user_community_id_seq = ACSDB::get_next_seq('community_id_seq');
			$user_community_id = $user_community_id_seq;

			// (1) ユーザコミュニティ (community)
			$sql  = "INSERT INTO community";
			$sql .= " (community_id, community_name, community_type_code)";
			$sql .= " VALUES ($user_community_id_seq, null, '$user_community_type_code')";
			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			// (2) ユーザ情報 (user_info)
			$sql  = "INSERT INTO user_info";
			$sql .= " (user_id, user_community_id, administrator_flag)";
			$sql .= " VALUES ('$form[user_id]', '$user_community_id_seq', 'f')";
			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			// (3) マイフレンズ
			$contents_form = array();
			$contents_form['community_id'] = $user_community_id_seq;
			$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D11'), $contents_type_master_array);
			$contents_form['contents_value'] = '';
			$contents_form['open_level_code'] = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D11'));
			$ret = ACSCommunity::set_contents($contents_form);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			// (4) マイフレンズコミュニティ (community)
			$friends_community_id_seq = ACSDB::get_next_seq('community_id_seq');
			$sql  = "INSERT INTO community";
			$sql .= " (community_id, community_type_code)";
			$sql .= " VALUES ('$friends_community_id_seq', '$friends_community_type_code')";
			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			// (5) マイフレンズコミュニティをユーザコミュニティのサブコミュニティとする (sub_community)
			$sql  = "INSERT INTO sub_community";
			$sql .= " (community_id, sub_community_id)";
			$sql .= " VALUES ('$user_community_id_seq', '$friends_community_id_seq')";
			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			$is_ldap_user_login_flag = 1;
			// ユーザ基本情報ここまで
		}


		// ニックネーム
		$ret = ACSCommunity::set_community_name($user_community_id, $form['community_name']);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// コンテンツ: 氏名, メールアドレス, 所属, 専攻, 出身, 生年月日, プロフィールは３種
		foreach (array('user_name', 'mail_addr', 'belonging', 'speciality', 'birthplace', 'birthday', 'community_profile', 'community_profile_login', 'community_profile_friend', 'friends_list', 'mail_lang') as $contents_key) {
			$contents_form = array(
								   'community_id' => $user_community_id,
								   'contents_type_code' => $contents_type_code_array[$contents_key],
								   'contents_value' => $form[$contents_key],
								   'open_level_code' => $form['open_level_code_array'][$contents_key]
								   );
			$ret = ACSCommunity::set_contents($contents_form);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		}

		// コンテンツ: ページ全体 = 一般公開とする
		$contents_form = array(
							   'community_id' => $user_community_id,
							   'contents_type_code' => $contents_type_code_array['self'],
							   'contents_value' => '',
							   'open_level_code' => array_search(ACSMsg::get_mst('open_level_master','D01'), $open_level_master_array)
							   );
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// 信頼済みコミュニティ: 出身, 生年月日
		foreach (array('birthplace', 'birthday') as $contents_key) {
			if ($form['trusted_community_flag'][$contents_key]) {
				// マイフレンズグループ
				$trusted_community_id_array = explode(',', $form['trusted_community_id_csv_array'][$contents_key]);
				foreach ($trusted_community_id_array as $trusted_community_id) {
					if ($trusted_community_id == '') {
						continue;
					}
					$contents_trusted_community_form = array(
														 'community_id' => $user_community_id,
														 'contents_type_code' => $contents_type_code_array[$contents_key],
														 'open_level_code' => $form['open_level_code_array'][$contents_key],
														 'trusted_community_id' => $trusted_community_id
														 );
					$ret = ACSCommunity::set_contents_trusted_community($contents_trusted_community_form);
					if (!$ret) {
						ACSDB::_do_query("ROLLBACK");
						return $ret;
					}
				}
			} else {
				// マイフレンズ
				$trusted_community_id = ACSUser::get_friends_community_id($user_community_id); // フレンズコミュニティID
				$contents_trusted_community_form = array(
													 'community_id' => $user_community_id,
													 'contents_type_code' => $contents_type_code_array[$contents_key],
													 'open_level_code' => $form['open_level_code_array'][$contents_key],
													 'trusted_community_id' => $trusted_community_id
													 );
				$ret = ACSCommunity::set_contents_trusted_community($contents_trusted_community_form);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		if ($is_ldap_user_login_flag) {
			$acs_user_info_row = ACSUser::get_user_info_row_by_user_id($form['user_id']);
			// ログ登録: LDAPユーザ新規登録
			ACSLog::set_log($acs_user_info_row, 'New LDAP User Registration', $ret, "[UserID:{$form['user_id']}]");
			// ラストログイン登録
			ACSUser::set_last_login($acs_user_info_row);
		}

		return $ret;
	}


	/**
	 * 全てのユーザ情報を検索する
	 *
	 * @param $form 検索条件
	 * @return ユーザ情報の配列 (連想配列の配列)
	 */
	static function search_all_user_info_row_array($form) {
		$sql  = "SELECT *,";
		$sql .="  USER_NAME_C.contents_value as user_name,";
		$sql .="  MAIL_ADDR_C.contents_value as mail_addr";

		$sql .= " FROM user_info, community as USER_COMMUNITY, community_type_master,";
		$sql .= "  contents as USER_NAME_C, contents_type_master as USER_NAME_CTM,"; // 氏名
		$sql .= "  contents as MAIL_ADDR_C, contents_type_master as MAIL_ADDR_CTM,"; // メールアドレス
		$sql .= "  contents as SELF_C,	  contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // マイページ全体

		$sql .= " WHERE user_info.user_community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D10')."'";

		// 氏名
		$sql .= "  AND USER_COMMUNITY.community_id = USER_NAME_C.community_id";
		$sql .= "  AND USER_NAME_C.contents_type_code = USER_NAME_CTM.contents_type_code";
		$sql .= "  AND USER_NAME_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D01')."'";
		// メールアドレス
		$sql .= "  AND USER_COMMUNITY.community_id = MAIL_ADDR_C.community_id";
		$sql .= "  AND MAIL_ADDR_C.contents_type_code = MAIL_ADDR_CTM.contents_type_code";
		$sql .= "  AND MAIL_ADDR_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D02')."'";


		// 検索条件 //
		// キーワード
		if ($form['q'] != '') {
			$query_array_array = ACSLib::get_query_array_array($form['q']);
			$where_sql = '';
			foreach ($query_array_array as $query_array) {
				if (!count($query_array)) {
					continue;
				}

				$sub_where_sql = '';
				foreach ($query_array as $query) {
					$query = pg_escape_string($query);
					ACSLib::escape_ilike($query);

					if ($sub_where_sql != '') {
						$sub_where_sql .= " OR ";
					}

					$sub_where_sql .= "(";
					$sub_where_sql .= " user_info.user_id ILIKE '%$query%'";
					$sub_where_sql .= " OR USER_NAME_C.contents_value ILIKE '%$query%'";
					$sub_where_sql .= " OR USER_COMMUNITY.community_name ILIKE '%$query%'";
					$sub_where_sql .= " OR MAIL_ADDR_C.contents_value ILIKE '%$query%'";
					$sub_where_sql .= ")";
				}

				if ($sub_where_sql != '') {
					if ($where_sql != '') {
						$where_sql .= " AND ";
					}
					$where_sql .= "($sub_where_sql)";
				}
			}

			if ($where_sql != '') {
				$sql .= " AND ($where_sql)";
			}
		}
		//

		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";
		// 全体=非公開を除く
		$sql .= "  AND USER_COMMUNITY.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		//$sql .= "  AND SELF_OLM.open_level_name != '非公開'";

		$sql .= " ORDER BY user_info.user_id ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ユーザ情報を検索する
	 *
	 * @param $form 検索条件
	 * @return ユーザ情報の配列 (連想配列の配列)
	 */
	static function search_user_info_row_array($form, $open_level_name_array) {
		$sql  = "SELECT user_info.*, USER_COMMUNITY.*";

		if ($form['order'] == 'friends_num') {
			$sql .= ", acs_get_friends_num(USER_COMMUNITY.community_id) AS friends_num";
		} elseif ($form['order'] == 'community_num') {
			$sql .= ", acs_get_community_num(USER_COMMUNITY.community_id) AS community_num";
		}

		$sql .= " FROM user_info, community as USER_COMMUNITY,";

		// サブクエリ: コンテンツ //
		$sql .= "  (";
		$sql .= "   SELECT DISTINCT SUB_USER_COMMUNITY.community_id";
		$sql .= "	FROM community as SUB_USER_COMMUNITY, community_type_master, contents, open_level_master";
		$sql .= "	WHERE SUB_USER_COMMUNITY.community_type_code = community_type_master.community_type_code";
		$sql .= "	 AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D10')."'";
		$sql .= "	 AND SUB_USER_COMMUNITY.community_id = contents.community_id";
		// 公開範囲を限定
		$sql .= "	 AND contents.open_level_code = open_level_master.open_level_code";

		$open_level_name_where_sql = '';
		foreach ($open_level_name_array as $open_level_name) {
			if ($open_level_name_where_sql != '') {
				$open_level_name_where_sql .= ' OR ';
			}
			$open_level_name_where_sql .= "open_level_master.open_level_name = '" . pg_escape_string($open_level_name) . "'";
		}
		$sql .= "	 AND ($open_level_name_where_sql)";

		// 検索条件 //
		// キーワード
		if ($form['q'] != '') {
			$query_array_array = ACSLib::get_query_array_array($form['q']);
			$where_sql = '';
			foreach ($query_array_array as $query_array) {
				if (!count($query_array)) {
					continue;
				}

				$sub_where_sql = '';
				foreach ($query_array as $query) {
					$query = pg_escape_string($query);
					ACSLib::escape_ilike($query);

					if ($sub_where_sql != '') {
						$sub_where_sql .= " OR ";
					}

					$sub_where_sql .= "(";
					$sub_where_sql .= " SUB_USER_COMMUNITY.community_name ILIKE '%$query%'";
					$sub_where_sql .= " OR contents.contents_value ILIKE '%$query%'";
					$sub_where_sql .= ")";
				}

				if ($sub_where_sql != '') {
					if ($where_sql != '') {
						$where_sql .= " AND ";
					}
					$where_sql .= "($sub_where_sql)";
				}
			}

			if ($where_sql != '') {
				$sql .= " AND ($where_sql)";
			}
		}
		$sql .= "	ORDER BY SUB_USER_COMMUNITY.community_id ASC ";
		$sql .= "  ) as SUB_USER_COMMUNITY";
		//

		$sql .= "  ,";
		$sql .= "  contents as SELF_C,	   contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // マイページ全体

		$sql .= " WHERE user_info.user_community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = SUB_USER_COMMUNITY.community_id";

		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";
		// 全体=非公開を除く
		$sql .= "  AND USER_COMMUNITY.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		$sql .= "  AND SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";

		// ORDER
		if ($form['order'] == 'community_name') {
			$sql .= " ORDER BY USER_COMMUNITY.community_name ASC";
		} elseif ($form['order'] == 'friends_num') {
			$sql .= " ORDER BY friends_num DESC, user_info.user_id ASC";
		} elseif ($form['order'] == 'community_num') {
			$sql .= " ORDER BY community_num DESC, user_info.user_id ASC";
		} else {
			$sql .= " ORDER BY user_info.user_id ASC";
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ユーザランキング情報を取得する
	 *
	 * @return ユーザランキング情報の一覧
	 */
	static function get_ranking_user_info_row_array() {
		$limit = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
		$term = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D05'), 'USER_RANKING_COUNT_TERM');

		$sql  = "SELECT user_info.user_community_id, USER_COMMUNITY.community_name, USER_COMMUNITY.file_id,";
		$sql .= " (";
		// 投稿したダイアリー件数
		$sql .= "  acs_get_diary_score(USER_COMMUNITY.community_id, '$term')";
		// 投稿したダイアリーコメント件数
		$sql .= "  + acs_get_diary_comment_score(USER_COMMUNITY.community_id, '$term')";
		// アップロードしたファイル数
		$sql .= "  + acs_get_file_info_score(USER_COMMUNITY.community_id, '$term')";
		// コメントされたダイアリー件数
		//$sql .= "  + acs_get_commented_diary_score(USER_COMMUNITY.community_id, '$term')";
		// 投稿したbbs件数
		$sql .= "  + acs_get_bbs_score_by_u_c_id(USER_COMMUNITY.community_id, '$term')";
		// 投稿したbbs_res件数
		$sql .= "  + acs_get_bbs_res_score_by_u_c_id(USER_COMMUNITY.community_id, '$term')";
		$sql .= " ) as ranking_score";

		$sql .= " FROM user_info, (community LEFT OUTER JOIN community_image_file USING(community_id)) as USER_COMMUNITY, community_type_master,";
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // マイページ全体

		$sql .= " WHERE user_info.user_community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D10')."'";

		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";
		// 全体=非公開を除く
		$sql .= "  AND USER_COMMUNITY.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		$sql .= "  AND SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";

		$sql .= " ORDER BY ranking_score DESC";
		// LIMIT
		$sql .= " LIMIT $limit";

		$row_array = ACSDB::_get_row_array($sql);

		// 0ptは除外
		$new_row_array = array();
		foreach ($row_array as $row) {
			if ($row['ranking_score'] > 0) {
				array_push($new_row_array, $row);
			}
		}

		return $new_row_array;
	}

	/**
	 * ユーザ情報を登録する
	 *
	 * @param $form ユーザ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_user_info($form) {
		// コミュニティ種別マスタ
		$community_type_master_array = ACSDB::get_master_array('community_type');
		// コミュニティ種別コード
		$user_community_type_code = array_search(ACSMsg::get_mst('community_type_master','D10'), $community_type_master_array);
		$friends_community_type_code = array_search(ACSMsg::get_mst('community_type_master','D20'), $community_type_master_array);

		// コミュニティ種別マスタ
		$contents_type_master_array = ACSDB::get_master_array('contents_type');
		// コンテンツ種別コード
		$self_contents_type_code = array_search(ACSMsg::get_mst('contents_type_master','D00'), $contents_type_master_array);
		$user_name_contents_type_code = array_search(ACSMsg::get_mst('contents_type_master','D01'), $contents_type_master_array);
		$mail_addr_contents_type_code = array_search(ACSMsg::get_mst('contents_type_master','D02'), $contents_type_master_array);


		$org_form = $form;
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		ACSDB::_do_query("BEGIN");

		// (1) ユーザコミュニティ (community)
		$user_community_id_seq = ACSDB::get_next_seq('community_id_seq');
		$sql  = "INSERT INTO community";
		$sql .= " (community_id, community_name, community_type_code)";
		$sql .= " VALUES ($user_community_id_seq, $form[user_name], '$user_community_type_code')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (2-1) ユーザ情報 (user_info)
		$sql  = "INSERT INTO user_info";
		$sql .= " (user_id, user_community_id, administrator_flag)";
		$sql .= " VALUES ($form[user_id], '$user_community_id_seq', 'f')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (2-2) (マイページ)全体
		$contents_form = array();
		$contents_form['community_id'] = $user_community_id_seq;
		$contents_form['contents_type_code'] = $self_contents_type_code;
		$contents_form['contents_value'] = '';
		$contents_form['open_level_code'] = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D00'));
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (2-3) 氏名
		$user_name_default_open_level_code = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D08'), 'NAME_DISPLAY_LEVEL');
		//$user_name_default_open_level_code = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D01'));
		$sql  = "INSERT INTO contents";
		$sql .= " (community_id, contents_type_code, contents_value, open_level_code)";
		$sql .= " VALUES ($user_community_id_seq, '$user_name_contents_type_code', $form[user_name], '$user_name_default_open_level_code')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (2-4) メールアドレス
		$mail_addr_default_open_level_code = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D02'));
		$sql  = "INSERT INTO contents";
		$sql .= " (community_id, contents_type_code, contents_value, open_level_code)";
		$sql .= " VALUES ($user_community_id_seq, '$mail_addr_contents_type_code', $form[mail_addr], '$mail_addr_default_open_level_code')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (2-5) マイフレンズ
		$contents_form = array();
		$contents_form['community_id'] = $user_community_id_seq;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D11'), $contents_type_master_array);
		$contents_form['contents_value'] = '';
		$contents_form['open_level_code'] = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D11'));
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (3) マイフレンズコミュニティ (community)
		$friends_community_id_seq = ACSDB::get_next_seq('community_id_seq');
		$sql  = "INSERT INTO community";
		$sql .= " (community_id, community_type_code)";
		$sql .= " VALUES ('$friends_community_id_seq', '$friends_community_type_code')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (4) マイフレンズコミュニティをユーザコミュニティのサブコミュニティとする (sub_community)
		$sql  = "INSERT INTO sub_community";
		$sql .= " (community_id, sub_community_id)";
		$sql .= " VALUES ('$user_community_id_seq', '$friends_community_id_seq')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		// (5) パスワードファイル
		ACSSystem::update_passwd($org_form['user_id'], $org_form['passwd']);

		return $ret;
	}


	/**
	 * マイページ情報を取得する
	 *
	 * @param ユーザコミュニティID
	 * @return マイページ情報 (連想配列)
	 */
	function get_user_community_row($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT *";
		$sql .= " FROM community, community_type_master";
		$sql .= " WHERE community_id = '$user_community_id'";
		$sql .= "  AND community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D10')."'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}


	/**
	 * ユーザコミュニティIDを指定して、マイフレンズコミュニティIDを取得する
	 *
	 * @param ユーザコミュニティID
	 * @return マイフレンズコミュニティID
	 */
	static function get_friends_community_id($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT MY_FRIENDS_COMMUNIY.community_id";
		$sql .= " FROM community as USER_COMMUNITY, sub_community, community as MY_FRIENDS_COMMUNIY, community_type_master";
		$sql .= " WHERE USER_COMMUNITY.community_id = '$user_community_id'";
		$sql .= "  and USER_COMMUNITY.community_id = sub_community.community_id";
		$sql .= "  and sub_community.sub_community_id = MY_FRIENDS_COMMUNIY.community_id";
		$sql .= "  AND MY_FRIENDS_COMMUNIY.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D20')."'";

		$value = ACSDB::_get_value($sql);
		return $value;
	}

	/**
	 * マイフレンズの一覧を取得する
	 *
	 * @param ユーザコミュニティID
	 * @return マイフレンズの一覧 (連想配列)
	 */
	static function get_friends_id_array($user_community_id) {

		static $cache_rows;

		$user_community_id = pg_escape_string($user_community_id);

		if (is_array($cache_rows[$user_community_id])) {
			return $cache_rows[$user_community_id];
		}

		$sql  = "SELECT user_info.user_community_id";
		$sql .= " FROM community, sub_community, community as FRIENDS_COMMUNITY, community_type_master, community_member, user_info, community as USER_COMMUNITY,";
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // マイページ全体

		$sql .= " WHERE community.community_id = '$user_community_id'";
		$sql .= "  AND community.community_id = sub_community.community_id";
		$sql .= "  AND sub_community.sub_community_id = FRIENDS_COMMUNITY.community_id";
		$sql .= "  AND FRIENDS_COMMUNITY.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D20')."'";
		$sql .= "  AND FRIENDS_COMMUNITY.community_id = community_member.community_id";
		$sql .= "  AND community_member.user_community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";

		// 全体=非公開を除く
		$sql .= "  AND USER_COMMUNITY.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		$sql .= "  AND SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";
		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";

		$row_array = ACSDB::_get_row_array($sql);

		$friends_id_array = array();
		foreach ($row_array as $row) {
			array_push($friends_id_array, $row['user_community_id']);
		}

		$cache_rows[$user_community_id] = $friends_id_array;

		return $friends_id_array;
	}

	/**
	 * マイフレンズの一覧を取得する (simple)
	 *
	 * @param ユーザコミュニティID
	 * @return マイフレンズの一覧 (連想配列)
	 */
	static function get_simple_friends_row_array($user_community_id) {

		static $cache_rows;

		$user_community_id = pg_escape_string($user_community_id);

		if (is_array($cache_rows[$user_community_id])) {
			return $cache_rows[$user_community_id];
		}

		// マイフレンズコミュニティidの取得
		$sql = "SELECT 
					FRIENDS_COMMUNITY.community_id
				FROM 
					community, 
					sub_community, 
					community as FRIENDS_COMMUNITY
				WHERE
					community.community_id = '$user_community_id'
					AND community.community_id = sub_community.community_id
					AND sub_community.sub_community_id = FRIENDS_COMMUNITY.community_id
					AND FRIENDS_COMMUNITY.community_type_code = '20'";

		$row = ACSDB::_get_row($sql);
		$friends_community_id = $row['community_id'];

		// 2009.09.02 /
		if ($friends_community_id == NULL || $friends_community_id == '') {
			return array();
		}

		// マイフレンズの取得
		$sql = "SELECT
					mmb.user_community_id,
					usrcom.community_name,
					img.file_id
				FROM
					(community_member AS mmb
					INNER JOIN community AS usrcom ON mmb.user_community_id = 
					usrcom.community_id)
						LEFT JOIN community_image_file AS img ON usrcom.community_id = 
						img.community_id
				WHERE
					mmb.community_id = '$friends_community_id'
					AND usrcom.delete_flag != 't'
				ORDER BY
					mmb.user_community_id";

		$row_array = ACSDB::_get_row_array($sql);

		$cache_rows[$user_community_id] = $row_array;

		return $row_array;
	}

	/**
	 * マイフレンズの一覧を取得する
	 *
	 * @param ユーザコミュニティID
	 * @return マイフレンズの一覧 (連想配列)
	 */
	static function get_friends_row_array($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT user_info.user_community_id, USER_NAME_C.contents_value as user_name, USER_COMMUNITY.community_name, USER_COMMUNITY.file_id";
		$sql .= " FROM community, sub_community, community as FRIENDS_COMMUNITY, community_type_master, community_member,";
		$sql .= "  (community LEFT OUTER JOIN community_image_file USING(community_id)) as USER_COMMUNITY, user_info,"; 
		$sql .= "  contents as USER_NAME_C, contents_type_master as USER_NAME_CTM,"; // 氏名
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // マイページ全体

		$sql .= " WHERE community.community_id = '$user_community_id'";
		$sql .= "  AND community.community_id = sub_community.community_id";
		$sql .= "  AND sub_community.sub_community_id = FRIENDS_COMMUNITY.community_id";
		$sql .= "  AND FRIENDS_COMMUNITY.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D20')."'";
		$sql .= "  AND FRIENDS_COMMUNITY.community_id = community_member.community_id";
		$sql .= "  AND community_member.user_community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		// 氏名
		$sql .= "  AND USER_COMMUNITY.community_id = USER_NAME_C.community_id";
		$sql .= "  AND USER_NAME_C.contents_type_code = USER_NAME_CTM.contents_type_code";
		$sql .= "  AND USER_NAME_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D01')."'";

		// 全体=非公開を除く
		$sql .= "  AND USER_COMMUNITY.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		$sql .= "  AND SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";

		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";

		$sql .= " ORDER BY user_info.user_id ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * マイフレンズの人数を取得する
	 *
	 * @param ユーザコミュニティID
	 * @return マイフレンズの一覧 (連想配列)
	 */
	static function get_friends_row_array_num($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT count(*)";
		$sql .= " FROM community, sub_community, community as FRIENDS_COMMUNITY, community_type_master, community_member,"; 
		$sql .= "  community as USER_COMMUNITY, contents as SELF_C, contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // マイページ全体

		$sql .= " WHERE community.community_id = '$user_community_id'";
		$sql .= "  AND community.community_id = sub_community.community_id";
		$sql .= "  AND sub_community.sub_community_id = FRIENDS_COMMUNITY.community_id";
		$sql .= "  AND FRIENDS_COMMUNITY.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D20')."'";
		$sql .= "  AND FRIENDS_COMMUNITY.community_id = community_member.community_id";
		$sql .= "  AND community_member.user_community_id = USER_COMMUNITY.community_id";

		// 全体=非公開を除く
		$sql .= "  AND USER_COMMUNITY.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		$sql .= "  AND SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";

		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";

		$value = ACSDB::_get_value($sql);
		return $value;
	}

	/**
	 * マイフレンズかどうか
	 *
	 * @param ユーザコミュニティID
	 * @param 対象となるコミュニティID
	 * @return true(マイフレンズである) / false(マイフレンズではない)
	 */
	static function is_friends($user_community_id, $target_user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);
		$target_user_community_id = pg_escape_string($target_user_community_id);

		if ($target_user_community_id == NULL  || $target_user_community_id == '') {
			return 0;
		}

		$sql  = "SELECT count(*)";

		$sql .= " FROM community, sub_community, community as FRIENDS_COMMUNITY, community_type_master, community_member";

		$sql .= " WHERE community.community_id = '$user_community_id'";
		$sql .= "  AND community.community_id = sub_community.community_id";
		$sql .= "  AND sub_community.sub_community_id = FRIENDS_COMMUNITY.community_id";
		$sql .= "  AND FRIENDS_COMMUNITY.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D20')."'";
		$sql .= "  AND FRIENDS_COMMUNITY.community_id = community_member.community_id";
		$sql .= "  AND community_member.user_community_id = '$target_user_community_id'";

		$value = ACSDB::_get_value($sql);
		return $value;
	}

	/**
	 * マイフレンズがfriends_id_arrayに含まれるか
	 *
	 * @param $acs_user_info_row ACSユーザ情報
	 * @param $target_user_community_id 対象となるコミュニティID
	 * @return true(マイフレンズである) / false(マイフレンズではない)
	 */
	static function is_in_friends_id_array($acs_user_info_row, $target_user_community_id) {
		if (in_array($target_user_community_id, $acs_user_info_row['friends_id_array'])) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * マイフレンズグループ一覧を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @return マイフレンズグループ一覧 (連想配列の配列)
	 */
	static function get_friends_group_row_array($user_community_id) {
		// マイフレンズのコミュニティIDを取得する
		$friends_community_id = ACSUser::get_friends_community_id($user_community_id);

		$sql  = "SELECT FRIENDS_GROUP_COMMUNITY.community_id, FRIENDS_GROUP_COMMUNITY.community_name";
		$sql .= " FROM community as FRIENDS_COMMUNITY, sub_community, community as FRIENDS_GROUP_COMMUNITY";
		$sql .= " WHERE FRIENDS_COMMUNITY.community_id = '$friends_community_id'";
		$sql .= "  AND FRIENDS_COMMUNITY.community_id = sub_community.community_id";
		$sql .= "  AND sub_community.sub_community_id = FRIENDS_GROUP_COMMUNITY.community_id";
		$sql .= " ORDER BY FRIENDS_GROUP_COMMUNITY.community_name ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}


	/**
	 * マイコミュニティの一覧を取得する
	 *
	 * @param ユーザコミュニティID
	 * @return マイコミュニティの一覧 (連想配列)
	 */
	static function get_community_row_array($user_community_id) {

		static $cache_rows;

		$user_community_id = pg_escape_string($user_community_id);

		if (is_array($cache_rows[$user_community_id])) {
			return $cache_rows[$user_community_id];
		}

		$sql  = "SELECT *";
		$sql .= " FROM community, community_type_master, community_member,";
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // コミュニティ全体
		$sql .= " WHERE community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D40')."'";
		$sql .= "  AND community.community_id = community_member.community_id";
		$sql .= "  AND community_member.user_community_id = '$user_community_id'";
		// 削除フラグOFF
		$sql .= "  AND community.delete_flag != 't'";
		// コミュニティ全体の公開範囲
		$sql .= "  AND community.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";

		$sql .= " ORDER BY community.community_name ASC";

		$row_array = ACSDB::_get_row_array($sql);

		$cache_rows[$user_community_id] = $row_array;

		return $row_array;
	}


	/**
	 * マイフレンズを登録する
	 *
	 * @param ユーザコミュニティID
	 * @param ユーザコミュニティID
	 * @return true(成功) / false(失敗)
	 */
	static function set_friends($user_community_id, $target_user_community_id) {
		// BEGIN
		ACSDB::_do_query("BEGIN");

		// フレンズコミュニティID
		$friends_community_id = ACSUser::get_friends_community_id($user_community_id);
		$target_friends_community_id = ACSUser::get_friends_community_id($target_user_community_id);

		// 自分のマイフレンズに相手を追加する
		$form = array(
					  'community_id' => $friends_community_id,
					  'user_community_id' => $target_user_community_id,
					  'community_member_type_code' => ''
					  );
		$ret = ACSCommunityMemberModel::insert_community_member($form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// 相手のマイフレンズに自分を追加する
		$form = array(
					  'community_id' => $target_friends_community_id,
					  'user_community_id' => $user_community_id,
					  'community_member_type_code' => ''
					  );
		$ret = ACSCommunityMemberModel::insert_community_member($form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * マイフレンズを削除する
	 *
	 * @param ユーザコミュニティID
	 * @param ユーザコミュニティID
	 * @return true(成功) / false(失敗)
	 */
	static function delete_friends($user_community_id, $target_user_community_id) {
		// BEGIN
		ACSDB::_do_query("BEGIN");

		// フレンズコミュニティID
		$friends_community_id = ACSUser::get_friends_community_id($user_community_id);
		$target_friends_community_id = ACSUser::get_friends_community_id($target_user_community_id);

		// コミュニティ種別マスタ
		$community_type_master_array = ACSDB::get_master_array('community_type');
		// マイフレンズ
		$friends_community_type_code = array_search(ACSMsg::get_mst('community_type_master','D20'), $community_type_master_array);
		// マイフレンズグループ
		$friends_group_community_type_code = array_search(ACSMsg::get_mst('community_type_master','D30'), $community_type_master_array);

		$user_community_id = pg_escape_string($user_community_id);
		$target_user_community_id = pg_escape_string($target_user_community_id);


		// (1) 互いのマイフレンズから削除
		$sql  = "DELETE";
		$sql .= " FROM community_member";
		$sql .= " WHERE (community_id = '$friends_community_id' OR community_id = '$target_friends_community_id')";
		$sql .= "  AND (user_community_id = '$target_user_community_id' OR user_community_id = '$user_community_id')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}


		// (2) 互いのマイフレンズグループから削除
		$friends_group_row_array = ACSUser::get_friends_group_row_array($user_community_id);
		$target_friends_group_row_array = ACSUser::get_friends_group_row_array($target_user_community_id);

		$friends_group_community_id_array = array();
		foreach ($friends_group_row_array as $friends_group_row) {
			array_push($friends_group_community_id_array, $friends_group_row['community_id']);
		}
		foreach ($target_friends_group_row_array as $target_friends_group_row) {
			array_push($friends_group_community_id_array, $target_friends_group_row['community_id']);
		}

		// 削除対象となるフレンズグループコミュニティIDのCSV
		if ($friends_group_community_id_array) {
			$friends_group_community_id_csv = implode(',', $friends_group_community_id_array);
		} else {
			$friends_group_community_id_csv = 'null';
		}

		// 削除
		$sql  = "DELETE";
		$sql .= " FROM community_member";
		$sql .= " WHERE community_id IN ($friends_group_community_id_csv)";
		$sql .= "  AND (user_community_id = '$target_user_community_id' OR user_community_id = '$user_community_id')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * マイフレンズコミュニティを作成する (マイフレンズコミュニティが存在しないとき)
	 */
	function set_friends_community($user_community_id) {
		// コミュニティ種別マスタ
		$community_type_master_array = ACSDB::get_master_array('community_type');
		// コミュニティ種別コード
		$friends_community_type_code = array_search(ACSMsg::get_mst('community_type_master','D20'), $community_type_master_array);

		$user_community_id = pg_escape_string($user_community_id);

		// BEGIN
		ACSDB::_do_query("BEGIN");

		// マイフレンズコミュニティ (community)
		$friends_community_id_seq = ACSDB::get_next_seq('community_id_seq');
		$sql  = "INSERT INTO community";
		$sql .= " (community_id, community_type_code)";
		$sql .= " VALUES ('$friends_community_id_seq', '$friends_community_type_code')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// マイフレンズコミュニティをユーザコミュニティのサブコミュニティとする (sub_community)
		$sql  = "INSERT INTO sub_community";
		$sql .= " (community_id, sub_community_id)";
		$sql .= " VALUES ('$user_community_id', '$friends_community_id_seq')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		return $friends_community_id_seq;
	}


	/**
	 * マイフレンズグループメンバを更新する
	 *
	 * @param マイフレンズグループメンバ情報 (連想配列)
	 * @return true(成功) / false(失敗)
	 */
	static function update_friends_group_member($form) {
		$org_form = $form;

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		ACSDB::_do_query("BEGIN");

		// マイフレンズグループ名
		$sql  = "UPDATE community";
		$sql .= " SET community_name = $form[community_name]";
		$sql .= " WHERE community_id = $form[community_id]";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// DELETE
		$sql  = "DELETE";
		$sql .= " FROM community_member";
		$sql .= " WHERE community_id = $form[community_id]";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		$form = $org_form;

		// INSERT
		foreach ($form['trusted_community_id_array'] as $trusted_community_id) {
			$trusted_community_id = pg_escape_string($trusted_community_id);
			$community_member_form = array(
										   'community_id' => $form['community_id'],
										   'user_community_id' => $trusted_community_id,
										   'community_member_type_code' => ''
										   );
			$ret = ACSCommunityMemberModel::insert_community_member($community_member_form);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}


	/**
	 * マイフレンズグループを登録する
	 *
	 * @param マイフレンズグループコミュニティ情報 (連想配列)
	 * @return マイフレンズグループコミュニティID(成功) / false(失敗)
	 */
	static function set_friends_group($form) {
		// コミュニティ種別マスタ
		$community_type_master_array = ACSDB::get_master_array('community_type');
		// コミュニティ種別コード
		$community_type_code = array_search(ACSMsg::get_mst('community_type_master','D30'), $community_type_master_array);
		// フレンズコミュニティID
		$friends_community_id = ACSUser::get_friends_community_id($form['user_community_id']);

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		ACSDB::_do_query("BEGIN");

		$community_id_seq = ACSDB::get_next_seq('community_id_seq');

		// INSERT
		$sql  = "INSERT INTO community";
		$sql .= " (community_id, community_name, community_type_code)";
		$sql .= " VALUES ($community_id_seq, $form[community_name], '$community_type_code')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// INSERT
		$sql  = "INSERT INTO sub_community";
		$sql .= " (community_id, sub_community_id)";
		$sql .= " VALUES ('$friends_community_id', '$community_id_seq')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $community_id_seq;
	}

	/**
	 * マイフレンズグループを削除する
	 *
	 * @param マイフレンズグループコミュニティID
	 * @return true(成功) / false(失敗)
	 */
	static function delete_friends_group($friends_group_community_id) {
		$friends_group_community_id = pg_escape_string($friends_group_community_id);

		$sql  = "DELETE";
		$sql .= " FROM community";
		$sql .= " WHERE community_id = '$friends_group_community_id'";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}


	/**
	 * デフォルトの URL を返す
	 *
	 * @param view_mode		 表示モード : NULL, thumb, rss
	 */
	static function get_default_image_url ($view_mode) {
		if ($view_mode == 'thumb') {
			return ACS_DEFAULT_USER_IMAGE_FILE_THUMB;
		} else {
			return ACS_DEFAULT_USER_IMAGE_FILE;
		}
	}

	/**
	 * image_urlを加工する
	 *
	 * @param user_community_id
	 * @param view_mode		 表示モード : NULL, thumb, rss
	 */
	static function get_image_url($user_community_id, $view_mode = '') {
		$file_id = ACSCommunityImageFileModel::get_file_id($user_community_id);

		if ($file_id != '') {
			$image_url  = SCRIPT_PATH . '?';
			$image_url .= MODULE_ACCESSOR . '=User';
			$image_url .= '&' . ACTION_ACCESSOR . '=UserImage';
			$image_url .= '&id=' . $user_community_id;
			$image_url .= '&mode=' . $view_mode;
		} else {
			$image_url = ACSUser::get_default_image_url($view_mode);
		}

		return $image_url;
	}

	/**
	 * ユーザ情報を更新する（管理者）
	 * name，password，mail_addr のみ更新対象となる
	 * @param $form　変更情報
	 * @return true(成功) / false(失敗)
	 */
	static function update_user_info($form) {
		// コンテンツ種別マスタ
		$contents_type_master_array = ACSDB::get_master_array('contents_type');
		// コンテンツ種別コード
		$user_name_contents_type_code = array_search(ACSMsg::get_mst('contents_type_master','D01'), $contents_type_master_array);
		$mail_addr_contents_type_code = array_search(ACSMsg::get_mst('contents_type_master','D02'), $contents_type_master_array);

		$org_form = $form;
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		ACSDB::_do_query("BEGIN");

		// ユーザIDが変更された場合
		if ($form['old_user_id'] != $form['user_id']) {
			// (0) ユーザID
			$sql  = "UPDATE user_info";
			$sql .= " SET user_id = $form[user_id]";
			$sql .= " WHERE user_community_id = $form[user_community_id]";
			
			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
			// パスワードユーザの場合
			if (ACSSystem::is_htpasswd_user($org_form['old_user_id'])) {
				$ret = ACSSystem::update_passwd_with_userid($org_form['user_id'], $org_form['old_user_id']);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
			
		}
		
		// (1) 氏名
		$sql  = "UPDATE contents";
		$sql .= " SET contents_value = $form[user_name]";
		$sql .= " WHERE community_id = $form[user_community_id]";
		$sql .= " AND contents_type_code = '$user_name_contents_type_code'";
		
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (2) メールアドレス
		$sql  = "UPDATE contents";
		$sql .= " SET contents_value = $form[mail_addr]";
		$sql .= " WHERE community_id = $form[user_community_id]";
		$sql .= " AND contents_type_code = '$mail_addr_contents_type_code'";

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		// (3) パスワードファイル
		if($org_form['passwd_change'] == 'change_on'){
			$ret = ACSSystem::update_passwd($org_form['user_id'], $org_form['passwd']);
		}
		return $ret;
	}

	/**
	 * ユーザを削除する
	 *
	 * @param ユーザID
	 * @return true(成功) / false(失敗)
	 */
	static function delete_user_community($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);
		// BEGIN
		ACSDB::_do_query("BEGIN");
		
		// (1) コミュニティから削除する
		$sql  = "UPDATE community";
		$sql .= " SET delete_flag = 't'";
		$sql .= " WHERE community_id = '$user_community_id'";

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// COMMIT
		ACSDB::_do_query("COMMIT");

		// (2) パスワードファイル
		$ret = ACSSystem::delete_passwd($user_community_id);
		
		return $ret;
	}

	/**
	 * 足跡情報を登録する
	 *
	 * @param $form 足跡情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_footprint($form) {

		ACSLib::escape_sql_array($form);

		$sql = "INSERT INTO footprint 
					(community_id, visitor_community_id, contents_type_code,
					 contents_title, contents_link_url, contents_date, post_date) 
				VALUES
					(" . $form['community_id'] . ", " . $form['visitor_community_id'] . ", " .
					 "'" . $form['contents_type_code'] . "', '" . $form['contents_title'] . "', " .
					 "'" . $form['contents_link_url'] . "', '" . $form['contents_date'] . "', " .
					 "'" . $form['post_date'] . "')";

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * 足跡情報一覧を取得する
	 *
	 * @param $user_community_id コミュニティID
	 *		$days データ取得日数
	 * @return $row(成功) / false(失敗)
	 */
	static function get_footprint_row($user_community_id, $days=0) {
		
		// 日数指定がある場合
		if($days != 0){
			$where = ACSLib::get_sql_condition_from_today("foot.post_date", $days);
		}

		$ret = ACSUser::get_footprint_list($user_community_id, $where);

		return $ret;
	}

	/**
	 * 足跡情報一覧をDBより取得する
	 *
	 * @param $user_community_id コミュニティID
	 *		$where 条件
	 * @return $row(成功) / false(失敗)
	 */
	static function get_footprint_list($user_community_id, $where="", $select="") {

		$sql = "SELECT ";

		if($select == ""){
			$sql .= "foot.*, community.community_name, access_ctm.contents_type_name ";
		}else{
			$sql = $sql . $select . " ";
		}

		$sql = $sql . "		
				FROM footprint AS foot, 
					 community,
					 contents_type_master AS access_ctm
				WHERE foot.visitor_community_id = community.community_id AND 
					  foot.contents_type_code = access_ctm.contents_type_code AND
					  foot.community_id = " . $user_community_id;

		if($where != ""){
			$sql = $sql . " AND " . $where;
		}

		$sql .= " ORDER BY foot.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);

		return $row_array;
	}

	/**
	 * ラストログインを登録する
	 *
	 * @param $acs_user_info_row ACSユーザ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_last_login($acs_user_info_row) {

		$date = date("Y/m/d H:i:s");

		$contents_type_name = ACSMsg::get_mst('contents_type_master','D52');
		$contents_type_arr = ACSDB::get_master_array(
										"contents_type", 
										"contents_type_name='" . $contents_type_name . "'");
		
		$form['community_id'] = $acs_user_info_row['user_community_id'];
		$form['contents_type_code'] = array_search($contents_type_name, $contents_type_arr);
		$form['contents_value'] = $date;
		// ログインユーザに公開 
		$form['open_level_code'] =
					ACSAccessControl::get_default_open_level_code(
							ACSMsg::get_mst('community_type_master','D10'),
							$contents_type_name);

		// BEGIN
		ACSDB::_do_query("BEGIN");
		$ret = ACSCommunity::set_contents($form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * マイページデザインを登録する
	 *
	 * @param $acs_user_info_row ACSユーザ情報
	 * @param $css_file デザインのＣＳＳファイル名
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_design($acs_user_info_row,$css_file) {

		$contents_type_name = ACSMsg::get_mst('contents_type_master','D53');
		$contents_type_arr = ACSDB::get_master_array(
										"contents_type", 
										"contents_type_name='" . $contents_type_name . "'");
		
		$form['community_id'] = $acs_user_info_row['user_community_id'];
		$form['contents_type_code'] = array_search($contents_type_name, $contents_type_arr);
		$form['contents_value'] = $css_file;

		// ログインユーザに公開 
		$form['open_level_code'] =
					ACSAccessControl::get_default_open_level_code(
							ACSMsg::get_mst('community_type_master','D10'),
							$contents_type_name);

		// BEGIN
		ACSDB::_do_query("BEGIN");
		$ret = ACSCommunity::set_contents($form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * ログイン情報を登録する
	 *
	 * @param $user ユーザ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_login_date(&$user) {

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$user_community_id = $acs_user_info_row['user_community_id'];
		// BEGIN
		ACSDB::_do_query("BEGIN");

		// ログイン情報のインサート
		$login_id_seq = ACSDB::get_next_seq('login_id_seq');
		$sql  = "INSERT INTO login_info";
		$sql .= " (logout_id, community_id, login_date, logout_date)";
		$sql .= " VALUES (" . $login_id_seq. "," . $user_community_id . ", CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		$user->setAttribute('logout_id', $login_id_seq);
		return $ret;
	}

	/**
	 * アクセス毎にログアウト時間を更新する
	 *
	 * @param $user ユーザ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function acs_login_date(&$user) {

		$login_id_seq = $user->getAttribute('logout_id');

		// BEGIN
		ACSDB::_do_query("BEGIN");

		// ログイン情報のインサート
		$sql  = "UPDATE login_info";
		$sql .= " SET ";
		$sql .= " logout_date = CURRENT_TIMESTAMP, ";
		$sql .= " use_button_flg = FALSE ";
		$sql .= " WHERE logout_id = " . $login_id_seq. "";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}
	
	/**
	 * ログアウト情報にログアウト時間を更新する
	 *
	 * @param $user ユーザ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function upd_login_date(&$user) {

		$login_id_seq = $user->getAttribute('logout_id');

		// BEGIN
		ACSDB::_do_query("BEGIN");

		// ログイン情報のインサート
		$sql  = "UPDATE login_info";
		$sql .= " SET ";
		$sql .= " logout_date = CURRENT_TIMESTAMP, ";
		$sql .= " use_button_flg = TRUE ";
		$sql .= " WHERE logout_id = " . $login_id_seq. "";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}
	
	/**
	 * image_urlをopen_level別に取得する
	 *
	 * @param user_community_id ユーザのコミュニティID
	 * @param open_level_code_row 公開レベルコードの行
	 * @param view_mode 表示モード : NULL, thumb, rss
	 * @return 成功(true) / 失敗(false)
	 */
	static function get_image_url_with_open_level($user_community_id, $open_level_code_row, $view_mode = '') {
		$row = ACSCommunityImageFileModel::get_file_id_with_open_level($user_community_id);

		$image_url = array();
		for ($i = 0; $i < count($open_level_code_row); $i++) {		
			if ($row && $row['file_id_ol' . $open_level_code_row[$i]] != '') {
				$image_url[$i]  = SCRIPT_PATH . '?';
				$image_url[$i] .= MODULE_ACCESSOR . '=User';
				$image_url[$i] .= '&' . ACTION_ACCESSOR . '=EditProfileImageDisp';
				$image_url[$i] .= '&id=' . $user_community_id;
				$image_url[$i] .= '&mode=' . $view_mode;
				$image_url[$i] .= '&open_level_code=' . $open_level_code_row[$i];
			} else {
				$image_url[$i] = ACSUser::get_default_image_url($view_mode);
			}
		}

		return $image_url;
	}

	/**
	 * ログイン情報を削除する
	 *
	 * @param $before_date 現在日時より何日前より過去のログイン情報を消すか
	 * @param $use_button_flg ログアウトボタン押下有無で消す消さないを変えるか
	 * @return 成功(true) / 失敗(false)
	 */
	static function delete_login_info($before_date, $use_button_flg = NULL) {
		
		// BEGIN
		ACSDB::_do_query("BEGIN");
		
		$sql  = "DELETE";
		$sql .= " FROM ";
		$sql .= " login_info";
		$sql .= " WHERE ";
		$sql .= " login_date < current_timestamp + '-$before_date days'";
		if ($use_button_flg) {
			$sql .= " and use_button_flg = '" . $use_button_flg . "'";
		}
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	
	/**
	 * ログイン情報を取得する
	 *
	 * @param ユーザID
	 * @return ログイン情報 (連想配列)
	 */
	static function get_login_info_row_array($user_id) {
		
		static $cache_rows;

		$user_id = pg_escape_string($user_id);

		if (is_array($cache_rows[$user_id])) {
			return $cache_rows[$user_id];
		}
		
		$sql  = "SELECT community.community_id,";
		$sql .= "		community.community_name,";
		$sql .= "		contents.contents_value,";
		$sql .= "		user_info.user_id,";
		$sql .= "		login_info.login_date,";
		$sql .= "		login_info.logout_date,";
		$sql .= "		login_info.use_button_flg";
		$sql .= " FROM community,";
		$sql .= "  contents,";
		$sql .= "  user_info,";
		$sql .= "  login_info";
		$sql .= " WHERE community.community_id = '$user_id'";
		$sql .= "  AND community.community_id = login_info.community_id";
		$sql .= "  AND community.community_id = user_info.user_community_id";
		$sql .= "  AND community.community_id = contents.community_id";
		$sql .= "  AND contents.contents_type_code = '01'";
		$sql .= "  ORDER BY login_info.login_date DESC";
		
		$row = ACSDB::_get_row_array($sql);

		$cache_rows[$user_id] = $row;

		return $row;
	}

	/**
	 * ログインユーザ情報を取得する
	 *
	 * @param ユーザID
	 * @return ログインユーザ情報 (連想配列)
	 */
	static function get_user_info_row($user_id) {

		$sql  = "SELECT community.community_id,";
		$sql .= "		community.community_name,";
		$sql .= "		contents.contents_value as user_name,";
		$sql .= "		user_info.user_id";
		$sql .= " FROM community,";
		$sql .= "  contents,";
		$sql .= "  user_info";
		$sql .= " WHERE community.community_id = '$user_id'";
		$sql .= "  AND community.community_id = user_info.user_community_id";
		$sql .= "  AND community.community_id = contents.community_id";
		$sql .= "  AND contents.contents_type_code = '01'";
		
		$row = ACSDB::_get_row($sql);
		
		return $row;
	}
}
?>
