<?php
// $Id: ACSDB.class.php,v 1.5 2007/03/27 02:12:31 w-ota Exp $

require_once(ACS_LIB_MESSAGE_DIR . 'ACSMsg.class.php');

/*
 * DB�����������饹
 */
class ACSDB {

	/**
	 * �ǥХå���: �¹Ԥ˼��Ԥ���SQLʸ�ȥ��顼��å�������ɽ������
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
	 * (�����ؿ�) SQLʸ��¹Ԥ���
	 *
	 * @param SQLʸ
	 * @return �������̥꥽����
	 */
	static function _query($sql) {
		static $db;

		// DB��³
		if (!$db) {
			$db = DB::Connect(ACS_DSN);
			$db->setFetchMode(DB_FETCHMODE_ASSOC);
		}

		// �̾�ơ��֥�̾�������ͥơ��֥�̾���Ѵ�
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
				$timelimit = 0; // �ǥե����0��
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
	 * (�����ؿ�) SQLʸ��¹Ԥ��ƥ������̥꥽�������������
	 *
	 * @param SQLʸ
	 * @return �������̥꥽����
	 */
	static function _get_res($sql) {
		$res = ACSDB::_query($sql);
		if (DB::isError($res)) {
			ACSDB::_print_debug_msg($sql);
		}
		return $res;
	}

	/**
	 * (�����ؿ�) SQLʸ��¹Ԥ��ƥ쥳�������Τ��������
	 *
	 * @param SQLʸ
	 * @return Ϣ�����������
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
	 * (�����ؿ�) SQLʸ��¹Ԥ���1�쥳���ɤΤߤ�Ϣ������Ǽ�������
	 *
	 * @param SQLʸ
	 * @return Ϣ������
	 */
	static function _get_row($sql) {
		$res = ACSDB::_get_res($sql);
		if (!DB::isError($res)) {
			$row = $res->fetchRow();
			if (($num_rows = $res->numRows()) > 1) {
				// �������̤�ʣ���쥳���ɤ�����Ϸٹ��å�������ɽ������
				ACSDB::_print_debug_msg($sql, "Warning: rows = $num_rows");
			}
		}
		return $row;
	}

	/**
	 * (�����ؿ�) SQLʸ��¹Ԥ���1�쥳����1�������������
	 *
	 * @param SQLʸ
	 * @return �����顼
	 */
	static function _get_value($sql) {
		$res = ACSDB::_get_res($sql);
		if (!DB::isError($res)) {
			$row = $res->fetchRow(DB_FETCHMODE_ORDERED); // ���ͤΥ���ǥå����ǥ����˥��������Ǥ���褦���ѹ�
			if (($num_rows = $res->numRows()) > 1 || ($num_cols = $res->numCols()) > 1) {
				// �������̥꥽������ʣ���쥳���ɤޤ���ʣ������ढ����Ϸٹ��å�������ɽ������
				ACSDB::_print_debug_msg($sql, "Warning: rows = $num_rows, cols = $num_cols");
			}
		}
		return $row[0];
	}

	/**
	 * (�����ؿ�) ����SQLʸ��¹Ԥ��� (INSERT, UPDATE)
	 *
	 * @param DB��³�꥽����
	 * @param SQLʸ
	 * @return ����(true) / ����(false)
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
	 * ���Υ��������ֹ���������
	 *
	 * @param ��������̾
	 * @return ���������ֹ�
	 */
	static function get_next_seq($seq_name) {
		$sql = "SELECT nextval('$seq_name')";
		$seq = ACSDB::_get_value($sql);
		return $seq;
	}

	/**
	 * �ޥ����ǡ�����Ϣ������Ȥ��Ƽ�������
	 *
	 * @param �ޥ����ơ��֥�̾��prefix (***_master)
	 * @return �ޥ����ǡ�����Ϣ������
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
