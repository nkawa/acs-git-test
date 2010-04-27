<?php
/**
 * �ե���� DB��������
 *
 * @author  kuwayama
 * @version $Revision: 1.39 $ $Date: 2007/03/01 09:01:12 $
 */
require_once(ACS_CLASS_DIR . 'ACSFileInfoModel.class.php');
require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
class ACSFolderModel
{
	/**
	 * �ե����������� (����)
	 *
	 * @param $sql_where
	 */
	static function select_common_folder_row_array ($sql_where) {
		$row_array = array();

		$sql  = "SELECT folder.*, open_level_master.*,";
		$sql .=       " ENTRY_USER_COMMUNITY.community_name AS entry_user_community_name,";
		$sql .=       " UPDATE_USER_COMMUNITY.community_name AS update_user_community_name";
		$sql .= " FROM (folder LEFT OUTER JOIN open_level_master ON folder.open_level_code = open_level_master.open_level_code)";
		$sql .=     ", community AS ENTRY_USER_COMMUNITY, community AS UPDATE_USER_COMMUNITY";
		$sql .= " WHERE ";
		$sql .=       " folder.entry_user_community_id = ENTRY_USER_COMMUNITY.community_id";
		$sql .=   " AND folder.update_user_community_id = UPDATE_USER_COMMUNITY.community_id";
		if ($sql_where) {
			$sql .= " AND " . $sql_where;
		}
		$sql .= " ORDER BY folder_name";

		$row_array = ACSDB::_get_row_array($sql);

		return $row_array;
	}

	/**
	 * �ե�����������ĥ��ߥ�˥ƥ����� (����)
	 *
	 * @param $folder_id
	 */
	static function select_trusted_community ($folder_id) {
		$row_array = array();

		$sql  = "SELECT community.community_id, community.community_name, community.community_type_code, community_type_master.community_type_name";
		$sql .= " FROM folder_trusted_community, community, community_type_master";
		$sql .= " WHERE folder_trusted_community.folder_id = '$folder_id'";
		$sql .= "  AND folder_trusted_community.trusted_community_id = community.community_id";
		$sql .= "  AND community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community.delete_flag != 't'";

		$row_array = ACSDB::_get_row_array($sql);

		return $row_array;
	}

	/**
	 * �ץå��襳�ߥ�˥ƥ����� (����)
	 *
	 * @param $folder_id
	 */
	static function select_put_community ($folder_id) {
		$row_array = array();

		$sql  = "SELECT";
		// community ����Υ����
		$sql .=     " community.community_id, community.community_name, community.community_type_code, community_type_master.community_type_name,";
		// �ץå��襳�ߥ�˥ƥ������Τθ����ϰϾ���
		$sql .=     " contents.open_level_code, open_level_master.open_level_name,";
		// folder  ����Υ����ʥץå���ե���������
		$sql .=     " folder.folder_id as put_community_folder_id, folder.folder_name as put_community_folder_name";
		$sql .= " FROM put_community, community, community_type_master, contents, contents_type_master, open_level_master, folder";
		$sql .= " WHERE put_community.folder_id = '$folder_id'";
		$sql .= "  AND put_community.put_community_id = community.community_id";
		$sql .= "  AND community.community_type_code = community_type_master.community_type_code";
		$sql .= "  AND community.delete_flag != 't'";

		// ���Τθ����ϰ�
		$sql .= "  AND community.community_id = contents.community_id";
		$sql .= "  AND contents.contents_type_code = contents_type_master.contents_type_code";
		$sql .= "  AND contents.open_level_code = open_level_master.open_level_code";
		$sql .= "  AND contents_type_master.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D00')."'";

		$sql .= "  AND put_community.put_community_folder_id = folder.folder_id";

		$row_array = ACSDB::_get_row_array($sql);

		return $row_array;
	}

