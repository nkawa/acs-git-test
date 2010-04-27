<?php
// $Id: ACSAccessControl.class.php,v 1.17 2006/11/20 08:44:02 w-ota Exp $


/*
 * アクセス制御クラス
 */
class ACSAccessControl {

	/**
	 * 公開範囲の配列を取得する
	 *
	 * @param コミュニティ種別名
	 * @param コンテンツ種別名
	 * @return 設定可能な公開範囲の配列 (連想配列の配列)
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
	 * 公開レベルマスタの配列を取得する
	 *
	 * @param $open_level_code 公開レベルコード
	 * @return 公開レベルマスタの配列
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
	 * デフォルトのopen_level_codeを取得する
	 *
	 * @param $community_type_code コミュニティ種別コード
	 * @param $contents_type_code コンテンツ種別コード
	 * @return $open_level_code 公開レベルコード
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
	 * マイページ内コンテンツのアクセス時におけるrole_arrayを取得する
	 *
	 * @param $acs_user_info_row アクセス者のユーザ情報
	 * @param $target_user_info_row アクセス対象のユーザ情報
	 * @return role_array (連想配列)
	 */
	static function get_user_community_role_array($acs_user_info_row, $target_user_info_row) {
		$role_array = array('public' => false, 'user' => false, 'member' => false, 'administrator' => false, 'system_administrator' => false);

		// (1) 一般ユーザ(外部ユーザ)かどうか
		if (!$acs_user_info_row['is_acs_user']) {
			$role_array['public'] = true;

		} else {
			// (2) ログインユーザかどうか
			$role_array['user'] = true;

			// (3) 友人かどうか
			if (ACSUser::is_in_friends_id_array($acs_user_info_row, $target_user_info_row['user_community_id'])) {
				$role_array['member'] = true;
			}

			// (4) 本人かどうか
			if ($acs_user_info_row['user_id'] == $target_user_info_row['user_id']) {
				$role_array['administrator'] = true;
			}

			// (5) システム管理者かどうか
			if (ACSAccessControl::is_system_administrator($acs_user_info_row)) {
				$role_array['system_administrator'] = true;
			}
		}

		return $role_array;
	}

	/**
	 * マイページ(ユーザコミュニティ)のコンテンツにアクセス可能かどうか
	 *
	 * @param $role_array アクセス者のrole_array
	 * @param $row アクセス対象となるデータ
	 * @return アクセス可(true)/アクセス不可(false)
	 */
	static function is_valid_user_for_user_community($acs_user_info_row, $role_array, $row) {
		$ret = false;

		foreach ($role_array as $role_key => $role_value) {
			if (ACSLib::get_boolean($row["open_for_{$role_key}"]) && $role_value) {
				if ($role_key == 'member') {
					// マイフレンズ or マイフレンズグループ検索
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
	 * role_arrayに応じてrow_arrayを取得する (ユーザコミュニティ)
	 *
	 * @param $acs_user_info_row アクセス者のユーザ情報
	 * @param $role_array アクセス者のrole_array
	 * @param $row_array アクセス対象となるデータ (連想配列の配列)
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
	 * role_arrayに応じてobj_row_arrayを取得する (ユーザコミュニティ)
	 *
	 * @param  $acs_user_info_row アクセス者のユーザ情報
	 * @param  $role_array        アクセス者のrole_array
	 * @param  $obj_row_array     アクセス対象となるデータ (オブジェクトの配列)
	 * @return アクセス可能なデータ(オブジェクトの配列)
	 */
	static function get_valid_obj_row_array_for_user_community($acs_user_info_row, $role_array, $obj_array) {
		$new_obj_array = array();

		/* 公開範囲マスタ取得 */
		$open_level_master_row_array = ACSAccessControl::get_all_open_level_master_row_array();

		foreach ($obj_array as $obj) {
			$open_level_code = $obj->get_open_level_code();

			// obj -> row に変換
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
	 * 公開範囲マスタ取得
	 *
	 * @return open_level_code をキーにした配列
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
	 * role_arrayに応じてrowを取得する (ユーザコミュニティ)
	 *
	 * @param $acs_user_info_row アクセス者のユーザ情報
	 * @param $role_array アクセス者のrole_array
	 * @param $row アクセス対象となるデータ (連想配列)
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
	 * コミュニティ内コンテンツのアクセス時におけるrole_arrayを取得する
	 *
	 * @param $acs_user_info_row アクセス者のユーザ情報
	 * @param $target_community_row アクセス対象のコミュニティ情報
	 * @return role_array (連想配列)
	 */
	static function get_community_role_array($acs_user_info_row, $target_community_row) {
		$role_array = array('public' => false, 'user' => false, 'member' => false, 'administrator' => false, 'system_administrator' => false);

		// (1) 一般ユーザ(外部ユーザ)かどうか
		if (!$acs_user_info_row['is_acs_user']) {
			$role_array['public'] = true;

		} else {
			// (2) ログインユーザかどうか
			$role_array['user'] = true;

			// (3) コミュニティメンバかどうか
			if (ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $target_community_row['community_id'])) {
				$role_array['member'] = true;
			}

			// (4) コミュニティ管理者かどうか
			if (ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $target_community_row['community_id'])) {
				$role_array['administrator'] = true;
			}

			// (5) システム管理者かどうか
			if (ACSAccessControl::is_system_administrator($acs_user_info_row)) {
				$role_array['administrator'] = true;
			}
		}

		return $role_array;
	}

	/**
	 * コミュニティのコンテンツにアクセス可能かどうか
	 *
	 * @param $acs_user_info_row アクセス者のユーザ情報
	 * @param $role_array アクセス者のrole_array
	 * @param $row アクセス対象となるデータ (連想配列)
	 * @return アクセス可(true)/アクセス不可(false)
	 */
	static function is_valid_user_for_community($acs_user_info_row, $role_array, $row) {
		$ret = false;

		// コミュニティメンバ、システム管理者以外の場合
		// コミュニティ全体の公開範囲をチェック
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
				// 閲覧許可を与えるコミュニティが指定されている場合
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
	 * role_arrayに応じてrow_arrayを取得する (コミュニティ)
	 *
	 * @param $acs_user_info_row アクセス者のユーザ情報
	 * @param $role_array アクセス者のrole_array
	 * @param $row アクセス対象となるデータ (連想配列の配列)
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
	 * role_arrayに応じてobj_row_arrayを取得する (コミュニティ)
	 *
	 * @param  $acs_user_info_row アクセス者のユーザ情報
	 * @param  $role_array        アクセス者のrole_array
	 * @param  $obj_row_array     アクセス対象となるデータ (オブジェクトの配列)
	 * @return アクセス可能なデータ(オブジェクトの配列)
	 */
	static function get_valid_obj_row_array_for_community($acs_user_info_row, $role_array, $obj_array) {
		$new_obj_array = array();

		/* 公開範囲マスタ取得 */
		$open_level_master_row_array = ACSAccessControl::get_all_open_level_master_row_array();

		foreach ($obj_array as $obj) {
			$open_level_code = $obj->get_open_level_code();

			// obj -> row に変換
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
	 * システム管理者かどうか
	 *
	 * @param $acs_user_info_row ユーザ情報の配列
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
	 * アクセス権に応じて変化する表示情報を設定する
	 *
	 * @param $profile_row プロフィール情報の１つ
	 * @param $view_at     表示のアクセス権
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
