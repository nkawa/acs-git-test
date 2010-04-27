<?php
// $Id: ACSCommunity.class.php,v 1.44 2006/12/28 07:36:06 w-ota Exp $

/*
 * コミュニティ
 */
class ACSCommunity {

	/**
	 * コミュニティ情報を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return コミュニティ情報の配列
	 */
	static function get_community_row($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT *";
		$sql .= " FROM ((community LEFT OUTER JOIN category_master ON community.category_code = category_master.category_code)";
		$sql .= "  LEFT OUTER JOIN community_image_file USING(community_id)) as JOINED_COMMUNITY,"; 
		$sql .= "  community_type_master";
		$sql .= " WHERE JOINED_COMMUNITY.community_id = '$community_id'";
		$sql .= "  AND JOINED_COMMUNITY.community_type_code = community_type_master.community_type_code";
		// 削除フラグOFF
		$sql .= "  AND JOINED_COMMUNITY.delete_flag != 't'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * コミュニティ情報(プロフィール)を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return コミュニティ情報の配列
	 */
	static function get_community_profile_row($community_id) {
		$community_row = ACSCommunity::get_community_row($community_id);
		
		// プロフィール (contents)
		$community_row['contents_row_array'] = array();
		$community_row['contents_row_array']['community_profile'] = 
				ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D07'));
		$community_row['contents_row_array']['bbs'] = 
				ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D41'));
		$community_row['contents_row_array']['community_folder'] = 
				ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D31'));
		$community_row['contents_row_array']['self'] = 
				ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D00'));
		$community_row['contents_row_array']['ml_addr'] = 
				ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D61'));
		$community_row['contents_row_array']['ml_status'] = 
				ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D62'));

		// データ無しの場合は空のarrayを入れる
		foreach ($community_row['contents_row_array'] as $contents_key => $contents_row) {
			if (!$contents_row) {
				$community_row['contents_row_array'][$contents_key] = array();
			}
		}


		// 参加資格
		$community_row['join_trusted_community_row_array'] = ACSCommunity::get_join_trusted_community_row_array($community_id);

		// 信頼済みコミュニティ
		// bbs (掲示板)
		$community_row['contents_row_array']['bbs']['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $community_row['contents_row_array']['bbs']['contents_type_code'], $community_row['contents_row_array']['bbs']['open_level_code']);
		// community_folder (コミュニティフォルダ)
		$community_row['contents_row_array']['community_folder']['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $community_row['contents_row_array']['community_folder']['contents_type_code'], $community_row['contents_row_array']['community_folder']['open_level_code']);

		return $community_row;
	}


	/**
	 * コミュニティを登録する
	 *
	 * @param $form コミュニティ情報
	 * return 成功(コミュニティID) / 失敗(false)
	 */
	static function set_community($form) {
		// コミュニティ種別マスタ
		$community_type_master_array = ACSDB::get_master_array('community_type');
		//$community_type_code = array_search('コミュニティ', $community_type_master_array);
		$community_type_code = array_search(ACSMsg::get_mst('community_type_master','D40'), $community_type_master_array);

		// コンテンツ種別マスタ
		$contents_type_master_array = ACSDB::get_master_array('contents_type');
		// コミュニティメンバ種別マスタ
		$community_member_type_master_array = ACSDB::get_master_array('community_member_type');

		$org_form = $form;

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		ACSDB::_do_query("BEGIN");

		// (1) コミュニティ (community)
		$community_id_seq = ACSDB::get_next_seq('community_id_seq');
		$admission_flag = ACSLib::get_pg_boolean($org_form['admission_flag']);
		$sql  = "INSERT INTO community";
		$sql .= " (community_id, community_name, community_type_code, category_code, admission_flag)";
		$sql .= " VALUES ('$community_id_seq', $form[community_name], '$community_type_code', $form[category_code], '$admission_flag')";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// contents
		// (2-1) (コミュニティ全体)
		$contents_form = array();
		$contents_form['community_id'] = $community_id_seq;
		//$contents_form['contents_type_code'] = array_search('全体', $contents_type_master_array);
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D00'), $contents_type_master_array);
		$contents_form['contents_value'] = '';
		$contents_form['open_level_code'] = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D00'));
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (2-2) コミュニティプロフィール
		$contents_form = array();
		$contents_form['community_id'] = $community_id_seq;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D07'), $contents_type_master_array);
		$contents_form['contents_value'] = $org_form['community_profile'];
		$contents_form['open_level_code'] = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D07'));
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (3) 参加資格 (join_trusted_community)
		if (is_array($org_form['join_trusted_community_id_array'])) {
			$join_trusted_community_form = array();
			$join_trusted_community_form['community_id'] = $community_id_seq;
			foreach ($org_form['join_trusted_community_id_array'] as $trusted_community_id) {
				$join_trusted_community_form['trusted_community_id'] = $trusted_community_id;
				$ret = ACSCommunity::set_join_trusted_community($join_trusted_community_form);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// (4) 公開範囲 電子掲示板
		// contents
		$contents_form = array();
		$contents_form['community_id'] = $community_id_seq;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D41'), $contents_type_master_array);
		$contents_form['contents_value'] = '';
		$contents_form['open_level_code'] = $org_form['bbs_open_level_code'];
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// contents_trusted_community
		if (is_array($org_form['bbs_trusted_community_id_array'])) {
			$contents_trusted_community_form = array();
			$contents_trusted_community_form['community_id'] = $community_id_seq;
			$contents_trusted_community_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D41'), $contents_type_master_array);
			$contents_trusted_community_form['open_level_code'] = $org_form['bbs_open_level_code'];
			foreach ($org_form['bbs_trusted_community_id_array'] as $trusted_community_id) {
				$contents_trusted_community_form['trusted_community_id'] = $trusted_community_id;
				$ret = ACSCommunity::set_contents_trusted_community($contents_trusted_community_form);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// (5) 公開範囲 コミュニティフォルダ (cotents)
		// contents
		$contents_form = array();
		$contents_form['community_id'] = $community_id_seq;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D31'), $contents_type_master_array);
		$contents_form['contents_value'] = '';
		$contents_form['open_level_code'] = $org_form['community_folder_open_level_code'];
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// contents_trusted_community
		if (is_array($org_form['community_folder_trusted_community_id_array'])) {
			$contents_trusted_community_form = array();
			$contents_trusted_community_form['community_id'] = $community_id_seq;
			$contents_trusted_community_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D31'), $contents_type_master_array);
			$contents_trusted_community_form['open_level_code'] = $org_form['community_folder_open_level_code'];
			foreach ($org_form['community_folder_trusted_community_id_array'] as $trusted_community_id) {
				$contents_trusted_community_form['trusted_community_id'] = $trusted_community_id;
				$ret = ACSCommunity::set_contents_trusted_community($contents_trusted_community_form);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// (6) 公開範囲 全体
		// contents
		$contents_form = array();
		$contents_form['community_id'] = $community_id_seq;
		//$contents_form['contents_type_code'] = array_search('全体', $contents_type_master_array);
		$contents_form['contents_type_code'] = 
				array_search(ACSMsg::get_mst('contents_type_master','D00'), $contents_type_master_array);
		$contents_form['contents_value'] = '';
		$contents_form['open_level_code'] = $org_form['self_open_level_code'];
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (7) コミュニティ管理者をセットする
		$community_admin_form = array();
		$community_admin_form['community_id'] = $community_id_seq;
		$community_admin_form['user_community_id'] = $org_form['user_community_id'];
		$ret = ACSCommunity::set_community_admin($community_admin_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (8) コミュニティML
		// contents
		// コミュニティMLアドレス
		if ($org_form['community_ml_address']) {
			$contents_form = array();
			$contents_form['community_id'] = $community_id_seq;
			$contents_form['contents_type_code'] = 
					array_search(ACSMsg::get_mst('contents_type_master','D61'), 
					$contents_type_master_array);
			$contents_form['contents_value'] = $org_form['community_ml_address'];
			$contents_form['open_level_code'] = 
					ACSAccessControl::get_default_open_level_code(
						ACSMsg::get_mst('community_type_master','D40'), 
						ACSMsg::get_mst('contents_type_master','D61'));
			$ret = ACSCommunity::set_contents($contents_form);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
			// コミュニティMLステータス
			$contents_form = array();
			$contents_form['community_id'] = $community_id_seq;
			$contents_form['contents_type_code'] = 
					array_search(ACSMsg::get_mst('contents_type_master','D62'), 
					$contents_type_master_array);
			$contents_form['contents_value'] = 'QUEUE';
			$contents_form['open_level_code'] = 
					ACSAccessControl::get_default_open_level_code(
						ACSMsg::get_mst('community_type_master','D40'), 
						ACSMsg::get_mst('contents_type_master','D62'));
			$ret = ACSCommunity::set_contents($contents_form);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $community_id_seq;
	}


	/**
	 * コミュニティ名を更新する
	 *
	 * @param $community_id コミュニティID
	 * @param $community_name コミュニティ名
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_community_name($community_id, $community_name) {
		$community_id = pg_escape_string($community_id);
		$community_name = pg_escape_string($community_name);

		$sql  = "UPDATE community";
		$sql .= " SET";
		$sql .= " community_name = '$community_name'";
		$sql .= " WHERE community_id = '$community_id'";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * コミュニティのコンテンツ情報を取得する
	 *
	 * @param $community_id コミュニティID
	 * @param $contents_type_name コンテンツ種別名
	 * @return コンテンツ情報 (連想配列)
	 */
	static function get_contents_row($community_id, $contents_type_name) {
		$community_id = pg_escape_string($community_id);
		$contents_type_name = pg_escape_string($contents_type_name);

		$sql  = "SELECT contents.*, contents_type_master.*, open_level_master.*, community_type_master.*";
		$sql .= " FROM contents, community, community_type_master, contents_type_master, open_level_master";
		$sql .= " WHERE contents.community_id = '$community_id'";
		$sql .= "  AND contents.community_id = community.community_id";
		$sql .= "  AND community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND contents.contents_type_code = contents_type_master.contents_type_code";
		$sql .= "  AND contents_type_master.contents_type_name = '$contents_type_name'";
		$sql .= "  AND contents.open_level_code = open_level_master.open_level_code";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * コミュニティのコンテンツ情報を取得する
	 *
	 * @param $community_type_name コミュニティ種別名
	 * @param $contents_type_name コンテンツ種別名
	 * @return コンテンツ情報 (連想配列)
	 */
	static function get_empty_contents_row($community_type_name, $contents_type_name) {
		$community_type_name = pg_escape_string($community_type_name);
		$contents_type_name = pg_escape_string($contents_type_name);

		$sql  = "SELECT *";
		$sql .= " FROM open_level_list, community_type_master, contents_type_master, open_level_master";
		$sql .= " WHERE open_level_list.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '$community_type_name'";
		$sql .= "  AND open_level_list.contents_type_code = contents_type_master.contents_type_code";
		$sql .= "  AND contents_type_master.contents_type_name = '$contents_type_name'";
		$sql .= "  AND open_level_list.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND open_level_list.is_default = 't'";
		$row = ACSDB::_get_row($sql);

		return $row;
	}

	/**
	 * コミュニティのコンテンツを登録する
	 *
	 * @param コンテンツ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_contents($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// DELETE
		$sql  = "DELETE";
		$sql .= " FROM contents";
		$sql .= " WHERE community_id = $form[community_id]";
		$sql .= "  AND contents_type_code = $form[contents_type_code]";
		// open_level_codeを指定しない
		$ret = ACSDB::_do_query($sql);

		// INSERT
		$sql  = "INSERT INTO contents";
		$sql .= " (community_id, contents_type_code, contents_value, open_level_code)";
		$sql .= " VALUES ($form[community_id], $form[contents_type_code], $form[contents_value], $form[open_level_code])";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/*
	 * コンテンツの信頼済みコミュニティ情報を取得する
	 *
	 * @param $community_id コミュニティID
	 * @param $contents_type_code コンテンツ種別コード
	 * @param $open_level_code 公開レベルコード
	 * @param $community_type_name コミュニティ種別名
	 * @return コンテンツの信頼済みコミュニティ情報 (連想配列)
	 */
	static function get_contents_trusted_community_row_array($community_id, $contents_type_code, $open_level_code) {
		$community_id = pg_escape_string($community_id);
		$contents_type_code = pg_escape_string($contents_type_code);
		$open_level_code = pg_escape_string($open_level_code);
		$community_type_name = pg_escape_string($community_type_name);

		$sql  = "SELECT community.community_id, community.community_name, community.community_type_code,community_type_master.community_type_name";
		$sql .= " FROM contents_trusted_community, community, community_type_master";
		$sql .= " WHERE contents_trusted_community.community_id = '$community_id'";
		$sql .= "  AND contents_trusted_community.contents_type_code = '$contents_type_code'";
		$sql .= "  AND contents_trusted_community.open_level_code = '$open_level_code'";
		$sql .= "  AND contents_trusted_community.trusted_community_id = community.community_id";
		$sql .= "  AND community.community_type_code = community_type_master.community_type_code";
		$sql .= " ORDER BY community.community_name ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/*
	 * コンテンツの信頼済みコミュニティ情報を登録する
	 *
	 * @param $form コンテンツの信頼済みコミュニティ情報 (連想配列)
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_contents_trusted_community($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "INSERT INTO contents_trusted_community";
		$sql .= " (community_id, contents_type_code, open_level_code, trusted_community_id)";
		$sql .= " VALUES ($form[community_id], $form[contents_type_code], $form[open_level_code], $form[trusted_community_id])";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/*
	 * コンテンツの信頼済みコミュニティ情報を更新する前準備
	 *
	 * @param $community_id
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_contents_trusted_community($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);
		
		$sql  = "DELETE FROM contents_trusted_community";
		$sql .= " WHERE community_id = $form[community_id]";
		$sql .= "  AND contents_type_code = $form[contents_type_code]";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}
	
	/*
	 * 参加資格の信頼済みコミュニティ情報の一覧を取得する
	 *
	 * @param $community_id
	 * @return 信頼済みコミュニティ情報の一覧 (連想配列の配列)
	 */
	static function get_join_trusted_community_row_array($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT community.community_id, community.community_name";
		$sql .= " FROM join_trusted_community, community";
		$sql .= " WHERE join_trusted_community.community_id = '$community_id'";
		$sql .= "  AND join_trusted_community.trusted_community_id = community.community_id";
		$sql .= " ORDER BY community.community_name";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/*
	 * 参加資格の信頼済みコミュニティ情報を登録する
	 *
	 * @param $form 参加資格の信頼済みコミュニティ情報 (連想配列)
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_join_trusted_community($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "INSERT INTO join_trusted_community";
		$sql .= " (community_id, trusted_community_id)";
		$sql .= " VALUES ($form[community_id], $form[trusted_community_id])";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/*
	 * 参加資格の信頼済みコミュニティ情報を削除する
	 *
	 * @param $form 参加資格の信頼済みコミュニティ情報 (連想配列)
	 * @return 成功(true) / 失敗(false)
	 */
	static function delete_join_trusted_community($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "DELETE FROM join_trusted_community";
		$sql .= " WHERE community_id = $form[community_id]";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}


	/*
	 * 参加資格の信頼済みコミュニティ情報を更新する
	 *
	 * @param $form 参加資格の信頼済みコミュニティ情報 (連想配列)
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_join_trusted_community($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "UPDATE join_trusted_community";
		$sql .= " SET trusted_community_id = $form[trusted_community_id]";
		$sql .= " WHERE community_id = $form[community_id]";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * コミュニティ間リンクを設定する
	 *
	 * @param $parent_community_id 親コミュニティID
	 * @param $sub_community_id サブコミュニティID
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_community_link($parent_community_id, $sub_community_id) {
		$parent_community_id = pg_escape_string($parent_community_id);
		$sub_community_id = pg_escape_string($sub_community_id);

		$sql  = "INSERT INTO sub_community";
		$sql .= " (community_id, sub_community_id)";
		$sql .= " VALUES ('$parent_community_id', '$sub_community_id')";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * コミュニティ間リンクを削除する
	 *
	 * @param $parent_community_id 親コミュニティID
	 * @param $sub_community_id サブコミュニティID
	 * @return 成功(true) / 失敗(false)
	 */
	static function delete_community_link($parent_community_id, $sub_community_id) {
		$parent_community_id = pg_escape_string($parent_community_id);
		$sub_community_id = pg_escape_string($sub_community_id);

		$sql  = "DELETE FROM sub_community";
		$sql .= " WHERE community_id = '$parent_community_id'";
		$sql .= "  AND sub_community_id = '$sub_community_id'";
		$ret = ACSDB::_do_query($sql);

		return $ret;
	}


	/**
	 * サブコミュニティ情報の一覧を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return サブコミュニティ情報の一覧 (連想配列の配列)
	 */
	static function get_sub_community_row_array($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT SUB.community_id, SUB.community_name, SUB.file_id";
		$sql .= " FROM community, sub_community, (community LEFT OUTER JOIN community_image_file USING(community_id)) as SUB";
		$sql .= " WHERE community.community_id = '$community_id'";
		$sql .= "  AND community.community_id = sub_community.community_id";
		$sql .= "  AND sub_community.sub_community_id = SUB.community_id";
		$sql .= " ORDER BY SUB.community_name ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * サブコミュニティ情報を取得する
	 *
	 * @param $parent_community_id 親コミュニティのコミュニティID
	 * @param $sub_community_id サブコミュニティのコミュニティID
	 * @return サブコミュニティ情報 (連想配列)
	 */
	static function get_sub_community_row($parent_community_id, $sub_community_id) {
		$parent_community_id = pg_escape_string($parent_community_id);
		$sub_community_id = pg_escape_string($sub_community_id);

		$sql  = "SELECT SUB.*";
		$sql .= " FROM community, community_type_master, sub_community, community as SUB";
		$sql .= " WHERE community.community_type_code = community_type_master.community_type_code"; 
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D40')."'"; 
		$sql .= "  AND community.community_id = '$parent_community_id'";
		$sql .= "  AND community.community_id = sub_community.community_id";
		$sql .= "  AND sub_community.sub_community_id = SUB.community_id";
		$sql .= "  AND SUB.community_id = '$sub_community_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * 親コミュニティ情報の一覧を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return 親コミュニティ情報の一覧 (連想配列の配列)
	 */
	static function get_parent_community_row_array($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT community.community_id, community.community_name, community.file_id";
		$sql .= " FROM (community LEFT OUTER JOIN community_image_file USING(community_id)) as community, sub_community, community as SUB";
		$sql .= " WHERE community.community_id = sub_community.community_id";
		$sql .= "  AND sub_community.sub_community_id = SUB.community_id";
		$sql .= "  AND SUB.community_id = '$community_id'";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 親コミュニティ情報を取得する
	 *
	 * @param $parent_community_id 親コミュニティのコミュニティID
	 * @param $sub_community_id サブコミュニティのコミュニティID
	 * @return サブコミュニティ情報 (連想配列)
	 */
	static function get_parent_community_row($parent_community_id, $sub_community_id) {
		$parent_community_id = pg_escape_string($parent_community_id);
		$sub_community_id = pg_escape_string($sub_community_id);

		$sql  = "SELECT community.*";
		$sql .= " FROM community, community_type_master, sub_community, community as SUB";
		$sql .= " WHERE community.community_type_code = community_type_master.community_type_code"; 
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D40')."'"; 
		$sql .= "  AND community.community_id = '$parent_community_id'";
		$sql .= "  AND community.community_id = sub_community.community_id";
		$sql .= "  AND sub_community.sub_community_id = SUB.community_id";
		$sql .= "  AND SUB.community_id = '$sub_community_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * コミュニティメンバ情報の一覧を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return コミュニティメンバ情報の一覧 (連想配列の配列)
	 */
	static function get_community_member_user_info_row_array($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT user_info.user_community_id, USER_NAME_C.contents_value as user_name, USER_COMMUNITY.community_name, USER_COMMUNITY.file_id";
		$sql .= " FROM community_member, user_info, (community LEFT OUTER JOIN community_image_file USING(community_id)) as USER_COMMUNITY, contents as USER_NAME_C, contents_type_master as USER_NAME_CTM";
		$sql .= " WHERE community_member.community_id = '$community_id'";
		$sql .= "  AND community_member.user_community_id = user_info.user_community_id";
		$sql .= "  AND user_info.user_community_id = USER_COMMUNITY.community_id";
		// 氏名
		$sql .= "  AND user_info.user_community_id = USER_NAME_C.community_id";
		$sql .= "  AND USER_NAME_C.contents_type_code = USER_NAME_CTM.contents_type_code";
		$sql .= "  AND USER_NAME_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D01')."'";
		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";
		$sql .= " ORDER BY user_info.user_id ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * コミュニティメンバメールアドレスの一覧を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return array コミュニティメンバ情報の一覧
	 */
	static function get_community_member_mail_address_row_array($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT user_info.user_id, user_info.user_community_id, " .
				" USER_MAIL_C.contents_value as mail_address, " .
				" USER_NAME_C.contents_value as user_community_name, " .
				" USER_LANG_C.contents_value as mail_lang " .
				" FROM ((((community_member " .
				"  INNER JOIN user_info " .
				"   ON community_member.user_community_id = user_info.user_community_id) " .
				"  INNER JOIN community as USER_COMMUNITY " .
				"   ON community_member.user_community_id = USER_COMMUNITY.community_id) " .
				"  INNER JOIN contents as USER_MAIL_C " .
				"   ON community_member.user_community_id = USER_MAIL_C.community_id) " .
				"  INNER JOIN contents as USER_NAME_C ".
				"   ON community_member.user_community_id = USER_NAME_C.community_id) " .
				"  LEFT JOIN (SELECT * FROM contents WHERE contents_type_code = '51') " .
				"     as USER_LANG_C ".
				"   ON community_member.user_community_id = USER_LANG_C.community_id " .
				" WHERE community_member.community_id = ${community_id}" .
				"  AND USER_MAIL_C.contents_type_code = '02' " . // 02...メールアドレス
				"  AND USER_NAME_C.contents_type_code = '01' " . // 01...氏名

		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";
		$sql .= " ORDER BY user_info.user_id ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * コミュニティメンバ人数を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return int コミュニティメンバ人数
	 */
	static function get_community_member_count($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT COUNT(*) AS cnt " .
				" FROM community_member " .
				"  INNER JOIN community as USER_COMMUNITY " .
				"   ON community_member.user_community_id = USER_COMMUNITY.community_id " .
				" WHERE community_member.community_id = ${community_id}" .
				"  AND USER_COMMUNITY.delete_flag != 't'";

		return ACSDB::_get_value($sql);
	}

	/**
	 * コミュニティメンバ情報の件数を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return コミュニティメンバ情報の一覧 (連想配列の配列)
	 */
	static function get_community_member_num($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT count(*)";
		$sql .= " FROM community_member, community as USER_COMMUNITY, user_info";
		$sql .= " WHERE community_member.community_id = '$community_id'";
		$sql .= "  AND community_member.user_community_id = user_info.user_community_id";
		$sql .= "  AND user_info.user_community_id = USER_COMMUNITY.community_id";
		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";

		$value = ACSDB::_get_value($sql);
		return $value;
	}

	/**
	 * コミュニティ管理者情報の一覧を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return コミュニティ管理者情報の一覧 (連想配列の配列)
	 */
	static function get_community_admin_user_info_row_array($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT user_info.user_community_id, USER_NAME_C.contents_value as user_name, USER_COMMUNITY.community_name, USER_COMMUNITY.file_id";
		$sql .= " FROM community_member, community_member_type_master, user_info, (community LEFT OUTER JOIN community_image_file USING(community_id)) as USER_COMMUNITY, contents as USER_NAME_C, contents_type_master as USER_NAME_CTM";
		$sql .= " WHERE community_member.community_id = '$community_id'";
		$sql .= "  AND community_member.community_member_type_code = community_member_type_master.community_member_type_code";
		$sql .= "  AND community_member_type_master.community_member_type_name = '".ACSMsg::get_mst('community_member_type_master','D10')."'";
		$sql .= "  AND community_member.user_community_id = user_info.user_community_id";
		$sql .= "  AND user_info.user_community_id = USER_COMMUNITY.community_id";
		// 氏名
		$sql .= "  AND user_info.user_community_id = USER_NAME_C.community_id";
		$sql .= "  AND USER_NAME_C.contents_type_code = USER_NAME_CTM.contents_type_code";
		$sql .= "  AND USER_NAME_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D01')."'";
		// 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";
		$sql .= " ORDER BY user_info.user_community_id ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}


	/**
	 * コミュニティメンバを削除する
	 *
	 * @param  $community_id
	 * @param  $user_community_id_array
	 * @return true / false
	 */
	static function delete_community_member ($community_id, $user_community_id_array) {
		$target_user_community_id_str = implode(", ", $user_community_id_array);

		$sql  = "DELETE";
		$sql .= " FROM community_member";
		$sql .= " WHERE community_id = '$community_id'";
		$sql .=   " AND user_community_id IN ($target_user_community_id_str)";

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * コミュニティを削除する
	 *   delete_flag に true をセット
	 *
	 * @param  $community_id
	 * @return true / false
	 */
	static function delete_community ($community_id) {
		$sql  = "UPDATE community";
		$sql .= " SET";
		$sql .= " delete_flag = 't'";
		$sql .= " WHERE community_id = '$community_id'";

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * コミュニティ一覧を取得する
	 *
	 * @param $user_community_id アクセス者のユーザコミュニティID (非公開コミュニティも検索結果に含めるため)
	 * @return コミュニティ情報の配列
	 */
	static function get_community_row_array($user_community_id = '') {
		$sql  = "SELECT *";
		$sql .= " FROM community, community_type_master,";
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM,";  // 全体
		$sql .= "  open_level_master as SELF_OLM";                          // 全体のopen_level_master
		$sql .= " WHERE community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D40')."'";

		// 削除フラグOFF
		$sql .= " AND community.delete_flag != 't'";

		$sql .= " AND community.community_id = SELF_C.community_id";
		$sql .= " AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= " AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= " AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		$sql .= " AND (";
		$sql .= "       (";
		// 非公開コミュニティでない
		$sql .= "         SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";
		$sql .= "       )";
		if ($user_community_id) {
			$sql .= "   OR (";
			$sql .= "     SELF_OLM.open_level_name = '".ACSMsg::get_mst('open_level_master','D03')."'";
			$sql .= "     AND acs_is_community_member('$user_community_id', community.community_id)";
			$sql .= "   )";
		}
		$sql .= " )";

		$sql .= " ORDER BY community.community_name ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * コミュニティを検索する
	 *
	 * @param $user_community_id アクセス者のユーザコミュニティID (非公開コミュニティも検索結果に含めるため)
	 * @param $form 検索条件
	 * @return コミュニティ情報の配列
	 */
	static function search_community_row_array($user_community_id = '', $form = array()) {
		$sql  = "SELECT *";
		if ($form['order'] == 'community_member_num') {
			$sql .= ", acs_get_community_member_num(community.community_id) as community_member_num";
		}
		$sql .= " FROM community, community_type_master,";
		$sql .= "  category_master,";                                       // カテゴリ
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM,";  // 全体
		$sql .= "  open_level_master as SELF_OLM,";                         // 全体のopen_level_master
		$sql .= "  contents as COMMUNITY_PROFILE_C, contents_type_master as COMMUNITY_PROFILE_CTM";  // コミュニティプロフィール(概要)
		$sql .= " WHERE community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D40')."'";

		// 削除フラグOFF
		$sql .= " AND community.delete_flag != 't'";

		$sql .= " AND community.community_id = SELF_C.community_id";
		$sql .= " AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= " AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= " AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		$sql .= " AND (";
		$sql .= "       (";
		// 非公開コミュニティでない
		$sql .= "         SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";
		$sql .= "       )";
		if ($user_community_id) {
			$sql .= "   OR (";
			$sql .= "     SELF_OLM.open_level_name = '".ACSMsg::get_mst('open_level_master','D03')."'";
			$sql .= "     AND acs_is_community_member('$user_community_id', community.community_id)";
			$sql .= "   )";
		}
		$sql .= " )";

		// コミュニティプロフィール
		$sql .= " AND community.community_id = COMMUNITY_PROFILE_C.community_id";
		$sql .= " AND COMMUNITY_PROFILE_C.contents_type_code = COMMUNITY_PROFILE_CTM.contents_type_code";
		$sql .= " AND COMMUNITY_PROFILE_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D07')."'";

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
					$sub_where_sql .= " community.community_name ILIKE '%$query%'";
					$sub_where_sql .= "  OR COMMUNITY_PROFILE_C.contents_value ILIKE '%$query%'";
					$sub_where_sql .= "  OR category_master.category_name ILIKE '%$query%'";
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

		// カテゴリ
		$sql .= " AND community.category_code = category_master.category_code";
		if ($form['category_code']) {
			$sql .= " AND community.category_code = '" . pg_escape_string($form['category_code']) . "'";
		}

		// 参加資格
		if ($form['admission_flag'] == 't' || $form['admission_flag'] == 'f') {
			$sql .= " AND community.admission_flag = '$form[admission_flag]'";
		}

		// ORDER
		if ($form['order'] == 'new') {
			$sql .= " ORDER BY community.register_date DESC";
		} elseif ($form['order'] == 'community_member_num') {
			$sql .= " ORDER BY community_member_num DESC, community.community_name ASC";
		} else {
			$sql .= " ORDER BY community.community_name ASC";
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 新着コミュニティ情報一覧を取得する
	 *
	 * @return 新着コミュニティ情報の配列 (連想配列の配列)
	 */
	static function get_new_community_row_array() {
		$limit = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');

		$sql  = "SELECT *";
		$sql .= " FROM community, community_type_master,";
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM,";  // 全体
		$sql .= "  open_level_master as SELF_OLM";                          // 全体のopen_level_master
		$sql .= " WHERE community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D40')."'";

		// 削除フラグOFF
		$sql .= " AND community.delete_flag != 't'";
		// 非公開コミュニティでない
		$sql .= " AND community.community_id = SELF_C.community_id";
		$sql .= " AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= " AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= " AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		$sql .= " AND SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";

		$sql .= " ORDER BY community.register_date DESC";
		// LIMIT
		$sql .= " LIMIT $limit";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * コミュニティランキング情報を取得する
	 *
	 * @return コミュニティランキング情報の一覧
	 */
	static function get_ranking_community_row_array() {
		$limit = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
		$term = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D05'), 'COMMUNITY_RANKING_COUNT_TERM');

		$sql  = "SELECT community.community_id, community.community_name, community.file_id,";
		$sql .= " (";
		$sql .= "  acs_get_bbs_score_by_c_id(community.community_id, '$term')";
		$sql .= "  + acs_get_bbs_res_score_by_c_id(community.community_id, '$term')";
		$sql .= "  + acs_get_file_info_score(community.community_id, '$term')";
		$sql .= " ) as ranking_score";

		$sql .= " FROM (community LEFT OUTER JOIN community_image_file USING(community_id)) as community, community_type_master,";
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM, open_level_master as SELF_OLM"; // コミュニティ全体

		$sql .= " WHERE community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D40')."'";

		// 削除フラグOFF
		$sql .= "  AND community.delete_flag != 't'";
		// 全体=非公開を除く
		$sql .= "  AND community.community_id = SELF_C.community_id";
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
	 * コミュニティの管理者を登録する
	 *
	 * @param $form コミュニティメンバ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_community_admin($acs_user_info_row, $form) {
		// コミュニティメンバ種別マスタ
		$community_member_type_master_array = ACSDB::get_master_array('community_member_type');
		$community_admin_type_code = array_search(ACSMsg::get_mst('community_member_type_master','D10'), $community_member_type_master_array);
		$community_member_type_code = array_search(ACSMsg::get_mst('community_member_type_master','D20'), $community_member_type_master_array);

		$community_id = pg_escape_string($form['community_id']);

		// BEGIN
		ACSDB::_do_query("BEGIN");

		// アクセス者以外をコミュニティメンバとして設定
		$sql  = "UPDATE community_member";
		$sql .= " SET community_member_type_code = '$community_member_type_code'";
		$sql .= " WHERE user_community_id != '$acs_user_info_row[user_community_id]'";
		$sql .= "  AND community_id = '$community_id'";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		if (is_array($form['user_community_id_array'])) {
			// 指定されたユーザコミュニティIDをコミュニティ管理者として設定
			foreach ($form['user_community_id_array'] as $user_community_id) {
				$sql  = "UPDATE community_member";
				$sql .= " SET community_member_type_code = '$community_admin_type_code'";
				$sql .= " WHERE user_community_id = '$user_community_id'";
				$sql .= "  AND community_id = '$community_id'";
				$ret = ACSDB::_do_query($sql);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * コミュニティのメンバ(非管理者)を登録する
	 *
	 * @param $form コミュニティメンバ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_community_admin($form) {
		// コミュニティメンバ種別マスタ
		$community_member_type_master_array = ACSDB::get_master_array('community_member_type');

		// $form['community_id']      参加対象のコミュニティ
		// $form['user_community_id'] 参加するユーザコミュニティID
		$form['community_member_type_code'] = array_search(ACSMsg::get_mst('community_member_type_master','D10'), $community_member_type_master_array);
		$ret = ACSCommunityMemberModel::insert_community_member($form);

		return $ret;
	}

	/**
	 * コミュニティのメンバ(非管理者)を登録する
	 *
	 * @param $form コミュニティメンバ情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_community_member($form) {
		// コミュニティメンバ種別マスタ
		$community_member_type_master_array = ACSDB::get_master_array('community_member_type');

		// $form['community_id']      参加対象のコミュニティ
		// $form['user_community_id'] 参加するユーザコミュニティID
		$form['community_member_type_code'] = array_search(ACSMsg::get_mst('community_member_type_master','D20'), $community_member_type_master_array);
		$ret = ACSCommunityMemberModel::insert_community_member($form);

		return $ret;
	}


	// 汎用系 //

	/**
	 * ユーザがコミュニティのメンバーかどうか
	 *
	 * @param ユーザコミュニティID
	 * @param コミュニティID
	 * @return true / false
	 */
	static function is_community_member($user_community_id, $community_id) {
		$sql  = "SELECT count(*)";
		$sql .= " FROM community, community_member";
		$sql .= " WHERE community.community_id = '" . pg_escape_string($community_id) . "'";
		$sql .= "  AND community.community_id = community_member.community_id";
		$sql .= "  AND community_member.user_community_id = '" . pg_escape_string($user_community_id) . "'";

		$value = ACSDB::_get_value($sql);
		return intval($value);
	}

	/**
	 * ユーザがコミュニティの管理者かどうか
	 *
	 * @param DB接続リソース
	 * @param ユーザコミュニティID
	 * @param コミュニティID
	 * @return true / false
	 */
	static function is_community_admin($user_community_id, $community_id) {
		$sql  = "SELECT count(*)";
		$sql .= " FROM community, community_member, community_member_type_master";
		$sql .= " WHERE community.community_id = '" . pg_escape_string($community_id) . "'";
		$sql .= "  AND community.community_id = community_member.community_id";
		$sql .= "  AND community_member.user_community_id = '" . pg_escape_string($user_community_id) . "'";
		$sql .= "  AND community_member.community_member_type_code = community_member_type_master.community_member_type_code";
		$sql .= "  AND community_member_type_master.community_member_type_name = '".ACSMsg::get_mst('community_member_type_master','D10')."'";

		$value = ACSDB::_get_value($sql);
		return intval($value);
	}

	/**
	 * コミュニティのカテゴリグループ一覧を取得する
	 *
	 * @return カテゴリグループ一覧 (連想配列の配列)
	 */
	static function get_category_group_master_row_array() {
		$sql  = "SELECT *";
		$sql .= " FROM category_group_master";
		$sql .= " ORDER BY category_group_code ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * コミュニティのカテゴリグループを指定してカテゴリ一覧を取得する
	 *
	 * @param $category_group_code カテゴリグループコード
	 * @return カテゴリ一覧 (連想配列の配列)
	 */
	static function get_category_master_row_array_by_category_group_code($category_group_code) {
		$category_group_code = pg_escape_string($category_group_code);

		$sql  = "SELECT category_code, category_name";
		$sql .= " FROM category_master";
		$sql .= " WHERE category_master.category_group_code = '$category_group_code'";
		$sql .= " ORDER BY category_master.category_code ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * コミュニティのカテゴリコードごとの件数を取得する
	 *
	 * @return カテゴリコードをキーにした件数の配列
	 */
	static function get_category_code_community_num_array() {
		$ret_array = array();

		$sql  = "SELECT community.category_code, count(*) as community_num";
		$sql .= " FROM community, category_master";
		$sql .= " WHERE community.category_code = category_master.category_code";
		$sql .= " GROUP BY community.category_code";
		$sql .= " ORDER BY community.category_code";
		$row_array = ACSDB::_get_row_array($sql);

		foreach ($row_array as $row) {
			$ret_array[$row['category_code']] = $row['community_num'];
		}

		return $ret_array;
	}


	/*
	 * コミュニティ参加の承認が必要かどうか
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @param $community_id コミュニティID
	 * @return 承認が必要(true) / 自由参加(false)
	 */
	static function is_admission_required_for_join_community($user_community_id, $community_id) {
		// 承認必要フラグ
		$is_admission_required = true;

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		// 自由参加可能コミュニティ
		$join_trusted_community_row_array = ACSCommunity::get_join_trusted_community_row_array($community_id);

		// マイコミュニティ
		$my_community_row_array = ACSUser::get_community_row_array($user_community_id);
		$my_community_id_array = array();
		foreach ($my_community_row_array as $my_community_row) {
			array_push($my_community_id_array, $my_community_row['community_id']);
		}

		if (!ACSLib::get_boolean($community_row['admission_flag'])) {
			// 承認フラグがfなら自由参加
			$is_admission_required = false;
		} else {
			// マイコミュニティが自由参加可能コミュニティ一覧に含まれるか
			foreach ($join_trusted_community_row_array as $join_trusted_community_row) {
				//if (array_search($join_trusted_community_row['community_id'], $my_community_id_array)) {
				if (in_array($join_trusted_community_row['community_id'], $my_community_id_array)) {
					$is_admission_required = false;
					break;
				}
			}
		}

		return $is_admission_required;
	}


	/**
	 * デフォルトの URL を返す
	 *
	 * @param view_mode    表示モード : NULL, thumb, rss
	 */
	static function get_default_image_url ($view_mode) {
		if ($view_mode == 'thumb') {
			return ACS_DEFAULT_COMMUNITY_IMAGE_FILE_THUMB;
		} else {
			return ACS_DEFAULT_COMMUNITY_IMAGE_FILE;
		}
	}

	/**
	 * image_urlを加工する
	 *
	 * @param community_id
	 * @param view_mode    表示モード : NULL, thumb, rss
	 */
	static function get_image_url($community_id, $view_mode = '') {
		$file_id = ACSCommunityImageFileModel::get_file_id($community_id);

		if ($file_id != '') {
			$image_url  = SCRIPT_PATH . '?';
			$image_url .= MODULE_ACCESSOR . '=Community';
			$image_url .= '&' . ACTION_ACCESSOR . '=CommunityImage';
			$image_url .= '&community_id=' . $community_id;
			$image_url .= '&mode=' . $view_mode;
		} else {
			$image_url = ACSCommunity::get_default_image_url($view_mode);
		}

		return $image_url;
	}
	
	/**
	 * コミュニティ情報を複数取得する
	 *
	 * @param $community_id_array コミュニティID配列
	 * @return コミュニティ情報の配列
	 */
	static function get_each_community_row_array($community_row_array) {
		if (count($community_row_array)) {
			$community_id_csv = implode(',', $community_row_array);
		} else {
			$community_id_csv = 'null';
		}
		$sql  = "SELECT *";
		$sql .= " FROM ((community LEFT OUTER JOIN category_master ON community.category_code = category_master.category_code)";
		$sql .= "  LEFT OUTER JOIN community_image_file USING(community_id)) as JOINED_COMMUNITY,"; 
		$sql .= "  community_type_master";
		$sql .= " WHERE JOINED_COMMUNITY.community_id IN( " .$community_id_csv ." )";
		$sql .= "  AND JOINED_COMMUNITY.community_type_code = community_type_master.community_type_code";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * コミュニティを更新する
	 * 2006/3/9
	 * @param $form コミュニティ情報
	 * return 成功(コミュニティID) / 失敗(false)
	 */
	static function update_community($form) {
		// コミュニティ種別マスタ
		$community_type_master_array = ACSDB::get_master_array('community_type');
		$community_type_code = array_search(ACSMsg::get_mst('community_type_master','D40'), $community_type_master_array);
		// コンテンツ種別マスタ
		$contents_type_master_array = ACSDB::get_master_array('contents_type');
		// コミュニティメンバ種別マスタ
		$community_member_type_master_array = ACSDB::get_master_array('community_member_type');
		$community_id_seq = $form['community_id'];
		$org_form = $form;

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		ACSDB::_do_query("BEGIN");

		// (1) コミュニティ (community)
		$admission_flag = ACSLib::get_pg_boolean($org_form['admission_flag']);
		$sql  = "UPDATE community";
		$sql .= " SET community_name = $form[community_name],";
		$sql .= " category_code = $form[category_code],";
		$sql .= " admission_flag = $form[admission_flag]";
		$sql .= " WHERE community_id = $community_id_seq";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (2) コミュニティプロフィール
		$contents_form = array();
		$contents_form['community_id'] = $community_id_seq;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D07'), $contents_type_master_array);
		$contents_form['contents_value'] = $org_form['community_profile'];
		$contents_form['open_level_code'] = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D07'));
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		

		// (3) 参加資格 (join_trusted_community)
		$join_trusted_community_form = array();
		$join_trusted_community_form['community_id'] = $community_id_seq;
		// join_trusted_community 前準備　旧データの一括削除
		$ret = ACSCommunity::delete_join_trusted_community($join_trusted_community_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		//登録
		if (is_array($org_form['join_trusted_community_id_array'])) {
			foreach ($org_form['join_trusted_community_id_array'] as $trusted_community_id) {
				$join_trusted_community_form['trusted_community_id'] = $trusted_community_id;
				$ret = ACSCommunity::set_join_trusted_community($join_trusted_community_form);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// (4) 公開範囲 電子掲示板
		// contents
		$contents_form = array();
		$contents_form['community_id'] = $community_id_seq;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D41'), $contents_type_master_array);
		$contents_form['contents_value'] = '';
		$contents_form['open_level_code'] = $org_form['bbs_open_level_code'];
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// contents_trusted_community
		if (is_array($org_form['bbs_trusted_community_id_array'])) {
			$contents_trusted_community_form = array();
			$contents_trusted_community_form['community_id'] = $community_id_seq;
			$contents_trusted_community_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D41'), $contents_type_master_array);
			$contents_trusted_community_form['open_level_code'] = $org_form['bbs_open_level_code'];
		// contents_trusted_community 前準備　旧データの一括削除
			$ret = ACSCommunity::update_contents_trusted_community($contents_trusted_community_form);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		//登録
			foreach ($org_form['bbs_trusted_community_id_array'] as $trusted_community_id) {
				$contents_trusted_community_form['trusted_community_id'] = $trusted_community_id;
				$ret = ACSCommunity::set_contents_trusted_community($contents_trusted_community_form);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// (5) 公開範囲 コミュニティフォルダ (cotents)
		// contents
		$contents_form = array();
		$contents_form['community_id'] = $community_id_seq;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D31'), $contents_type_master_array);
		$contents_form['contents_value'] = '';
		$contents_form['open_level_code'] = $org_form['community_folder_open_level_code'];
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// contents_trusted_community
		if (is_array($org_form['community_folder_trusted_community_id_array'])) {
			$contents_trusted_community_form = array();
			$contents_trusted_community_form['community_id'] = $community_id_seq;
			$contents_trusted_community_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D31'), $contents_type_master_array);
			$contents_trusted_community_form['open_level_code'] = $org_form['community_folder_open_level_code'];
		// contents_trusted_community 前準備　旧データの一括削除
			$ret = ACSCommunity::update_contents_trusted_community($contents_trusted_community_form);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		// 登録
			foreach ($org_form['community_folder_trusted_community_id_array'] as $trusted_community_id) {
				$contents_trusted_community_form['trusted_community_id'] = $trusted_community_id;
				$ret = ACSCommunity::set_contents_trusted_community($contents_trusted_community_form);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// (6) 公開範囲 全体
		// contents
		$contents_form = array();
		$contents_form['community_id'] = $community_id_seq;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D00'), $contents_type_master_array);
		$contents_form['contents_value'] = '';
		$contents_form['open_level_code'] = $org_form['self_open_level_code'];
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// (7) コミュニティML
		// contents
		// コミュニティMLアドレス
		if ($org_form['community_ml_address']) {
			$contents_form = array();
			$contents_form['community_id'] = $community_id_seq;
			$contents_form['contents_type_code'] = 
					array_search(ACSMsg::get_mst('contents_type_master','D61'), 
					$contents_type_master_array);
			$contents_form['contents_value'] = $org_form['community_ml_address'];
			$contents_form['open_level_code'] = 
					ACSAccessControl::get_default_open_level_code(
						ACSMsg::get_mst('community_type_master','D40'), 
						ACSMsg::get_mst('contents_type_master','D61'));
			$ret = ACSCommunity::set_contents($contents_form);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
			// コミュニティMLステータス
			$contents_form = array();
			$contents_form['community_id'] = $community_id_seq;
			$contents_form['contents_type_code'] = 
					array_search(ACSMsg::get_mst('contents_type_master','D62'), 
					$contents_type_master_array);
			$contents_form['contents_value'] = 'QUEUE';
			$contents_form['open_level_code'] = 
					ACSAccessControl::get_default_open_level_code(
						ACSMsg::get_mst('community_type_master','D40'), 
						ACSMsg::get_mst('contents_type_master','D62'));
			$ret = ACSCommunity::set_contents($contents_form);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $community_id_seq;
	}

	/**
	 * MLアドレスの存在チェック
	 * @param $ml_addr メールアドレス
	 * return boolean true...既に存在/false...存在しない
	 */
	static function is_exists_ml_addr($ml_addr) {
		$sql  = "SELECT count(*) AS cnt FROM contents " .
				"WHERE contents_type_code = '61' " .
				"AND contents_value = '" . pg_escape_string($ml_addr) ."'";
		$row = ACSDB::_get_row($sql);
		return ($row['cnt']>0 ? TRUE : FALSE);
	}
}
?>
