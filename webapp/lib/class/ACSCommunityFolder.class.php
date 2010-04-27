<?php
/**
 * ACS Community Folder
 *
 * @author  kuwayama
 * @version $Revision: 1.14 $ $Date: 2007/03/01 09:01:12 y-yuki Exp $
 */
require_once(ACS_CLASS_DIR . 'ACSGenericFolder.class.php');
require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');

define('_ACSCOMMUNITYFOLDER_COMMUNITY_TYPE_MASTER',
		ACSMsg::get_mst('community_type_master','D40'));

class ACSCommunityFolder extends ACSGenericFolder
{
	/* ���ߥ�˥ƥ�������̾ */
	var $community_type_name = _ACSCOMMUNITYFOLDER_COMMUNITY_TYPE_MASTER;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param $community_id
	 * @param $acs_user_info_row ���������Ծ���
	 * @param $folder_id
	 */
	function ACSCommunityFolder ($community_id, $acs_user_info_row, $folder_id) {
		/* �ե����ID�λ��꤬�ʤ���硢�롼�ȥե������������� */
		if ($folder_id == "") {
			$folder_row = $this->get_root_folder_row($community_id);

			$folder_id = $folder_row['folder_id'];
		}

		// �ץåȥե��������
		$put_folder_id_array = ACSFolderModel::select_put_folder($community_id, $folder_id);

		// ɽ���оݤȤʤ�ե����������������ɽ�����륳�ߥ�˥ƥ��Υե���� + �ץåȤ���Ƥ���ե������
		$target_folder_array = array();
		$target_folder_array[] = $folder_id;
		$target_folder_array = array_merge($target_folder_array, $put_folder_id_array);

		parent::ACSGenericFolder($community_id, $acs_user_info_row, $folder_id, $target_folder_array);
	}

	/**
	 * ɽ���оݥ��ߥ�˥ƥ������ե�����ꥹ�ȥ��å�
	 *
	 * @param $community_id
	 */
	function set_all_community_folders_obj_array ($community_id) {
		parent::set_all_community_folders_obj_array($community_id);

		// �ѥ���˼���
		$target_folder_obj = $this->get_folder_obj();

		// �ץåȥե�����ξ��
		//    �ץåȤ���Ƥ���ե�����ޤǸ���
		if ($target_folder_obj->is_put_folder($this->get_community_id())) {
			$add_folder_obj_array = array();    // �ץåȤ���Ƥ���ե�����Υ桼���ե������Υѥ����Ǽ
			// �ץåȤ���Ƥ���ե�����Υ桼���ե���������
			$put_folder_obj = new ACSUserFolder($target_folder_obj->get_community_id(),
			                                    $target_folder_obj->get_acs_user_info_row(),
			                                    $target_folder_obj->get_folder_id());

			// �ץåȤ���Ƥ���ե�����Υ桼���ե������Υѥ������
			//   �������ܤΥե���������ץåȤǤ��ʤ����ᡢ�������ܤΥե�����ʹߤΥѥ������
			$add_folder_obj_array = array_slice($put_folder_obj->get_path_folder_obj_array(), 1);
		}
		//_debug($add_folder_obj_array);
		//_debug($this->all_community_folders_obj_array);
		if ($this->all_community_folders_obj_array == NULL || $add_folder_obj_array == NULL) {
			return;
		}
		$this->all_community_folders_obj_array = array_merge($this->all_community_folders_obj_array, $add_folder_obj_array);
		//_debug($this->all_community_folders_obj_array);
	}

