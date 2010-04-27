<?php
/**
 * Valuee���֥������Ȥδ��쥯�饹
 * (Value��ͽ���Τ��ᡢValuee��̿̾���Ƥ���)
 * @access public
 * @package logic/value
 * @category value
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
class Value
{
	/**
	 * �Ź沽�ե饰
	 * �Ŀ;��󡢵�̩����ʤɥǡ�����Ź沽�����硢
	 * �Ź沽�ե饰��ON�ˤ���
	 * ���Ź沽�ե饰��ON�ˤ�����ˡ�ϡ�encryptFlagON()��¹Ԥ��뤫��
	 * �����󥹥ȥ饯���ΰ�����true�򥻥åȤ��롣
	 * ���Ź沽�ե饰��OFF�ˤ�����ˡ�ϡ�encryptFlagOFF()��¹Ԥ��롣
	 * @var boolean true:�Ź沽���롢false:�Ź沽���ʤ�
	 */
	private $encryptFlag;
	
	/* �Ź沽���饹 */
	private $encryption;
	
    /**
     * ���󥹥ȥ饯��
     * @access public
     * @param boolean $encryptFlag �Ź沽�ե饰�ʥǥե���ȡ�true��
     */
	public function __construct($encryptFlag = true)
	{
		$this->encryptFlag = $encryptFlag;
		$this->encryption = CommonEncryption::getInstance();
	}
	
	/**
	 * �Ź沽�ե饰��ON�ˤ���
	 * @access public
	 */
	public function encryptFlagON()
	{
		$this->encryptFlag = true;
	}

	/**
	 * �Ź沽�ե饰��OFF�ˤ���
	 * @access public
	 */
	public function encryptFlagOFF()
	{
		$this->encryptFlag = false;
	}
	
	/**
	 * �ǡ�����Ź沽����
	 * ���Ź沽�ե饰��ON�λ��������Υǡ�����Ź沽���롣
	 * ���Ź沽�ե饰��OFF�λ��������Υǡ����򤽤Τޤ��᤹��
	 * @access  public
	 * @param mixed $data �ǡ���
	 * @return string �Ź沽�ǡ���
	 */	
	public function encrypt($data)
	{
		// �Ź沽���å����������
		$retVal = $data;
			
		return $retVal;
	}
	
	/**
	 * �ǡ��������沽����
	 * ���Ź沽�ե饰��ON�λ��������Υǡ��������沽���롣
	 * ���Ź沽�ե饰��OFF�λ��������Υǡ����򤽤Τޤ��᤹��
	 * @access  public
	 * @param mixed $data �ǡ���
	 * @return string ���沽�ǡ���
	 */	
	public function decrypt($data)
	{
		// ʣ�粽���å����������
		$retVal = $data;
			
		return $retVal;
	}

	/**
	 * ����ǡ�����Ź沽����
	 * ���Ź沽�ե饰��ON�λ�������������ǡ�����Ź沽���롣
	 * ���Ź沽�ե饰��OFF�λ�������������ǡ����򤽤Τޤ��᤹��
	 * ������ǡ����ΰ�����Ź沽������ϡ��Ź沽��������ǡ����ν�������ǥå���������Ϥ����ȡ�
	 * @access  public
	 * @param array $data ����ǡ���
	 * @param array $indexs ��������ǥå���
	 * @return array ���沽����ǡ���
	 */	
	public function encryptArray($data, $indexs = null)
	{
		$retVal = array();
		if ($this->encryptFlag) {
			if (isset($indexs)) {
				foreach ($data as $key => $value) {
					if (in_array($key, $indexs)) {
						$retVal[$key] = $this->encrypt($value);
					} else {
						$retVal[$key] = $value;
					}
				}
			} else {
				$retVal = array_map(array("Value", "encrypt"), $data);
			}
		} else {
			$retVal = $data;
		}
		
		return $retVal;
	}
	
	/**
	 * ����ǡ��������沽����
	 * ���Ź沽�ե饰��ON�λ�������������ǡ��������沽���롣
	 * ���Ź沽�ե饰��OFF�λ�������������ǡ����򤽤Τޤ��᤹��
	 * ������ǡ����ΰ��������沽������ϡ����沽��������ǡ����ν�������ǥå���������Ϥ����ȡ�
	 * @access  public
	 * @param array $data ����ǡ���
	 * @param array $indexs ��������ǥå���
	 * @return array ���沽����ǡ���
	 */	
	public function decryptArray($data, $indexs = null)
	{
		$retVal = array();
		if ($this->encryptFlag) {
			if (isset($indexs)) {
				foreach ($data as $key => $value) {
					if (in_array($key, $indexs)) {
						$retVal[$key] = $this->decrypt($value);
					} else {
						$retVal[$key] = $value;
					}
				}
			} else {
				$retVal = array_map(array("Value", "decrypt"), $data);
			}
		} else {
			$retVal = $data;
		}
		
		return $retVal;
	}
}
?>