<?php
// $Id: ACSLib.class.php,v 1.26 2007/03/30 05:27:15 w-ota Exp $


/*
 * ライブラリクラス
 */
class ACSLib {

	/**
	 * 配列に対してpg_escape_string()する (SQLエスケープ)
	 *
	 * @param 配列
	 * @return エスケープ後の配列
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
	 * 配列に対してget_sql_value()する (クォートで囲む、またはnullとする)
	 *
	 * @param 配列
	 * @return 配列
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
	 * SQLの値をクォートで囲む、またはnullとする
	 *
	 * @param 文字列
	 * @return 加工後の文字列
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
	 * 半角スペースやタブを&nbsp;に変換する
	 *
	 */
	static function sp2nbsp($str) {
		$str = str_replace(' ', '&nbsp;', $str);
		$str = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $str);
		return $str;
	}

	/**
	 * ILIKE句をエスケープする \, %
	 *
	 * @param $str 文字列
	 * @param エスケープ後の文字列
	 */
	static function escape_ilike(&$str) {
		$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('%', '\\\\%', $str);
		return $str;
	}

	/**
	 * LDAPフィルタ文字列をエスケープする \, (, ), *
	 *
	 * @param $str 文字列
	 * @param エスケープ後の文字列
	 */
	static function escape_ldap_filter($str) {
		$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('(', '\\(', $str);
		$str = str_replace(')', '\\)', $str);
		$str = str_replace('*', '\\*', $str);
		return $str;
	}

	/*
	 * 全半角スペースで区切られた文字列を、文字列パターンごとの配列の配列として取得する
	 * (全角ひらがな, 全角カタカナ, 半角カタカナ, 全角英数字, 半角英数字のパターンを作成)
	 *
	 * @param $str 文字列
	 * @return 配列の配列
	 *
	 */
	static function get_query_array_array($str) {
		// 1個以上の全半角スペースでsplitして配列に格納
		$query_array = mb_split('[ 　]+', trim($str));

		// 分割後のクエリを保持する配列の配列
		$ret_query_array_array = array();

		foreach ($query_array as $query) {
			if ($query == '') {
				continue;
			}
			$ret_query_array = array();
			array_push($ret_query_array, $query);
			array_push($ret_query_array, mb_convert_kana($query, "HcV")); // 全角ひらがな
			array_push($ret_query_array, mb_convert_kana($query, "KCV")); // 全角カタカナ
			array_push($ret_query_array, mb_convert_kana($query, "kh"));  // 半角カタカナ
			array_push($ret_query_array, mb_convert_kana($query, "A"));   // 全角英数字 (ＡＢＣ１２３)
			array_push($ret_query_array, mb_convert_kana($query, "a"));   // 半角英数字 (ABC123)

			// uniqueにしてからarray_values
			$ret_query_array = array_values(array_unique($ret_query_array));
			// push
			array_push($ret_query_array_array, $ret_query_array);
		}

		return $ret_query_array_array;
	}

	/**
	 * PostgreSQL形式の日時を変換し整形する YYYY/MM/DD(wday) H:MM
	 *
	 * @param PostgreSQLから与えられた形式の日付を表す文字列
	 * @param 曜日を表示する
	 * @param H:MM(時:分)を表示する
	 * @param SS(秒)を表示する
	 * @return 変換し整形された日付の文字列
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
	 * Timestamp を PostgreSQL 形式の日時に整形する YYYY-MM-DD H:M:S
	 *
	 * @param  $timestamp 指定のない場合、現在のローカル時刻
	 * @return 整形された日付の文字列
	 */
	static function convert_timestamp_to_pg_date ($timestamp = '') {
		if ($timestamp == '') {
			$timestamp = time();
		}
		return date("Y-m-d H:i:s", $timestamp);
	}

	/**
	 * PostgreSQL 形式の日時を Timestamp に変換する
	 *
	 * @param  string $pg_date PostgreSQL形式の日時(文字列)
	 * @return timestamp phpの日時(空の場合は空文字列を返す)
	 */
	static function convert_pg_date_to_timestamp ($pg_date) {
		if ($pg_date == '') {
			return '';
		}
		return strtotime($pg_date);
	}

	/**
	 * 月の最終日を取得する
	 *
	 * @param $year 年
	 * @param $month 月 (1-12)
	 * @return 月の最終日
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
	 * PostgreSQLの真偽値をPHPの真偽値として取得する
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
	 * PHPの真偽値をPostgreSQLの真偽値として取得する
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
	 * ポストされてきたデータを内部エンコーディングに変換する
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
	 * メール送信: メールを送信する (SMTP)
	 */
	static function send_mail($from, $to, $cc, $subject, $body, $additional_headers = "") {
		require_once 'Mail.php';
		$ret = 0;

		// params (for Mail)
		//$params['host'] = ACSSystemConfig::get_keyword_value('システム', 'SMTP_SERVER');
		//$params['port'] = ACSSystemConfig::get_keyword_value('システム', 'SMTP_PORT');
		$params['host'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SMTP_SERVER');
		$params['port'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SMTP_PORT');

		// encode
		// 2010.03.24 文字化け対応
		//$subject = mb_encode_mimeheader($subject, 'ISO-2022-JP');
		//$body = mb_convert_encoding($body, 'JIS', mb_internal_encoding());
		// 文字列をJISコードに変換
		$subject = mb_convert_encoding($subject, 'ISO-2022-JP', 'EUC-JP');
		// 元のエンコーディングを保存
		$orgEncoding = mb_internal_encoding();
		// 内部エンコーディングをJISに変更
		mb_internal_encoding('ISO-2022-JP');
		// MIME変換
		$subject = mb_encode_mimeheader($subject, 'ISO-2022-JP');
		// 本文のエンコード変換
		$body = mb_convert_encoding($body, 'JIS', 'EUC-JP');
		// 内部エンコーディングを元に戻す
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

		// 追加headers
		if (is_array($additional_headers)) {
			$headers = array_merge($headers,$additional_headers);
		}

		// toの配列
		$to = explode(',', $to);

		// SMTPでメールを送信する
		$mail_object = &Mail::factory('smtp', $params);
		if($mail_object->send($to, $headers, $body) === true) {
			$ret = 1;
		}
		return $ret;
	}
	
	/*
	 * 入力書式のチェック
	 *
	 * @param $str 文字列
	 * @return 配列の配列
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
	 * 配列からcsv文字列(カンマ区切り文字列)を作成する
	 *
	 * @param $array 配列
	 * @param $column_name 配列の連想配列名
	 * @return csv文字列
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
	 * 今日から何日前までというSQL条件文取得
	 *
	 * @param $column_name 日付カラム名
	 *        $days 日数(falseの場合は全て)
	 * @return csv文字列
	 *
	 */
	static function get_sql_condition_from_today($column_name, $days=0) {

		// 日数指定がある場合
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
	 * ディレクトリの作成
	 * @param string $dir ディレクトリパス
	 * @param int $mode ファイルモード
	 * @return integer 1...作成した場合/0...作成しなかった場合
	 */
	static function make_dir ($dir, $mode = 0777) {
		// ディレクトリが存在しない場合、ディレクトリの作成
		if (!file_exists($dir)) {
			mkdir($dir); 
			chmod($dir, $mode);
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * ディレクトリの一括削除
	 * @param string $dir ディレクトリパス
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
