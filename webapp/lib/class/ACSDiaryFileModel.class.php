<?php
/**
 * diary�ѥե�������� DB�ơ��֥�
 * ACSDiaryFileModel.class.php
 *
 * diary_file�ơ��֥��ǥ�
 * @author  akitsu
 * @version $Revision: 1.1 $
 */
 
class ACSDiaryFileModel
{
	/* �ե�����ID
	* @type��BIG_INT */
	var $file_id;

	/* diary��ID
	* @type��BIG_INT */
	var $diary_id;

	/**
	 * ���󥹥ȥ饯��
	 * ɬ�����������Ϥ����
	 * @param $diary_file_row
	 */
	function ACSdiaryFileModel ($diary_file_row) {
		$this->set_diary_file_info($diary_file_row);
	}
 
 	/**
	 * �ե�������󥻥å�
	 * ɬ�����������Ϥ����
	 * @param $diary_file_row
	 */
	function set_diary_file_info ($diary_file_row) {
		$this->set_file_id($diary_file_row['file_id']);
		$this->set_diary_id($diary_file_row['diary_id']);
	}

	/**
	 * ���󥹥��󥹼����ʥե�����ID,diaryid�����
	 *
	 * @param $file_id
	 */
	static function get_diary_file_info_instance ($file_id,$diary_id) {
		$diary_file_row['file_id'] = $file_id;
		$diary_file_row['diary_id'] = $diary_id;
		$file_obj = new ACSdiaryFileModel($diary_file_row);

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
	 * diary ID���å�
	 *
	 * @param $diary_id
	 */
	function set_diary_id ($diary_id) {
		$this->file_id = $diary_id;
	}
	/**
	 * diary ID���å�
	 *
	 * @param none
	 */
	function get_diary_id () {
		return $this->diary_id;
	}
	
	
	
}
