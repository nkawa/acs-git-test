<?php
/**
 * ���̥�å��������饹
 * ���ץꥱ�������ǻ��Ѥ��붦�̥�å��������������
 * @access public
 * @package webapp/lib
 * @category utility
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
class CommonMessages extends ParameterHolder
{
	/* ���󥰥�ȥ󥪥֥������� */
	private static $singleton = null;
	
	/**
	 * ���󥹥ȥ饯��
	 * @access public
	 */
	private function __construct()
	{
		$this->parameters = array();
	}
	
	/**
	 * ���󥰥�ȥ󥪥֥������Ȥ��������
	 * @access public
	 * @return CommonMessages ���󥹥���
	 */
	public static function getInstance() {
		if (CommonMessages::$singleton == null) {
			CommonMessages::$singleton = new CommonMessages();
		}
		return CommonMessages::$singleton;
	}
	
	/**
	 * �������
	 * Ϣ������ǡ���(key�������ɡ�value�����̥�å�����)�������ꡢ���̥�å������򹹿�����
	 * @access public
	 * @param array $parameters ���̥�å�����(Ϣ������ǡ���)
	 * @return boolean �������
	 */
	public function initialize ($parameters = null)
	{
		if ($parameters != null) {
			$this->parameters = array_merge($this->parameters, $parameters);
		}
		
		return true;
	}
}
