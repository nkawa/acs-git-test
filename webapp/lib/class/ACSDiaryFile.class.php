<?php
/**
 * diary�ѥե�������� DB��������
 * diary_file��file_info�η��ե��������
 * ACSDiaryFile.class.php
 *
 * diary_file�ơ��֥��Manage����
 * @author  akitsu
 * @version $Revision: 1.3 $
 */
class ACSDiaryFile
{
	/** ACSDiaryFile�Υ��饹
	* @type ACSDiaryFileModel */
	var $diary_file_obj;

	/** ACSFile�Υ��饹
	* @type ACSFileModel */
	var $file_info;

	/**
	 * diary�ե������������ʣ����
	 * �ҤȤĤ�diary�ʷǼ��ġˤ�����File����
	 *
	 * @param $diary_id
	 * @return diary_file��file_info�η��ե��������
	 */
	function select_diary_file_row ($diary_id) {
		$sql  = "SELECT diary_file.*,";
		$sql .=	   " file_info.display_file_name AS save_file_name,file_info.server_file_name AS insystem_file_name,";
		$sql .=	   " file_info.mime_type AS file_kind";	//����
		$sql .= " FROM file_info, diary_file";
		$sql .= " WHERE diary_file.diary_id = " . $diary_id;
		$sql .=   " AND file_info.file_id = diary_file.file_id";
		$sql .= " ORDER BY diary_file.file_id ASC";

		$result = ACSDB::_get_row($sql);
		return $result;
	}

	/**
	 * diary�ե�����������
	 *
	 * @param $file_obj 
	 */
	function get_diary_file ($file_obj,$diary_id) {
		$sql  = "SELECT diary_file.*,";
		$sql .= " FROM diary_file";

		$ret = ACSDB::_do_query($sql);
		if($ret){
			$diary_file_obj = ACSDiaryFileModel::get_diary_file_info_instance($file_obj,$diary_id);
			return $diary_file_obj;
		}else{
			return $ret;
		}
	}

	/**
	 * diary_file�������
	 *
	 * @param $file_id �ե�����ID
	 * @return diary_file����
	 */
	function get_diary_file_row_by_file_id($file_id) {
		$sql  = "SELECT *";
		$sql .= " FROM diary_file";
		$sql .= " WHERE file_id = '" . pg_escape_string($file_id) . "'";
		$sql .= " LIMIT 1";
		$row = ACSDB::_get_row($sql);
		return $row;
	}


	/**
	 * diary�ե���������ɲ�
	 *
	 * @param $file_obj 
	 */
	function insert_diary_file ($file_obj,$diary_id) {
		$id = $file_obj->get_file_id();
		$sql  = "INSERT INTO diary_file";
		$sql .= " (file_id, diary_id)";
		$sql .= " VALUES (";
		$sql .=		   "" . $id . ",";
		$sql .=		   "" . $diary_id;
		$sql .=  ")";

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * diary�ե�������󹹿�
	 *
	 * @param $file_obj 
	 */
	function update_diary_file ($file_obj,$diary_id) {
		$sql  = "UPDATE diary_file";
		$sql .= " SET file_id = ";
		$sql .= $file_obj->get_file_id() . ",";
		$sql .= " diary_id = ";
		$sql .=		   "" . $diary_id;

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}

	/**
	 * diary�ե����������
	 * 
	* @param $file_id
	* @param $diary_id
	*/
	function delete_diary_file ($file_id,$diary_id) {
		$sql  = "DELETE FROM diary_file";
		$sql .= " WHERE";
		$sql .=		 " file_id = " . $file_id;
		$sql .= " AND";
		$sql .=		" diary_id = " . $diary_id;

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
	
	
	/**
	 * image_url��ù�����
	 *
	 * @param diary_file_id
	 * @param view_mode         ɽ���⡼�� : NULL, thumb, rss
	 */
	function get_image_url($diary_file_id, $view_mode = '') {
		$image_url  = SCRIPT_PATH . '?';
		$image_url .= MODULE_ACCESSOR . '=User';
		$image_url .= '&' . ACTION_ACCESSOR . '=DiaryImage';
		$image_url .= '&id=' . $diary_file_id;
		$image_url .= '&mode=' . $view_mode;

		return $image_url;
	}

}
