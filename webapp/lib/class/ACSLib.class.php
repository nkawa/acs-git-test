<?php
// $Id: ACSLib.class.php,v 1.26 2007/03/30 05:27:15 w-ota Exp $


/*
 * �饤�֥�ꥯ�饹
 */
class ACSLib {

	/**
	 * ������Ф���pg_escape_string()���� (SQL����������)
	 *
	 * @param ����
	 * @return ���������׸������
	 */
	static function escape_sql_array(&$arr) {
		foreach ($arr as $key => $value) {
			if (is_scalar($value)) {
				$arr[$key] = pg_escape_string($value);
			}
		}
		return $arr;
	}

	/**
	 * ������Ф���get_sql_value()���� (�������ȤǰϤࡢ�ޤ���null�Ȥ���)
	 *
	 * @param ����
	 * @return ����
	 */
	static function get_sql_value_array(&$arr) {
		foreach ($arr as $key => $value) {
			if (is_scalar($value) || is_null($value)) {
				$arr[$key] = ACSLib::get_sql_value($value);
			}
		}
		return $arr;
	}

	/**
	 * SQL���ͤ򥯥����ȤǰϤࡢ�ޤ���null�Ȥ���
	 *
	 * @param ʸ����
	 * @return �ù����ʸ����
	 */
	static function get_sql_value($value) {
		if ($value != '') {
			$value = "'$value'";
		} else {
			$value = "null";
		}
		return $value;
	}

