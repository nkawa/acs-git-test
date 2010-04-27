<?php
/**
 * ACS Generic Folder (ACS ���ѥե����)
 *
 * �桼���Υե���������ߥ�˥ƥ��Υե�����ǷѾ������
 *
 * @author  kuwayama
 * @version $Revision: 1.21 $ $Date: 2008/04/24 16:00:00 y-yuki Exp $
 */
require_once(ACS_CLASS_DIR . 'ACSFolder.class.php');
require_once(ACS_CLASS_DIR . 'ACSFolderModel.class.php');
class ACSGenericFolder
{
	/* ���ߥ�˥ƥ�ID */
	var $community_id;

	/* �����ϰ� */
	var $open_level_code;
	/* �������ĥ��ߥ�˥ƥ��ꥹ�� */
	var $trusted_community_row_array = array();

	/* �ե���� */
	var $folder_obj;

	/* ɽ���оݥ��ߥ�˥ƥ������ե�����ꥹ�� */
	var $all_community_folders_obj_array;

	/* ɽ���оݥե�����Υѥ��ե�����ꥹ�� */
	var $path_folder_obj_array;

	/* ���ߥ�˥ƥ�������̾ */
	// �Ѿ����륯�饹�����
	var $community_type_name = "";

	/* ���������桼������ */
	var $acs_user_info_row = array();

	/**
	 * ���󥹥ȥ饯��
	 *
	 * ���������桼�����󡢥桼���ե��������
	 * �оݥե�����Υ��֥������Ȥ�����������åȤ��롣
	 *
	 * @param $community_id
	 * @param $acs_user_info_row	  ���������桼������
	 * @param $folder_id
	 * @param $target_folder_id_array �ե�������ե�����Υꥹ�ȼ����оݤȤʤ�ե����ID
	 */
	function ACSGenericFolder ($community_id, $acs_user_info_row, $folder_id, $target_folder_id_array) {
		/* ���ߥ�˥ƥ�ID���å� */
		$this->set_community_id($community_id);

		/* �ե�������󥻥å� */
		// �ե�������Ȥθ����ϰ����򥻥å�
		$this->set_folder_info($community_id);

		/* �ե�������֥������ȥ��å� */
		$this->set_folder_obj($community_id, $acs_user_info_row, $folder_id, $target_folder_id_array);

		/* ɽ���оݥ��ߥ�˥ƥ������ե�����ꥹ�ȥ��å� */
		$this->set_all_community_folders_obj_array($community_id);

		/* ɽ���оݥե�����Υѥ��ե�����ꥹ�ȥ��å� */
		$this->set_path_folder_obj_array();

		/* �ե�����θ����ϰϥ��å� */
		$this->set_folder_obj_open_level();

		/* ���������桼�����󥻥å�*/
		$this->set_acs_user_info_row($acs_user_info_row);
	}

	/**
	 * ���ߥ�˥ƥ�ID���å�
	 *
	 * @param $community_id
	 */
	function set_community_id ($community_id) {
		$this->community_id = $community_id;
	}

	/**
	 * ���ߥ�˥ƥ�ID���å�
	 *
	 * @param $community_id
	 */
	function get_community_id () {
		return $this->community_id;
	}

	/**
	 * ���������桼�����󥻥å�
	 *
	 * @param $acs_user_info_row
	 */
	function set_acs_user_info_row ($acs_user_info_row) {
		$this->acs_user_info_row = $acs_user_info_row;
	}

	/**
	 * ���������桼�����󥲥å�
	 *
	 * @param $acs_user_info_row
	 */
	function get_acs_user_info_row () {
		return $this->acs_user_info_row;
	}

	/**
	 * �ե�������֥������ȥ��å�
	 *
	 * @param $folder_id
	 */
	function set_folder_obj ($community_id, $acs_user_info_row, $folder_id, $target_folder_id_array) {
		static $cache_rows;

		if (is_array($cache_rows[$folder_id])) {
			$folder_row = $cache_rows[$folder_id];
		} else {
			/* �ե����������� */
			$folder_row = ACSFolderModel::select_folder_row($folder_id);
			$cache_rows[$folder_id] = $folder_row;
		}

		/* �ե�������󥹥��󥹥��å� */
		$this->folder_obj = new ACSFolder($folder_row, $target_folder_id_array, $acs_user_info_row);
	}

