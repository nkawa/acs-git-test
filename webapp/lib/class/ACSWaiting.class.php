<?php
// $Id: ACSWaiting.class.php,v 1.12 2009/06/19 09:50:00 acs Exp $


/*
 * 待機
 */
class ACSWaiting {

	/**
	 * 待機を取得する
	 *
	 * @param $waiting_id 待機ID
	 * @return 待機情報 (連想配列)
	 */
	static function get_waiting_row($waiting_id) {
		$waiting_id = pg_escape_string($waiting_id);

		$sql  = "SELECT *";
		$sql .= " FROM waiting, waiting_type_master, waiting_status_master";
		$sql .= " WHERE waiting.waiting_id = '$waiting_id'";
		$sql .= "  AND waiting.waiting_type_code = waiting_type_master.waiting_type_code";
		$sql .= "  AND waiting.waiting_status_code = waiting_status_master.waiting_status_code";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * 待機コミュニティメンバ情報を取得する
	 *
	 * @param $community_id 対象のコミュニティIDまたはユーザコミュニティID (承認側)
	 * @param 待機種別名
	 * @param 待機状態名
	 * @return 待機情報 (連想配列の配列)
	 */
	static function get_waiting_row_array($community_id, $waiting_type_name, $waiting_status_name) {
		$community_id = pg_escape_string($community_id);
		$waiting_type_name = pg_escape_string($waiting_type_name);
		$waiting_status_name = pg_escape_string($waiting_status_name);

		$sql  = "SELECT *";
		$sql .= " FROM waiting, community, waiting_type_master, waiting_status_master";
		$sql .= " WHERE waiting.community_id = '$community_id'";
		$sql .= "  AND waiting.waiting_community_id = community.community_id";
		$sql .= "  AND waiting.waiting_type_code = waiting_type_master.waiting_type_code";
		$sql .= "  AND waiting_type_master.waiting_type_name = '$waiting_type_name'";
		$sql .= "  AND waiting.waiting_status_code = waiting_status_master.waiting_status_code";
		$sql .= "  AND waiting_status_master.waiting_status_name = '$waiting_status_name'";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}


	/**
	 * マイフレンズ追加 承認待ち を登録する
	 *
	 * @param $user_community_id 対象のユーザコミュニティID (承認側)
	 * @param $waiting_community_id 待機コミュニティID (依頼側)
	 * @param $message メッセージ
	 * @return 成功(waiting_id) / 失敗(false)
	 */
	static function set_waiting_for_add_friends($user_community_id, $waiting_community_id, $message) {
		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		$waiting_form = array();
		$waiting_form['waiting_id'] = ACSDB::get_next_seq('waiting_id_seq');
		$waiting_form['community_id'] = $user_community_id;
		$waiting_form['waiting_community_id'] = $waiting_community_id;
		$waiting_form['waiting_type_code'] = array_search(ACSMsg::get_mst('waiting_type_master','D10'), $waiting_type_master_array);
		$waiting_form['waiting_status_code'] = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		$waiting_form['message'] = $message;
		$waiting_form['entry_user_community_id'] = $waiting_community_id;
		$ret = ACSWaitingModel::insert_waiting($waiting_form);

		if ($ret) {
			$ret = $waiting_form['waiting_id'];
		}
		return $ret;
	}

	/**
	 * コミュニティ参加 承認待ち を登録する
	 *
	 * @param $user_community_id 対象のコミュニティID (承認側)
	 * @param $waiting_community_id 待機コミュニティID (依頼側)
	 * @param $message メッセージ
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_waiting_for_join_community($community_id, $waiting_community_id, $message) {
		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		$waiting_form = array();
		$waiting_form['waiting_id'] = ACSDB::get_next_seq('waiting_id_seq');
		$waiting_form['community_id'] = $community_id;
		$waiting_form['waiting_community_id'] = $waiting_community_id;
		$waiting_form['waiting_type_code'] = array_search(ACSMsg::get_mst('waiting_type_master','D20'), $waiting_type_master_array);
		$waiting_form['waiting_status_code'] = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		$waiting_form['message'] = $message;
		$waiting_form['entry_user_community_id'] = $waiting_community_id;
		$ret = ACSWaitingModel::insert_waiting($waiting_form);

		if ($ret) {
			$ret = $waiting_form['waiting_id'];
		}
		return $ret;
	}

	/**
	 * コミュニティ招待 承認待ち を登録する
	 *
	 * @param $user_community_id 対象のコミュニティID (承認側)
	 * @param $waiting_community_id 待機コミュニティID (依頼側)
	 * @param $entry_user_community_id 登録したユーザコミュニティID
	 * @param $message メッセージ
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_waiting_for_invite_to_community($community_id, $waiting_community_id, $entry_user_community_id, $message) {
		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		$waiting_form = array();
		$waiting_form['waiting_id'] = ACSDB::get_next_seq('waiting_id_seq');
		$waiting_form['community_id'] = $community_id;
		$waiting_form['waiting_community_id'] = $waiting_community_id;
		$waiting_form['waiting_type_code'] = array_search(ACSMsg::get_mst('waiting_type_master','D30'), $waiting_type_master_array);
		$waiting_form['waiting_status_code'] = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		$waiting_form['message'] = $message;
		$waiting_form['entry_user_community_id'] = $entry_user_community_id;
		$ret = ACSWaitingModel::insert_waiting($waiting_form);

		if ($ret) {
			$ret = $waiting_form['waiting_id'];
		}
		return $ret;
	}

	/**
	 * 親コミュニティ追加 承認待ち を登録する
	 *
	 * @param $user_community_id 対象のコミュニティID (承認側)
	 * @param $waiting_community_id 待機コミュニティID (依頼側)
	 * @param $entry_user_community_id 登録したユーザコミュニティID
	 * @param $message メッセージ
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_waiting_for_parent_community_link($community_id, $waiting_community_id, $entry_user_community_id, $message) {
		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		$waiting_form = array();
		$waiting_form['waiting_id'] = ACSDB::get_next_seq('waiting_id_seq');
		$waiting_form['community_id'] = $community_id;
		$waiting_form['waiting_community_id'] = $waiting_community_id;
		$waiting_form['waiting_type_code'] = array_search(ACSMsg::get_mst('waiting_type_master','D40'), $waiting_type_master_array);
		$waiting_form['waiting_status_code'] = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		$waiting_form['message'] = $message;
		$waiting_form['entry_user_community_id'] = $entry_user_community_id;
		$ret = ACSWaitingModel::insert_waiting($waiting_form);

		if ($ret) {
			$ret = $waiting_form['waiting_id'];
		}
		return $ret;
	}

	/**
	 * 親コミュニティ追加 承認待ち を登録する
	 *
	 * @param $user_community_id 対象のコミュニティID (承認側)
	 * @param $waiting_community_id 待機コミュニティID (依頼側)
	 * @param $entry_user_community_id 登録したユーザコミュニティID
	 * @param $message メッセージ
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_waiting_for_sub_community_link($community_id, $waiting_community_id, $entry_user_community_id, $message) {
		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		$waiting_form = array();
		$waiting_form['waiting_id'] = ACSDB::get_next_seq('waiting_id_seq');
		$waiting_form['community_id'] = $community_id;
		$waiting_form['waiting_community_id'] = $waiting_community_id;
		$waiting_form['waiting_type_code'] = array_search(ACSMsg::get_mst('waiting_type_master','D50'), $waiting_type_master_array);
		$waiting_form['waiting_status_code'] = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		$waiting_form['message'] = $message;
		$waiting_form['entry_user_community_id'] = $entry_user_community_id;
		$ret = ACSWaitingModel::insert_waiting($waiting_form);

		if ($ret) {
			$ret = $waiting_form['waiting_id'];
		}
		return $ret;
	}

	/**
	 * 待機状態コードを更新する
	 *
	 * @param $waiting_community_id 待機コミュニティID
	 * @param $waiting_status_name 待機状態名
	 * @param $reply_message 返信メッセージ
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_waiting_waiting_status_code($waiting_id, $waiting_status_name, $complete_user_community_id, $reply_message = '') {
		$waiting_id = pg_escape_string($waiting_id);
		$complete_user_community_id = pg_escape_string($complete_user_community_id);
		$reply_message = pg_escape_string($reply_message);

		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');
		$waiting_status_code = array_search($waiting_status_name, $waiting_status_master_array);

		$sql  = "UPDATE waiting";
		$sql .= " SET";
		$sql .= " waiting_status_code = '$waiting_status_code',";
		if ($reply_message != '') {
			$sql .= " reply_message = '$reply_message',";
		}
		$sql .= " complete_user_community_id = '$complete_user_community_id',";
		$sql .= " complete_date = CURRENT_TIMESTAMP";
		$sql .= " WHERE waiting_id = '$waiting_id'";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * 関連する待機テーブルを更新する (マイフレンズ追加)
	 *
	 * @param コミュニティID
	 * @param 待機側コミュニティID
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_waiting_for_add_friends($community_id, $waiting_community_id) {
		$community_id = pg_escape_string($community_id);
		$waiting_community_id = pg_escape_string($waiting_community_id);

		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		// 「マイフレンズ追加」の待機種別コード
		$waiting_type_code_for_add_friends = array_search(ACSMsg::get_mst('waiting_type_master','D10'), $waiting_type_master_array);
		// 「承認待ち」の待機状態コード
		$waiting_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		// 「承認済み」の待機状態コード
		$complete_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D20'), $waiting_status_master_array);

		// UPDATE
		$sql  = "UPDATE waiting";
		$sql .= " SET"; 
		$sql .= "  waiting_status_code = '$complete_waiting_status_code'";
		$sql .= " WHERE waiting_type_code = '$waiting_type_code_for_add_friends'";
		$sql .= "  AND ((community_id = '$community_id' AND waiting_community_id = '$waiting_community_id')"; 
		$sql .= "       OR (community_id = '$waiting_community_id' AND waiting_community_id = '$community_id'))";  
		$sql .= "  AND waiting_status_code = '$waiting_waiting_status_code'";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * 関連する待機テーブルを更新する (コミュニティ参加)
	 *
	 * @param コミュニティID
	 * @param 待機側コミュニティID
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_waiting_for_join_community($community_id, $waiting_community_id) {
		$community_id = pg_escape_string($community_id);
		$waiting_community_id = pg_escape_string($waiting_community_id);

		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		// 「コミュニティ参加」の待機種別コード
		$waiting_type_code_for_join_community = array_search(ACSMsg::get_mst('waiting_type_master','D20'), $waiting_type_master_array);
		// 「承認待ち」の待機状態コード
		$waiting_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		// 「承認済み」の待機状態コード
		$complete_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D20'), $waiting_status_master_array);

		// UPDATE
		$sql  = "UPDATE waiting";
		$sql .= " SET"; 
		$sql .= "  waiting_status_code = '$complete_waiting_status_code'";    // 承認済み
		$sql .= " WHERE waiting_type_code = '$waiting_type_code_for_join_community'";
		$sql .= "  AND community_id = '$community_id'";
		$sql .= "  AND waiting_community_id = '$waiting_community_id'";
		$sql .= "  AND waiting_status_code = '$waiting_waiting_status_code'"; // 承認待ち

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * 関連する待機テーブルを更新する (コミュニティ招待)
	 *
	 * @param コミュニティID
	 * @param 待機側コミュニティID
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_waiting_for_invite_to_community($community_id, $waiting_community_id) {
		$community_id = pg_escape_string($community_id);
		$waiting_community_id = pg_escape_string($waiting_community_id);

		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		// 「コミュニティ招待」の待機種別コード
		$waiting_type_code_for_invite_to_community = array_search(ACSMsg::get_mst('waiting_type_master','D30'), $waiting_type_master_array);
		// 「承認待ち」の待機状態コード
		$waiting_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		// 「承認済み」の待機状態コード
		$complete_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D20'), $waiting_status_master_array);

		// UPDATE
		$sql  = "UPDATE waiting";
		$sql .= " SET"; 
		$sql .= "  waiting_status_code = '$complete_waiting_status_code'";    // 承認済み
		$sql .= " WHERE waiting_type_code = '$waiting_type_code_for_invite_to_community'";
		$sql .= "  AND community_id = '$community_id'";
		$sql .= "  AND waiting_community_id = '$waiting_community_id'";
		$sql .= "  AND waiting_status_code = '$waiting_waiting_status_code'"; // 承認待ち

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * 関連する待機テーブルを更新する (親コミュニティ追加)
	 *
	 * @param コミュニティID
	 * @param 待機側コミュニティID
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_waiting_for_parent_community_link($community_id, $waiting_community_id) {
		$community_id = pg_escape_string($community_id);
		$waiting_community_id = pg_escape_string($waiting_community_id);

		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		// 「親コミュニティ追加」の待機種別コード
		$waiting_type_code_for_parent_community_link = array_search(ACSMsg::get_mst('waiting_type_master','D40'), $waiting_type_master_array);
		// 「サブコミュニティ追加」の待機種別コード
		$waiting_type_code_for_sub_community_link = array_search(ACSMsg::get_mst('waiting_type_master','D50'), $waiting_type_master_array);
		// 「承認待ち」の待機状態コード
		$waiting_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		// 「承認済み」の待機状態コード
		$complete_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D20'), $waiting_status_master_array);

		// UPDATE
		$sql  = "UPDATE waiting";
		$sql .= " SET"; 
		$sql .= "  waiting_status_code = '$complete_waiting_status_code'";    // 承認済み
		$sql .= " WHERE (";
		$sql .= "   ("; 
		$sql .= "    waiting_type_code = '$waiting_type_code_for_parent_community_link'";
		$sql .= "    AND community_id = '$community_id'";
		$sql .= "    AND waiting_community_id = '$waiting_community_id'";
		$sql .= "   ) OR (";
		$sql .= "    waiting_type_code = '$waiting_type_code_for_sub_community_link'";
		$sql .= "    AND community_id = '$waiting_community_id'";
		$sql .= "    AND waiting_community_id = '$community_id'";
		$sql .= "   )";
		$sql .= "  )";
		$sql .= "  AND waiting_status_code = '$waiting_waiting_status_code'"; // 承認待ち

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * 関連する待機テーブルを更新する (サブコミュニティ追加)
	 *
	 * @param コミュニティID
	 * @param 待機側コミュニティID
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_waiting_for_sub_community_link($community_id, $waiting_community_id) {
		$community_id = pg_escape_string($community_id);
		$waiting_community_id = pg_escape_string($waiting_community_id);

		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		// 「親コミュニティ追加」の待機種別コード
		$waiting_type_code_for_parent_community_link = array_search(ACSMsg::get_mst('waiting_type_master','D40'), $waiting_type_master_array);
		// 「サブコミュニティ追加」の待機種別コード
		$waiting_type_code_for_sub_community_link = array_search(ACSMsg::get_mst('waiting_type_master','D50'), $waiting_type_master_array);
		// 「承認待ち」の待機状態コード
		$waiting_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);
		// 「承認済み」の待機状態コード
		$complete_waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D20'), $waiting_status_master_array);

		// UPDATE
		$sql  = "UPDATE waiting";
		$sql .= " SET"; 
		$sql .= "  waiting_status_code = '$complete_waiting_status_code'";    // 承認済み
		$sql .= " WHERE (";
		$sql .= "   ("; 
		$sql .= "    waiting_type_code = '$waiting_type_code_for_sub_community_link'";
		$sql .= "    AND community_id = '$community_id'";
		$sql .= "    AND waiting_community_id = '$waiting_community_id'";
		$sql .= "   ) OR (";
		$sql .= "    waiting_type_code = '$waiting_type_code_for_parent_community_link'";
		$sql .= "    AND community_id = '$waiting_community_id'";
		$sql .= "    AND waiting_community_id = '$community_id'";
		$sql .= "   )";
		$sql .= "  )";
		$sql .= "  AND community_id = '$community_id'";
		$sql .= "  AND waiting_community_id = '$waiting_community_id'"; 
		$sql .= "  AND waiting_status_code = '$waiting_waiting_status_code'"; // 承認待ち

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}


	/**
	 * 依頼通知メールを送信する
	 *
	 * @param $waiting_id 待機ID
	 * @param $return 成功(true) / 失敗(false)
	 * @return 
	 */
	static function send_admission_request_notify_mail($waiting_id) {
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');
		// 「承認待ち」の待機状態コード
		$waiting_status_code = array_search(ACSMsg::get_mst('waiting_status_master','D10'), $waiting_status_master_array);

		// 待機情報
		$waiting_row = ACSWaiting::get_waiting_row($waiting_id);

		$system_group = ACSMsg::get_mst('system_config_group','D01');


		// システムURL
		//$system_base_url = ACSSystemConfig::get_keyword_value('システム', 'SYSTEM_BASE_URL');
		$system_base_url = ACSSystemConfig::get_keyword_value($system_group, 'SYSTEM_BASE_URL');
		// システムログインURL
		//$system_base_login_url = ACSSystemConfig::get_keyword_value('システム', 'SYSTEM_BASE_LOGIN_URL');
		$system_base_login_url = ACSSystemConfig::get_keyword_value($system_group, 'SYSTEM_BASE_LOGIN_URL');

		// システムのメールアドレス (From:)
		$system_mail_addr = ACSSystemConfig::get_keyword_value($system_group, 'SYSTEM_MAIL_ADDR');

		// 利用者の言語設定を一時的に保存
		$org_lang = ACSMsg::get_lang();

		// 各言語のタイトルを取得
		$mail_titles = array();
		foreach (ACSMsg::get_lang_list_array() as $lang_key => $lang_name) {
			ACSMsg::set_lang($lang_key);
			$mail_titles[$lang_key] = 
					ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','MTL%03d')."\n";
		}

		// 言語を一旦元に戻す
		ACSMsg::set_lang($org_lang);

		if ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D10')) {
			// 依頼側のユーザ情報
			$user_info_row = ACSUser::get_user_profile_row($waiting_row['waiting_community_id']);
			// 依頼される側のユーザ情報
			$target_user_info_row = ACSUser::get_user_profile_row($waiting_row['community_id']);

			// 待機承認URL
			$waiting_url  = $system_base_login_url . SCRIPT_PATH;
			$waiting_url .= "?" . MODULE_ACCESSOR . "=User";
			$waiting_url .= "&" . ACTION_ACCESSOR . "=WaitingList";
			$waiting_url .= "&id={$waiting_row['community_id']}";
			$waiting_url .= "&waiting_type_code={$waiting_row['waiting_type_code']}";
			$waiting_url .= "&waiting_status_code={$waiting_status_code}";

			// メール本文
			$target_lang = ACSMsg::get_mail_lang_by_inforow($target_user_info_row);

			// 一旦依頼される側の言語に設定する
			ACSMsg::set_lang($target_lang);

			$body = $mail_titles[$target_lang];
			$body .= ACSMsg::get_tag_replace( 
					ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','ADF%03d'),
					array(
						"{TARGET_USER_NAME}"	=> $target_user_info_row['user_name'],
						"{USER_NAME}"			=> $user_info_row['user_name'],
						"{USER_COMMUNITY_NAME}"	=> $user_info_row['community_name'],
						"{MESSAGE}"				=> trim($waiting_row['message']),
						"{WAITING_URL}"			=> $waiting_url,
						"{SYSTEM_BASE_URL}"		=> $system_base_url
					)
			);
			$subject = ACSMsg::get_mdmsg(__FILE__,'M002');

			// 言語を元に戻す
			ACSMsg::set_lang($org_lang);

			$ret = ACSLib::send_mail($system_mail_addr, 
					$target_user_info_row['mail_addr'], null, $subject, $body);

		} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D20')) {
			// 依頼側のユーザ情報
			$user_info_row = ACSUser::get_user_profile_row($waiting_row['waiting_community_id']);
			// 依頼される側のコミュニティ情報
			$target_community_row = ACSCommunity::get_community_row($waiting_row['community_id']);

			// コミュニティ管理者情報の配列
			$community_admin_user_info_row_array = ACSCommunity::get_community_admin_user_info_row_array($target_community_row['community_id']);

			// 待機承認URL
			$waiting_url  = $system_base_login_url . SCRIPT_PATH;
			$waiting_url .= "?" . MODULE_ACCESSOR . "=Community";
			$waiting_url .= "&" . ACTION_ACCESSOR . "=WaitingList";
			$waiting_url .= "&community_id={$waiting_row['community_id']}";
			$waiting_url .= "&waiting_type_code={$waiting_row['waiting_type_code']}";
			$waiting_url .= "&waiting_status_code={$waiting_status_code}";

			foreach ($community_admin_user_info_row_array as $community_admin_user_info_row) {

				$community_admin_user_info_row = ACSUser::get_user_profile_row(
						$community_admin_user_info_row['user_community_id']);

				$target_lang = ACSMsg::get_mail_lang_by_inforow($community_admin_user_info_row);

				// 一旦依頼される側の言語に設定する
				ACSMsg::set_lang($target_lang);

				$body = $mail_titles[$target_lang];
				$body .= ACSMsg::get_tag_replace( 
						ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','JCM%03d'),
						array(
							"{TARGET_COMMUNITY_NAME}"	=> $target_community_row['community_name'],
							"{USER_NAME}"				=> $user_info_row['user_name'],
							"{USER_COMMUNITY_NAME}"		=> $user_info_row['community_name'],
							"{MESSAGE}"					=> trim($waiting_row['message']),
							"{WAITING_URL}"				=> $waiting_url,
							"{SYSTEM_BASE_URL}"			=> $system_base_url
						)
				);
				$subject = ACSMsg::get_mdmsg(__FILE__,'M003');

				$ret = ACSLib::send_mail($system_mail_addr, 
						$community_admin_user_info_row['mail_addr'], null, $subject, $body);
			}
			// 言語を元に戻す
			ACSMsg::set_lang($org_lang);

		} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D30')) {
			// 招待されたコミュニティ情報
			$community_row = ACSCommunity::get_community_row($waiting_row['waiting_community_id']);
			// 依頼側のユーザ情報
			$user_info_row = ACSUser::get_user_profile_row($waiting_row['entry_user_community_id']);
			// 依頼される側のユーザ情報
			$target_user_info_row = ACSUser::get_user_profile_row($waiting_row['community_id']);

