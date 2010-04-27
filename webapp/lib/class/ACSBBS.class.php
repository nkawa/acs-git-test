<?php
/**
 * 掲示板　共通クラス
 * 
 * ACSBBS.class.php
 * @package  acs/webapp/lib/class
 * @author   ota                     @editor akitsu
 * @since    PHP 4.0
 */
// $Id: ACSBBS.class.php,v 1.38 2008/05/21 01:53:36 y-yuki Exp $

/*
 * コミュニティ
 */
class ACSBBS {

	/**
	 * 掲示板の親記事一覧を取得する
	 *
	 * @param コミュニティID
	 * @return 掲示板の親記事一覧 (連想配列の配列)
	 */
	static function get_bbs_row_array($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT bbs.*, open_level_master.*, acs_get_bbs_last_post_date(bbs.bbs_id) as bbs_last_post_date, acs_get_bbs_res_num(bbs.bbs_id) as bbs_res_num, bbs_file.file_id as file_id, community.community_name as community_name";
		$sql .= " FROM ((bbs LEFT OUTER JOIN community ON bbs.user_community_id = community.community_id)";
		$sql .= " LEFT OUTER JOIN bbs_file ON bbs.bbs_id = bbs_file.bbs_id) ,";
		$sql .= " open_level_master";
		$sql .= " WHERE bbs.community_id = '$community_id'";
		$sql .= "  AND bbs.open_level_code = open_level_master.open_level_code";
		// 削除フラグOFF
		$sql .= "  AND bbs.bbs_delete_flag != 't'";
		$sql .= " ORDER BY bbs_last_post_date DESC";		//投稿日の新しい順

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * bbs_idを指定して掲示板の親記事情報を取得する
	 *
	 * @param bbs_id
	 * @return 掲示板の親記事情報 (連想配列)
	 */
	static function get_bbs_row($bbs_id) {
		$bbs_id = pg_escape_string($bbs_id);
		$sql  = "SELECT *, bbs.community_id AS bbs_community_id";
		$sql .= " FROM bbs LEFT OUTER JOIN community on bbs.user_community_id = community.community_id";
		$sql .= "  LEFT OUTER JOIN bbs_file USING(bbs_id)";
		$sql .= ", open_level_master";
		$sql .= " WHERE bbs.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND bbs.bbs_id = '$bbs_id'";
		// 削除フラグOFF
		$sql .= "  AND bbs.bbs_delete_flag != 't'";
		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * bbs_idを指定して掲示板の信頼済みコミュニティ一覧を取得する
	 *
	 * @param bbs_id
	 * @return コミュニティ一覧 (連想配列の配列)
	 */
	static function get_bbs_trusted_community_row_array($bbs_id) {
		$bbs_id = pg_escape_string($bbs_id);

		$sql  = "SELECT community.community_id, community.community_name";
		$sql .= " FROM bbs, bbs_trusted_community, community";
		$sql .= " WHERE bbs.bbs_id = '$bbs_id'";
		$sql .= "  AND bbs.bbs_id = bbs_trusted_community.bbs_id";
		$sql .= "  AND bbs_trusted_community.trusted_community_id = community.community_id";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}


	/**
	 * bbs_res_idを指定して掲示板の返信記事を取得する
	 *
	 * @param bbs_res_id
	 * @return 掲示板の返信記事一覧 (連想配列の配列)
	 */
	static function get_bbs_res_row($bbs_res_id) {
		$bbs_res_id = pg_escape_string($bbs_res_id);

		$sql  = "SELECT *";
		$sql .= " FROM bbs_res LEFT OUTER JOIN community ON bbs_res.user_community_id = community.community_id";
		$sql .= " WHERE bbs_res.bbs_res_id = '$bbs_res_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * bbs_idを指定して掲示板の返信記事一覧を取得する
	 *
	 * @param bbs_id
	 * @return 掲示板の返信記事一覧 (連想配列の配列)
	 */
	static function get_bbs_res_row_array($bbs_id) {
		$bbs_id = pg_escape_string($bbs_id);

		$sql  = "SELECT *";
		$sql .= " FROM bbs_res LEFT OUTER JOIN community ON bbs_res.user_community_id = community.community_id";
		$sql .= " WHERE bbs_res.bbs_id = '$bbs_id'";
		$sql .= " ORDER BY bbs_res.post_date ASC";		//投稿日の新しい順   bbs_res.delete_flag,(削除フラグのないもの→→削除フラグのあるもの)

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 掲示板の親記事を登録する
	 *
	 * @param 親記事情報の配列
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_bbs($form) {
		$org_form = $form;

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		//ACSDB::_do_query("BEGIN");

		$bbs_id_seq = ACSDB::get_next_seq('bbs_id_seq');

		$sql  = "INSERT INTO bbs";
		$sql .= " (bbs_id, community_id, user_community_id, subject, body, open_level_code, expire_date,ml_send_flag)";
		$sql .= " VALUES ($bbs_id_seq, $form[community_id], $form[user_community_id], $form[subject], $form[body], $form[open_level_code], $form[xdate], ". 
				($org_form['is_ml_send']=='t' ? "TRUE" : "FALSE") . ")";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			print "bbs insert error";
			return $ret;
		}

		$form = $org_form;
		if($form['new_file']!=""){
			//画像ファイルの登録
			$file_obj = $form['new_file'];
			$ret = $file_obj->save_upload_file('BBS');		//ファイルをディスクに保存
			if($ret){
				$ret =  $file_obj->add_file();				//ファイル情報をDBへ保存
			}
			if($ret){
				$ret = ACSBBSFile::insert_bbs_file($file_obj,$bbs_id_seq);
			}
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				print "bbs_file insert error";	
				return $ret;
			}
		}

		// bbs_trusted_community
		$open_level_master_array = ACSDB::get_master_array('open_level');
		// 非公開 (メンバのみ)
		if ($open_level_master_array[$form['open_level_code']] == ACSMsg::get_mst('open_level_master','D04')
			&& is_array($form['trusted_community_id_array'])) {
			foreach ($form['trusted_community_id_array'] as $trusted_community_id) {
				$trusted_community_id = pg_escape_string($trusted_community_id);

				$sql  = "INSERT INTO bbs_trusted_community";
				$sql .= " (bbs_id, trusted_community_id)";
				$sql .= " VALUES ($bbs_id_seq, $trusted_community_id)";

				$ret = ACSDB::_do_query($sql);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					print "bbs_trusted_community insert error";
					return $ret;
				}
			}
		}

		if ($ret) {
			$ret = $bbs_id_seq;
		}
		
		// COMMIT
		//ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * 掲示板の親記事を更新する
	 *
	 * @param 親記事情報の配列
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_bbs($form) {
		$org_form = $form;

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// bbs更新
		$sql  = "UPDATE bbs";
		$sql .= " SET";
		$sql .= "  subject = $form[subject],";
		$sql .= "  body = $form[body]";
		$sql .= " WHERE";
		$sql .= "  bbs_id = $form[bbs_id]";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		$form = $org_form;
		if ($_FILES['new_file']['tmp_name'] != '') {
			// 画像ファイルobjの取得
			$file_obj = ACSFile::get_upload_file_info_instance($_FILES['new_file'], $form['community_id'], $form['user_community_id']);
			// ファイルをディスクに保存
			$ret = $file_obj->save_upload_file('BBS');
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			//ファイル情報をDBへ保存
			$ret = $file_obj->add_file();
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			// bbsファイルの更新
			$ret = ACSBBSFile::update_bbs_file($file_obj, $form['bbs_id']);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		}

		return $ret;
	}

	/**
	 * 掲示板の返信記事を登録する
	 *
	 * @param 返信記事情報の配列
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_bbs_res($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$bbs_res_id_seq = ACSDB::get_next_seq('bbs_res_id_seq');

		$sql  = "INSERT INTO bbs_res";
		$sql .= " (bbs_id, bbs_res_id, user_community_id, subject, body ,bbs_res_delete_flag)";
		$sql .= " VALUES ($form[bbs_id], $bbs_res_id_seq,$form[user_community_id], $form[subject], $form[body],'f')";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * 掲示板の親記事を更新する
	 *
	 * @param 親記事情報の配列
	 * @return 成功(true) / 失敗(false)
	 */
	static function update_bbs_res($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// bbs更新
		$sql  = "UPDATE bbs_res";
		$sql .= " SET";
		$sql .= "  subject = $form[subject],";
		$sql .= "  body = $form[body]";
		$sql .= " WHERE";
		$sql .= "  bbs_res_id = $form[bbs_res_id]";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * 掲示板の新着記事一覧を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID (BBSへのアクセス者となるユーザ)
     *        $days 取得する日数(最近何日間の新着情報を取得)
	 * @return 新着記事一覧
	 *
	 */
	static function get_new_bbs_row_array($user_community_id, $days=false, $offset=false) {

		// マイコミュニティのコミュニティIDのCSVを作成する
		$community_row_array = ACSUser::get_community_row_array($user_community_id);
		$community_id_array = array();
		foreach ($community_row_array as $index => $community_row) {
			array_push($community_id_array, $community_row['community_id']);
		}
		if (count($community_id_array)) {
			$community_id_csv = implode(',', $community_id_array);
		} else {
			$community_id_csv = 'null';
		}

		//
		$user_community_id = pg_escape_string($user_community_id);

		// 掲示板の新着記事を最新順に取得する
		$sql = "SELECT
			bbs.*, community.*, open_level_master.*,
			COALESCE(bbs_res_num_n,0) AS bbs_res_num,
			lastts.bbs_last_timestamp AS bbs_last_post_date,
			CASE
				WHEN acdate.access_date IS NULL THEN TRUE
				WHEN lastts.bbs_last_timestamp > acdate.access_date THEN TRUE
				ELSE FALSE
			END AS is_unread
		FROM
			(((bbs INNER JOIN community
			ON bbs.community_id = community.community_id)
				INNER JOIN open_level_master
				ON bbs.open_level_code = open_level_master.open_level_code)
					LEFT JOIN
						(SELECT bbs_id, count(*) AS bbs_res_num_n
						FROM bbs_res GROUP BY bbs_id) AS rescnt
					ON bbs.bbs_id = rescnt.bbs_id)
						LEFT JOIN acs_view_bbs_last_timestamp AS lastts
						ON bbs.bbs_id = lastts.bbs_id
							LEFT JOIN
								(SELECT bbs_id, access_date
								FROM bbs_access_history
								WHERE user_community_id='".$user_community_id."') AS acdate
							ON bbs.bbs_id = acdate.bbs_id
		WHERE
			bbs.community_id IN (".$community_id_csv.")
			AND bbs.bbs_delete_flag != 't'";

		if($days !== false){
			$sql .= " AND " . ACSLib::get_sql_condition_from_today(
			"lastts.bbs_last_timestamp", $days);
		}

		$sql .= " ORDER BY bbs_last_timestamp DESC, bbs.bbs_id DESC";
		if($offset !== false){
			// 表示件数制御 //
			$display_count = 
					ACSSystemConfig::get_keyword_value(ACSMsg::get_mst(
							'system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
			$sql = $sql . " OFFSET 0 LIMIT ". $display_count;
		} else {
			//$sql .= " ORDER BY bbs_last_timestamp DESC, bbs.bbs_id DESC";
		}
		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 掲示板の新着パブリックリリース一覧を取得する
	 *
	 * @return 新着パブリックリリース一覧
	 */
	static function get_new_bbs_for_press_release_row_array() {
		$limit = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');

		$sql  = "SELECT *";
		$sql .= " FROM bbs LEFT OUTER JOIN bbs_file USING(bbs_id), open_level_master as BBS_OLM,";
		$sql .= "  community, community_type_master,";
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM,";  // 全体
		$sql .= "  open_level_master as SELF_OLM";                          // 全体のopen_level_master

		$sql .= " WHERE bbs.community_id = community.community_id";
		$sql .= "  AND community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D40')."'";

		// BBS: パブリックリリース
		$sql .= "  AND bbs.open_level_code = BBS_OLM.open_level_code";
		$sql .= "  AND BBS_OLM.open_level_name = '".ACSMsg::get_mst('open_level_master','D06')."'";
		// BBS: 削除フラグOFF
		$sql .= "  AND bbs.bbs_delete_flag != 't'";
		// BBS: 掲載終了日に達していない
		$sql .= "  AND (bbs.expire_date is null OR CURRENT_DATE <= bbs.expire_date::DATE)";

		// コミュニティ: 削除フラグOFF
		$sql .= "  AND community.delete_flag != 't'";
		// コミュニティ: 非公開コミュニティでない
		$sql .= "  AND community.community_id = SELF_C.community_id";
		$sql .= "  AND SELF_C.contents_type_code = SELF_CTM.contents_type_code";
		$sql .= "  AND SELF_C.open_level_code = SELF_OLM.open_level_code";
		$sql .= "  AND SELF_CTM.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";
		$sql .= "  AND SELF_OLM.open_level_name != '".ACSMsg::get_mst('open_level_master','D03')."'";

		// ORDER
		$sql .= " ORDER BY bbs.post_date DESC";

		// LIMIT
		$sql .= " LIMIT $limit";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 掲示板のアクセス履歴情報を取得する
	 *
	 * @param $user_community_id ユーザコミュニティID
	 * @param $bbs_id bbs_id
	 * @return 掲示板のアクセス履歴情報 (連想配列)
	 */
	static function get_bbs_access_history_row($user_community_id, $bbs_id) {
		$user_community_id = pg_escape_string($user_community_id);
		$bbs_id = pg_escape_string($bbs_id);

		$sql  = "SELECT *";
		$sql .= " FROM bbs_access_history";
		$sql .= " WHERE user_community_id = '$user_community_id'";
		$sql .= "  AND bbs_id = '$bbs_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * 掲示板のアクセス履歴を登録する
	 *
	 * @param $bbs_id
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_bbs_access_history($user_community_id, $bbs_id) {
		// 掲示板アクセス履歴
		$bbs_access_history_row = ACSBBS::get_bbs_access_history_row($user_community_id, $bbs_id);

		$bbs_access_history_form = array(
										   'user_community_id' => $user_community_id,
										   'bbs_id' => $bbs_id,
										   'access_date' => 'now'
										   );

		if ($bbs_access_history_row) {
			// レコードが存在する場合はUPDATE
			ACSBBSAccessHistoryModel::update_bbs_access_history($bbs_access_history_form);
		} else {
			// レコードが存在しない場合はINSERT
			ACSBBSAccessHistoryModel::insert_bbs_access_history($bbs_access_history_form);
		}
	}

	/**
	 * 掲示板の親記事を削除する
	 *
	 * @param 親記事ID
	 * @return 成功(true) / 失敗(false)
	 */
	static function delete_bbs($bbs_obj) {
		$bbs_id = $bbs_obj['bbs_id'];
		
		ACSDB::_do_query("BEGIN");
		//サブ記事の取得
		$sub_row_array = ACSBBS::get_bbs_res_row_array($bbs_id);
		$bbs_res_id_array = array();
		if(count($sub_row_array) > 0){
			foreach ($sub_row_array as $index => $sub_row) {
				array_push($bbs_res_id_array, $sub_row['bbs_res_id']);
			}
			//サブ記事の削除設定
			$ret = ACSBBS::delete_bbs_res($bbs_res_id_array);
			if(!$ret){
				ACSDB::_do_query("ROLLBACK");
				echo ACSMsg::get_mdmsg(__FILE__,'M001');
				return false;
			}
		}
		//親記事が持っているファイル情報の削除
		$bbs_row = ACSBBSFile::select_bbs_file_row($bbs_id);
		$file_id = $bbs_row['file_id'];
		if($file_id != ''){
			$ret = ACSBBSFile::delete_bbs_file($file_id,$bbs_id);
			if(!$ret){
				ACSDB::_do_query("ROLLBACK");
				echo ACSMsg::get_mdmsg(__FILE__,'M002');
				return false;
			}
		}
		//親記事の削除(削除フラグ扱い)
		$sql = "UPDATE bbs";
		$sql .= " SET bbs_delete_flag = 't'";
		$sql .= " WHERE bbs.bbs_id = $bbs_id";
		$ret = ACSDB::_do_query($sql);
		if(!$ret){
			ACSDB::_do_query("ROLLBACK");
			echo ACSMsg::get_mdmsg(__FILE__,'M003');
			return false;
		}
				
		ACSDB::_do_query("COMMIT");
	 	return true;
	}
	

	/**
	 * 掲示板の返信記事を削除する
	 *
	 * @param 親記事ID
	 * @return 成功(true) / 失敗(false)
	 */
	static function delete_bbs_res($bbs_res_id_array) {
		/*
		if (count($bbs_res_id_array) > 1) {
			$bbs_res_id = implode(',', $bbs_res_id_array);
		}else{
			$bbs_res_id = $bbs_res_id_array;
		}
		*/
		$bbs_res_id_csv = implode(',', $bbs_res_id_array);
		ACSDB::_do_query("BEGIN");
		//サブ記事の削除
		$sql = "UPDATE bbs_res";
		$sql .= " SET bbs_res_delete_flag = 't'";
		$sql .= " WHERE bbs_res.bbs_res_id IN($bbs_res_id_csv)";
		$ret = ACSDB::_do_query($sql);
		if(!$ret){
			ACSDB::_do_query("ROLLBACK");
			echo ACSMsg::get_mdmsg(__FILE__,'M001');
			return false;
		}
		ACSDB::_do_query("COMMIT");
	 	return true;
	}

	/**
	 * get_bbs_rss_row_array メソッド
	 *
	 * RSS表示情報の作成
	 * 条件：公開範囲＝パブリックリリース & 削除フラグではないもの
	 * @param  community_id_array １個のときも複数でも対応できるようにする
	 *
	 * @order  bbs_last_post_date 最新順に取得
	 * @return row_array          掲示板親記事　の配列
	 *
	 * @return $row_array RSS出力データ
	 */
	static function get_bbs_rss_row_array($community_id_array,$flag = 0) {
		if (count($community_id_array) > 1) {
			$community_id = implode(',', $community_id_array);
		}else{
			$community_id = $community_id_array;
		}

		$sql  = "SELECT * ,bbs.bbs_id as bbs_id";
		$sql .= " FROM (bbs LEFT OUTER JOIN bbs_file ON bbs.bbs_id = bbs_file.bbs_id) , open_level_master ";
		$sql .= " WHERE bbs.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND open_level_master.open_level_name = '".ACSMsg::get_mst('open_level_master','D06')."'";	//公開範囲：パブリックリリース
		$sql .= "  AND bbs.bbs_delete_flag = 'f'";						//削除フラグではないもの
		if($flag == 0){
			// 除外するコミュニティIDの指定があった場合のみ、絞り込む
			if ($community_id) {
				$sql .= "  AND bbs.community_id NOT IN( $community_id )";	//該当しないコミュニティすべて
			}
		}else{
	 		$sql .= "  AND bbs.community_id IN( $community_id )";	//該当するコミュニティのみ
		}
		$sql .= " ORDER BY post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}
	
	/**
	 * BBSファイル情報array取得
	 * @param $where_list 検索用条件指定
	 */
	static function get_bbs_where_array ($where_list , $open_lebel_cd , $no_array) {
		$sql  = "SELECT *";
		$sql .= " FROM bbs INNER JOIN open_level_master ON bbs.open_level_code = open_level_master.open_level_code";
		$sql .= " WHERE bbs.bbs_delete_flag = 'f'";
		if($open_lebel_cd != '00'){
			$sql .= "  AND bbs.open_level_code ='$open_lebel_cd'";
		}
		if($where_list != ''){
			$sql .= $where_list;
		}
		if(count($no_array) > 0){
			$no_array = ACSLib::get_sql_value_array($no_array);
			$sql .= " AND bbs.bbs_id NOT IN (" . implode(", ", $no_array) . ")";
		}
		$sql .= " ORDER BY bbs.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}
	
	/**
	 * BBSを検索するためのwhere句の１部を作成
	 *
	 * @param $form　条件
	    [id] => 1000
	    [move_id] => 2
	    [q_text] => 検索条件									//条件（※必須）
	    [search_title] => title_in_serch			//件名を選択
	    [search_all] => subject_in_serch			//本文を選択
	    [open_level_code] => 00								//公開範囲（00は選択なし）（※必須）
	    [search_all_about] => all_in_serch		//すべてのCommunityを選択
	 * @return str_array($like_sql , $err_str , $str_count)
	 */
	 static function set_bbs_where_list($form , $flag) {
	 	$str_array = array();
	 	$str_array['like_sql'] = ""; //成功時の戻り値
		$str_array['err_str']  = "";	//失敗時の戻り値
		$str_array['str_count'] = 1;  //成功時のキーワード数
	// 検索キーワードの取得（注意！バイト単位で処理）
		$search_text = $form['q_text'];				//formから検索文字列を取得
//〜〜〜〜〜〜〜〜〜〜〜〜〜〜配列として利用(Likeの作成)〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜
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
				$str_title = " bbs.subject";
				$str_like_array = ACSDiary::create_sql_where_field($search_args , $str_title , $flag);
				$str_like = " (" . $str_like_array['str_like'] .") ";
				$str_array['str_count'] = $str_like_array['str_count'];
			}
			//本文を選択している場合
			if($form['search_all']){																
				$str_body = " bbs.body";
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
				$str_array['err_str'] = ACSMsg::get_mdmsg(__FILE__,'M005');
			}else{
				$str_array['like_sql'] = $str_array['like_sql'] . $str_array['like_sql'] ." AND (" . $str_like .") ";
			}
		}
//〜〜〜〜〜〜〜〜〜〜〜〜〜〜特定日記検索〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜〜
		//必ずAND
		if(!$form['search_all_about']){													//すべての	Communityを選択していない場合
				$str_array['like_sql'] = $str_array['like_sql'] ." AND bbs.community_id  = " . $form['community_id'];
		}
		// エラーメッセージが配列になってしまう暫定対処
		return $str_array;
	}

	/**
	 * BBSを検索するためのwhere句の１部を作成2
	 * 件名と本文がそれぞれ単独指定・複合指定になる可能性を持つため
	 * 列ごとのｷｰﾜｰﾄﾞを繰り返す処理のみを行う関数
	 *
	 * @param  $query_array_array ｷｰﾜｰﾄﾞ配列
	 * @param  $str_title 列名
	 * @return $where_sql 作成したSQL条件文
	 */
	static function create_sql_where_field($query_array_array , $str_field ,$flag){
		$str_like = "";
		foreach ($query_array_array as $query_array) {			//全ｷｰﾜｰﾄﾞに対する処理
			if (!count($query_array)) {
				continue;																				//１ｷｰﾜｰﾄﾞ中に値が無い場合（区切り文字？）
			}
			foreach ($query_array as $id => $str_q) {					//１ｷｰﾜｰﾄﾞに対する処理（文字種のためor処理）
					$str_q = pg_escape_string($str_q);
					ACSLib::escape_ilike($str_q);
					if($id == 0){
							$str_like = $str_field . " LIKE '%" . $str_q ."%'";
					}else{
							$where_sql['str_count'] = 2;
							$str_like = $str_like . " OR " . $str_field . " LIKE '%" . $str_q ."%'";
					}
			}
			if ($str_like != '') {													//全ｷｰﾜｰﾄﾞをANDかORでつなげる
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
	 * 最新の掲示板親記事一覧を取得する (掲示板RSS用)
	 *
	 * @param $community_id ユーザコミュニティID
	 * @param $term 取得期間
	 * @return 掲示版親記事の一覧 (連想配列の配列)
	 */
	static function get_new_bbs_rss_row_array($community_id, $term) {
		$community_id = pg_escape_string($community_id);
		$term = pg_escape_string($term);

		// 掲示板の新着記事を最新順に取得する
		$sql  = "SELECT *, bbs.bbs_id as bbs_id, acs_get_bbs_last_post_date(bbs.bbs_id) as bbs_last_post_date";
		$sql .= " FROM (bbs LEFT OUTER JOIN bbs_file ON bbs.bbs_id = bbs_file.bbs_id), community, open_level_master";
		$sql .= " WHERE bbs.community_id = '$community_id'";
		$sql .= "  AND bbs.community_id = community.community_id";
		$sql .= "  AND bbs.open_level_code = open_level_master.open_level_code";
		// 削除フラグOFF
		$sql .= "  AND bbs.bbs_delete_flag != 't'";
		// $term日以内に投稿のあった記事を取得
		$sql .= "  AND acs_get_bbs_last_post_date(bbs.bbs_id)::DATE > (CURRENT_DATE - '@ $term days'::INTERVAL)";
		// 最終投稿日時でソート
		$sql .= " ORDER BY bbs_last_post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 掲示板RSSを出力する
	 *
	 * @param $community_row 対象コミュニティ情報
	 * @param $bbs_row_array 掲示板親記事一覧
	 * @param $params パラメータ等
	 */
	static function print_bbs_rss($community_row, $bbs_row_array, $params) {
		// 使用クラス: acs/webapp/lib/feedcreator/feedcreator.class.php
		$rss = new UniversalFeedCreator();

		// 概要等 <channel>
		$rss->useCached();
		$rss->title = $community_row['community_name']; // コミュニティ名
		$rss->description = $community_row['community_profile']['contents_value'];  // プロフィール (公開範囲別)
		$rss->link = $params['base_url'] . $community_row['top_page_url']; // コミュニティトップページURL
		$rss->url = $params['base_url'] . $community_row['image_url'];     // 画像URL  <image rdf:resource="...">
		$rss->syndicationURL = $rss_syndication_url;                              // 自身のURL <channel rdf:about="...">

		// ロゴ画像 <image>
		$image = new FeedImage();
		$image->title = $community_row['image_title'];     // ファイル名
		$image->link = ACSMsg::get_mdmsg(__FILE__,'M006'); // 研究室ロゴ画像
		$image->url = $params['base_url'] . $community_row['image_url'];
		$rss->image = $image;

		// 1件のダイアリー: <item>
		foreach ($bbs_row_array as $index => $bbs_row) {
			// CRLF → LF
			$body = preg_replace('/\r\n/', "\n", $bbs_row['body']);

			$item = new FeedItem(); 
			$item->post_date = $bbs_row['post_date']; 
			$item->title = $bbs_row['subject']; 
			$item->link = $params['base_url'] . $bbs_row['bbs_res_url'];
			$item->description = $body;
			if ($bbs_row['file_url'] != '') {
				$item->image_link = $params['base_url'] . $bbs_row['file_url'];
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