	/**
	 * �ץåȥե��������
	 *
	 * @param $community_id
	 * @param $folder_id
	 */
	static function select_put_folder ($community_id, $folder_id) {
		$row       = array();   // select ��� ��Ϣ�������
		$ret_array = array();   // folder_id ������

		$sql  = "SELECT folder_id";
		$sql .= " FROM put_community";
		$sql .= " WHERE";
		$sql .=         " put_community.put_community_id = " . $community_id;
		$sql .=     " AND put_community.put_community_folder_id = " . $folder_id;

		$row_array = ACSDB::_get_row_array($sql);
		foreach ($row_array as $row) {
			array_push($ret_array, $row['folder_id']);
		}

		return $ret_array;
	}

	/**
	 * �ե�������󹹿� (����)
	 *
	 * @param  $target_folder_id
	 * @param  $row
	 * @return $ret ������� (true/false)
	 */
	static function update_folder ($target_folder_id, $row) {
		$set_values = array();
		foreach ($row as $key => $value) {
			$value = pg_escape_string($value);
			$value = ACSLib::get_sql_value($value);

			$value_str = "";
			$value_str = " " . $key . " = " . $value;

			array_push($set_values, $value_str);
		}

		$sql  = "UPDATE folder";
		$sql .= " SET";
		$sql .= implode(", ", $set_values);
		$sql .= " WHERE folder_id = " . $target_folder_id;

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * �ե�����������ĥ��ߥ�˥ƥ������ʶ��̡�
	 *
	 * @param  $target_folder_id
	 * @param  $trusted_community_id_array
	 * @return true / false
	 */
	static function update_folder_trusted_community ($target_folder_id, $trusted_community_id_array) {
		/* ��� */
		$sql  = "DELETE FROM folder_trusted_community";
		$sql .= " WHERE folder_id = '" . $target_folder_id . "'";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			return $ret;
		}

		/* ���� */
		if ($trusted_community_id_array) {
			$ret = ACSFolderModel::insert_folder_trusted_community($target_folder_id, $trusted_community_id_array);
			if (!$ret) {
				return $ret;
			}
		}
		return $ret;
	}

	/**
	 * �ե�������󹹿�
	 *
	 * @param  $target_folder_id
	 * @param  $input_folder_row
	 * @param  $input_trusted_community_id_array
	 * @return true / false
	 */
	static function update_folder_info ($target_folder_id, $input_folder_row, $input_trusted_community_id_array) {
		$ret = ACSFolderModel::update_folder($target_folder_id, $input_folder_row);
		if (!$ret) {
			return $ret;
		}

		// �ե�����������ĥ��ߥ�˥ƥ�����
		$ret = ACSFolderModel::update_folder_trusted_community($target_folder_id, $input_trusted_community_id_array);
		if (!$ret) {
			return $ret;
		}

		return $ret;
	}

	/**
	 * �ե�����ι������򹹿�
	 * ���顼���� (ROLLBACK) �ϸƤӸ��ǹԤ�����
	 *
	 * @param  $target_folder_id
	 * @param  $update_user_community_id
	 * @param  $update_date
	 * @return $ret ������� (true/false)
	 */
	static function update_folder_update_date ($target_folder_id, $update_user_community_id, $update_date) {
		$row = array();

		/* �����ǡ������å� */
		$row['update_user_community_id'] = $update_user_community_id;
		$row['update_date'] = $update_date;

		/* �������� */
		$ret = ACSFolderModel::update_folder($target_folder_id, $row);

		return $ret;
	}

	/**
	 * �ե����̾�򹹿�
	 * ���顼���� (ROLLBACK) �ϸƤӸ��ǹԤ�����
	 *
	 * @param  $target_folder_id
	 * @param  $new_folder_name
	 * @return $ret ������� (true/false)
	 */
	static function update_folder_name ($target_folder_id, $new_folder_name) {
		$row = array();

		/* �����ǡ������å� */
		$row['folder_name'] = $new_folder_name;

		/* �������� */
		$ret = ACSFolderModel::update_folder($target_folder_id, $row);

		return $ret;
	}