	/*
	 * Ⱦ�ѥ��ڡ����䥿�֤�&nbsp;���Ѵ�����
	 *
	 */
	static function sp2nbsp($str) {
		$str = str_replace(' ', '&nbsp;', $str);
		$str = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $str);
		return $str;
	}

	/**
	 * ILIKE��򥨥������פ��� \, %
	 *
	 * @param $str ʸ����
	 * @param ���������׸��ʸ����
	 */
	static function escape_ilike(&$str) {
		$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('%', '\\\\%', $str);
		return $str;
	}

	/**
	 * LDAP�ե��륿ʸ����򥨥������פ��� \, (, ), *
	 *
	 * @param $str ʸ����
	 * @param ���������׸��ʸ����
	 */
	static function escape_ldap_filter($str) {
		$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('(', '\\(', $str);
		$str = str_replace(')', '\\)', $str);
		$str = str_replace('*', '\\*', $str);
		return $str;
	}

	/*
	 * ��Ⱦ�ѥ��ڡ����Ƕ��ڤ�줿ʸ�����ʸ����ѥ����󤴤Ȥ����������Ȥ��Ƽ�������
	 * (���ѤҤ餬��, ���ѥ�������, Ⱦ�ѥ�������, ���ѱѿ���, Ⱦ�ѱѿ����Υѥ���������)
	 *
	 * @param $str ʸ����
	 * @return ���������
	 *
	 */
	static function get_query_array_array($str) {
		// 1�İʾ����Ⱦ�ѥ��ڡ�����split��������˳�Ǽ
		$query_array = mb_split('[ ��]+', trim($str));

		// ʬ���Υ�������ݻ��������������
		$ret_query_array_array = array();

		foreach ($query_array as $query) {
			if ($query == '') {
				continue;
			}
			$ret_query_array = array();
			array_push($ret_query_array, $query);
			array_push($ret_query_array, mb_convert_kana($query, "HcV")); // ���ѤҤ餬��
			array_push($ret_query_array, mb_convert_kana($query, "KCV")); // ���ѥ�������
			array_push($ret_query_array, mb_convert_kana($query, "kh"));  // Ⱦ�ѥ�������
			array_push($ret_query_array, mb_convert_kana($query, "A"));   // ���ѱѿ��� (���£ã�����)
			array_push($ret_query_array, mb_convert_kana($query, "a"));   // Ⱦ�ѱѿ��� (ABC123)

			// unique�ˤ��Ƥ���array_values
			$ret_query_array = array_values(array_unique($ret_query_array));
			// push
			array_push($ret_query_array_array, $ret_query_array);
		}

		return $ret_query_array_array;
	}

	/**
	 * PostgreSQL�������������Ѵ����������� YYYY/MM/DD(wday) H:MM
	 *
	 * @param PostgreSQL����Ϳ����줿���������դ�ɽ��ʸ����
	 * @param ������ɽ������
	 * @param H:MM(��:ʬ)��ɽ������
	 * @param SS(��)��ɽ������
	 * @return �Ѵ����������줿���դ�ʸ����
	 */
	static function convert_pg_date_to_str($pg_date, $wday = 1, $hhmm = 1, $ss = 0) {
		$wday_arr = array(ACSMsg::get_mdmsg(__FILE__,'M001'), ACSMsg::get_mdmsg(__FILE__,'M002'), ACSMsg::get_mdmsg(__FILE__,'M003'), ACSMsg::get_mdmsg(__FILE__,'M004'), ACSMsg::get_mdmsg(__FILE__,'M005'), ACSMsg::get_mdmsg(__FILE__,'M006'), ACSMsg::get_mdmsg(__FILE__,'M007'));
		if ($pg_date == '') {
			$date = '';
		} else {
			$t = strtotime($pg_date) + 9*60*60; // UNIX timestamp (JST)
			$date = gmdate("Y/m/d", $t); // YYYY/MM/DD
			if ($wday) {
				$date .= "(" . $wday_arr[gmdate("w", $t)] . ")"; // (wday)
			}
			if ($hhmm) {
				$date .= gmdate(" G:i", $t); // H:MM
			}
			if ($ss) {
				$date .= gmdate(":s", $t); // SS
			}
		}
		return $date;
	}

	/**
	 * Timestamp �� PostgreSQL �������������������� YYYY-MM-DD H:M:S
	 *
	 * @param  $timestamp ����Τʤ���硢���ߤΥ��������
	 * @return �������줿���դ�ʸ����
	 */
	static function convert_timestamp_to_pg_date ($timestamp = '') {
		if ($timestamp == '') {
			$timestamp = time();
		}
		return date("Y-m-d H:i:s", $timestamp);
	}

	/**
	 * PostgreSQL ������������ Timestamp ���Ѵ�����
	 *
	 * @param  string $pg_date PostgreSQL����������(ʸ����)
	 * @return timestamp php������(���ξ��϶�ʸ������֤�)
	 */
	static function convert_pg_date_to_timestamp ($pg_date) {
		if ($pg_date == '') {
			return '';
		}
		return strtotime($pg_date);
	}

	/**
	 * ��κǽ������������
	 *
	 * @param $year ǯ
	 * @param $month �� (1-12)
	 * @return ��κǽ���
	 */
	static function get_end_day($year, $month) {
		for ($i = 28; $i <= 31; $i++) {
			if (checkdate($month, $i, $year)) {
				$day = $i;
			} else {
				break;
			}
		}
		return $day;
	}


	/**
	 * PostgreSQL�ο����ͤ�PHP�ο����ͤȤ��Ƽ�������
	 */
	static function get_boolean($pg_boolean) {
		if ($pg_boolean == 't') {
			$ret = true;
		} else {
			$ret = false;
		}
		return $ret;
	}

	/**
	 * PHP�ο����ͤ�PostgreSQL�ο����ͤȤ��Ƽ�������
	 */
	static function get_pg_boolean($php_boolean) {
		if ($php_boolean) {
			$pg_boolean = 't';
		} else {
			$pg_boolean = 'f';
		}
		return $pg_boolean;
	}

	/**
	 * �ݥ��Ȥ���Ƥ����ǡ������������󥳡��ǥ��󥰤��Ѵ�����
	 */
	static function convert_post_data_encoding($params) {

		if ($params == NULL) {
			return $params;
		}
		foreach ($params as $key => $value) {
			if (is_array($value)) {
				$params[$key] = ACSLib::convert_post_data_encoding($value);
			} else {
				$params[$key] = mb_convert_encoding($value, mb_internal_encoding(), implode(',', mb_detect_order()));
			}
		}
		return $params;
	}


	/*
	 * �᡼������: �᡼����������� (SMTP)
	 */
	static function send_mail($from, $to, $cc, $subject, $body, $additional_headers = "") {
		require_once 'Mail.php';
		$ret = 0;

		// params (for Mail)
		//$params['host'] = ACSSystemConfig::get_keyword_value('�����ƥ�', 'SMTP_SERVER');
		//$params['port'] = ACSSystemConfig::get_keyword_value('�����ƥ�', 'SMTP_PORT');
		$params['host'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SMTP_SERVER');
		$params['port'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SMTP_PORT');

		// encode
		// 2010.03.24 ʸ�������б�
		//$subject = mb_encode_mimeheader($subject, 'ISO-2022-JP');
		//$body = mb_convert_encoding($body, 'JIS', mb_internal_encoding());
		// ʸ�����JIS�����ɤ��Ѵ�
		$subject = mb_convert_encoding($subject, 'ISO-2022-JP', 'EUC-JP');
		// ���Υ��󥳡��ǥ��󥰤���¸
		$orgEncoding = mb_internal_encoding();
		// �������󥳡��ǥ��󥰤�JIS���ѹ�
		mb_internal_encoding('ISO-2022-JP');
		// MIME�Ѵ�
		$subject = mb_encode_mimeheader($subject, 'ISO-2022-JP');
		// ��ʸ�Υ��󥳡����Ѵ�
		$body = mb_convert_encoding($body, 'JIS', 'EUC-JP');
		// �������󥳡��ǥ��󥰤򸵤��᤹
		mb_internal_encoding($orgEncoding);

		// headers
		$headers['From'] = $from;
		$headers['To'] = $to;
		if ($cc != '') {
			$to = "$to,$cc";
			$headers['Cc'] = $cc;
		}
		if ($additional_headers['Bcc'] != '') {
			$to .= ",".$additional_headers['Bcc'];
		}

		$headers['Subject'] = $subject;
		$headers['Content-Type'] = 'text/plain; charset=ISO-2022-JP';

		// �ɲ�headers
		if (is_array($additional_headers)) {
			$headers = array_merge($headers,$additional_headers);
		}

		// to������
		$to = explode(',', $to);

		// SMTP�ǥ᡼�����������
		$mail_object = &Mail::factory('smtp', $params);
		if($mail_object->send($to, $headers, $body) === true) {
			$ret = 1;
		}
		return $ret;
	}
	
	/*
	 * ���Ͻ񼰤Υ����å�
	 *
	 * @param $str ʸ����
	 * @return ���������
	 *
	 */
	function get_value_array($str) {
		$value_array = trim($str);
		$ymd = 1;
		$err = "OK";
		for ($i = 0; $i < 10 ;$i++) {
			$value = substr($value_array, $i , 1);
			if(ereg('[0-9]',$value)){
				$set_value = $set_value . $value;
			}else if($value == '/'){
				switch($ymd){
					case 1:	//yyyy
						if(strlen($set_value) == 4){
							if($set_value > 1900 && $set_value <2035){
							 $ymd = 2; $year = $set_value; $set_value ='';
							 continue;
							}else{$err = ACSMsg::get_mdmsg(__FILE__,'M008'); break;}
						}else{
							$err = ACSMsg::get_mdmsg(__FILE__,'M009'); break;
						}
					case 2:	//mm
						if(strlen($set_value) == 2){
							if($set_value > 0 && $set_value <13){
							 $ymd = 3; $month = $set_value; $set_value ='';
							 continue;
							}else{$err = ACSMsg::get_mdmsg(__FILE__,'M010'); break;}
						}else{
							$err = ACSMsg::get_mdmsg(__FILE__,'M011'); break;
						}
				}
			}else{
				$err = ACSMsg::get_mdmsg(__FILE__,'M012'); break;
			}
		}
		if($ymd = 3){	//dd
			if(strlen($set_value) == 2){
				$d = date("t", mktime(0, 0, 0, $month, 1, $year));
				if($set_value > 0 && $set_value <= $d){
				 $ymd = 0;
				}else{$err = ACSMsg::get_mdmsg(__FILE__,'M013');}
			}else{
				$err = ACSMsg::get_mdmsg(__FILE__,'M014');;
			}
		}
		return $err;
	}

	/*
	 * ���󤫤�csvʸ����(����޶��ڤ�ʸ����)���������
	 *
	 * @param $array ����
	 * @param $column_name �����Ϣ������̾
	 * @return csvʸ����
	 *
	 */
	static function get_csv_string_from_array(&$array, $column_name="") {
		$csv_string = "";
		foreach ($array as $row) {
			if($csv_string != ""){
				$csv_string .= ",";
			}
			if($column_name != ""){
				$csv_string .= $row[$column_name];
			}else{
				$csv_string .= $row;
			}
		}

		return $csv_string;
	}

	/*
	 * �������鲿�����ޤǤȤ���SQL���ʸ����
	 *
	 * @param $column_name ���ե����̾
	 *        $days ����(false�ξ�������)
	 * @return csvʸ����
	 *
	 */
	static function get_sql_condition_from_today($column_name, $days=0) {

		// �������꤬������
		if($days > 0){
			$year = date("Y");
			$month = date("m");
			$day = date("d");
			$date_start = date("Y/m/d", mktime(0, 0, 0, $month, $day-$days+1, $year));
			return "(" . $column_name . " >= '" . $date_start . "')"; 
		} else {
			return "true";
		}
	}

	/**
	 * �ǥ��쥯�ȥ�κ���
	 * @param string $dir �ǥ��쥯�ȥ�ѥ�
	 * @param int $mode �ե�����⡼��
	 * @return integer 1...�����������/0...�������ʤ��ä����
	 */
	static function make_dir ($dir, $mode = 0777) {
		// �ǥ��쥯�ȥ꤬¸�ߤ��ʤ���硢�ǥ��쥯�ȥ�κ���
		if (!file_exists($dir)) {
			mkdir($dir); 
			chmod($dir, $mode);
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * �ǥ��쥯�ȥ�ΰ����
	 * @param string $dir �ǥ��쥯�ȥ�ѥ�
	 */
	static function remove_dir ($dir) {
		if ($handle = @opendir("$dir")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir($dir.'/'.$item)) {
						ACSLib::remove_dir($dir.'/'.$item);
					} else {
						unlink($dir.'/'.$item);
					}
				}
			}
			closedir($handle);
			rmdir($dir);
		}
	}
}

?>
