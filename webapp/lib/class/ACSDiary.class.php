<?php
/**
 * �������꡼�����̥��饹
 * 
 * ACSDiary.class.php
 * @package  acs/webapp/lib/class
 * @author   w-ota                     @editor akitsu
 * @since    PHP 5.0
 */
// $Id: ACSDiary.class.php,v 1.36 2008/05/28 00:38:00 y-yuki Exp $

/*
 * �������꡼���饹
 */
class ACSDiary {
	/**
	 * �������꡼�οƵ����������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @return �������꡼�Ƶ����ΰ��� (Ϣ�����������)
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
	 * �������꡼�οƵ���������1��������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @param $year ǯ
	 * @param $month ��
	 * @param $day ��
	 * @return �������꡼�Ƶ����ΰ��� (Ϣ�����������)
	 */
	static function get_diary_row_array_by_year_month_day($user_community_id, $year, $month, $day) {
		$user_community_id = pg_escape_string($user_community_id);

		// ǯ, ��, ��
		$year = sprintf("%04d", $year);
		$month = sprintf("%02d", $month);
		$day = sprintf("%02d", $day);
		// ������ (YYYY/MM/DD)
		$date = "$year/$month/$day";

		$sql  = "SELECT *, acs_get_diary_comment_num(diary.diary_id) as diary_comment_num, diary.diary_id as diary_id";
		$sql .= " FROM diary LEFT OUTER JOIN diary_file USING(diary_id)";
		$sql .= ", community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id = '$user_community_id'";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND diary.diary_delete_flag = 'f'";
		// post_date����ꤹ��
		$sql .= "  AND diary.post_date::DATE = '$date'::DATE";
		$sql .= " ORDER BY diary.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �������꡼�οƵ���������1��ʬ������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @param $year ǯ
	 * @param $month ��
	 * @return �������꡼�Ƶ����ΰ��� (Ϣ�����������)
	 */
	static function get_diary_row_array_by_year_month($user_community_id, $year, $month) {
		$user_community_id = pg_escape_string($user_community_id);

		// ��κǽ��������
		$end_day = ACSLib::get_end_day($year, $month);
		// ǯ, ��
		$year = sprintf("%04d", $year);
		$month = sprintf("%02d", $month);

		// ������ (YYYY/MM/DD)
		$start_date = "$year/$month/01";
		// ��λ�� (YYYY/MM/DD)
		$end_date = "$year/$month/$end_day";

		$sql  = "SELECT *, acs_get_diary_comment_num(diary.diary_id) as diary_comment_num, diary.diary_id as diary_id";
		$sql .= " FROM diary LEFT OUTER JOIN diary_file USING(diary_id)";
		$sql .= ", community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id = '$user_community_id'";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND diary.diary_delete_flag = 'f'";
		// post_date�δ��֤���ꤹ��
		$sql .= "  AND diary.post_date::DATE BETWEEN '$start_date'::DATE AND '$end_date'::DATE";
		$sql .= " ORDER BY diary.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * diary�ե��������array����
	 * @param $where_list �����Ѿ�����
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
	 * �����������꡼�οƵ����������������
	 *
	 * @return �����������꡼�οƵ�������
	 */
	static function get_new_open_diary_row_array() {
		$limit = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');

		$sql  = "SELECT *";
		$sql .= " FROM diary LEFT OUTER JOIN diary_file USING(diary_id)";
		$sql .= ", community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		// �桼�����ߥ�˥ƥ� ����ե饰OFF
		$sql .= "  AND USER_COMMUNITY.delete_flag != 't'";
		// �����ϰϤ����̸���
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND open_level_master.open_level_name = '".ACSMsg::get_mst('open_level_master','D01')."'";
		// �������꡼ ����ե饰OFF
		$sql .= "  AND diary.diary_delete_flag = 'f'";
		$sql .= " ORDER BY diary.post_date DESC";
		// LIMIT
		$sql .= " LIMIT $limit";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �������꡼�οƵ���������������
	 *
	 * @param $diary_id �������꡼ID
	 * @return �������꡼�οƵ������� (Ϣ������)
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
		// �������꡼ ����ե饰OFF
		$sql .= "  AND diary.diary_delete_flag != 't'";
		$sql .= " ORDER BY diary.post_date DESC";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * �������꡼�ο���Ѥߥ��ߥ�˥ƥ�������������
	 *
	 * @param $diary_id �������꡼ID
	 * @return �������꡼�ο���Ѥߥ��ߥ�˥ƥ����� (Ϣ�����������)
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
	 * �������꡼�Υ����Ȱ������������
	 *
	 * @param $diary_id �������꡼ID
	 * @return �ޥ��������꡼�Υ����Ȱ���(�������̥꥽����)
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
	 * �������꡼����Ͽ����
	 *
	 * @param $form �������꡼���������
	 * @return ����(��Ͽ���줿�������꡼ID) / ����(false)
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
		//�����ե��������Ͽ
		$file_obj = $form['new_file'];
			$ret = $file_obj->save_upload_file('DIARY');	//�ե������ǥ���������¸
			if($ret){
				$ret =  $file_obj->add_file();				//�ե���������DB����¸
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
		// ͧ�ͤ˸��������򤷤����
		$open_level_master_array = ACSDB::get_master_array('open_level');
		// ͧ�ͤ˸���
		if ($open_level_master_array[$form['open_level_code']] == ACSMsg::get_mst('open_level_master','D05')) {

			// �ޥ��ե�󥺥��롼�פ���ꤷ�����
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
				// ���ƤΥޥ��ե��
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
	 * �������꡼�����Ȥ���Ͽ����
	 *
	 * @param $form �������꡼�����Ⱦ��������
	 * @return ����(true) / ����(false)
	 */
	static function set_diary_comment($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

//��ջ���Τ���Υ�����ID
		$diary_comment_id_seq = ACSDB::get_next_seq('diary_comment_id_seq');

		$sql  = "INSERT INTO diary_comment";
		$sql .= " (diary_comment_id, diary_id, user_community_id, body,diary_comment_delete_flag)";
		$sql .= " VALUES ($diary_comment_id_seq, $form[diary_id], $form[user_community_id], $form[body],'f')";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * �ޥ��ե�󥺤ο���������꡼�������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID (�������꡼�ؤΥ��������ԤȤʤ�桼��)
	 * 		  $days ������������(�ǶᲿ���֤ο����������)
	 * @return ����������꡼���� (Ϣ�����������)
	 */
	static function get_new_diary_row_array($user_community_id, $days=false, $offset=false) {

		// �ޥ��ե�󥺤Υ桼�����ߥ�˥ƥ�ID��CSV���������
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

		// �ޥ��ե�󥺤Υ������꡼��ǿ���˼�������
		$sql  = "SELECT *, acs_is_unread_diary('$user_community_id', diary.diary_id) as is_unread,";
		$sql .= "       acs_get_diary_comment_num(diary.diary_id) as diary_comment_num";
		$sql .= " FROM diary, community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.community_id IN ($friends_user_community_id_csv)";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";
		// �������꡼ ����ե饰OFF
		$sql .= "  AND diary.diary_delete_flag = 'f'";

		//------ 2007.2 ɽ������û���б�
		// �������꤬������
		if($days !== false){
			$sql = $sql . " AND " . ACSLib::get_sql_condition_from_today("diary.post_date", $days);
		}

		if($offset !== false){
			$sql = $sql . " ORDER BY diary.post_date DESC";
			// ɽ��������� //
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
	 * �������꡼�Υ����������������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @param $diary_id �������꡼ID
	 * @return �������꡼�Υ�������������� (Ϣ������)
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
	 * �������꡼�Υ��������������Ͽ����
	 *
	 * @param $diary_id
	 * @return ����(true) / ����(false)
	 */
	static function set_diary_access_history($user_community_id, $diary_id) {
		// �������꡼������������
		$diary_access_history_row = ACSDiary::get_diary_access_history_row($user_community_id, $diary_id);

		$diary_access_history_form = array(
										   'user_community_id' => $user_community_id,
										   'diary_id' => $diary_id,
										   'access_date' => 'now'
										   );

		if ($diary_access_history_row) {
			// �쥳���ɤ�¸�ߤ������UPDATE
			ACSDiaryAccessHistoryModel::update_diary_access_history($diary_access_history_form);
		} else {
			// �쥳���ɤ�¸�ߤ��ʤ�����INSERT
			ACSDiaryAccessHistoryModel::insert_diary_access_history($diary_access_history_form);
		}
	}

	/**
	 * �����Ȥ����������꡼������������� (��ʬ�Υ������꡼�ʳ����о�)
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID (�����Ȥ����桼��)
	 * 		  $days ������������(�ǶᲿ���֤ο����������)
	 * @return ����������꡼���� (Ϣ�����������)
	 */
	static function get_commented_diary_row_array($user_community_id, $days=false, $offset=false) {
		
		$user_community_id = pg_escape_string($user_community_id);

		// �����Ȥ����������꡼
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

		// �����Ȥ����������꡼��ǿ���˼�������
		$sql  = "SELECT *, acs_get_diary_comment_num(diary.diary_id) as diary_comment_num, acs_get_diary_last_post_date(diary.diary_id) as diary_last_post_date, acs_is_unread_diary_comment('$user_community_id', diary.diary_id) as is_unread";
		$sql .= " FROM diary, community as USER_COMMUNITY, user_info, open_level_master";
		$sql .= " WHERE diary.diary_id IN ($commented_diary_id_csv)";
		$sql .= "  AND diary.community_id = USER_COMMUNITY.community_id";
		$sql .= "  AND USER_COMMUNITY.community_id = user_info.user_community_id";
		$sql .= "  AND diary.open_level_code = open_level_master.open_level_code";

		//------- 2007.2 ɽ������û���б�
		// �������꤬������
		if ($days !== false) {
			$sql = $sql . " AND " . 
					ACSLib::get_sql_condition_from_today("acs_get_diary_last_post_date(diary.diary_id)", $days);
		}

		if ($offset != false) {
		$sql = $sql . " ORDER BY diary_last_post_date DESC";
			// ɽ��������� //
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
	 * ���女���ȤΤ���(�ޥ�)�������꡼�������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID (�оݤΥޥ��������꡼�Υ桼��)
	 * @return ���女���ȤΤ���������꡼���� (Ϣ�����������)
	 */
	static function get_new_comment_diary_row_array($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		// SQL�ؿ�: acs_get_diary_c_last_post_date(diary.diary_id)
		//          �ǿ��Υ������꡼�����Ȥ�������������롣�����Ȥ�0��Ǥ����null
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
	 * �������꡼��������
	 *
	 * @param $diary_id �������꡼ID
	 * @return ����(true) / ����(false)
	 */
	static function delete_diary($diary_id) {
	
		// BEGIN
		
		// diary �����Ȥμ���
		$sub_row_array = ACSDiary::get_diary_comment_row_array($diary_id);
		$diary_comment_id_array = array();
		if(count($sub_row_array) > 0){
			foreach ($sub_row_array as $index => $sub_row) {
				array_push($diary_comment_id_array, $sub_row['diary_comment_id']);
			}
			//�����Ȥκ��
			$ret = ACSDiary::delete_diary_comment($diary_comment_id_array);
			if(!$ret){
				return false;
			}
		}

		//���������äƤ���ե��������κ��
		$diary_row = ACSDiary::get_diary_row($diary_id);
			$file_id = $diary_row['file_id'];
		if($file_id != ''){
			$ret = ACSDiaryFile::delete_diary_file($file_id,$diary_id);
			if(!$ret){
				echo "ERROR: Delete attached file information failed.";
				return false;
			}
		}
	
		//�����κ��(����ե饰����)	
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
	 * �����Υ����Ȥ�������
	 *
	 * @param $diary_comment_id_array ������ID_array
	 * @return ����(true) / ����(false)
	 */
	 static function delete_diary_comment($diary_comment_id_array) {
		if (count($diary_comment_id_array) > 1) {
			$diary_comment_id = implode(',', $diary_comment_id_array);
		} else {
			$diary_comment_id = array_shift($diary_comment_id_array);
		}

		//�����Ȥκ��
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
	 * �����򸡺����뤿���where��Σ��������
	 *
	 * @param $form�����
	    [id] => 1000
	    [move_id] => 2
	    [q_text] => �������					//���ʢ�ɬ�ܡ�
	    [search_title] => title_in_serch		//��̾������
	    [search_all] => subject_in_serch		//��ʸ������
	    [open_level_code] => 00					//�����ϰϡ�00������ʤ��ˡʢ�ɬ�ܡ�
	    [search_all_about] => all_in_serch		//���٤Ƥ�����������
	 * @return str_array($like_sql , $err_str , $str_count)
	 */
	static  function set_diary_where_list($form , $flag) {
		$str_array = array();
		$str_array['like_sql'] = ""; //�������������
		$str_array['err_str']  = "";	//���Ի��������
		$str_array['str_count'] = 1;  //�������Υ�����ɿ�

		// ����������ɤμ�������ա��Х���ñ�̤ǽ�����
		$search_text = $form['q_text'];				//form���鸡��ʸ��������

		//��������������������������Ȥ�������(Like�κ���)����������������������
		if($search_text != ''){
			$search_args = ACSLib::get_query_array_array($search_text);	//����ʸ����������Ѵ�
		}
		if(count($search_args) > 0){						//����ʸ����¸�ߤ�����
			$search_args = ACSLib::escape_sql_array($search_args);
		//�������where����ɵ�
		//���Τ����줫��ɬ��
			$str_like = '';
			//��̾�����򤷤Ƥ�����
			if($form['search_title']){
				$str_title = " diary.subject";
				$str_like_array = ACSDiary::create_sql_where_field($search_args , $str_title , $flag);
				$str_like = " (" . $str_like_array['str_like'] .") ";
				$str_array['str_count'] = $str_like_array['str_count'];
			}
			//��ʸ�����򤷤Ƥ�����
			if($form['search_all']){																
				$str_body = " diary.body";
				if($str_like != ""){
					$str_like = $str_like . " OR ";											//��̾�ȶ������򤷤Ƥ�����
				}
				$where_sql_array = ACSDiary::create_sql_where_field($search_args , $str_body, $flag);
				$where_sql = " (" . $where_sql_array['str_like'] .") ";
				$str_like =  $str_like . $where_sql;
				$str_array['str_count'] = $where_sql_array['str_count'];
			}
			//�������where����ɵ���λ
			if($str_like == ""){																		//������ɤ�����Τˡ��оݤ��ʤ����
				$str_array['err_str'] = ACSMsg::get_mdmsg(__FILE__,'M001');
				return $str_array;
			}else{
				$str_array['like_sql'] = $str_array['like_sql'] . $str_array['like_sql'] ." AND (" . $str_like .") ";
			}
		}
		//��������������������������������������������������������������������������������������
		//ɬ��AND
		if(!$form['search_all_about']){													//���٤Ƥ����������򤷤Ƥ��ʤ����
				$str_array['like_sql'] = $str_array['like_sql'] ." AND diary.community_id  = " . $form['id'];
		}
		return $str_array;
	}

	/**
	 * �����򸡺����뤿���where��Σ��������2
	 * ��̾����ʸ�����줾��ñ�Ȼ��ꡦʣ�����ˤʤ��ǽ������Ĥ���
	 * �󤴤ȤΎ����܎��Ďޤ򷫤��֤������Τߤ�Ԥ��ؿ�
	 *
	 * @param  $query_array_array �����܎��Ď�����
	 * @param  $str_title ��̾
	 * @return $where_sql ��������SQL���ʸ
	 */
	static function create_sql_where_field($query_array_array , $str_field ,$flag){
		$str_like = "";
		foreach ($query_array_array as $query_array) {		//�������܎��Ďޤ��Ф������
			if (!count($query_array)) {
				continue;									//�������܎��Ď�����ͤ�̵�����ʶ��ڤ�ʸ������
			}
			foreach ($query_array as $id => $str_q) {		//�������܎��Ďޤ��Ф��������ʸ����Τ���or������
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
			if ($str_like != '') {			//�������܎��Ďޤ�AND��OR�ǤĤʤ���
				if($where_sql['str_like'] != '' ){
					switch($flag){
						case 1;	//���٤ƹ���
							$where_sql['str_like'] = $where_sql['str_like'] ." AND ($str_like)"; break;
						case 2: //���٤ƹ��פ���
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
	 * �������꡼�Υ����Ȥ��������
	 *
	 * @param $diary_comment_id �������꡼ID
	 * @return �ޥ��������꡼�Υ�����(�������̥꥽����)
	 */
	static function get_diary_comment_row ($diary_comment_id) {
		$sql  = "SELECT *";
		$sql .= " FROM (diary_comment LEFT OUTER JOIN community ON diary_comment.user_community_id = community.community_id) as JOINED_DIARY_COMMENT";
		$sql .= " WHERE JOINED_DIARY_COMMENT.diary_comment_id = '$diary_comment_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * �ǿ��Υ������꡼������������� (�������꡼RSS��)
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @param $term ��������
	 * @return �������꡼�Ƶ����ΰ��� (Ϣ�����������)
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
		// $term���������Ƥ����
		$sql .= "  AND diary.post_date::DATE > (CURRENT_DATE - '@ $term days'::INTERVAL)";
		$sql .= " ORDER BY diary.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �������꡼RSS����Ϥ���
	 *
	 * @param $target_user_info_row �оݥ桼������
	 * @param $diary_row_array �������꡼����
	 * @param $params �ѥ�᡼����
	 */
	static function print_diary_rss($target_user_info_row, $diary_row_array, $params) {
		// ���ѥ��饹: acs/webapp/lib/feedcreator/feedcreator.class.php

		$rss = new UniversalFeedCreator();

		// ������ <channel>
		$rss->useCached();
		$rss->title = ACSMsg::get_tag_replace(ACSMsg::get_mdmsg(__FILE__,'NAME'),
											  array("{USER_NAME}" => $target_user_info_row['community_name'])); // ��������
		$rss->description = $params['description'];                               // �ץ�ե����� (�����ϰ���)
		$rss->link = $params['base_url'] . $target_user_info_row['top_page_url']; // �ޥ��ڡ���URL
		$rss->url = $params['base_url'] . $target_user_info_row['image_url'];     // ����URL  <image rdf:resource="...">
		$rss->syndicationURL = $params['rss_syndication_url'];                    // ���Ȥ�URL <channel rdf:about="...">

		// ������ <image>
		$image = new FeedImage();
		$image->title = $target_user_info_row['image_title']; // �ե�����̾
		$image->link = ACSMsg::get_mdmsg(__FILE__,'M002');    // �̿�
		$image->url = $params['base_url'] . $target_user_info_row['image_url'];
		$rss->image = $image;

		// 1��Υ������꡼: <item>
		foreach ($diary_row_array as $index => $diary_row) {
			// CRLF �� LF
			$body = preg_replace('/\r\n/', "\n", $diary_row['body']);

			$item = new FeedItem(); 
			$item->post_date = $diary_row['post_date']; 
			$item->title = $diary_row['subject']; 
			$item->link = $params['base_url'] . $diary_row['diary_comment_url'];
			$item->description = $body;
			if ($diary_row['file_url'] != '') {
				$item->image_link = $params['base_url'] . $diary_row['file_url'];
			}
			$item->description2 = $body; //��2����ʸ  <content:encoded>
			
			$rss->addItem($item); 
		}

		// http-header
		mb_http_output('pass');
		header('Content-type: application/xml; charset=UTF-8');
		echo mb_convert_encoding($rss->createFeed("RSS1.0"), 'UTF-8', mb_internal_encoding());
	}
}
?>