	/**
	 * ɽ���оݥե�����Υѥ��ե�����ꥹ�ȥ��å�
	 *    �ץåȤ���Ƥ���ե�������б�
	 */
	function set_path_folder_obj_array () {
		// �ե�����ѥ�����
		$all_community_folders_obj_array  = $this->get_all_community_folders_obj_array();
		$display_all_folders_obj_array = array();

		// �ѥ���˼���
		$target_folder_obj = $this->get_folder_obj();

		$search_parent_folder_id = $target_folder_obj->get_parent_folder_id();
		$is_root_folder = $target_folder_obj->get_is_root_folder();
		if ($is_root_folder) {
			// �롼�ȥǥ��쥯�ȥ�ξ��ϡ��ѥ��˥롼�ȥǥ��쥯�ȥ���ɲä���
			array_push($display_all_folders_obj_array, $target_folder_obj);
		}

		while (!$is_root_folder) {
			// �ƥե�����򸡺�
			foreach ($all_community_folders_obj_array as $all_community_folders_obj) {
				$target_folder_id = $all_community_folders_obj->get_folder_id();

				if ($search_parent_folder_id == $target_folder_id) {
					$search_parent_folder_id = $all_community_folders_obj->get_parent_folder_id();
					// �ץåȤ���Ƥ���ե�����ξ�硢��������ե������ץå���ե�������ѹ�����
					if ($all_community_folders_obj->has_put_community()) {
						$search_parent_folder_id = $all_community_folders_obj->get_put_community_folder_id($this->get_community_id());
						break;
					}

					array_unshift($display_all_folders_obj_array, $all_community_folders_obj);
					break;
				}
			}
			$is_root_folder = $all_community_folders_obj->get_is_root_folder();
			if ($is_root_folder) {
				// �Ǹ�ˡ����ߤΥե�������ɲ�
				array_push($display_all_folders_obj_array, $target_folder_obj);
			}
		}

		$this->path_folder_obj_array = $display_all_folders_obj_array;
	}

	/**
	 * �ե�������
	 * �ץåȥե�����������뵡ǽ���ɲ�
	 *
	 * @param $target_folder_obj  ����оݤΥե����
	 */
	function delete_folder ($target_folder_obj) {
		// �۲��Υե������������
		$target_folder_obj = $this->get_lower_folder_tree($target_folder_obj);

		// �۲������ե����ID����
		$folder_id_array = array();  // �۲��Υե�������Ƥ����åȤ����
		$this->get_lower_folder_obj_array($target_folder_obj, $folder_id_array);

		// �оݤȤʤ�ե�������ɲ�
		array_push($folder_id_array, $target_folder_obj->get_folder_id());

		// �ץåȲ��
		// �۲��Υե�����Υץåȥե������������
		$put_community_id = $target_folder_obj->get_community_id();
		foreach ($folder_id_array as $folder_id) {
			$put_community_folder_id = $folder_id;
			$ret = ACSFolderModel::delete_all_put_community($put_community_id, $put_community_folder_id);
			if (!$ret) {
				return $ret;
			}
		}

		// �оݤΥե�������
		return parent::delete_folder($target_folder_obj);
	}

	/**
	 * �ե��������
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
	 * @param $form �������
	 * @return �ե�������������
	 */
	function search_folder_row_array($community_id, $form) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT *";
		$sql .= " FROM folder";
		$sql .= " WHERE folder.community_id = '$community_id'";
		$sql .= "  AND folder.parent_folder_id is not null";

		if ($form['q'] != '') {
			$query_array_array = ACSLib::get_query_array_array($form['q']);
			$where_sql = '';
			foreach ($query_array_array as $query_array) {
				if (!count($query_array)) {
					continue;
				}

				$sub_where_sql = '';
				foreach ($query_array as $query) {
					$query = pg_escape_string($query);
					ACSLib::escape_ilike($query);

					if ($sub_where_sql != '') {
						$sub_where_sql .= " OR ";
					}

					$sub_where_sql .= "(";
					$sub_where_sql .= " folder.folder_name ILIKE '%$query%'";
					$sub_where_sql .= " OR folder.comment ILIKE '%$query%'";
					$sub_where_sql .= ")";
				}

				if ($sub_where_sql != '') {
					if ($where_sql != '') {
						$where_sql .= " AND ";
					}
					$where_sql .= "($sub_where_sql)";
				}
			}

			if ($where_sql != '') {
				$sql .= " AND ($where_sql)";
			}
		}
		//