	/**
	 * �ե�������֥������ȥ��å�
	 *
	 * @param none
	 */
	function get_folder_obj () {
	 	return $this->folder_obj;
	}

	/**
	 * ɽ���оݥ��ߥ�˥ƥ������ե�����ꥹ�ȥ��å�
	 *
	 * @param $community_id
	 */
	function set_all_community_folders_obj_array ($community_id) {
	 	$all_community_folders_obj_array = array();
		/* ���ե����������� */
		$folder_row_array = ACSFolderModel::select_all_community_folder_row_array($community_id);

		foreach ($folder_row_array as $folder_row) {
			/* ���Υ��󥹥��󥹤Τ߼��� */
			$folder_obj = new ACSFolder($folder_row, '', '');

			array_push($all_community_folders_obj_array, $folder_obj);
		}

		/* �ե�������󥹥��󥹥��å� */
		$this->all_community_folders_obj_array = $all_community_folders_obj_array;
	}

	/**
	 * ɽ���оݥ��ߥ�˥ƥ������ե�����ꥹ�ȥ��å�
	 *
	 * @param none
	 */
	function get_all_community_folders_obj_array () {
		return $this->all_community_folders_obj_array;
	}

	
	/**
	 * ɽ���оݥե�����Υѥ��ե�����ꥹ�ȥ��å�
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
	 * ɽ���оݥե�����Υѥ��ե�����ꥹ�ȥ��å�
	 */
	function get_path_folder_obj_array () {
		return $this->path_folder_obj_array;
	}

	/**
	 * �롼�ȥե��������
	 *
	 * @param $community_id
	 */
	function get_root_folder_row ($community_id) {
		/* �롼�ȥե������������� */
		$folder_row = ACSFolderModel::select_root_folder_row($community_id);

		/* �ʤ���硢�������� */
		if ($folder_row == "") {
			$ret = ACSFolderModel::insert_root_folder($community_id);
			/* �ʤ���硢���顼 */
			if (!$ret) {
				print "ERROR: Create root folder failed.<br>\n";
				exit;
			} else {
				// �⤦���ټ�������
				return $this->get_root_folder_row($community_id);
			}
		}

		return $folder_row;
	}

	/**
	 * �ե�����θ����ϰϥ��å�
	 *
	 * @param none
	 */
	function set_folder_obj_open_level () {
		$open_level_code = "";
		$open_level_name = "";
		$trusted_community_row_array = array();

		// �롼�ȥե�����ξ�硢�ե�������Τθ����ϰϤ򥻥å�
		if ($this->folder_obj->get_is_root_folder()) {
			$open_level_code = $this->get_contents_folder_open_level_code();
			$open_level_name = $this->get_contents_folder_open_level_name();
			$trusted_community_row_array = $this->get_contents_folder_trusted_community_row_array();

		} else {
			// �쳬���ܥե�����θ����ϰϤ򥻥å�
			$folder_obj = $this->get_first_level_folder_obj();
			if ($folder_obj->get_open_level_code() == "") {
				$folder_obj = $this;
			}
			$open_level_code = $folder_obj->get_open_level_code();
			$open_level_name = $folder_obj->get_open_level_name();
			$trusted_community_row_array = $folder_obj->get_trusted_community_row_array();
		}

		$this->folder_obj->set_open_level_code($open_level_code);
		$this->folder_obj->set_open_level_name($open_level_name);
		$this->folder_obj->set_trusted_community_row_array($trusted_community_row_array);
	}

	/**
	 * �ե�������󥻥å�
	 *
	 * @param $community_id
	 */
	function set_folder_info ($community_id) {
		$this->set_contents_folder_open_level($community_id);
	}