	/**
	 * �ե������ư
	 * �ƥե����ID�򹹿�����
	 * ���顼���� (ROLLBACK) �ϸƤӸ��ǹԤ�����
	 *
	 * @param  $target_folder_id
	 * @param  $new_parent_folder_id
	 * @return $ret ������� (true/false)
	 */
	static function update_parent_folder_id ($target_folder_id, $new_parent_folder_id) {
		$row = array();

		/* �����ǡ������å� */
		$row['parent_folder_id'] = $new_parent_folder_id;

		/* �������� */
		$ret = ACSFolderModel::update_folder($target_folder_id, $row);

		return $ret;
	}

	/**
	 * �ե���������ϰϹ���
	 *
	 * @param  $target_folder_id
	 * @param  $new_open_level_code
	 * @param  $new_trusted_community_row_array
	 * @return $ret ������� (true/false)
	 */
	static function update_folder_open_level_code ($target_folder_id, $new_open_level_code, $new_trusted_community_row_array) {
		$row = array();

		/* �����ǡ������å� */
		$row['open_level_code'] = $new_open_level_code;

		/* �ե�������󹹿� */
		$ret = ACSFolderModel::update_folder($target_folder_id, $row);
		if (!$ret) {
			return $ret;
		}

		/* �������ĥ��ߥ�˥ƥ����� */
		if ($new_trusted_community_row_array) {
			// ��������������ĥ��ߥ�˥ƥ�ID�����
			$trusted_community_id_array = array();
			foreach ($new_trusted_community_row_array as $new_trusted_community_row) {
				array_push($trusted_community_id_array, $new_trusted_community_row['community_id']);
			}
		}
		$ret = ACSFolderModel::update_folder_trusted_community($target_folder_id, $trusted_community_id_array);
		if (!$ret) {
			return $ret;
		}

		return $ret;
	}

	/**
	 * �ե�����������
	 *
	 * @param $folder_id
	 */
	static function select_folder_row ($folder_id) {
		$folder_id = pg_escape_string($folder_id);
		$sql_where = "folder.folder_id = '$folder_id'";

		$row_array = ACSFolderModel::select_common_folder_row_array($sql_where);
		$row = $row_array[0];

		/* �������ĥ��ߥ�˥ƥ��򥻥å� */
		$trusted_community_row_array = ACSFolderModel::select_trusted_community($folder_id);
		$row['trusted_community_row_array'] = $trusted_community_row_array;

		/* �ץå��襳�ߥ�˥ƥ��򥻥å� */
		$put_community_row_array = ACSFolderModel::select_put_community($folder_id);
		$row['put_community_row_array'] = $put_community_row_array;

		return $row;
	}

	/**
	 * ���֥ե�����������
	 *
	 * @param $folder_id
	 */
	static function select_sub_folder_row_array ($parent_folder_id_array) {
		$sub_folder_row_array = array();

		$target_parent_folder_id = implode(", ", $parent_folder_id_array);
		$sql_where = "folder.parent_folder_id IN (" . $target_parent_folder_id . ")";

		$row_array = ACSFolderModel::select_common_folder_row_array($sql_where);

		foreach ($row_array as $row) {
			$row_tmp = array();
			$trusted_community_row_array = array();

			$row_tmp = $row;

			/* �������ĥ��ߥ�˥ƥ��򥻥å� */
			$trusted_community_row_array = ACSFolderModel::select_trusted_community($row['folder_id']);
			$row_tmp['trusted_community_row_array'] = $trusted_community_row_array;

			/* �ץå��襳�ߥ�˥ƥ��򥻥å� */
			$put_community_row_array = ACSFolderModel::select_put_community($row['folder_id']);
			$row_tmp['put_community_row_array'] = $put_community_row_array;

			array_push($sub_folder_row_array, $row_tmp);
		}

		return $sub_folder_row_array;
	}

