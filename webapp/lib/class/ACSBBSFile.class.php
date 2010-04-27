<?php
/**
 * bbs�ѥե�������� DB��������
 * bbs_file��file_info�η��ե��������
 * ACSBBSFile.class.php
 *
 * bbs_file�ơ��֥��Manage����
 * @author  akitsu
 * @version $Revision: 1.5 $
 */
class ACSBBSFile
{
	/** ACSBBSFile�Υ��饹
	* @type ACSBBSFileModel */
	var $bbs_file_obj;

	/** ACSFile�Υ��饹
	* @type ACSFileModel */
	var $file_info;

	/**
	 * BBS�ե������������ʣ����
	 * �ҤȤĤ�BBS�ʷǼ��ġˤ�����File����
	 *
	 * @param $bbs_id
	 * @return bbs_file��file_info�η��ե��������
	 */
	static function select_bbs_file_row ($bbs_id) {
		$sql  = "SELECT bbs_file.*,";
		$sql .=	   " file_info.display_file_name AS save_file_name,file_info.server_file_name AS insystem_file_name,";
		$sql .=	   " file_info.mime_type AS file_kind";	//����
		$sql .= " FROM file_info, bbs_file";
		$sql .= " WHERE bbs_id = " . $bbs_id;
		$sql .=   " AND file_info.file_id = bbs_file.file_id";
		$sql .= " ORDER BY bbs_file.file_id ASC";

		$result = ACSDB::_get_row($sql);
		return $result;
	}

	/**
	 * BBS�ե�����������
	 *
	 * @param $file_obj 
	 */
	static function get_bbs_file ($file_obj,$bbs_id) {
		$sql  = "SELECT bbs_file.*,";
		$sql .= " FROM bbs_file";

		$ret = ACSDB::_do_query($sql);
		if($ret){
			$bbs_file_obj = ACSBBSFileModel::get_bbs_file_info_instance($file_obj,$bbs_id);
			return $bbs_file_obj;
		}else{
			return $ret;
		}
	}


	/**
	 * BBS�ե���������ɲ�
	 *
	 * @param $file_obj 
	 */
	static function insert_bbs_file ($file_obj,$bbs_id) {
		$id = $file_obj->get_file_id();
		$sql  = "INSERT INTO bbs_file";
		$sql .= " (file_id, bbs_id)";
		$sql .= " VALUES (";
		$sql .=		   "" . $id . ",";
		$sql .=		   "" . $bbs_id;
		$sql .=  ")";

		$ret = ACSDB::_do_query($sql);

		return $ret;
	}

	/**
	 * BBS�ե�������󹹿� (��BEGIN, COMMIT)
	 *
	 * @param $file_obj 
	 */
	static function update_bbs_file ($file_obj, $bbs_id) {
		$file_id = pg_escape_string($file_obj->get_file_id());
		$bbs_id = pg_escape_string($bbs_id);

		// ���
		$sql  = "DELETE";
		$sql .= " FROM bbs_file";
		$sql .= " WHERE bbs_id = '$bbs_id'";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// INSERT
		$sql  = "INSERT INTO bbs_file";
		$sql .= " (file_id, bbs_id)";
		$sql .= " VALUES ('$file_id', '$bbs_id')";

		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		return $ret;
	}

	/**
	 * BBS�ե����������
	 * 
	* @param $file_id
	* @param $bbs_id
	*/
	static function delete_bbs_file ($file_id,$bbs_id) {
		$sql  = "DELETE FROM bbs_file";
		$sql .= " WHERE";
		$sql .=		 " file_id = " . $file_id;
		$sql .= " AND";
		$sql .=		" bbs_id = " . $bbs_id;

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
	
	
	/**
	 * image_url��ù�����
	 *
	 * @param bbs_id
	 * @param view_mode         ɽ���⡼�� : NULL, thumb, rss
	 */
	static function get_image_url($bbs_id, $view_mode = '') {
		$image_url  = SCRIPT_PATH . '?';
		$image_url .= MODULE_ACCESSOR . '=Community';
		$image_url .= '&' . ACTION_ACCESSOR . '=BBSImage';
		$image_url .= '&id=' . $bbs_id;
		$image_url .= '&mode=' . $view_mode;

		return $image_url;
	}

}
