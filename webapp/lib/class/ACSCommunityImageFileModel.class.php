<?php
/**
 * �����ե�������� DB��������
 * ACSCommunityImageFileModel.class.php
 *
 * community_image_file�ơ��֥�
 * @author  akitsu
 * @version $Revision: 1.5 $Date: 2008/03/24 07:00:36 $
 */
class ACSCommunityImageFileModel
{
	/* �ե�����ID */
	var $file_id;

	/* ���ߥ�˥ƥ�ID */
	var $community_id;
		
	/**
	 * �����ե�������������ʣ����
	 * �ҤȤ�Υ桼�������Ĳ�������Τ��٤�
	 * @param $file_id_array
	 */
	static function select_community_image_row_array ($community_id) {
		$sql  = "SELECT community_image_file.*,";
		$sql .=	   " file_info.display_file_name AS save_file_name,file_info.server_file_name AS insystem_file_name,";
		$sql .=	   " file_info.mime_type AS file_kind";	//����
		$sql .= " FROM file_info, community_image_file";
		$sql .= " WHERE community_id = " . $community_id . ")";
		$sql .=   " AND file_info.owner_community_id = community_image_file.community_id";
		$sql .= " ORDER BY community_image_file.file_id ASC";
		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �����ե������������ʣ����
	 *
	 * @param $file_id
	 */
	static function select_community_image_row ($community_id) {
		$community_id_array = array($community_id);
		$row_array = ACSCommunityImageModel::select_community_image_row_array($file_id_array);

		return $row_array[0];
	}

	/**
	 * ���ߥ�˥ƥ������ե�����ID���������
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
	 * @return file_id
	 */
	static function get_file_id($community_id) {
		if (!$community_id) {
			return;
		}
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT file_id";
		$sql .= " FROM community_image_file";
		$sql .= " WHERE community_image_file.community_id = '$community_id'";

		$value = ACSDB::_get_value($sql);
		return $value;
	}

	/**
	 * �����ե���������ɲ�
	 *
	 * @param $file_obj 
	 */
	static function insert_community_image ($file_obj) {
		$sql  = "INSERT INTO community_image_file";
		$sql .= " (file_id, community_id)";
		$sql .= " VALUES (";
		$sql .=		   "" . $file_obj->get_file_id() . ",";
		$sql .=		   "" . $file_obj->get_owner_community_id();
		$sql .=  ")";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
	/**
	 * �����ե����������
	 *
	* @param $file_obj
	*/
	static function delete_community_image ($file_obj) {
		$sql  = "DELETE FROM community_image_file";
		$sql .= " WHERE";
		$sql .=		 " file_id = " . $file_obj->get_file_id();
		$sql .= " AND";
		$sql .=		" community_id = " . $file_obj->get_owner_community_id();

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * ���ߥ�˥ƥ������ե�����ID���������ʸ����ϰ�����ͭ���
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
	 * @return �Ƹ����ϰ���Υե�����ID
	 */
	static function get_file_id_with_open_level($community_id) {
		if (!$community_id) {
			return;
		}
		$sql  = "SELECT file_id, file_id_ol01, file_id_ol02, file_id_ol05 ";
		$sql .= " FROM community_image_file";
		$sql .= " WHERE community_image_file.community_id = '$community_id'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * �����ե�����������������ʸ����ϰ�����ͭ���
	 *
	 * @param $file_obj �����ե�����Υ��֥�������
	 * @param $open_level_code ������٥륳����
	 * @return ����(true) / ����(false)
	*/
	static function delete_community_image_with_open_level ($file_obj, $open_level_code) {

		$row = ACSCommunityImageFileModel::get_file_id_with_open_level(
				$file_obj->get_owner_community_id());
				
		// �����ϰϤ����ե�����ID��NULL�Ȥ���
		$sql  = "UPDATE community_image_file";
		$sql .= " SET";
		// ͧ�͸������Ф��ƺ���ξ��
		if ($open_level_code == "05" ) {
			if ($row['file_id_ol02'] != NULL) {
				// ������桼�������Υե�����ID������С�file_id�ϥ�����桼�������ˤ���
				$sql .= " file_id = " . $row['file_id_ol02'] . ",";
			} else if ($row['file_id_ol01'] != NULL) {
				// ���̸����Υե�����ID������С�file_id�ϰ��̸����ˤ���
				$sql .= " file_id = " . $row['file_id_ol01'] . ",";
			}
		}
		
		// ������桼���������Ф��ƺ���ξ�� 
		if ($open_level_code == "02" ) {
			if ($row['file_id_ol05'] != NULL) {
				// ͧ�͸����Υե�����ID������С�file_id��ͧ�͸����ˤ���
				$sql .= " file_id = " . $row['file_id_ol05'] . ",";
			} else if ($row['file_id_ol01'] != NULL) {
				// ���̸����Υե�����ID������С�file_id�ϰ��̸����ˤ���
				$sql .= " file_id = " . $row['file_id_ol01'] . ",";
			}
		}

		// ���̸������Ф��ƺ���ξ��
		if ($open_level_code == "01" ) {
			if ($row['file_id_ol05'] != NULL) {
				// ͧ�͸����Υե�����ID������С�file_id��ͧ�͸����ˤ���
				$sql .= " file_id = " . $row['file_id_ol05'] . ",";
			} else if ($row['file_id_ol02'] != NULL) {
				// ������桼�������Υե�����ID������С�file_id�ϥ�����桼�������ˤ���
				$sql .= " file_id = " . $row['file_id_ol02'] . ",";
			}
		}
		$sql .=		" file_id_ol" . $open_level_code . " = NULL ";
		$sql .= " WHERE";
		$sql .=		" community_id = " . $file_obj->get_owner_community_id();
		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * �����ե���������ɲáʸ����ϰ�����ͭ���
	 *
	 * @param $file_obj �ե��������
	 * @param $open_level_code ������٥륳����
	 * @return ����(true) / ����(false)
	 */
	static function put_community_image_with_open_level ($file_obj, $open_level_code) {
		
		// �쥳���ɤ�¸�ߤ��뤫��
		$row = ACSCommunityImageFileModel::get_file_id_with_open_level($file_obj->get_owner_community_id());

		if (!$row) {
			$ret = ACSCommunityImageFileModel::insert_community_image_with_open_level($file_obj, $open_level_code);
			
		} else {
			// ¸�ߤ����碪���åץǡ���
			$ret = ACSCommunityImageFileModel::update_community_image_with_open_level($file_obj, $open_level_code, $row);
		}
		return $ret;
	}

	/**
	 * �����ե��������INSERT�ʸ����ϰ�����ͭ���
	 *
	 * @param $file_obj �ե��������
	 * @param $open_level_code ������٥륳����
	 * @return ����(true) / ����(false)
	 */
	static function insert_community_image_with_open_level ($file_obj, $open_level_code) {
		
		$sql  = "INSERT INTO community_image_file";
		$sql .= " (";
		$sql .= 	" file_id, ";
		if ($open_level_code == "01" ) {
			$sql .= 	" file_id_ol01,";
			$sql .= 	" file_id_ol02,";
			$sql .= 	" file_id_ol05,";
		} else if ($open_level_code == "02" ) {
			$sql .= 	" file_id_ol02,";
			$sql .= 	" file_id_ol05,";
		} else if ($open_level_code == "05" ) {
			$sql .= 	" file_id_ol05,";
		}
		$sql .= 	" community_id";
		$sql .= " )";
		$sql .= " VALUES (";
		$sql .=		   "" . $file_obj->get_file_id() . ",";
		if ($open_level_code == "01" ) {
			$sql .=		   "" . $file_obj->get_file_id() . ",";
			$sql .=		   "" . $file_obj->get_file_id() . ",";
			$sql .=		   "" . $file_obj->get_file_id() . ",";
		} else if ($open_level_code == "02" ) {
			$sql .=		   "" . $file_obj->get_file_id() . ",";
			$sql .=		   "" . $file_obj->get_file_id() . ",";
		} else if ($open_level_code == "05" ) {
			$sql .=		   "" . $file_obj->get_file_id() . ",";
		}
		$sql .=		   "" . $file_obj->get_owner_community_id();
		$sql .=  ")";
		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
	

	/**
	 * �����ե��������UPDATE�ʸ����ϰ�����ͭ���
	 *
	 * @param $file_obj �ե��������
	 * @param $open_level_code ������٥륳����
	 * @param $file_id_upd_flg = NULL �ե�������󹹿��ե饰
	 * @return ����(true) / ����(false)
	 */
	static function update_community_image_with_open_level ($file_obj, $open_level_code, $row = NULL) {
		
		$sql  = "UPDATE community_image_file";
		$sql .= " SET ";
		if ($row != NULL) {
			$sql .= 	" file_id = " . $file_obj->get_file_id() . ",";

			// ���̸������Ф��ƹ���������
			if ($open_level_code == "01" ) {
				if ($row['file_id_ol05'] == NULL) {
					$sql .= " file_id_ol05 = " . $file_obj->get_file_id(). ",";
				}
				if ($row['file_id_ol02'] == NULL) {
					$sql .= " file_id_ol02 = " . $file_obj->get_file_id(). ",";
				}
			}
			// ������桼���������Ф��ƹ���������
			if ($open_level_code == "02" ) {
				if ($row['file_id_ol05'] == NULL) {
					$sql .= " file_id_ol05 = " . $file_obj->get_file_id(). ",";
				}
			}
		}
		$sql .= 	" file_id_ol" . $open_level_code . " = " . $file_obj->get_file_id();
		$sql .= " WHERE ";
		$sql .=		" community_id = " . $file_obj->get_owner_community_id() ;
		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
	

	/**
	 * �����θ����ϰϤΥե�����ID���������
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
	 * @param $open_level_code �����ϰ�
	 * @return �����θ����ϰϤΥե�����ID
	 */
	static function get_file_id_for_open_level($community_id, $open_level_code) {
		if (!$community_id) {
			return null;
		}
		if (!$open_level_code) {
			return null;
		}
		$row = ACSCommunityImageFileModel::get_file_id_with_open_level($community_id);
		$match_file_id = $row['file_id_ol' . $open_level_code];
		if (!$match_file_id) {
			return NULL;
		}
		$match_count = 0;
		if ($match_file_id == $row['file_id_ol05']) {
			$match_count++;
		}
		if ($match_file_id == $row['file_id_ol02']) {
			$match_count++;
		}
		if ($match_file_id == $row['file_id_ol01']) {
			$match_count++;
		}
		
		if ($match_count == 1) {
			return $match_file_id;
		}
		return NULL;
	}

}