	/**
	 * �ե�������Τθ����ϰϥ��å�
	 * �Ѿ����륯�饹�����С��饤�ɤ��뤳��
	 *
	 * @param $community_id
	 */
	function set_contents_folder_open_level ($community_id) {
		print "ERROR: not overridden (set_contents_folder_open_level)";
		exit;
	}

	/**
	 * �ե�������Τθ����ϰϥ����ɥ��å�
	 *
	 * @param none
	 */
	function get_contents_folder_open_level_code () {
		return $this->open_level_code;
	}

	/**
	 * �ե�������Τθ����ϰ�̾���å�
	 *
	 * @param none
	 */
	function get_contents_folder_open_level_name () {
		return $this->open_level_name;
	}

	/**
	 * �ե�������Τθ����ϰ� �������ĥ��ߥ�˥ƥ����å�
	 *
	 * @param $trusted_community_row_array
	 */
	function set_contents_folder_trusted_community_row_array ($trusted_community_row_array) {
		$this->trusted_community_row_array = $trusted_community_row_array;
	}

	/**
	 * �ե�������Τθ����ϰ� �������ĥ��ߥ�˥ƥ����å�
	 *
	 * @param none
	 */
	function get_contents_folder_trusted_community_row_array () {
		return $this->trusted_community_row_array;
	}

	/**
	 * �쳬���ܥե��������
	 *
	 * @param  none
	 * @return $folder_obj
	 */
	function get_first_level_folder_obj () {
		$path_folder_obj_array = $this->get_path_folder_obj_array();

		$path_count = count($path_folder_obj_array);
		if ($path_count >= 2) {
			$folder_obj = $path_folder_obj_array[1];
		}

		return $folder_obj;
	}

	/**
	 * ���ߥ�˥ƥ�������̾���å�
	 */
	function get_community_type_name () {
		return $this->community_type_name;
	}

	/**
	 * �ե�����������å�
	 */
	function get_folder_tree () {

		// �롼�ȥե��������
		$root_folder_obj = ACSFolder::get_folder_instance($this->get_root_folder_row($this->get_community_id()));

		// ���֥ե�����򸡺����Ƥ���
		$root_folder_obj->set_sub_folder_obj_array($this->search_sub_folder_obj_array($root_folder_obj));

		return $root_folder_obj;
	}
	function search_sub_folder_obj_array($parent_folder_obj) {
		$all_community_folders_obj_array = $this->get_all_community_folders_obj_array();
		$sub_folder_obj_array = array();

		// ���֥ե��������
		foreach ($all_community_folders_obj_array as $folder_obj) {
			if ($folder_obj->get_parent_folder_id() == $parent_folder_obj->get_folder_id()) {
				// ����˥��֥ե�����򸡺��ʺƵ���
				$folder_obj->set_sub_folder_obj_array($this->search_sub_folder_obj_array($folder_obj));
				array_push($sub_folder_obj_array, $folder_obj);
			}
		}

		return $sub_folder_obj_array;
	}

