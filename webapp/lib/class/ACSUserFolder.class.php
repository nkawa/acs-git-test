<?php
/**
 * ACS User Folder
 *
 * @author  kuwayama
 * @version $Revision: 1.13 $ $Date: 2008/04/24 16:00:00 y-yuki Exp $
 */
require_once(ACS_CLASS_DIR . 'ACSGenericFolder.class.php');

define('_ACSUSERFOLDER_COMMUNITY_TYPE_MASTER', 
		ACSMsg::get_mst('community_type_master','D10'));

class ACSUserFolder extends ACSGenericFolder
{
	/* ���ߥ�˥ƥ�������̾ */
	var $community_type_name = _ACSUSERFOLDER_COMMUNITY_TYPE_MASTER;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param $user_community_id
	 * @param $acs_user_info_row ���������Ծ���
	 * @param $folder_id
	 */
	function ACSUserFolder ($user_community_id, $acs_user_info_row, $folder_id) {
		/* �ե����ID�λ��꤬�ʤ���硢�롼�ȥե������������� */
		if ($folder_id == "") {
			$folder_row = $this->get_root_folder_row($user_community_id);

			$folder_id = $folder_row['folder_id'];
		}

		// �桼���ե�����Ǥϡ������оݤΥե���������ɽ������ե������Ʊ��
		parent::ACSGenericFolder($user_community_id, $acs_user_info_row, $folder_id, array($folder_id));
	}

	/**
	 * �ե��������
	 *
	 * @param $user_community_id
	 * @param $form �������
	 * @return �ե�������������
	 */
	function search_folder_row_array($user_community_id, $form) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT *";
		$sql .= " FROM folder";
		$sql .= " WHERE folder.community_id = '$user_community_id'";
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
	 * @param $user_community_id
	 * @param $form �������
	 * @return �ե�������������
	 */
	function search_file_info_row_array($user_community_id, $form) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT *";
		$sql .= " FROM folder, folder_file, file_info";
		$sql .= " WHERE folder.community_id = '$user_community_id'";
		$sql .= "  AND folder.folder_id = folder_file.folder_id";
		$sql .= "  AND folder_file.file_id = file_info.file_id";
		$sql .= "  AND file_info.owner_community_id  = '$user_community_id'";

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
	 * �ե�������Τθ����ϰϥ��å�
	 * �桼���Υե�����ξ��ϡ����̸���
	 *
	 * @param $community_id
	 */
	function set_contents_folder_open_level ($community_id) {
		$open_level_code = "";
		$open_level_name = "";

		// �ե�������Τθ����ϰ� ���̸��������
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D32'));
		foreach ($open_level_master_row_array as $open_level_master_row) {
			if ($open_level_master_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D01')) {
				$open_level_code = $open_level_master_row['open_level_code'];
				$open_level_name = $open_level_master_row['open_level_name'];

				break;
			}
		}

		$this->open_level_code = $open_level_code;
		$this->open_level_name = $open_level_name;

		// �������ĥ��ߥ�˥ƥ����å�
		// ����ʤ�
		$this->set_contents_folder_trusted_community_row_array(array());
	}

	/**
	 * �ե�����˥��������������뤫
	 *
	 * @param  $target_user_info_row ɽ���оݥޥ��ڡ�������
	 * @return true / false
	 */
	function has_privilege ($target_user_info_row) {
		$ret_folder_obj_array = array();

		/* role_array ���� */
		$role_array = ACSAccessControl::get_user_community_role_array($this->get_acs_user_info_row(), $target_user_info_row);

		$folder_obj = $this->get_folder_obj();
		$ret_folder_obj = ACSAccessControl::get_valid_obj_row_array_for_user_community($this->get_acs_user_info_row(), $role_array, array($folder_obj));

		if ($ret_folder_obj) {
			return true;

		// �ʤ����ϡ����������Բ�
		} else {
			return false;
		}
	}

	/**
	 * �ޥ��ե�󥺤ο���ե����������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID (�������꡼�ؤΥ��������ԤȤʤ�桼��)
	 *        $days ������������(�ǶᲿ���֤ο����������)
	 * @return ����ե�������� (Ϣ�����������)
	 */
	function get_new_friends_file_row_array($user_community_id, $days=false, $offset=false) {
		// �ޥ��ե�󥺤Υ桼�����ߥ�˥ƥ�ID��CSV���������
		// �ޥ��ե�󥺤ξ��csvʸ�������
		$csv_string = ACSUserFolder::get_new_friends_csv_string($user_community_id);

		$row_array = array();

		if ($csv_string!='') {
			// �ޥ��ե�󥺥ե�����ο����������
			$row_array = ACSUserFolder::get_new_folder_row_array(
								$user_community_id, $csv_string, $days, $offset);
		}

		return $row_array;

	}

	/**
	 * �ޥ��ե�󥺤�csvʸ������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID (���������ԤȤʤ�桼��)
	 * @return csvʸ����
	 */
	function get_new_friends_csv_string($user_community_id) {

		// �ޥ��ե�󥺤Υ桼�����ߥ�˥ƥ�ID��CSV���������
		// �ޥ��ե�󥺤μ���
		$friends_row_array = ACSUser::get_simple_friends_row_array($user_community_id);

		// �ޥ��ե�󥺤ξ��csvʸ�������
		$csv_string = 
			ACSLib::get_csv_string_from_array($friends_row_array, 'user_community_id');

		return $csv_string;
	}

	/**
	 * �ޥ��ե�󥺤ο���ե����������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID (�������꡼�ؤΥ��������ԤȤʤ�桼��)
	 * @return ����ե�������� (Ϣ�����������)
	 */
	function get_new_friends_folder_row_array($user_community_id) {

		// �ޥ��ե�󥺤Υ桼�����ߥ�˥ƥ�ID��CSV���������
		// �ޥ��ե�󥺤μ���
		$friends_row_array = ACSUser::get_simple_friends_row_array($user_community_id);

		// �ޥ��ե�󥺤ξ��csvʸ�������
		$csv_string = 
			ACSLib::get_csv_string_from_array($friends_row_array, 'user_community_id');

		// �ޥ��ե�󥺥ե�����ο����������
		$row_array = ACSUserFolder::get_new_folder_row_array(
							$user_community_id, $csv_string);

		return $row_array;

	}
}
?>