	/**
	 * ���ߥ�˥ƥ������ե��������
	 *
	 * @param $community
	 */
	static function select_all_community_folder_row_array ($community_id) {

		static $cache_rows;

		if (is_array($cache_rows[$community_id])) {
			return $cache_rows[$community_id];
		}

		$all_community_row_array = array();
		$sql_where = "folder.community_id = " . $community_id;

		$row_array = ACSFolderModel::select_common_folder_row_array($sql_where);

		foreach ($row_array as $row) {
			$row_tmp = array();
			$trusted_community_row_array = array();

			$row_tmp = $row;

			/* �������ĥ��ߥ�˥ƥ��򥻥å� */
			$trusted_community_row_array = ACSFolderModel::select_trusted_community($row['folder_id']);
			$row_tmp['trusted_community_row_array'] = $trusted_community_row_array;

			/* �ץå��襳�ߥ�˥ƥ��򥻥å� */
			$put_community_row_array = ACSFolderModel::select_put_community($row['folder_id']);
			$row_tmp['put_community_row_array'] = $put_community_row_array;

			array_push($all_community_row_array, $row_tmp);
		}

		$cache_rows[$community_id] = $all_community_row_array;

		return $all_community_row_array;
	}

	/**
	 * �ե�����������
	 *
	 * @param $folder_id_array
	 */
	static function select_folder_file_info_row_array ($folder_id_array) {
		/* �ե�����ե��������� */
		$target_folder_id = implode(", ", $folder_id_array);
		$folder_id_sql  = "SELECT *";
		$folder_id_sql .= " FROM folder_file";
		$folder_id_sql .= " WHERE folder_id IN (" . $target_folder_id . ")";

		$folder_file_row_array = ACSDB::_get_row_array($folder_id_sql);

		/* �ե����ID������˳�Ǽ */
		$file_id_array = array();
		foreach ($folder_file_row_array as $folder_file_row) {
			array_push($file_id_array, $folder_file_row['file_id']);
		}

		/* �ե������������ */
		if (count($file_id_array) > 0) {
			$row_array = ACSFileInfoModel::select_file_info_row_array($file_id_array);
		}
		return $row_array;
	}

	/**
	 * �롼�ȥե��������
	 *
	 * @param $community_id
	 */
	static function select_root_folder_row ($community_id) {
		$sql_where  = "folder.community_id = " . $community_id;
		$sql_where .=  " AND folder.parent_folder_id IS NULL ";

		$row_array = ACSFolderModel::select_common_folder_row_array($sql_where);

		return $row_array[0];
	}

	/**
	 * �ե��������
	 *
	 * @param  $folder_row
	 * @param  $trusted_community_id_array
	 * @return true / false
	 */
	static function insert_folder ($folder_row, $trusted_community_id_array = '') {

		ACSLib::escape_sql_array($folder_row);
		ACSLib::get_sql_value_array($folder_row);

		$sql  = "INSERT INTO folder";
		$sql .= " (folder_id, community_id, folder_name, comment, parent_folder_id, ";
		$sql .=   "entry_user_community_id, entry_date, ";
		$sql .=   "update_user_community_id, update_date, open_level_code)";
		$sql .= " VALUES (";
		$sql .=           $folder_row['folder_id'] . ",";
		$sql .=           $folder_row['community_id'] . ",";
		$sql .=           $folder_row['folder_name'] . ",";
		$sql .=           $folder_row['comment'] . ",";
		$sql .=           $folder_row['parent_folder_id'] . ",";
		$sql .=           $folder_row['entry_user_community_id'] . ",";
		$sql .=           $folder_row['entry_date'] . ",";
		$sql .=           $folder_row['update_user_community_id'] . ",";
		$sql .=           $folder_row['update_date'] . ",";
		$sql .=           $folder_row['open_level_code'];
		$sql .=  ")";

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			return $ret;
		}