			// 待機承認URL
			$waiting_url  = $system_base_login_url . SCRIPT_PATH;
			$waiting_url .= "?" . MODULE_ACCESSOR . "=User";
			$waiting_url .= "&" . ACTION_ACCESSOR . "=WaitingList";
			$waiting_url .= "&id={$waiting_row['community_id']}";
			$waiting_url .= "&waiting_type_code={$waiting_row['waiting_type_code']}";
			$waiting_url .= "&waiting_status_code={$waiting_status_code}";

			// コミュニティトップページURL
			$community_top_page_url  = $system_base_login_url . SCRIPT_PATH;
			$community_top_page_url .= "?" . MODULE_ACCESSOR . "=Community";
			$community_top_page_url .= "&" . ACTION_ACCESSOR . "=" . DEFAULT_ACTION;
			$community_top_page_url .= "&community_id=" . $community_row['community_id'];

			$target_lang = ACSMsg::get_mail_lang_by_inforow($target_user_info_row);

			// 一旦依頼される側の言語に設定する
			ACSMsg::set_lang($target_lang);

			$body = $mail_titles[$target_lang];
			$body .= ACSMsg::get_tag_replace( 
					ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','ICM%03d'),
					array(
						"{TARGET_USER_NAME}"		=> $target_user_info_row['user_name'],
						"{USER_NAME}"				=> $user_info_row['user_name'],
						"{USER_COMMUNITY_NAME}"		=> $user_info_row['community_name'],
						"{COMMUNITY_NAME}"			=> $community_row['community_name'],
						"{COMMUNITY_URL}"			=> $community_top_page_url,
						"{MESSAGE}"					=> trim($waiting_row['message']),
						"{WAITING_URL}"				=> $waiting_url,
						"{SYSTEM_BASE_URL}"			=> $system_base_url
					)
			);
			$subject = ACSMsg::get_mdmsg(__FILE__,'M004');

