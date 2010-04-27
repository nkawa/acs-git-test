<?php
/**
 * �����ƥ����� DB��������
 *
 * @author  kuwayamm
 * @version $Revision: 1.5 $ $Date: 2007/03/01 09:01:12 $
 */
class ACSSystemConfigModel
{
	/**
	 * �����ƥ�����������
	 *
	 * @parma $sql_where
	 */
	static function select_system_config_row_array ($sql_where = "") {
		$sql  = "SELECT";
		$sql .=     " system_config_group.system_config_group_code, system_config_group.system_config_group_name,";
		$sql .=     " system_config.keyword, system_config.value, system_config.type";
		$sql .= " FROM system_config_group, system_config";
		$sql .= " WHERE";
		$sql .=     " system_config_group.system_config_group_code = system_config.system_config_group_code";
		if ($sql_where) {
			// ���λ��꤬������Τߡ��ɲ�
			$sql .=     " AND " . $sql_where;
		}
		$sql .= " ORDER BY system_config_group.display_order, system_config.display_order";
		$row_array = ACSDB::_get_row_array($sql);

		return $row_array;
	}

	/**
	 * �����ƥ�����������
	 * �����ƥ����ꥰ�롼�ס�������ɻ���
	 *
	 * @param $system_config_group_name
	 * @param $keyword
	 */
	static function select_system_config_keyword_row ($system_config_group_name, $keyword) {
		$sql_where  = "system_config_group_name = '" . $system_config_group_name . "'";
		$sql_where .= " AND ";
		$sql_where .= "keyword = '" . $keyword . "'";

		$row_array = ACSSystemConfigModel::select_system_config_row_array($sql_where);
		return $row_array[0];
	}

	/**
	 * �����ƥ�����������
	 * �����ƥ����ꥰ�롼�׻���
	 *
	 * @param $system_config_group_name
	 */
	static function select_system_config_group_row ($system_config_group_name) {
		$sql_where  = "system_config_group_name = '" . $system_config_group_name . "'";

		$row_array = ACSSystemConfigModel::select_system_config_row_array($sql_where);
		return $row_array;
	}

	/**
	 * �͹���
	 *
	 * @param  $system_config_group �����оݤΥ���
	 * @param  $keyword             �����оݤΥ���
	 * @param  $update_value        ����������
	 */
	static function update_system_config_value ($system_config_group_name, $keyword, $update_value) {
		$sql  = "UPDATE system_config";
		$sql .= " SET";
		$sql .=		   " value = '" . pg_escape_string($update_value) . "'";
		$sql .= " WHERE";				
		$sql .=        " keyword = '" . $keyword . "'";

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}
}
