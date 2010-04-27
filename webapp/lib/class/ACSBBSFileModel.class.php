<?php
/**
 * bbs�ѥե�������� DB�ơ��֥�
 * ACSBBSFileModel.class.php
 *
 * bbs_file�ơ��֥��ǥ�
 * @author  akitsu
 * @version $Revision: 1.1 $
 */
 
class ACSBBSFileModel
{
	/* �ե�����ID
	* @type��BIG_INT */
	var $file_id;

	/* BBS��ID
	* @type��BIG_INT */
	var $bbs_id;

	/**
	 * ���󥹥ȥ饯��
	 * ɬ�����������Ϥ����
	 * @param $bbs_file_row
	 */
	function ACSBBSFileModel ($bbs_file_row) {
		$this->set_bbs_file_info($bbs_file_row);
	}
 
 	/**
	 * �ե�������󥻥å�
	 * ɬ�����������Ϥ����
	 * @param $bbs_file_row
	 */
	function set_bbs_file_info ($bbs_file_row) {
		$this->set_file_id($bbs_file_row['file_id']);
		$this->set_bbs_id($bbs_file_row['bbs_id']);
	}

	/**
	 * ���󥹥��󥹼����ʥե�����ID,BBSid�����
	 *
	 * @param $file_id
	 */
	static function get_bbs_file_info_instance ($file_id,$bbs_id) {
		$bbs_file_row['file_id'] = $file_id;
		$bbs_file_row['bbs_id'] = $bbs_id;
		$file_obj = new ACSBBSFileModel($bbs_file_row);

		return $file_obj;
	}


/* �������å� */
 	/**
	 * �ե�����ID���å�
	 *
	 * @param $file_id
	 */
	function set_file_id ($file_id) {
		$this->file_id = $file_id;
	}
	/**
	 * �ե�����ID���å�
	 *
	 * @param none
	 */
	function get_file_id () {
		return $this->file_id;
	}
 
	/**
	 * BBS ID���å�
	 *
	 * @param $bbs_id
	 */
	function set_bbs_id ($bbs_id) {
		$this->file_id = $bbs_id;
	}
	/**
	 * BBS ID���å�
	 *
	 * @param none
	 */
	function get_bbs_id () {
		return $this->bbs_id;
	}
	
	
	
}