	/**
	 * ����ե�����۲��Υե�����������å�
	 */
	function get_lower_folder_tree ($target_folder_obj) {

		// ���֥ե�����򸡺����Ƥ���
		$target_folder_obj->set_sub_folder_obj_array($this->search_sub_folder_obj_array($target_folder_obj));

		return $target_folder_obj;
	}
	/**
	 * �ե�������
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

		// �ե�������
		$ret = ACSFolderModel::delete_folder($folder_id_array);

		return $ret;
	}
	function get_lower_folder_obj_array ($target_folder_obj, &$_folder_id_array, $tree_level = 0) {
		// ���֥ե��������
		$sub_folder_obj_array = $this->search_sub_folder_obj_array($target_folder_obj);

		foreach ($sub_folder_obj_array as $sub_folder_obj) {
			$tree_level++;

			array_push($_folder_id_array, $sub_folder_obj->get_folder_id());

			// ����˥��֥ե�����򸡺��ʺƵ���
			$this->get_lower_folder_obj_array($sub_folder_obj, $_folder_id_array, $tree_level);

			// 1���ؾ�θ��������
			$tree_level--;
		}
	}

	/**
	 * �ץåȵ�ǽ�����ѤǤ���ե�������ɤ���
	 *
	 * @param true / false
	 */
	function is_put_available () {
		if ($this->folder_obj->get_is_root_folder()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * �����ϰ����굡ǽ�����ѤǤ���ե�������ɤ���
	 *
	 * @param true / false
	 */
	function is_set_open_level_available () {
		if ($this->folder_obj->get_is_root_folder()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * �ե�����˥��������������뤫
	 * �Ѿ����륯�饹�ǥ����С��饤�ɤ��뤳��
	 */
	function has_privilege ($row) {
		return false;
	}


	/**
	 * �Ƶ�Ū�˥ե����ID�����
	 *
	 */
	static function get_recursive_folder_id_array($folder_id_array) {
		$ret_folder_id_array = $folder_id_array;

		foreach ($folder_id_array as $folder_id) {
			$sub_folder_id_array = ACSGenericFolder::get_sub_folder_id_array($folder_id);
			if (count($sub_folder_id_array)) {
				// ���֥ե����������Ȥ�
				$ret_folder_id_array = array_merge($ret_folder_id_array, ACSGenericFolder::get_recursive_folder_id_array($sub_folder_id_array));
			}
		}

		return $ret_folder_id_array;
	}

	/**
	 * ���֥ե������ID���������
	 *
	 */
	static function get_sub_folder_id_array($folder_id) {
		$folder_id = pg_escape_string($folder_id);

		$sql  = "SELECT folder_id";
		$sql .= " FROM folder";
		$sql .= " WHERE folder.parent_folder_id = '$folder_id'";
		$row_array = ACSDB::_get_row_array($sql);
		$folder_id_array = array();
		foreach ($row_array as $row) {
			array_push($folder_id_array, $row['folder_id']);
		} 

		return $folder_id_array;
	}

	/**
	 * $user_community_id�ο���ե����������������
	 * ($commyunity_row_array�˴ޤޤ��community_id����ͭ����ե�������оݤȤ���)
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID (�������꡼�ؤΥ��������ԤȤʤ�桼��)
	 * @param $csv_string ��ͭ�Ծ��csvʸ����
	 * @return ����ե�������� (Ϣ�����������)
	 */
	function get_new_folder_row_array($user_community_id, $csv_string, $days=false, $offset=false) {
		
		$user_community_id = pg_escape_string($user_community_id);

		// �ե�����ο��嵭����ǿ���˼�������
		$sql = "SELECT *, acs_is_unread_file(" . 
							$user_community_id . ", fi.file_id) as is_unread " . 
				" FROM folder_file AS ff, folder AS fo, " .
				"	  file_info AS fi, community AS cm" .
				//" FROM   folder AS fo, folder_file AS ff, " . 
				//"		file_info AS fi, community AS cm" .
				" WHERE fi.file_id = ff.file_id" .
				"  AND ff.folder_id = fo.folder_id" .
				"  AND fi.owner_community_id = cm.community_id" .
				"  AND fi.owner_community_id IN (" . $csv_string . ")";

		//------ 2007.2 ɽ������û���б�
		// �������꤬������
		if($days !== false){
			$sql = $sql . " AND " . ACSLib::get_sql_condition_from_today("fi.update_date", $days);
		}

		//$sql = $sql . " ORDER BY fi.update_date DESC";
		if($offset != false){
			// ɽ��������� //
			$display_count = 
					ACSSystemConfig::get_keyword_value(ACSMsg::get_mst(
							'system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
			$sql = $sql . " AND open_level_code is not null";
			$sql = $sql . " ORDER BY fi.update_date DESC";
			$sql = $sql . " OFFSET 0 LIMIT ". $display_count;
		} else {
			$sql = $sql . " ORDER BY fi.update_date DESC";
		}
		$row_array = ACSDB::_get_row_array($sql);

		return $row_array;

	}

}
?>
