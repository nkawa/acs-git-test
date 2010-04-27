<?php
/**
 * ACS SystemConfig
 * �����ƥ�����
 *
 * ������ˡ��
 *  <����Υ�����ɤ��ͤ����>
 *    $system_config_keyword_value = ACSSystemConfig::get_keyword_value('���롼��̾', '�������');
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2008/03/24 07:00:36 $
 */
require_once(ACS_CLASS_DIR . 'ACSSystemConfigModel.class.php');
class ACSSystemConfig
{
	/* �����ƥ����ꥰ�롼������ */
	var $system_config_group_array = array();

	/* ACSSystemConfigKeywordData ���� */
	var $system_config_keyword_data_obj_array = array();

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param $file_info_row
	 */
	function ACSSystemConfig ($system_config_group = '') {
		if (!$system_config_group) {
			// �ơ��֥뤫�����ǡ�������
			$system_config_row_array = ACSSystemConfigModel::select_system_config_row_array();

		} else {
			// ����Υ����ƥ����ꥰ�롼�ץǡ�������
			$system_config_row_array = ACSSystemConfigModel::select_system_config_group_row($system_config_group);
		}

		// ACSSystemConfigKeywordData ���� ���å�
		foreach ($system_config_row_array as $system_config_row) {
			// ACSSystemConfigKeywordData ���󥹥������������������
			$_system_config_keyword_data_obj = new ACSSystemConfigKeywordData($system_config_row);
			$this->add_system_config_keyword_data_obj($_system_config_keyword_data_obj);

			// �����ƥ����ꥰ�롼�׼���
			$_system_config_group = $_system_config_keyword_data_obj->get_system_config_group_name();
			if (!in_array($_system_config_group, $this->system_config_group_array)) {
				$this->add_system_config_group($_system_config_group);
			}
		}
	}

	/**
	 * ACSSystemConfigKeywordData ���� �ɲ�
	 *
	 * @param $system_config_keyword_data_obj
	 */
	function add_system_config_keyword_data_obj ($system_config_keyword_data_obj) {
		array_push($this->system_config_keyword_data_obj_array, $system_config_keyword_data_obj);
	}

	/**
	 * �����ƥ����ꥰ�롼������ �ɲ�
	 *
	 * @param $system_config_group
	 */
	function add_system_config_group ($system_config_group) {
		array_push($this->system_config_group_array, $system_config_group);
	}

	/**
	 * �����ƥ����ꥰ�롼�����󥲥å�
	 *
	 * @return �����ƥ����ꥰ�롼������
	 */
	function get_system_config_group_array () {
		return $this->system_config_group_array;
	}

	/**
	 * ���ꥷ���ƥ����ꥰ�롼�פ� ACSSystemConfigKeywordData ���󥲥å�
	 *
	 * @param $system_config_group_name
	 */
	function get_system_config_keyword_data_obj ($system_config_group_name) {
		$ret_config_keyword_data_obj_array = array();

		foreach ($this->system_config_keyword_data_obj_array as $system_config_keyword_data_obj) {
			if ($system_config_keyword_data_obj->get_system_config_group_name() == $system_config_group_name) {
				array_push($ret_config_keyword_data_obj_array, $system_config_keyword_data_obj);
			}
		}

		return $ret_config_keyword_data_obj_array;
	}

	/**
	 * ACSSystemConfigKeywordData ���󥲥å�
	 */
	function get_system_config_keyword_data_obj_array () {
		return $this->system_config_keyword_data_obj_array;
	}

	/**
	 * ���ꥷ���ƥ����ꥰ�롼�ס�������ɤ��ͥ��å�
	 *
	 * @param  $system_config_group_name
	 * @param  $keyword
	 */
	function get_keyword_value ($system_config_group_name, $keyword) {
		$system_config_data_row = ACSSystemConfigModel::select_system_config_keyword_row($system_config_group_name, $keyword);
		if (!isset($system_config_data_row)) {
			// �ǡ��������Ǥ��ʤ���硢���顼
			print "ERROR: Get system value failed. ($system_config_group_name, $keyword)";
			exit;
		}

		$keyword_data_instance = new ACSSystemConfigKeywordData($system_config_data_row);
		$value =  $keyword_data_instance->get_value();
		return $value;
	}

	/**
	 * �͹���
	 *
	 * @param  $system_config_group �����оݤΥ���
	 * @param  $keyword             �����оݤΥ���
	 * @param  $update_value        ����������
	 */
	function update_value ($system_config_group, $keyword, $update_value) {
		return ACSSystemConfigModel::update_system_config_value($system_config_group, $keyword, $update_value);
	}
}