		// �ե�����������ĥ��ߥ�˥ƥ�����
		if ($trusted_community_id_array) {
			$ret = ACSFolderModel::insert_folder_trusted_community($folder_row['folder_id'], $trusted_community_id_array);
			if (!$ret) {
				return $ret;
			}
		}
		return $ret;
	}

	/**
	 * �ե�����������ĥ��ߥ�˥ƥ������ʶ��̡�
	 *
	 * @param  $target_folder_id
	 * @param  $trusted_community_id_array
	 * @return true / false
	 */
	static function insert_folder_trusted_community ($target_folder_id, $trusted_community_id_array) {
		foreach ($trusted_community_id_array as $trusted_community_id) {
			$sql  = "INSERT INTO folder_trusted_community";
			$sql .= " (folder_id, trusted_community_id)";
			$sql .= " VALUES (";
			$sql .=           $target_folder_id . ",";
			$sql .=           $trusted_community_id;
			$sql .=  ")";

			$ret = ACSDB::_do_query($sql);
			if (!$ret) {
				return $ret;
			}
		}

		return $ret;
	}

	/**
	 * �롼�ȥե��������
	 *
	 * @param $community_id
	 */
	static function insert_root_folder ($community_id) {
		$folder_row = array();
		$timestamp  = ACSLib::convert_timestamp_to_pg_date();

		$folder_row['folder_id']        = ACSDB::get_next_seq('folder_id_seq');
		$folder_row['community_id']     = $community_id;
		$folder_row['folder_name']      = ACSMsg::get_mdmsg(__FILE__,'M001');
		$folder_row['comment']          = "";
		$folder_row['parent_folder_id'] = "";
		$folder_row['entry_user_community_id']  = $community_id;
		$folder_row['entry_date']       = $timestamp;
		$folder_row['update_user_community_id'] = $community_id;
		$folder_row['update_date']      = $timestamp;
		$folder_row['open_level_code']  = "";

		ACSDB::_do_query("BEGIN");

		$ret = ACSFolderModel::insert_folder($folder_row);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * �ե�����ե���������
	 *
	 * @param $foder_id
	 * @param $file_id
	 */
	static function insert_folder_file($folder_id, $file_id) {
		$sql  = "INSERT INTO folder_file";
		$sql .= " (folder_id, file_id)";
		$sql .= " VALUES (" . $folder_id . "," . $file_id . ")";

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * �ե�����ե�������󹹿� (����)
	 *
	 * @param  $target_folder_id
	 * @param  $target_folder_file_id
	 * @param  $row
	 * @return $ret ������� (true/false)
	 */
	static function update_folder_file ($target_folder_id, $target_file_id, $row) {
		$set_values = array();
		foreach ($row as $key => $value) {
			$value_str = "";
			$value_str = " " . $key . " = '" . $value . "'";

			array_push($set_values, $value_str);
		}

		$sql  = "UPDATE folder_file";
		$sql .= " SET";
		$sql .= implode(", ", $set_values);
		$sql .= " WHERE folder_id = " . $target_folder_id;
		$sql .=   " AND file_id = " . $target_file_id;

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * �ե�����ե����� �ե������ư
	 *
	 * @param  $target_folder_id
	 * @param  $target_file_id
	 * @param  $row
	 * @return $ret ������� (true/false)
	 */
	static function update_folder_file_folder_id($target_folder_id, $target_file_id, $new_folder_id) {
		$row = array();

		/* �����ǡ������å� */
		$row['folder_id'] = $new_folder_id;

		/* �������� */
		$ret = ACSFolderModel::update_folder_file($target_folder_id, $target_file_id, $row);

		return $ret;
	}

	/**
	 * �ե�������Τθ����ϰϼ���
	 *
	 * @param  $community_id
	 * @return open_level_row
	 */
	static function select_contents_folder_open_level_row ($community_id) {
		$row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D31'));

		// �������ĥ��ߥ�˥ƥ�����
		$row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $row['contents_type_code'], $row['open_level_code']);

		return $row;
	}

	/**
	 * �ե�������Τθ����ϰ� (�ǥե����) ����
	 *
	 * @param  $community_type_name
	 * @return open_level_row
	 */
	static function select_folder_open_level_default_row ($community_type_name) {
		$default_row = array();
		$row_array = ACSAccessControl::get_open_level_master_row_array($community_type_name, ACSMsg::get_mst('contents_type_master','D31'));
		foreach ($row_array as $row) {
			if ($row['is_default']) {
				$default_row = $row;
				break;
			}
		}

		return $default_row;
	}

	/**
	 * �ץå��襳�ߥ�˥ƥ����
	 *
	 * @param $folder_id
	 * @param $put_community_id
	 */
	static function delete_put_community ($folder_id, $put_community_id) {
		$sql  = "DELETE FROM put_community";
		$sql .= " WHERE";
		$sql .=       " folder_id = '$folder_id'";
		$sql .=   " AND put_community_id = '$put_community_id'";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * �ץå��襳�ߥ�˥ƥ����ƺ��
	 *
	 * @param $put_community_id
	 * @param $put_community_folder_id
	 */
	static function delete_all_put_community ($put_community_id, $put_community_folder_id) {
		$sql  = "DELETE FROM put_community";
		$sql .= " WHERE";
		$sql .=       " put_community_id = '$put_community_id'";
		$sql .=   " AND put_community_folder_id = '$put_community_folder_id'";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * �ե�����Υץåȥ��ߥ�˥ƥ���������ƺ��
	 *
	 * @param $folder_id �ץåȾ���
	 * @return true(����) / false(����)
	 */
	static function delete_put_community_by_folder_id($folder_id) {
		$folder_id = pg_escape_string($folder_id);
		$sql  = "DELETE";
		$sql .= " FROM put_community";
		$sql .= " WHERE folder_id = '$folder_id'";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * �ץå��襳�ߥ�˥ƥ�����
	 *
	 * @param $folder_id
	 * @param $put_community_id
	 * @param $put_community_folder_id
	 */
	static function insert_put_community ($folder_id, $put_community_id, $put_community_folder_id) {
		$sql  = "INSERT INTO put_community";
		$sql .= " (folder_id, put_community_id, put_community_folder_id)";
		$sql .= " VALUES (";
		$sql .=           $folder_id . ",";
		$sql .=           $put_community_id . ",";
		$sql .=           $put_community_folder_id;
		$sql .=  ")";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * �ե�������
	 * �۲��Υե������������
	 *
	 * @param $folder_id_array
	 */
	static function delete_folder ($folder_id_array) {
		// folder_file ���
		$ret = ACSFolderModel::delete_folder_file($folder_id_array);
		if (!$ret) {
			return false;
		}

		// folder ���
		$target_parent_folder_id = implode(", ", $folder_id_array);

		$sql  = "DELETE FROM folder";
		$sql .= " WHERE";
		$sql .=       " folder_id IN (" . $target_parent_folder_id . ")";

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			return false;
		}

		return $ret;
	}
	static function delete_folder_file ($folder_id_array) {
		/* folder_file �� file_info ������� */
		// folder_file ������˼�������ɬ�פ���
		$file_info_row_array = ACSFolderModel::select_folder_file_info_row_array($folder_id_array);

		/* folder_file ��� */
		$target_folder_id = implode(", ", $folder_id_array);
		$sql  = "DELETE FROM folder_file";
		$sql .= " WHERE";
		$sql .=       " folder_id IN (" . $target_folder_id . ")";

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			return false;
		}

		/* �ե������� */
		if ($file_info_row_array) {
			foreach ($file_info_row_array as $file_info_row) {
				$file_obj = new ACSFile($file_info_row);
				$ret = $file_obj->delete_file();
				if (!$ret) {
					return false;
				}
			}
		}

		return $ret;
	}
}
?>
