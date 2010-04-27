<?php
/**
 * ACS Done
 *
 * Common �⥸�塼��� Done �����������Ϥ����֥�������
 * ɽ�������������Ƥ��ݻ�����
 *
 * �������ˡ��
 * -------------------------------------------------------------------
 * require_once(ACS_CLASS_DIR . 'ACSDone.class.php');
 *
 * $done_obj = new ACSDone();
 *
 * $done_obj->set_title('�����ȥ�');
 * $done_obj->set_message('��å�����');
 *
 * // ��󥯤�ɬ�פ�ʬ������add_link ����
 * $done_obj->add_link('�����̾��', 'link1_url');
 * $done_obj->add_link('�����̾��', 'link2_url');
 *
 * $request->setAttribute('done_obj', $done_obj);
 * -------------------------------------------------------------------
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/01/06 07:55:08 $
 */
class ACSDone
{
	/* �����ȥ� */
	var $title;

	/* ��å����� */
	var $message;

	/* ��� */
	var $donelink_obj_array = array();


	/**
	 * �����ȥ륻�å�
	 *
	 * @param $title
	 */
	function set_title ($title) {
		$this->title = $title;
	}

	/**
	 * �����ȥ륲�å�
	 *
	 * @return �����ȥ�
	 */
	function get_title () {
		return $this->title;
	}

	/**
	 * ��å��������å�
	 *
	 * @param $message
	 */
	function set_message ($message) {
		$this->message = $message;
	}

	/**
	 * ��å��������å�
	 *
	 * @return ��å�����
	 */
	function get_message () {
		return $this->message;
	}

	/**
	 * ��󥯥��å�
	 *
	 * @param $link_name
	 * @param $link_url
	 */
	function add_link ($link_name, $link_url) {
		$donelink_obj = new ACSDoneLink();

		$donelink_obj->set_link_name($link_name);
		$donelink_obj->set_url($link_url);

		array_push($this->donelink_obj_array, $donelink_obj);
	}

	/**
	 * ��󥯥��å�
	 *
	 * @return ��󥯤�����
	 */
	function get_link_row_array () {
		$link_row_array = array();
		foreach ($this->donelink_obj_array as $donelink_obj) {
			$link_row = array();

			$link_row['link_name'] = $donelink_obj->get_link_name();
			$link_row['url']  = $donelink_obj->get_url();

			array_push($link_row_array, $link_row);
		}
		return $link_row_array;
	}
}

/**
 * ACS DoneLink
 *
 * Done���饹���ݻ������󥯤Υ��֥�������
 * �����̾��URL���ݻ�����
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/01/06 07:55:08 $
 */
class ACSDoneLink
{
	/* �����̾ */
	var $link_name;

	/* ����� URL */
	var $url;

	/**
	 * �����̾���å�
	 *
	 * @param �����
	 */
	function set_link_name ($link_name) {
		$this->link_name = $link_name;
	}

	/**
	 * �����̾���å�
	 */
	function get_link_name () {
		return $this->link_name;
	}

	/**
	 * ����� URL ���å�
	 *
	 * @param �����
	 */
	function set_url ($url) {
		$this->url = $url;
	}

	/**
	 * ����� URL ���å�
	 */
	function get_url () {
		return $this->url;
	}
}
?>
