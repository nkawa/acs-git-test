<?php
// $Id: ACSDB.class.php,v 1.5 2007/03/27 02:12:31 w-ota Exp $

require_once(ACS_LIB_MESSAGE_DIR . 'ACSMsg.class.php');

/*
 * DBアクセスクラス
 */
class ACSDB {

	/**
	 * デバッグ用: 実行に失敗したSQL文とエラーメッセージを表示する
	 */
	static function _print_debug_msg($sql, $msg = '') {
		if (ACS_DEBUG_MODE == 1) {
			echo "<span style=\"background-color:#ffffcc\"><code>$sql</code></span><br>\n";
			echo "<span style=\"background-color:#ffcccc\"><code>" . pg_last_error() ."</code></span><br>\n";
			if ($msg) {
				echo "<span style=\"background-color:#ccccff\"><code>[msg: $msg]</code></span><br>\n";
			}
		}
	}


	/**
	 * (内部関数) SQL文を実行する
	 *
	 * @param SQL文
	 * @return クエリ結果リソース
	 */
	static function _query($sql) {
		static $db;

		// DB接続
		if (!$db) {
			$db = DB::Connect(ACS_DSN);
			$db->setFetchMode(DB_FETCHMODE_ASSOC);
		}

		// 通常テーブル名を言語仕様テーブル名に変換
		$lang = ACSMsg::get_lang(FALSE);
		if ($lang != ACS_DEFAULT_LANG && $lang != "") {
			$tables_array = ACSMsg::get_lang_tables_array();
			foreach ($tables_array as $table) {
				$sql = mb_ereg_replace(
						"([, \t\n\(\)\*\+\-\/]+)".
						"(".$table.")".
						"([., \t\n\(\)\*\+\-\/]+|$)", 
						"\\1".$table."_".$lang."\\3", $sql);
			}
		}

		if (ACS_DEBUG_MODE && isset($_GET['timer'])) {
			$timelimit = $_GET['timer'];
			if (empty($timelimit)) {
				$timelimit = 0; // デフォルト0秒
			}

			$time_array = explode(' ', microtime());
			$start_time = substr(($time_array[1] . substr($time_array[0], 1)), 0, 14);

			$res = $db->query($sql);

			$time_array = explode(' ', microtime());
			$end_time   = substr(($time_array[1] . substr($time_array[0], 1)), 0, 14);

			$diff_time = substr($end_time - $start_time, 0, 5);
			if (floatval($diff_time) >= $timelimit) {
				_debug($sql);
				echo "<code>$start_time -&gt; $end_time  (<font color=red>$diff_time</font> sec)</code>";
			}

		} else {
			$res = $db->query($sql);
		}

		return $res;
	}


	/**
	 * (内部関数) SQL文を実行してクエリ結果リソースを取得する
	 *
	 * @param SQL文
	 * @return クエリ結果リソース
	 */
	static function _get_res($sql) {
		$res = ACSDB::_query($sql);
		if (DB::isError($res)) {
			ACSDB::_print_debug_msg($sql);
		}
		return $res;
	}

	/**
	 * (内部関数) SQL文を実行してレコード全体を取得する
	 *
	 * @param SQL文
	 * @return 連想配列の配列
	 */
	static function _get_row_array($sql) {
		$row_array = array();
		$res = ACSDB::_get_res($sql);
		if (!DB::isError($res)) {
			while ($row = $res->fetchRow()) {
				array_push($row_array, $row);
			}
		}
		return $row_array;
	}

	/**
	 * (内部関数) SQL文を実行して1レコードのみを連想配列で取得する
	 *
	 * @param SQL文
	 * @return 連想配列
	 */
	static function _get_row($sql) {
		$res = ACSDB::_get_res($sql);
		if (!DB::isError($res)) {
			$row = $res->fetchRow();
			if (($num_rows = $res->numRows()) > 1) {
				// クエリ結果が複数レコードある場合は警告メッセージを表示する
				ACSDB::_print_debug_msg($sql, "Warning: rows = $num_rows");
			}
		}
		return $row;
	}

	/**
	 * (内部関数) SQL文を実行して1レコード1カラムを取得する
	 *
	 * @param SQL文
	 * @return スカラー
	 */
	static function _get_value($sql) {
		$res = ACSDB::_get_res($sql);
		if (!DB::isError($res)) {
			$row = $res->fetchRow(DB_FETCHMODE_ORDERED); // 数値のインデックスでカラムにアクセスできるように変更
			if (($num_rows = $res->numRows()) > 1 || ($num_cols = $res->numCols()) > 1) {
				// クエリ結果リソースが複数レコードまたは複数カラムある場合は警告メッセージを表示する
				ACSDB::_print_debug_msg($sql, "Warning: rows = $num_rows, cols = $num_cols");
			}
		}
		return $row[0];
	}

	/**
	 * (内部関数) 操作系SQL文を実行する (INSERT, UPDATE)
	 *
	 * @param DB接続リソース
	 * @param SQL文
	 * @return 成功(true) / 失敗(false)
	 */
	static function _do_query($sql) {
		$res = ACSDB::_query($sql);
		if (DB::isError($res)) {
			ACSDB::_print_debug_msg($sql);
			$ret = false;
		} else {
			$ret = true;
		}
		return $ret;
	}


	/**
	 * 次のシーケンス番号を取得する
	 *
	 * @param シーケンス名
	 * @return シーケンス番号
	 */
	static function get_next_seq($seq_name) {
		$sql = "SELECT nextval('$seq_name')";
		$seq = ACSDB::_get_value($sql);
		return $seq;
	}

	/**
	 * マスタデータを連想配列として取得する
	 *
	 * @param マスタテーブル名のprefix (***_master)
	 * @return マスタデータの連想配列
	 */
	static function get_master_array($prefix, $where="") {
		$ret_array = array();

		$master_table = $prefix . '_master';
		$code = $prefix . '_code';
		$name = $prefix . '_name';

		$sql  = "SELECT *";
		$sql .= " FROM $master_table";

		if($where != ""){
			$sql .= " WHERE " . $where;
		}

		$sql .= " ORDER BY $code ASC";

		$res = ACSDB::_get_res($sql);
		while ($row = $res->fetchRow()) {
			$ret_array[$row[$code]] = $row[$name];
		}

		ksort($ret_array);
		return $ret_array;
	}
}

?>