		// ORDER
		if ($form['order'] == 'update_date') {
			$sql .= " ORDER BY folder.update_date DESC";
		} else {
			$sql .= " ORDER BY folder.folder_name ASC";
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �ץåȥե��������
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
	 * @param $form �������
	 * @return �ե�������������
	 */
	function search_put_folder_row_array($community_id, $form) {
		$community_id = pg_escape_string($community_id);

		// 1. ���ߥ�˥ƥ��˥ץåȤ���Ƥ�桼���ե������folder_id�����Ƽ�������
		$sql  = "SELECT folder_id";
		$sql .= " FROM put_community";
		$sql .= " WHERE put_community.put_community_id = '$community_id'";
		$row_array = ACSDB::_get_row_array($sql);
		$folder_id_array = array();
		foreach ($row_array as $row) {
			array_push($folder_id_array, $row['folder_id']);
		}
		if (count($folder_id_array) == 0) {
			// 0��
			return array();
		}

		// 2. ���ߥ�˥ƥ��ե�������饢��������ǽ����ȤΥե������folder_id�����Ƽ�������
		$user_folder_id_array = ACSGenericFolder::get_recursive_folder_id_array($folder_id_array);
		foreach ($folder_id_array as $folder_id) {
			// �ץåȤ����ե�������Τϸ����оݤˤʤ�ʤ�
			$key = array_search($folder_id, $user_folder_id_array);
			if (!($key === false)) {
				unset($user_folder_id_array[$key]);
			}
		}
		//sort($user_folder_id_array);
		if (count($user_folder_id_array) == 0) {
			// 0��
			return array();
		}

		// 3. �����оݤȤʤ롢�桼�����ץåȤ����ե�����ʲ��Υե����ID CSV
		$user_folder_id_array_csv = implode(',', $user_folder_id_array);


		$sql  = "SELECT *";
		$sql .= " FROM folder";
		$sql .= " WHERE folder.folder_id IN ($user_folder_id_array_csv)";
		$sql .= "  AND folder.parent_folder_id is not null";

		if ($form['q'] != '') {
			$query_array_array = ACSLib::get_query_array_array($form['q']);
			$where_sql = '';
			foreach ($query_array_array as $query_array) {
				if (!count($query_array)) {
					continue;
				}

				$sub_where_sql = '';
				foreach ($query_array as $query) {
					$query = pg_escape_string($query);
					ACSLib::escape_ilike($query);

					if ($sub_where_sql != '') {
						$sub_where_sql .= " OR ";
					}

					$sub_where_sql .= "(";
					$sub_where_sql .= " folder.folder_name ILIKE '%$query%'";
					$sub_where_sql .= " OR folder.comment ILIKE '%$query%'";
					$sub_where_sql .= ")";
				}

				if ($sub_where_sql != '') {
					if ($where_sql != '') {
						$where_sql .= " AND ";
					}
					$where_sql .= "($sub_where_sql)";
				}
			}

			if ($where_sql != '') {
				$sql .= " AND ($where_sql)";
			}
		}
		//

		// ORDER
		if ($form['order'] == 'update_date') {
			$sql .= " ORDER BY folder.update_date DESC";
		} else {
			$sql .= " ORDER BY folder.folder_name ASC";
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �ե����븡��
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
	 * @param $form �������
	 * @return �ե�������������
	 */
	function search_file_info_row_array($community_id, $form) {
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT *";
		$sql .= " FROM folder, folder_file, file_info";
		$sql .= " WHERE folder.community_id = '$community_id'";
		$sql .= "  AND folder.folder_id = folder_file.folder_id";
		$sql .= "  AND folder_file.file_id = file_info.file_id";
		$sql .= "  AND file_info.owner_community_id  = '$community_id'";

		if ($form['q'] != '') {
			$query_array_array = ACSLib::get_query_array_array($form['q']);
			$where_sql = '';
			foreach ($query_array_array as $query_array) {
				if (!count($query_array)) {
					continue;
				}

				$sub_where_sql = '';
				foreach ($query_array as $query) {
					$query = pg_escape_string($query);
					ACSLib::escape_ilike($query);

					if ($sub_where_sql != '') {
						$sub_where_sql .= " OR ";
					}

					$sub_where_sql .= "(";
					$sub_where_sql .= " file_info.display_file_name ILIKE '%$query%'";
					$sub_where_sql .= " OR file_info.comment ILIKE '%$query%'";
					$sub_where_sql .= ")";
				}

				if ($sub_where_sql != '') {
					if ($where_sql != '') {
						$where_sql .= " AND ";
					}
					$where_sql .= "($sub_where_sql)";
				}
			}

			if ($where_sql != '') {
				$sql .= " AND ($where_sql)";
			}
		}
		//

		// ORDER 
		if ($form['order'] == 'update_date') { 
			$sql .= " ORDER BY file_info.update_date DESC";
		} else {
			$sql .= " ORDER BY file_info.display_file_name ASC";
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ���ꥳ�ߥ�˥ƥ��ץåȥե����븡��
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
	 * @param $form �������
	 * @return �ե�������������
	 */
	function search_put_file_info_row_array($community_id, $form) {
		$community_id = pg_escape_string($community_id);

		return ACSCommunityFolder::search_all_put_file_info_row_array(
						$form, "put_community.put_community_id = '$community_id'");
	}

	/**
	 * �����ߥ�˥ƥ��ץåȥե����븡��
	 *
	 * @param $community_where put���ߥ�˥ƥ��������
	 * @param $unread_check_user_community_id unread�����å��»ܻ��Υ桼�����ߥ�˥ƥ�id
	 * @param $form �������
	 * @return �ե�������������
	 */
	function search_all_put_file_info_row_array(
			$form, $community_where = "", $unread_check_user_community_id = "", $days=false) {

		// 1. ���ߥ�˥ƥ��˥ץåȤ���Ƥ�桼���ե������folder_id�����Ƽ�������
		$sql  = "SELECT folder_id";
		$sql .= " FROM put_community";
		if($community_where != ""){
			$sql .= " WHERE " . $community_where;
		}

		$row_array = ACSDB::_get_row_array($sql);
		$folder_id_array = array();
		foreach ($row_array as $row) {
			array_push($folder_id_array, $row['folder_id']);
		}
		if (count($folder_id_array) == 0) {
			// 0��
			return array();
		}

		// 2. ���ߥ�˥ƥ��ե�������饢��������ǽ����ȤΥե������folder_id�����Ƽ�������
		$user_folder_id_array = ACSGenericFolder::get_recursive_folder_id_array($folder_id_array);
		if (count($user_folder_id_array) == 0) {
			// 0��
			return array();
		}

		// 3. �����оݤȤʤ롢�桼�����ץåȤ����ե�����ʲ��Υե����ID CSV
		$user_folder_id_array_csv = implode(',', $user_folder_id_array);


		$sql  = "SELECT * ";

		if ($unread_check_user_community_id != '') {
			$sql .= ",acs_is_unread_file(" . 
					$unread_check_user_community_id . ",file_info.file_id) as is_unread ";
		}

		$sql .= " FROM folder LEFT OUTER JOIN put_community ON folder.folder_id = put_community.folder_id, folder_file, file_info";
		$sql .= " WHERE folder.folder_id IN ($user_folder_id_array_csv)";
		$sql .= "  AND folder.folder_id = folder_file.folder_id";
		$sql .= "  AND folder_file.file_id = file_info.file_id";

		if ($form['q'] != '') {
			$query_array_array = ACSLib::get_query_array_array($form['q']);
			$where_sql = '';
			foreach ($query_array_array as $query_array) {
				if (!count($query_array)) {
					continue;
				}

				$sub_where_sql = '';
				foreach ($query_array as $query) {
					$query = pg_escape_string($query);
					ACSLib::escape_ilike($query);

					if ($sub_where_sql != '') {
						$sub_where_sql .= " OR ";
					}

					$sub_where_sql .= "(";
					$sub_where_sql .= " file_info.display_file_name ILIKE '%$query%'";
					$sub_where_sql .= " OR file_info.comment ILIKE '%$query%'";
					$sub_where_sql .= ")";
				}

				if ($sub_where_sql != '') {
					if ($where_sql != '') {
						$where_sql .= " AND ";
					}
					$where_sql .= "($sub_where_sql)";
				}
			}

			if ($where_sql != '') {
				$sql .= " AND ($where_sql)";
			}
		}
		//

		// �������꤬������
		if($days !== false){
			$sql = $sql . " AND " . 
					ACSLib::get_sql_condition_from_today("file_info.update_date", $days);
		}

		// ORDER
		if($rows != false){
			// ɽ��������� //
			$display_count = 
					ACSSystemConfig::get_keyword_value(ACSMsg::get_mst(
							'system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
			$sql = $sql . " OFFSET 0 LIMIT ". $display_count;
		} else {
			if ($form['order'] == 'update_date') { 
				$sql .= " ORDER BY file_info.update_date DESC";
			} else {
				$sql .= " ORDER BY file_info.display_file_name ASC";
			}
		}

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �ե�������Τθ����ϰϥ��å�
	 *
	 * @param $community_id
	 */
	function set_contents_folder_open_level ($community_id) {
		// �ե�������Τθ����ϰϼ���
		$open_level_row = ACSFolderModel::select_contents_folder_open_level_row($community_id);
		if (!$open_level_row) {
			$community_type_name = $this->get_community_type_name();
			$open_level_row = ACSFolderModel::select_folder_open_level_default_row ($community_type_name);
		}

		$this->open_level_code = $open_level_row['open_level_code'];
		$this->open_level_name = $open_level_row['open_level_name'];

		// �������ĥ��ߥ�˥ƥ����å�
		$this->set_contents_folder_trusted_community_row_array($open_level_row['trusted_community_row_array']);
	}

	/**
	 * �ե�����˥��������������뤫
	 *
	 * @param  $target_community_row ɽ���оݥ��ߥ�˥ƥ�����
	 * @return true / false
	 */
	function has_privilege ($target_community_row) {
		$ret_folder_obj_array = array();

		/* role_array ���� */
		$role_array = ACSAccessControl::get_community_role_array($this->get_acs_user_info_row(), $target_community_row);

		$folder_obj = $this->get_folder_obj();
		$ret_folder_obj = ACSAccessControl::get_valid_obj_row_array_for_community($this->get_acs_user_info_row(), $role_array, array($folder_obj));

		if ($ret_folder_obj) {
			return true;

		// �ʤ����ϡ����������Բ�
		} else {
			return false;
		}
	}

	/**
	 * �ޥ����ߥ�˥ƥ��ο���ե����������������
	 *
     * @param $user_community_id �桼�����ߥ�˥ƥ�ID (�������꡼�ؤΥ��������ԤȤʤ�桼��)
     *        $days ������������(�ǶᲿ���֤ο����������)
     * @return ����ե�������� (Ϣ�����������)
	 */
	function get_new_community_folder_row_array($user_community_id, $days=false, $offset=false) {

		// �ޥ����ߥ�˥ƥ��Υ��ߥ�˥ƥ�ID��CSV���������
		// �ޥ����ߥ�˥ƥ��μ���
		$community_row_array = ACSUser::get_community_row_array($user_community_id);

		// �ޥ����ߥ�˥ƥ��ξ��csvʸ�������
		$csv_string = 
			ACSLib::get_csv_string_from_array($community_row_array, 'community_id');

		$row_array = array();

		if ($csv_string!='') {
			// �ޥ����ߥ�˥ƥ��ե�����ο����������
			$row_array = ACSCommunityFolder::get_new_folder_row_array(
					$user_community_id, $csv_string, $days, $rows);
		}
		return $row_array;
	}

	/**
	 * �ޥ����ߥ�˥ƥ��ο���ץåȥե����������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID (���������ԤȤʤ�桼��)
	 *        $days ������������(�ǶᲿ���֤ο����������)
	 * @return ����ե�������� (Ϣ�����������)
	 */
	function get_new_community_put_folder_row_array($user_community_id, &$form, $days=false, $offset=false) {

		// �ޥ����ߥ�˥ƥ��Υ��ߥ�˥ƥ�ID��CSV���������
		// �ޥ����ߥ�˥ƥ��μ���
		$community_row_array = ACSUser::get_community_row_array($user_community_id);
		// �ޥ����ߥ�˥ƥ��ξ��csvʸ�������
		$csv_string = 
			ACSLib::get_csv_string_from_array($community_row_array, 'community_id');

		$row_array = array();

		if ($csv_string!='') {
			$condition = "put_community.put_community_id IN (" . $csv_string . ")";

			// �ޥ����ߥ�˥ƥ��ץåȥե�����ο����������
			$row_array = ACSCommunityFolder::search_all_put_file_info_row_array(
					$form, $condition, $user_community_id, $days, $rows);
		}
		return $row_array;
	}
}
?>