			// 言語を元に戻す
			ACSMsg::set_lang($org_lang);

			$ret = ACSLib::send_mail($system_mail_addr, 
					$target_user_info_row['mail_addr'], null, $subject, $body);

		} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D40') || $waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D50')) {
			// 依頼側のコミュニティ情報
			$community_row = ACSCommunity::get_community_row($waiting_row['waiting_community_id']);
			// 依頼側のユーザ情報
			$user_info_row = ACSUser::get_user_profile_row($waiting_row['entry_user_community_id']);

			// 依頼される側のコミュニティ情報
			$target_community_row = ACSCommunity::get_community_row($waiting_row['community_id']);
			// コミュニティ管理者情報の配列
			$community_admin_user_info_row_array = ACSCommunity::get_community_admin_user_info_row_array($target_community_row['community_id']);

			// 待機承認URL
			$waiting_url  = $system_base_login_url . SCRIPT_PATH;
			$waiting_url .= "?" . MODULE_ACCESSOR . "=Community";
			$waiting_url .= "&" . ACTION_ACCESSOR . "=WaitingList";
			$waiting_url .= "&community_id={$waiting_row['community_id']}";
			$waiting_url .= "&waiting_type_code={$waiting_row['waiting_type_code']}";
			$waiting_url .= "&waiting_status_code={$waiting_status_code}";

			foreach ($community_admin_user_info_row_array as $community_admin_user_info_row) {
				$community_admin_user_info_row = ACSUser::get_user_profile_row(
						$community_admin_user_info_row['user_community_id']);

				$target_lang = ACSMsg::get_mail_lang_by_inforow($community_admin_user_info_row);

				// 一旦依頼される側の言語に設定する
				ACSMsg::set_lang($target_lang);
				$body = $mail_titles[$target_lang];
				$body .= ACSMsg::get_tag_replace( 
						ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','CML%03d'),
						array(
							"{TARGET_COMMUNITY_NAME}"	=> $target_community_row['community_name'],
							"{COMMUNITY_NAME}"			=> $community_row['community_name'],
							"{USER_NAME}"				=> $user_info_row['user_name'],
							"{USER_COMMUNITY_NAME}"		=> $user_info_row['community_name'],
							"{WAITING_TYPE_NAME}"		=> $waiting_row['waiting_type_name'],
							"{COMMUNITY_URL}"			=> $community_top_page_url,
							"{MESSAGE}"					=> trim($waiting_row['message']),
							"{WAITING_URL}"				=> $waiting_url,
							"{SYSTEM_BASE_URL}"			=> $system_base_url
						)
				);
				$subject = ACSMsg::get_mdmsg(__FILE__,'M005');

				$ret = ACSLib::send_mail($system_mail_addr, 
						$community_admin_user_info_row['mail_addr'], null, $subject, $body);
			}
			// 言語を元に戻す
			ACSMsg::set_lang($org_lang);
		}

		return $ret;
	}

	/**
	 * 依頼承諾メールを送信する
	 *
	 * @param $waiting_id 待機ID
	 * @param $return 成功(true) / 失敗(false)
	 * @return 
	 */
	static function send_admission_accept_notify_mail($waiting_id) {
		// 待機情報
		$waiting_row = ACSWaiting::get_waiting_row($waiting_id);

		// システムURL
		$system_base_url = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_URL');
		// システムログインURL
		$system_base_login_url = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_LOGIN_URL');
		// システムのメールアドレス (From:)
		$system_mail_addr = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');

		// 利用者の言語設定を一時的に保存
		$org_lang = ACSMsg::get_lang();

		// 各言語のタイトルを取得
		$mail_titles = array();
		foreach (ACSMsg::get_lang_list_array() as $lang_key => $lang_name) {
			ACSMsg::set_lang($lang_key);
			$mail_titles[$lang_key] = 
					ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','MTL%03d')."\n";
		}

		// 言語を一旦元に戻す
		ACSMsg::set_lang($org_lang);

		if ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D10')) {
			// 承認側のユーザ情報
			$user_info_row = ACSUser::get_user_profile_row($waiting_row['community_id']);
			// 承認される側のユーザ情報
			$target_user_info_row = ACSUser::get_user_profile_row($waiting_row['waiting_community_id']);
			$target_lang = ACSMsg::get_mail_lang_by_inforow($target_user_info_row);

			// 一旦依頼される側の言語に設定する
			ACSMsg::set_lang($target_lang);

			$body = $mail_titles[$target_lang];
			$body .= ACSMsg::get_tag_replace( 
					ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','AMF%03d'),
					array(
						"{TARGET_USER_NAME}"		=> $target_user_info_row['user_name'],
						"{USER_NAME}"				=> $user_info_row['user_name'],
						"{USER_COMMUNITY_NAME}"		=> $user_info_row['community_name'],
						"{MESSAGE}"					=> trim($waiting_row['reply_message']),
						"{SYSTEM_BASE_URL}"			=> $system_base_url
					)
			);
			$subject = ACSMsg::get_mdmsg(__FILE__,'M006');

			$ret = ACSLib::send_mail($system_mail_addr, 
					$target_user_info_row['mail_addr'], null, $subject, $body);

			// 言語を元に戻す
			ACSMsg::set_lang($org_lang);

		} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D20')) {
			// 承認側のコミュニティ情報
			$community_row = ACSCommunity::get_community_row($waiting_row['community_id']);
			// 承認される側のユーザ情報
			$target_user_info_row = ACSUser::get_user_profile_row($waiting_row['waiting_community_id']);

			// 待機承認URL
			$waiting_url  = $system_base_login_url . SCRIPT_PATH;
			$waiting_url .= "?" . MODULE_ACCESSOR . "=Community";
			$waiting_url .= "&" . ACTION_ACCESSOR . "=WaitingList";
			$waiting_url .= "&community_id={$waiting_row['community_id']}";
			$waiting_url .= "&waiting_type_code={$waiting_row['waiting_type_code']}";
			$waiting_url .= "&waiting_status_code={$waiting_status_code}";

			$target_lang = ACSMsg::get_mail_lang_by_inforow($target_user_info_row);

			// 一旦依頼される側の言語に設定する
			ACSMsg::set_lang($target_lang);

			$body = $mail_titles[$target_lang];
			$body .= ACSMsg::get_tag_replace( 
					ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','ACM%03d'),
					array(
						"{TARGET_USER_NAME}"		=> $target_user_info_row['user_name'],
						"{COMMUNITY_NAME}"			=> $community_row['community_name'],
						"{MESSAGE}"					=> trim($waiting_row['reply_message']),
						"{SYSTEM_BASE_URL}"			=> $system_base_url
					)
			);
			$subject = ACSMsg::get_mdmsg(__FILE__,'M007');

			// 言語を元に戻す
			ACSMsg::set_lang($org_lang);

			$ret = ACSLib::send_mail($system_mail_addr, 
					$target_user_info_row['mail_addr'], null, $subject, $body);

		} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D30')) {
			// 招待されたコミュニティ情報
			$community_row = ACSCommunity::get_community_row($waiting_row['waiting_community_id']);
			// 承認側のユーザ情報
			$user_info_row = ACSUser::get_user_profile_row($waiting_row['community_id']);
			// 承認された側のユーザ情報
			$target_user_info_row = ACSUser::get_user_profile_row($waiting_row['entry_user_community_id']);

			// コミュニティトップページURL
			$community_top_page_url  = $system_base_login_url . SCRIPT_PATH;
			$community_top_page_url .= "?" . MODULE_ACCESSOR . "=Community";
			$community_top_page_url .= "&" . ACTION_ACCESSOR . "=" . DEFAULT_ACTION;
			$community_top_page_url .= "&community_id=" . $community_row['community_id'];

			$target_lang = ACSMsg::get_mail_lang_by_inforow($target_user_info_row);

			// 一旦依頼される側の言語に設定する
			ACSMsg::set_lang($target_lang);

			$body = $mail_titles[$target_lang];
			$body .= ACSMsg::get_tag_replace( 
					ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','AIM%03d'),
					array(
						"{TARGET_USER_NAME}"		=> $target_user_info_row['user_name'],
						"{USER_NAME}"				=> $user_info_row['user_name'],
						"{USER_COMMUNITY_NAME}"		=> $user_info_row['community_name'],
						"{COMMUNITY_NAME}"			=> $community_row['community_name'],
						"{COMMUNITY_URL}"			=> $community_top_page_url,
						"{MESSAGE}"					=> trim($waiting_row['reply_message']),
						"{SYSTEM_BASE_URL}"			=> $system_base_url
					)
			);

			$subject = ACSMsg::get_mdmsg(__FILE__,'M004');

			// 言語を元に戻す
			ACSMsg::set_lang($org_lang);

			$ret = ACSLib::send_mail($system_mail_addr, 
					$target_user_info_row['mail_addr'], null, $subject, $body);

		} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D40') || $waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D50')) {
			// 承認側のコミュニティ情報
			$community_row = ACSCommunity::get_community_row($waiting_row['community_id']);
			// 承認される側コミュニティ情報
			$target_community_row = ACSCommunity::get_community_row($waiting_row['waiting_community_id']);

			// 承認される側のコミュニティ管理者情報の配列
			$community_admin_user_info_row_array = ACSCommunity::get_community_admin_user_info_row_array($target_community_row['community_id']);

			// コミュニティトップページURL
			$community_top_page_url  = $system_base_login_url . SCRIPT_PATH;
			$community_top_page_url .= "?" . MODULE_ACCESSOR . "=Community";
			$community_top_page_url .= "&" . ACTION_ACCESSOR . "=" . DEFAULT_ACTION;
			$community_top_page_url .= "&community_id=" . $community_row['community_id'];

			foreach ($community_admin_user_info_row_array as $community_admin_user_info_row) {
				$community_admin_user_info_row = ACSUser::get_user_profile_row(
						$community_admin_user_info_row['user_community_id']);

				$target_lang = ACSMsg::get_mail_lang_by_inforow($community_admin_user_info_row);

				// 一旦依頼される側の言語に設定する
				ACSMsg::set_lang($target_lang);

				$body = $mail_titles[$target_lang];
				$body .= ACSMsg::get_tag_replace( 
						ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','ACL%03d'),
						array(
							"{TARGET_COMMUNITY_NAME}"	=> $target_community_row['community_name'],
							"{COMMUNITY_NAME}"			=> $community_row['community_name'],
							"{WAITING_TYPE_NAME}"		=> $waiting_row['waiting_type_name'],
							"{COMMUNITY_URL}"			=> $community_top_page_url,
							"{MESSAGE}"					=> trim($waiting_row['reply_message']),
							"{SYSTEM_BASE_URL}"			=> $system_base_url
						)
				);
				$subject = ACSMsg::get_mdmsg(__FILE__,'M009');

				$ret = ACSLib::send_mail($system_mail_addr, 
						$community_admin_user_info_row['mail_addr'], null, $subject, $body);
			}
			// 言語を元に戻す
			ACSMsg::set_lang($org_lang);
		}

		return $ret;
	}

}

?>
