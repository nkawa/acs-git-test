<?php
/**
 * Service���֥������Ȥδ��쥯�饹
 * @access public
 * @package logic/service
 * @category Service
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
class Service
{
	/**
	 * �ǡ����١������ͥ������
	 * @var DB_common
	 */
	protected $db = null;
	
	/**
	 * ���󥹥ȥ饯��
	 * @access public
	 */
	function __construct()
	{
		$this->db = ConnectionFactory::getConnection();
	}
	
    /**
     * �ǥ��ȥ饯��
     * @access public
     */
	function __destruct()
	{
		$this->db->disconnect();
	}

	/**
	 * �ȥ�󥶥�����󳫻�
	 * �����ȥ��ߥåȤ��������ȥ�󥶥������򳫻Ϥ��롣
	 * @access public
	 */
	function begin()
	{
		$this->db->autoCommit(false);
		$this->db->query("BEGIN");
	}
	
	/**
	 * �ȥ�󥶥������λ�ʥ��ߥåȡ�
	 * �ȥ�󥶥�������λ���������ȥ��ߥåȤ����ꤹ�롣
	 * @access public
	 */
	function end()
	{
		$this->db->query("COMMIT");
		$this->db->autoCommit(true);
	}
	
	/**
	 * �ȥ�󥶥�����󥳥ߥå�
	 * �ȥ�󥶥������򥳥ߥåȤ��������ȥ��ߥåȤ����ꤹ�롣
	 * @access public
	 */
	function commit()
	{
		$this->db->query("COMMIT");
		$this->db->autoCommit(true);
	}

	/**
	 * �ȥ�󥶥���������Хå�
	 * �ȥ�󥶥����������Хå����������ȥ��ߥåȤ����ꤹ�롣
	 * @access public
	 */
	function rollback()
	{
		$this->db->query("ROLLBACK");
		$this->db->autoCommit(true);
	}
}
?>