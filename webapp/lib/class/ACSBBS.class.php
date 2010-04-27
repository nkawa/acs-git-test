<?php
/**
 * �Ǽ��ġ����̥��饹
 * 
 * ACSBBS.class.php
 * @package  acs/webapp/lib/class
 * @author   ota                     @editor akitsu
 * @since    PHP 4.0
 */
// $Id: ACSBBS.class.php,v 1.38 2008/05/21 01:53:36 y-yuki Exp $

/*
 * ���ߥ�˥ƥ�
 */
class ACSBBS {

	/**
	 * �Ǽ��ĤοƵ����������������
	 *
	 * @param ���ߥ�˥ƥ�ID
	 * @return �Ǽ��ĤοƵ������� (Ϣ�����������)
	 */
	static function get_bbs_row_array($community_id) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT bbs.*, open_level_master.*, acs_get_bbs_last_post_date(bbs.bbs_id) as bbs_last_post_date, acs_get_bbs_res_num(bbs.bbs_id) as bbs_res_num, bbs_file.file_id as file_id, community.community_name as community_name";
		$sql .= " FROM ((bbs LEFT OUTER JOIN community ON bbs.user_community_id = community.community_id)";
		$sql .= " LEFT OUTER JOIN bbs_file ON bbs.bbs_id = bbs_file.bbs_id) ,";
		$sql .= " open_level_master";
		$sql .= " WHERE bbs.community_id = '$community_id'";
		$sql .= "  AND bbs.open_level_code = open_level_master.open_level_code";
		// ����ե饰OFF
		$sql .= "  AND bbs.bbs_delete_flag != 't'";
		$sql .= " ORDER BY bbs_last_post_date DESC";		//������ο�������

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * bbs_id����ꤷ�ƷǼ��ĤοƵ���������������
	 *
	 * @param bbs_id
	 * @return �Ǽ��ĤοƵ������� (Ϣ������)
	 */
	static function get_bbs_row($bbs_id) {
		$bbs_id = pg_escape_string($bbs_id);
		$sql  = "SELECT *, bbs.community_id AS bbs_community_id";
		$sql .= " FROM bbs LEFT OUTER JOIN community on bbs.user_community_id = community.community_id";
		$sql .= "  LEFT OUTER JOIN bbs_file USING(bbs_id)";
		$sql .= ", open_level_master";
		$sql .= " WHERE bbs.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND bbs.bbs_id = '$bbs_id'";
		// ����ե饰OFF
		$sql .= "  AND bbs.bbs_delete_flag != 't'";
		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * bbs_id����ꤷ�ƷǼ��Ĥο���Ѥߥ��ߥ�˥ƥ��������������
	 *
	 * @param bbs_id
	 * @return ���ߥ�˥ƥ����� (Ϣ�����������)
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
	 * bbs_res_id����ꤷ�ƷǼ��Ĥ��ֿ��������������
	 *
	 * @param bbs_res_id
	 * @return �Ǽ��Ĥ��ֿ��������� (Ϣ�����������)
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
	 * bbs_id����ꤷ�ƷǼ��Ĥ��ֿ������������������
	 *
	 * @param bbs_id
	 * @return �Ǽ��Ĥ��ֿ��������� (Ϣ�����������)
	 */
	static function get_bbs_res_row_array($bbs_id) {
		$bbs_id = pg_escape_string($bbs_id);

		$sql  = "SELECT *";
		$sql .= " FROM bbs_res LEFT OUTER JOIN community ON bbs_res.user_community_id = community.community_id";
		$sql .= " WHERE bbs_res.bbs_id = '$bbs_id'";
		$sql .= " ORDER BY bbs_res.post_date ASC";		//������ο�������   bbs_res.delete_flag,(����ե饰�Τʤ���΢�������ե饰�Τ�����)

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �Ǽ��ĤοƵ�������Ͽ����
	 *
	 * @param �Ƶ������������
	 * @return ����(true) / ����(false)
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
			//�����ե��������Ͽ
			$file_obj = $form['new_file'];
			$ret = $file_obj->save_upload_file('BBS');		//�ե������ǥ���������¸
			if($ret){
				$ret =  $file_obj->add_file();				//�ե���������DB����¸
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
		// ����� (���ФΤ�)
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
	 * �Ǽ��ĤοƵ����򹹿�����
	 *
	 * @param �Ƶ������������
	 * @return ����(true) / ����(false)
	 */
	static function update_bbs($form) {
		$org_form = $form;

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// bbs����
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
			// �����ե�����obj�μ���
			$file_obj = ACSFile::get_upload_file_info_instance($_FILES['new_file'], $form['community_id'], $form['user_community_id']);
			// �ե������ǥ���������¸
			$ret = $file_obj->save_upload_file('BBS');
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			//�ե���������DB����¸
			$ret = $file_obj->add_file();
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}

			// bbs�ե�����ι���
			$ret = ACSBBSFile::update_bbs_file($file_obj, $form['bbs_id']);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return $ret;
			}
		}

		return $ret;
	}