class ACSSystemConfigKeywordData
{
	/* �����ƥ����ꥰ�롼�ץ����� */
	var $system_config_group_code;

	/* �����ƥ����ꥰ�롼�� */
	var $system_config_group_name;

	/* ������� */
	var $keyword;

	/* ����̾ */
	var $name;

	/* �� */
	var $value;

	/* �� */
	var $type;

	/* ñ�� */
	var $unit;

	/* ���� */
	var $note;
	
	/* ����� */
	var $select;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param $system_config_group
	 * @param $keyword
	 */
	function ACSSystemConfigKeywordData ($system_config_data_row = array()) {
		$this->set_system_config_group_code($system_config_data_row['system_config_group_code']);
		$this->set_system_config_group_name($system_config_data_row['system_config_group_name']);
		$this->set_keyword($system_config_data_row['keyword']);
		//$this->set_name($system_config_data_row['name']);
		$this->set_value($system_config_data_row['value']);
		$this->set_type($system_config_data_row['type']);
		//$this->set_unit($system_config_data_row['unit']);
		//$this->set_note($system_config_data_row['note']);

		// ��å������ե����������
		$this->set_name(ACSMsg::get_mdmsg(__FILE__,$system_config_data_row['keyword']));
		$this->set_unit(ACSMsg::get_mdmsg(__FILE__,$system_config_data_row['keyword'].".unit"));
		$this->set_note(ACSMsg::get_mdmsg(__FILE__,$system_config_data_row['keyword'].".note"));

		$this->set_select(ACSMsg::get_mdmsg(__FILE__,$system_config_data_row['keyword'].".select"));
	}

	/**
	 * �����ƥ����ꥰ�롼�ץ����ɥ��å�
	 *
	 * @parma $system_config_group_code
	 */
	function set_system_config_group_code ($system_config_group_code) {
		$this->system_config_group_code = $system_config_group_code;
	}

	/**
	 * �����ƥ����ꥰ�롼�ץ����ɥ��å�
	 *
	 * @return �����ƥ����ꥰ�롼��
	 */
	function get_system_config_group_code () {
		return $this->system_config_group_code;
	}

	/**
	 * �����ƥ����ꥰ�롼��̾���å�
	 *
	 * @parma $system_config_group_name
	 */
	function set_system_config_group_name ($system_config_group_name) {
		$this->system_config_group_name = $system_config_group_name;
	}

	/**
	 * �����ƥ����ꥰ�롼��̾���å�
	 *
	 * @return �����ƥ����ꥰ�롼��
	 */
	function get_system_config_group_name () {
		return $this->system_config_group_name;
	}

	/**
	 * ������ɥ��å�
	 *
	 * @parma $keyword
	 */
	function set_keyword ($keyword) {
		$this->keyword = $keyword;
	}

	/**
	 * ������ɥ��å�
	 *
	 * @return �������
	 */
	function get_keyword () {
		return $this->keyword;
	}

	/**
	 * ����̾���å�
	 *
	 * @parma $name
	 */
	function set_name ($name) {
		$this->name = $name;
	}

	/**
	 * ����̾���å�
	 *
	 * @return ����̾
	 */
	function get_name () {
		return $this->name;
	}

	/**
	 * �ͥ��å�
	 *
	 * @parma $value
	 */
	function set_value ($value) {
		$this->value = $value;
	}

	/**
	 * �ͥ��å�
	 *
	 * @return ��
	 */
	function get_value () {
		return $this->value;
	}

	/**
	 * �����å�
	 *
	 * @parma $type
	 */
	function set_type ($type) {
		$this->type = $type;
	}

	/**
	 * �����å�
	 *
	 * @return ��
	 */
	function get_type () {
		return $this->type;
	}

	/**
	 * ñ�̥��å�
	 *
	 * @parma $unit
	 */
	function set_unit ($unit) {
		$this->unit = $unit;
	}

	/**
	 * ñ�̥��å�
	 *
	 * @return ñ��
	 */
	function get_unit () {
		return $this->unit;
	}

	/**
	 * ���ͥ��å�
	 *
	 * @parma $note
	 */
	function set_note ($note) {
		$this->note = $note;
	}

	/**
	 * ���ͥ��å�
	 *
	 * @return ����
	 */
	function get_note () {
		return $this->note;
	}
	
	/**
	 * ����襻�å�
	 *
	 * @parma $note
	 */
	function set_select ($select) {
		$this->select = $select;
	}

	/**
	 * ����襲�å�
	 *
	 * @return �����
	 */
	function get_select () {
		return $this->select;
	}

}
?>
