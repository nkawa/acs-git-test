<?php
/**
 * ダイアリー　共通クラス
 * 
 * ACSDiary.class.php
 * @package  acs/webapp/lib/class
 * @author   w-ota                     @editor akitsu
 * @since    PHP 5.0
 */
// $Id: ACSDiary.class.php,v 1.36 2008/05/28 00:38:00 y-yuki Exp $

/*
 * ダイアリークラス
 */
class ACSDiary {
	/**
	 * ダイアリーの親記事一覧を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @return ダイアリー親記事の一覧 (連想配列の配列)
	 */
	static function get_diary_row_array($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT *, acs_get_diary_comment_num(diary.diary_id) as diary_comment_num, diary.diary_id as diary_id";
		$sql .= " FROM diary LEFT OUTER JOIN diary_file USING(diary_id)";
		$sql .= ", community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id = '$user_community_id'";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND diary.diary_delete_flag = 'f'";
		$sql .= " ORDER BY diary.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ダイアリーの親記事一覧を1日だけ取得する
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @param $year 年
	 * @param $month 月
	 * @param $day 日
	 * @return ダイアリー親記事の一覧 (連想配列の配列)
	 */
	static function get_diary_row_array_by_year_month_day($user_community_id, $year, $month, $day) {
		$user_community_id = pg_escape_string($user_community_id);

		// 年, 月, 日
		$year = sprintf("%04d", $year);
		$month = sprintf("%02d", $month);
		$day = sprintf("%02d", $day);
		// 指定日 (YYYY/MM/DD)
		$date = "$year/$month/$day";

		$sql  = "SELECT *, acs_get_diary_comment_num(diary.diary_id) as diary_comment_num, diary.diary_id as diary_id";
		$sql .= " FROM diary LEFT OUTER JOIN diary_file USING(diary_id)";
		$sql .= ", community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id = '$user_community_id'";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND diary.diary_delete_flag = 'f'";
		// post_dateを指定する
		$sql .= "  AND diary.post_date::DATE = '$date'::DATE";
		$sql .= " ORDER BY diary.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ダイアリーの親記事一覧を1月分だけ取得する
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @param $year 年
	 * @param $month 月
	 * @return ダイアリー親記事の一覧 (連想配列の配列)
	 */
	static function get_diary_row_array_by_year_month($user_community_id, $year, $month) {
		$user_community_id = pg_escape_string($user_community_id);

		// 月の最終日を取得
		$end_day = ACSLib::get_end_day($year, $month);
		// 年, 月
		$year = sprintf("%04d", $year);
		$month = sprintf("%02d", $month);

		// 開始日 (YYYY/MM/DD)
		$start_date = "$year/$month/01";
		// 終了日 (YYYY/MM/DD)
		$end_date = "$year/$month/$end_day";

		$sql  = "SELECT *, acs_get_diary_comment_num(diary.diary_id) as diary_comment_num, diary.diary_id as diary_id";
		$sql .= " FROM diary LEFT OUTER JOIN diary_file USING(diary_id)";
		$sql .= ", community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id = '$user_community_id'";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND diary.diary_delete_flag = 'f'";
		// post_dateの期間を指定する
		$sql .= "  AND diary.post_date::DATE BETWEEN '$start_date'::DATE AND '$end_date'::DATE";
		$sql .= " ORDER BY diary.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * diaryファイル情報array取得
	 * @param $where_list 検索用条件指定
	 */
	static function get_diary_where_array ($where_list , $open_lebel_cd , $no_array) {
		$sql  = "SELECT *";
		$sql .= " FROM diary INNER JOIN open_level_master ON diary.open_level_code = open_level_master.open_level_code";
		$sql .= " WHERE diary.diary_delete_flag = 'f'";
		if($open_lebel_cd != '00'){
			$sql .= "  AND diary.open_level_code ='$open_lebel_cd'";
		}
		if($where_list != ''){
			$sql .= $where_list;
		}
		if(count($no_array) > 0){
			$no_array = ACSLib::get_sql_value_array($no_array);
			$sql .= " AND diary.diary_id NOT IN (" . implode(", ", $no_array) . ")";
		}
		$sql .= " ORDER BY diary.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 公開ダイアリーの親記事一覧を取得する
	 *
	 * @return 公開ダイアリーの親記事一覧
	 */
	static function get_new_open_diary_row_array() {
		$limit = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');

		$sql  = "SELECT *";
		$sql .= " FROM diary LEFT OUTER JOIN diary_file USING(diary_id)";
		$sql .= ", community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		// ユーザコミュニティ 削除フラグOFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";
		// 公開範囲が一般公開
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND open_level_master.open_level_name = '".ACSMsg::get_mst('open_level_master','D01')."'";
		// ダイアリー 削除フラグOFF
		$sql .= "  AND diary.diary_delete_flag = 'f'";
		$sql .= " ORDER BY diary.post_date DESC";
		// LIMIT
		$sql .= " LIMIT $limit";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ダイアリーの親記事情報を取得する
	 *
	 * @param $diary_id ダイアリーID
	 * @return ダイアリーの親記事情報 (連想配列)
	 */
	static function get_diary_row($diary_id) {
		$diary_id = pg_escape_string($diary_id);

		$sql  = "SELECT *, acs_get_diary_comment_num(diary.diary_id) as diary_comment_num";
		$sql .= " FROM diary LEFT OUTER JOIN diary_file USING(diary_id)";
		$sql .= ", community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.diary_id = '$diary_id'";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		// ダイアリー 削除フラグOFF
		$sql .= "  AND diary.diary_delete_flag != 't'";
		$sql .= " ORDER BY diary.post_date DESC";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * ダイアリーの信頼済みコミュニティ情報を取得する
	 *
	 * @param $diary_id ダイアリーID
	 * @return ダイアリーの信頼済みコミュニティ情報 (連想配列の配列)
	 */
	static function get_diary_trusted_community_row_array($diary_id) {
		$diary_id = pg_escape_string($diary_id);

		$sql  = "SELECT community.community_id, community.community_name, community.community_type_code, community_type_master.community_type_name";
		$sql .= " FROM diary, diary_trusted_community, community, community_type_master";
		$sql .= " WHERE diary.diary_id = '$diary_id'";
		$sql .= "  AND diary.diary_id = diary_trusted_community.diary_id";
		$sql .= "  AND diary_trusted_community.trusted_community_id = community.community_id";
		$sql .= "  AND community.community_type_code = community_type_master.community_type_code";
		$sql .= " ORDER BY community.community_name ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}


	/**
	 * ダイアリーのコメント一覧を取得する
	 *
	 * @param $diary_id ダイアリーID
	 * @return マイダイアリーのコメント一覧(クエリ結果リソース)
	 */
	static function get_diary_comment_row_array($diary_id) {
		$sql  = "SELECT *";
		$sql .= " FROM (diary_comment LEFT OUTER JOIN community ON diary_comment.user_community_id = community.community_id) as JOINED_DIARY_COMMENT";
		$sql .= " WHERE JOINED_DIARY_COMMENT.diary_id = '$diary_id'";
		$sql .= " ORDER BY JOINED_DIARY_COMMENT.post_date ASC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ダイアリーを登録する
	 *
	 * @param $form ダイアリー情報の配列
	 * @return 成功(登録されたダイアリーID) / 失敗(false)
	 */
	static function set_diary($form) {
		$org_form = $form;

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		//ACSDB::_do_query("BEGIN");

		$diary_id_seq = ACSDB::get_next_seq('diary_id_seq');

		// diary
		$sql  = "INSERT INTO diary";
		$sql .= " (diary_id, community_id, subject, body, open_level_code, diary_delete_flag)";
		$sql .= " VALUES ($diary_id_seq, $form[user_community_id], $form[subject], $form[body], $form[open_level_code],'f')";

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			echo "ERROR: insert diary error";
			return $ret;
		}

		$form = $org_form;

		if($form['new_file']!=""){
		//画像ファイルの登録
		$file_obj = $form['new_file'];
			$ret = $file_obj->save_upload_file('DIARY');	//ファイルをディスクに保存
			if($ret){
				$ret =  $file_obj->add_file();				//ファイル情報をDBへ保存
			}
			if($ret){
				$ret = ACSDiaryFile::insert_diary_file($file_obj,$diary_id_seq);
			}
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				echo "ERROR: insert diary_file error";
				return $ret;
			}
		}

		// diary_trusted_community
		// 友人に公開を選択した場合
		$open_level_master_array = ACSDB::get_master_array('open_level');
		// 友人に公開
		if ($open_level_master_array[$form['open_level_code']] == ACSMsg::get_mst('open_level_master','D05')) {

			// マイフレンズグループを指定した場合
			if($form['trusted_community_flag']) {
				foreach ($form['trusted_community_id_array'] as $trusted_community_id) {
					$trusted_community_id = pg_escape_string($trusted_community_id);

					$sql  = "INSERT INTO diary_trusted_community";
					$sql .= " (diary_id, trusted_community_id)";
					$sql .= " VALUES ($diary_id_seq, $trusted_community_id)";

					$ret = ACSDB::_do_query($sql);
					if (!$ret) {
						ACSDB::_do_query("ROLLBACK");
						echo "ERROR: insert diary_trusted_community error:FRIEND";
						return $ret;
					}
				}

			} else {
				// 全てのマイフレンズ
				$trusted_community_id = ACSUser::get_friends_community_id($form['user_community_id']);

				$sql  = "INSERT INTO diary_trusted_community";
				$sql .= " (diary_id, trusted_community_id)";
				$sql .= " VALUES ($diary_id_seq, $trusted_community_id)";

				$ret = ACSDB::_do_query($sql);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					echo "ERROR: insert diary_trusted_community error:ALL";
					return $ret;
				}
			}

		}

		// COMMIT
		//ACSDB::_do_query("COMMIT");

		if ($ret) {
			return $diary_id_seq;
		} else {
			return false;
		}
	}

	/**
	 * ダイアリーコメントを登録する
	 *
	 * @param $form ダイアリーコメント情報の配列
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_diary_comment($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

//一意指定のためのコメントID
		$diary_comment_id_seq = ACSDB::get_next_seq('diary_comment_id_seq');

		$sql  = "INSERT INTO diary_comment";
		$sql .= " (diary_comment_id, diary_id, user_community_id, body,diary_comment_delete_flag)";
		$sql .= " VALUES ($diary_comment_id_seq, $form[diary_id], $form[user_community_id], $form[body],'f')";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * マイフレンズの新着ダイアリー一覧を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID (ダイアリーへのアクセス者となるユーザ)
	 * 		  $days 取得する日数(最近何日間の新着情報を取得)
	 * @return 新着ダイアリー一覧 (連想配列の配列)
	 */
	static function get_new_diary_row_array($user_community_id, $days=false, $offset=false) {

		// マイフレンズのユーザコミュニティIDのCSVを作成する
		$friends_row_array = ACSUser::get_simple_friends_row_array($user_community_id);
		$friends_user_community_id_array = array();
		foreach ($friends_row_array as $index => $user_info_row) {
			array_push($friends_user_community_id_array, $user_info_row['user_community_id']);
		}
		if (count($friends_user_community_id_array)) {
			$friends_user_community_id_csv = implode(',', $friends_user_community_id_array);
		} else {
			$friends_user_community_id_csv = 'null';
		}

		//
		$user_community_id = pg_escape_string($user_community_id);

		// マイフレンズのダイアリーを最新順に取得する
		$sql  = "SELECT *, acs_is_unread_diary('$user_community_id', diary.diary_id) as is_unread,";
		$sql .= "       acs_get_diary_comment_num(diary.diary_id) as diary_comment_num";
		$sql .= " FROM diary, community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id IN ($friends_user_community_id_csv)";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		// ダイアリー 削除フラグOFF
		$sql .= "  AND diary.diary_delete_flag = 'f'";

		//------ 2007.2 表示時間短縮対応
		// 日数指定がある場合
		if($days !== false){
			$sql = $sql . " AND " . ACSLib::get_sql_condition_from_today("diary.post_date", $days);
		}

		if($offset !== false){
			$sql = $sql . " ORDER BY diary.post_date DESC";
			// 表示件数制御 //
			$display_count = 
					ACSSystemConfig::get_keyword_value(ACSMsg::get_mst(
							'system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
			$sql = $sql . " OFFSET 0 LIMIT ". $display_count;
		} else {
			$sql .= " ORDER BY diary.post_date DESC";
		}
		
		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ダイアリーのアクセス履歴情報を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @param $diary_id ダイアリーID
	 * @return ダイアリーのアクセス履歴情報 (連想配列)
	 */
	static function get_diary_access_history_row($user_community_id, $diary_id) {
		$user_community_id = pg_escape_string($user_community_id);
		$diary_id = pg_escape_string($diary_id);

		$sql  = "SELECT *";
		$sql .= " FROM diary_access_history";
		$sql .= " WHERE user_community_id = '$user_community_id'";
		$sql .= "  AND diary_id = '$diary_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * ダイアリーのアクセス履歴を登録する
	 *
	 * @param $diary_id
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_diary_access_history($user_community_id, $diary_id) {
		// ダイアリーアクセス履歴
		$diary_access_history_row = ACSDiary::get_diary_access_history_row($user_community_id, $diary_id);

		$diary_access_history_form = array(
										   'user_community_id' => $user_community_id,
										   'diary_id' => $diary_id,
										   'access_date' => 'now'
										   );

		if ($diary_access_history_row) {
			// レコードが存在する場合はUPDATE
			ACSDiaryAccessHistoryModel::update_diary_access_history($diary_access_history_form);
		} else {
			// レコードが存在しない場合はINSERT
			ACSDiaryAccessHistoryModel::insert_diary_access_history($diary_access_history_form);
		}
	}

	/**
	 * コメントしたダイアリー一覧を取得する (自分のダイアリー以外が対象)
	 *
	 * @param $user_community_id ユーザコミュニティID (コメントしたユーザ)
	 * 		  $days 取得する日数(最近何日間の新着情報を取得)
	 * @return 新着ダイアリー一覧 (連想配列の配列)
	 */
	static function get_commented_diary_row_array($user_community_id, $days=false, $offset=false) {
		
		$user_community_id = pg_escape_string($user_community_id);

		// コメントしたダイアリー
		$sql  = "SELECT DISTINCT diary.diary_id";
		$sql .= " FROM diary, diary_comment";
		$sql .= " WHERE diary.diary_id = diary_comment.diary_id";
		$sql .= "  AND diary.community_id != '$user_community_id'";
		$sql .= "  AND diary_comment.user_community_id = '$user_community_id'";
		$tmp_row_array = ACSDB::_get_row_array($sql);

		$commented_diary_id_array = array();
		foreach ($tmp_row_array as $tmp_row) {
			array_push($commented_diary_id_array, $tmp_row['diary_id']);
		}
		if (count($commented_diary_id_array)) {
			$commented_diary_id_csv = implode(',', $commented_diary_id_array);
		} else {
			$commented_diary_id_csv = 'null';
		}

		// コメントしたダイアリーを最新順に取得する
		$sql  = "SELECT *, acs_get_diary_comment_num(diary.diary_id) as diary_comment_num, acs_get_diary_last_post_date(diary.diary_id) as diary_last_post_date, acs_is_unread_diary_comment('$user_community_id', diary.diary_id) as is_unread";
		$sql .= " FROM diary, community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.diary_id IN ($commented_diary_id_csv)";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";

		//------- 2007.2 表示時間短縮対応
		// 日数指定がある場合
		if ($days !== false) {
			$sql = $sql . " AND " . 
					ACSLib::get_sql_condition_from_today("acs_get_diary_last_post_date(diary.diary_id)", $days);
		}

		if ($offset != false) {
		$sql = $sql . " ORDER BY diary_last_post_date DESC";
			// 表示件数制御 //
			$display_count = 
					ACSSystemConfig::get_keyword_value(ACSMsg::get_mst(
							'system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
			$sql = $sql . " OFFSET 0 LIMIT ". $display_count;
		} else {
			$sql .= " ORDER BY diary_last_post_date DESC";
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 新着コメントのある(マイ)ダイアリー一覧を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID (対象のマイダイアリーのユーザ)
	 * @return 新着コメントのあるダイアリー一覧 (連想配列の配列)
	 */
	static function get_new_comment_diary_row_array($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		// SQL関数: acs_get_diary_c_last_post_date(diary.diary_id)
		//          最新のダイアリーコメントの日時を取得する。コメントが0件であればnull
		$sql = "SELECT
					dia.diary_id, dia.community_id,
					dia.subject, lstcmt.last_post_date AS diary_comment_last_post_date
				FROM
					(diary AS dia LEFT JOIN diary_access_history AS hist
					ON hist.user_community_id = '".$user_community_id."' AND dia.diary_id = hist.diary_id)
						LEFT JOIN
						(SELECT diary_id, max(post_date) AS last_post_date
							FROM diary_comment GROUP BY diary_id) AS lstcmt
						ON dia.diary_id = lstcmt.diary_id
				WHERE
					dia.community_id = '".$user_community_id."'
					AND dia.diary_delete_flag = 'f'
					AND (hist.access_date IS null
					OR lstcmt.last_post_date > hist.access_date)
					AND lstcmt.last_post_date IS NOT NULL
				ORDER BY
					dia.post_date ASC ";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}
	
	/**
	 * ダイアリーを削除する
	 *
	 * @param $diary_id ダイアリーID
	 * @return 成功(true) / 失敗(false)
	 */
	static function delete_diary($diary_id) {
	
		// BEGIN
		
		// diary コメントの取得
		$sub_row_array = ACSDiary::get_diary_comment_row_array($diary_id);
		$diary_comment_id_array = array();
		if(count($sub_row_array) > 0){
			foreach ($sub_row_array as $index => $sub_row) {
				array_push($diary_comment_id_array, $sub_row['diary_comment_id']);
			}
			//コメントの削除
			$ret = ACSDiary::delete_diary_comment($diary_comment_id_array);
			if(!$ret){
				return false;
			}
		}

		//日記が持っているファイル情報の削除
		$diary_row = ACSDiary::get_diary_row($diary_id);
			$file_id = $diary_row['file_id'];
		if($file_id != ''){
			$ret = ACSDiaryFile::delete_diary_file($file_id,$diary_id);
			if(!$ret){
				echo "ERROR: Delete attached file information failed.";
				return false;
			}
		}
	
		//日記の削除(削除フラグ扱い)	
		$sql = "UPDATE diary";
		$sql .= " SET diary_delete_flag = 't'";
		$sql .= " WHERE diary.diary_id = $diary_id";
		$ret = ACSDB::_do_query($sql);
		if(!$ret){
			echo "ERROR: Delete parent article failed.";
			return false;
		}
	
		// COMMIT
		return true;
	}

	/**
	 * 日記のコメントを削除する
	 *
	 * @param $diary_comment_id_array コメントID_array
	 * @return 成功(true) / 失敗(false)
	 */
	 static function delete_diary_comment($diary_comment_id_array) {
		if (count($diary_comment_id_array) > 1) {
			$diary_comment_id = implode(',', $diary_comment_id_array);
		} else {
			$diary_comment_id = array_shift($diary_comment_id_array);
		}

		//コメントの削除
		$sql = "UPDATE diary_comment";
		$sql .= " SET diary_comment_delete_flag = 't'";
		$sql .= " WHERE diary_comment.diary_comment_id IN($diary_comment_id)";

		$ret = ACSDB::_do_query($sql);
		if(!$ret){
			echo "ERROR: Delete comment failed.";
			return false;
		}
		return true;
	}

	/**
	 * 日記を検索するためのwhere句の１部を作成
	 *
	 * @param $form　条件
	    [id] => 1000
	    [move_id] => 2
	    [q_text] => 検索条件					//条件（※必須）
	    [search_title] => title_in_serch		//件名を選択
	    [search_all] => subject_in_serch		//本文を選択
	    [open_level_code] => 00					//公開範囲（00は選択なし）（※必須）
	    [search_all_about] => all_in_serch		//すべての日記を選択
	 * @return str_array($like_sql , $err_str , $str_count)
	 */
	static  function set_diary_where_list($form , $flag) {
		$str_array = array();
		$str_array['like_sql'] = ""; //成功時の戻り値
		$str_array['err_str']  = "";	//失敗時の戻り値
		$str_array['str_count'] = 1;  //成功時のキーワード数

		// 検索キーワードの取得（注意！バイト単位で処理）
		$search_text = $form['q_text'];				//formから検索文字列を取得

		//〜〜〜〜〜〜〜〜〜〜〜配列として利用(Likeの作成)〜〜〜〜〜〜〜〜〜〜〜
		if($search_text != ''){
			$search_args = ACSLib::get_query_array_array($search_text);	//検索文字列配列に変換
		}
		if(count($search_args) > 0){						//検索文字列が存在する場合
			$search_args = ACSLib::escape_sql_array($search_args);
		//キーワードwhere句の追記
		//次のいずれかは必須
			$str_like = '';
			//件名を選択している場合
			if($form['search_title']){
				$str_title = " diary.subject";
				$str_like_array = ACSDiary::create_sql_where_field($search_args , $str_title , $flag);
				$str_like = " (" . $str_like_array['str_like'] .") ";
				$str_array['str_count'] = $str_like_array['str_count'];
			}
			//本文を選択している場合
			if($form['search_all']){																
				$str_body = " diary.body";
				if($str_like != ""){
					$str_like = $str_like . " OR ";											//件名と共に選択している場合
				}
				$where_sql_array = ACSDiary::create_sql_where_field($search_args , $str_body, $flag);
				$where_sql = " (" . $where_sql_array['str_like'] .") ";
				$str_like =  $str_like . $where_sql;
				$str_array['str_count'] = $where_sql_array['str_count'];
			}
			//キーワードwhere句の追記終了
			if($str_like == ""){																		//キーワードがあるのに、対象がない場合
				$str_array['err_str'] = ACSMsg::get_mdmsg(__FILE__,'M001');
				return $str_array;
			}else{
				$str_array['like_sql'] = $str_array['like_sql'] . $str_array['like_sql'] ." AND (" . $str_like .") ";
			}
		}
		//〜〜〜〜〜〜〜〜〜〜〜〜〜〜特定日記検索〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜
		//必ずAND
		if(!$form['search_all_about']){													//すべての日記を選択していない場合
				$str_array['like_sql'] = $str_array['like_sql'] ." AND diary.community_id  = " . $form['id'];
		}
		return $str_array;
	}

	/**
	 * 日記を検索するためのwhere句の１部を作成2
	 * 件名と本文がそれぞれ単独指定・複合指定になる可能性を持つため
	 * 列ごとのｷｰﾜｰﾄﾞを繰り返す処理のみを行う関数
	 *
	 * @param  $query_array_array ｷｰﾜｰﾄﾞ配列
	 * @param  $str_title 列名
	 * @return $where_sql 作成したSQL条件文
	 */
	static function create_sql_where_field($query_array_array , $str_field ,$flag){
		$str_like = "";
		foreach ($query_array_array as $query_array) {		//全ｷｰﾜｰﾄﾞに対する処理
			if (!count($query_array)) {
				continue;									//１ｷｰﾜｰﾄﾞ中に値が無い場合（区切り文字？）
			}
			foreach ($query_array as $id => $str_q) {		//１ｷｰﾜｰﾄﾞに対する処理（文字種のためor処理）
					$str_q = pg_escape_string($str_q);
					ACSLib::escape_ilike($str_q);
					if($id == 0){
							//$str_like = "(" . $str_field . " LIKE '%" . $str_q ."%'";
							$str_like = $str_field . " LIKE '%" . $str_q ."%'";
					}else{
							$where_sql['str_count'] = 2;
							$str_like = $str_like . " OR " . $str_field . " LIKE '%" . $str_q ."%'";
					}
					//$str_like = $str_like . " )";
			}
			if ($str_like != '') {			//全ｷｰﾜｰﾄﾞをANDかORでつなげる
				if($where_sql['str_like'] != '' ){
					switch($flag){
						case 1;	//すべて合致
							$where_sql['str_like'] = $where_sql['str_like'] ." AND ($str_like)"; break;
						case 2: //すべて合致せず
							$where_sql['str_like'] = $where_sql['str_like'] ." OR ($str_like)";
					}
				}else{
					$where_sql['str_like'] = " ($str_like)";
				}
			}
		}
		return $where_sql;
	}

	/**
	 * ダイアリーのコメントを取得する
	 *
	 * @param $diary_comment_id ダイアリーID
	 * @return マイダイアリーのコメント(クエリ結果リソース)
	 */
	static function get_diary_comment_row ($diary_comment_id) {
		$sql  = "SELECT *";
		$sql .= " FROM (diary_comment LEFT OUTER JOIN community ON diary_comment.user_community_id = community.community_id) as JOINED_DIARY_COMMENT";
		$sql .= " WHERE JOINED_DIARY_COMMENT.diary_comment_id = '$diary_comment_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * 最新のダイアリー一覧を取得する (ダイアリーRSS用)
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @param $term 取得期間
	 * @return ダイアリー親記事の一覧 (連想配列の配列)
	 */
	static function get_new_diary_rss_row_array($user_community_id, $term) {
		$user_community_id = pg_escape_string($user_community_id);
		$term = pg_escape_string($term);

		$sql  = "SELECT *, acs_get_diary_comment_num(diary.diary_id) as diary_comment_num, diary.diary_id as diary_id";
		$sql .= " FROM diary LEFT OUTER JOIN diary_file USING(diary_id)";
		$sql .= ", community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id = '$user_community_id'";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND diary.diary_delete_flag = 'f'";
		// $term日以内の投稿を取得
		$sql .= "  AND diary.post_date::DATE > (CURRENT_DATE - '@ $term days'::INTERVAL)";
		$sql .= " ORDER BY diary.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ダイアリーRSSを出力する
	 *
	 * @param $target_user_info_row 対象ユーザ情報
	 * @param $diary_row_array ダイアリー一覧
	 * @param $params パラメータ等
	 */
	static function print_diary_rss($target_user_info_row, $diary_row_array, $params) {
		// 使用クラス: acs/webapp/lib/feedcreator/feedcreator.class.php

		$rss = new UniversalFeedCreator();

		// 概要等 <channel>
		$rss->useCached();
		$rss->title = ACSMsg::get_tag_replace(ACSMsg::get_mdmsg(__FILE__,'NAME'),
											  array("{USER_NAME}" => $target_user_info_row['community_name'])); // ○○さん
		$rss->description = $params['description'];                               // プロフィール (公開範囲別)
		$rss->link = $params['base_url'] . $target_user_info_row['top_page_url']; // マイページURL
		$rss->url = $params['base_url'] . $target_user_info_row['image_url'];     // 画像URL  <image rdf:resource="...">
		$rss->syndicationURL = $params['rss_syndication_url'];                    // 自身のURL <channel rdf:about="...">

		// ロゴ画像 <image>
		$image = new FeedImage();
		$image->title = $target_user_info_row['image_title']; // ファイル名
		$image->link = ACSMsg::get_mdmsg(__FILE__,'M002');    // 写真
		$image->url = $params['base_url'] . $target_user_info_row['image_url'];
		$rss->image = $image;

		// 1件のダイアリー: <item>
		foreach ($diary_row_array as $index => $diary_row) {
			// CRLF → LF
			$body = preg_replace('/\r\n/', "\n", $diary_row['body']);

			$item = new FeedItem(); 
			$item->post_date = $diary_row['post_date']; 
			$item->title = $diary_row['subject']; 
			$item->link = $params['base_url'] . $diary_row['diary_comment_url'];
			$item->description = $body;
			if ($diary_row['file_url'] != '') {
				$item->image_link = $params['base_url'] . $diary_row['file_url'];
			}
			$item->description2 = $body; //第2の本文  <content:encoded>
			
			$rss->addItem($item); 
		}

		// http-header
		mb_http_output('pass');
		header('Content-type: application/xml; charset=UTF-8');
		echo mb_convert_encoding($rss->createFeed("RSS1.0"), 'UTF-8', mb_internal_encoding());
	}
}
?>
