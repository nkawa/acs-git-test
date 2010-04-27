<?php
/**
 * システム設定 DBアクセス
 *
 * @author  kuwayamm
 * @version $Revision: 1.5 $ $Date: 2007/03/01 09:01:12 $
 */
class ACSSystemConfigModel
{
	/**
	 * システム設定情報取得
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
			// 条件の指定がある場合のみ、追加
			$sql .=     " AND " . $sql_where;
		}
		$sql .= " ORDER BY system_config_group.display_order, system_config.display_order";
		$row_array = ACSDB::_get_row_array($sql);

		return $row_array;
	}

	/**
	 * システム設定情報取得
	 * システム設定グループ、キーワード指定
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
	 * システム設定情報取得
	 * システム設定グループ指定
	 *
	 * @param $system_config_group_name
	 */
	static function select_system_config_group_row ($system_config_group_name) {
		$sql_where  = "system_config_group_name = '" . $system_config_group_name . "'";

		$row_array = ACSSystemConfigModel::select_system_config_row_array($sql_where);
		return $row_array;
	}

	/**
	 * 値更新
	 *
	 * @param  $system_config_group 更新対象のキー
	 * @param  $keyword             更新対象のキー
	 * @param  $update_value        更新する値
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