	/**
	 * �Ǽ��Ĥ��ֿ���������Ͽ����
	 *
	 * @param �ֿ��������������
	 * @return ����(true) / ����(false)
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
	 * �Ǽ��ĤοƵ����򹹿�����
	 *
	 * @param �Ƶ������������
	 * @return ����(true) / ����(false)
	 */
	static function update_bbs_res($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// bbs����
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
	 * �Ǽ��Ĥο��嵭���������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID (BBS�ؤΥ��������ԤȤʤ�桼��)
     *        $days ������������(�ǶᲿ���֤ο����������)
	 * @return ���嵭������
	 *
	 */
	static function get_new_bbs_row_array($user_community_id, $days=false, $offset=false) {

		// �ޥ����ߥ�˥ƥ��Υ��ߥ�˥ƥ�ID��CSV���������
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

		// �Ǽ��Ĥο��嵭����ǿ���˼�������
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
			// ɽ��������� //
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
	 * �Ǽ��Ĥο���ѥ֥�å���꡼���������������
	 *
	 * @return ����ѥ֥�å���꡼������
	 */
	static function get_new_bbs_for_press_release_row_array() {
		$limit = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');

		$sql  = "SELECT *";
		$sql .= " FROM bbs LEFT OUTER JOIN bbs_file USING(bbs_id), open_level_master as BBS_OLM,";
		$sql .= "  community, community_type_master,";
		$sql .= "  contents as SELF_C, contents_type_master as SELF_CTM,";  // ����
		$sql .= "  open_level_master as SELF_OLM";                          // ���Τ�open_level_master

		$sql .= " WHERE bbs.community_id = community.community_id";
		$sql .= "  AND community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community_type_master.community_type_name = '".ACSMsg::get_mst('community_type_master','D40')."'";

		// BBS: �ѥ֥�å���꡼��
		$sql .= "  AND bbs.open_level_code = BBS_OLM.open_level_code";
		$sql .= "  AND BBS_OLM.open_level_name = '".ACSMsg::get_mst('open_level_master','D06')."'";
		// BBS: ����ե饰OFF
		$sql .= "  AND bbs.bbs_delete_flag != 't'";
		// BBS: �Ǻܽ�λ����ã���Ƥ��ʤ�
		$sql .= "  AND (bbs.expire_date is null OR CURRENT_DATE <= bbs.expire_date::DATE)";

		// ���ߥ�˥ƥ�: ����ե饰OFF
		$sql .= "  AND community.delete_flag != 't'";
		// ���ߥ�˥ƥ�: ��������ߥ�˥ƥ��Ǥʤ�
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
	 * �Ǽ��ĤΥ����������������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @param $bbs_id bbs_id
	 * @return �Ǽ��ĤΥ�������������� (Ϣ������)
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
	 * �Ǽ��ĤΥ��������������Ͽ����
	 *
	 * @param $bbs_id
	 * @return ����(true) / ����(false)
	 */
	static function set_bbs_access_history($user_community_id, $bbs_id) {
		// �Ǽ��ĥ�����������
		$bbs_access_history_row = ACSBBS::get_bbs_access_history_row($user_community_id, $bbs_id);

		$bbs_access_history_form = array(
										   'user_community_id' => $user_community_id,
										   'bbs_id' => $bbs_id,
										   'access_date' => 'now'
										   );

		if ($bbs_access_history_row) {
			// �쥳���ɤ�¸�ߤ������UPDATE
			ACSBBSAccessHistoryModel::update_bbs_access_history($bbs_access_history_form);
		} else {
			// �쥳���ɤ�¸�ߤ��ʤ�����INSERT
			ACSBBSAccessHistoryModel::insert_bbs_access_history($bbs_access_history_form);
		}
	}

	/**
	 * �Ǽ��ĤοƵ�����������
	 *
	 * @param �Ƶ���ID
	 * @return ����(true) / ����(false)
	 */
	static function delete_bbs($bbs_obj) {
		$bbs_id = $bbs_obj['bbs_id'];
		
		ACSDB::_do_query("BEGIN");
		//���ֵ����μ���
		$sub_row_array = ACSBBS::get_bbs_res_row_array($bbs_id);
		$bbs_res_id_array = array();
		if(count($sub_row_array) > 0){
			foreach ($sub_row_array as $index => $sub_row) {
				array_push($bbs_res_id_array, $sub_row['bbs_res_id']);
			}
			//���ֵ����κ������
			$ret = ACSBBS::delete_bbs_res($bbs_res_id_array);
			if(!$ret){
				ACSDB::_do_query("ROLLBACK");
				echo ACSMsg::get_mdmsg(__FILE__,'M001');
				return false;
			}
		}
		//�Ƶ��������äƤ���ե��������κ��
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
		//�Ƶ����κ��(����ե饰����)
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
	 * �Ǽ��Ĥ��ֿ�������������
	 *
	 * @param �Ƶ���ID
	 * @return ����(true) / ����(false)
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
		//���ֵ����κ��
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
	 * get_bbs_rss_row_array �᥽�å�
	 *
	 * RSSɽ������κ���
	 * �������ϰϡ�ѥ֥�å���꡼�� & ����ե饰�ǤϤʤ����
	 * @param  community_id_array ���ĤΤȤ���ʣ���Ǥ��б��Ǥ���褦�ˤ���
	 *
	 * @order  bbs_last_post_date �ǿ���˼���
	 * @return row_array          �Ǽ��ĿƵ�����������
	 *
	 * @return $row_array RSS���ϥǡ���
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
		$sql .= "  AND open_level_master.open_level_name = '".ACSMsg::get_mst('open_level_master','D06')."'";	//�����ϰϡ��ѥ֥�å���꡼��
		$sql .= "  AND bbs.bbs_delete_flag = 'f'";						//����ե饰�ǤϤʤ����
		if($flag == 0){
			// �������륳�ߥ�˥ƥ�ID�λ��꤬���ä����Τߡ��ʤ����
			if ($community_id) {
				$sql .= "  AND bbs.community_id NOT IN( $community_id )";	//�������ʤ����ߥ�˥ƥ����٤�
			}
		}else{
	 		$sql .= "  AND bbs.community_id IN( $community_id )";	//�������륳�ߥ�˥ƥ��Τ�
		}
		$sql .= " ORDER BY post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}
	
	/**
	 * BBS�ե��������array����
	 * @param $where_list �����Ѿ�����
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
	 * BBS�򸡺����뤿���where��Σ��������
	 *
	 * @param $form�����
	    [id] => 1000
	    [move_id] => 2
	    [q_text] => �������									//���ʢ�ɬ�ܡ�
	    [search_title] => title_in_serch			//��̾������
	    [search_all] => subject_in_serch			//��ʸ������
	    [open_level_code] => 00								//�����ϰϡ�00������ʤ��ˡʢ�ɬ�ܡ�
	    [search_all_about] => all_in_serch		//���٤Ƥ�Community������
	 * @return str_array($like_sql , $err_str , $str_count)
	 */
	 static function set_bbs_where_list($form , $flag) {
	 	$str_array = array();
	 	$str_array['like_sql'] = ""; //�������������
		$str_array['err_str']  = "";	//���Ի��������
		$str_array['str_count'] = 1;  //�������Υ�����ɿ�
	// ����������ɤμ�������ա��Х���ñ�̤ǽ�����
		$search_text = $form['q_text'];				//form���鸡��ʸ��������
//��������������������������������Ȥ�������(Like�κ���)����������������������������������������������
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
				$str_title = " bbs.subject";
				$str_like_array = ACSDiary::create_sql_where_field($search_args , $str_title , $flag);
				$str_like = " (" . $str_like_array['str_like'] .") ";
				$str_array['str_count'] = $str_like_array['str_count'];
			}
			//��ʸ�����򤷤Ƥ�����
			if($form['search_all']){																
				$str_body = " bbs.body";
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
				$str_array['err_str'] = ACSMsg::get_mdmsg(__FILE__,'M005');
			}else{
				$str_array['like_sql'] = $str_array['like_sql'] . $str_array['like_sql'] ." AND (" . $str_like .") ";
			}
		}
//��������������������������������������������������������������������������������������
		//ɬ��AND
		if(!$form['search_all_about']){													//���٤Ƥ�	Community�����򤷤Ƥ��ʤ����
				$str_array['like_sql'] = $str_array['like_sql'] ." AND bbs.community_id  = " . $form['community_id'];
		}
		// ���顼��å�����������ˤʤäƤ��ޤ������н�
		return $str_array;
	}

	/**
	 * BBS�򸡺����뤿���where��Σ��������2
	 * ��̾����ʸ�����줾��ñ�Ȼ��ꡦʣ�����ˤʤ��ǽ������Ĥ���
	 * �󤴤ȤΎ����܎��Ďޤ򷫤��֤������Τߤ�Ԥ��ؿ�
	 *
	 * @param  $query_array_array �����܎��Ď�����
	 * @param  $str_title ��̾
	 * @return $where_sql ��������SQL���ʸ
	 */
	static function create_sql_where_field($query_array_array , $str_field ,$flag){
		$str_like = "";
		foreach ($query_array_array as $query_array) {			//�������܎��Ďޤ��Ф������
			if (!count($query_array)) {
				continue;																				//�������܎��Ď�����ͤ�̵�����ʶ��ڤ�ʸ������
			}
			foreach ($query_array as $id => $str_q) {					//�������܎��Ďޤ��Ф��������ʸ����Τ���or������
					$str_q = pg_escape_string($str_q);
					ACSLib::escape_ilike($str_q);
					if($id == 0){
							$str_like = $str_field . " LIKE '%" . $str_q ."%'";
					}else{
							$where_sql['str_count'] = 2;
							$str_like = $str_like . " OR " . $str_field . " LIKE '%" . $str_q ."%'";
					}
			}
			if ($str_like != '') {													//�������܎��Ďޤ�AND��OR�ǤĤʤ���
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
	 * �ǿ��ηǼ��ĿƵ���������������� (�Ǽ���RSS��)
	 *
	 * @param $community_id �桼�����ߥ�˥ƥ�ID
	 * @param $term ��������
	 * @return �Ǽ��ǿƵ����ΰ��� (Ϣ�����������)
	 */
	static function get_new_bbs_rss_row_array($community_id, $term) {
		$community_id = pg_escape_string($community_id);
		$term = pg_escape_string($term);

		// �Ǽ��Ĥο��嵭����ǿ���˼�������
		$sql  = "SELECT *, bbs.bbs_id as bbs_id, acs_get_bbs_last_post_date(bbs.bbs_id) as bbs_last_post_date";
		$sql .= " FROM (bbs LEFT OUTER JOIN bbs_file ON bbs.bbs_id = bbs_file.bbs_id), community, open_level_master";
		$sql .= " WHERE bbs.community_id = '$community_id'";
		$sql .= "  AND bbs.community_id = community.community_id";
		$sql .= "  AND bbs.open_level_code = open_level_master.open_level_code";
		// ����ե饰OFF
		$sql .= "  AND bbs.bbs_delete_flag != 't'";
		// $term���������ƤΤ��ä����������
		$sql .= "  AND acs_get_bbs_last_post_date(bbs.bbs_id)::DATE > (CURRENT_DATE - '@ $term days'::INTERVAL)";
		// �ǽ���������ǥ�����
		$sql .= " ORDER BY bbs_last_post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �Ǽ���RSS����Ϥ���
	 *
	 * @param $community_row �оݥ��ߥ�˥ƥ�����
	 * @param $bbs_row_array �Ǽ��ĿƵ�������
	 * @param $params �ѥ�᡼����
	 */
	static function print_bbs_rss($community_row, $bbs_row_array, $params) {
		// ���ѥ��饹: acs/webapp/lib/feedcreator/feedcreator.class.php
		$rss = new UniversalFeedCreator();

		// ������ <channel>
		$rss->useCached();
		$rss->title = $community_row['community_name']; // ���ߥ�˥ƥ�̾
		$rss->description = $community_row['community_profile']['contents_value'];  // �ץ�ե����� (�����ϰ���)
		$rss->link = $params['base_url'] . $community_row['top_page_url']; // ���ߥ�˥ƥ��ȥåץڡ���URL
		$rss->url = $params['base_url'] . $community_row['image_url'];     // ����URL  <image rdf:resource="...">
		$rss->syndicationURL = $rss_syndication_url;                              // ���Ȥ�URL <channel rdf:about="...">

		// ������ <image>
		$image = new FeedImage();
		$image->title = $community_row['image_title'];     // �ե�����̾
		$image->link = ACSMsg::get_mdmsg(__FILE__,'M006'); // ���漼������
		$image->url = $params['base_url'] . $community_row['image_url'];
		$rss->image = $image;

		// 1��Υ������꡼: <item>
		foreach ($bbs_row_array as $index => $bbs_row) {
			// CRLF �� LF
			$body = preg_replace('/\r\n/', "\n", $bbs_row['body']);

			$item = new FeedItem(); 
			$item->post_date = $bbs_row['post_date']; 
			$item->title = $bbs_row['subject']; 
			$item->link = $params['base_url'] . $bbs_row['bbs_res_url'];
			$item->description = $body;
			if ($bbs_row['file_url'] != '') {
				$item->image_link = $params['base_url'] . $bbs_row['file_url'];
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
