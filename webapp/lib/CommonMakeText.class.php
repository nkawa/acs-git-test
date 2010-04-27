<?php
ini_set("display_errors", 0);
ini_set("error_reporting", false);
require_once(MO_PEAR_DIR . "/Mail/Queue.php");
require_once(MO_SMARTY_DIR.'/libs/Smarty.class.php');
ini_restore("error_reporting");
ini_restore("display_errors");

/**
 * ���̥ƥ����Ⱥ������饹
 * �ƥ�ץ졼��(Smarty)����ƥ����Ȥ�������롣
 * 
 * @access public
 * @package webapp/lib
 * @category utility
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
class CommonMakeText
{
	/* Smarty */
	protected  $smarty;
	
	/* ����ѥ���ǥ��쥯�ȥ� */
	protected $compileDir;
	
	/* �ƥ�ץ졼�ȥǥ��쥯�ȥ� */
	protected $directory;

	/* �ƥ�ץ졼�ȥե�����̾ */
	protected $template;
	
	/**
	 * ���󥹥ȥ饯��
	 * @access public
	 */
	function __construct()
	{
		/* Smarty���� */
		$this->smarty = new Smarty();
		$this->smarty->config_dir = MO_CONFIG_DIR;
		$this->smarty->cache_dir = MO_CACHE_DIR;
	}

	/**
	 * ����ѥ���ǥ��쥯�ȥ���������
	 * @access public
	 * @return string ����ѥ���ǥ��쥯�ȥ�
	 */
	public function getCompileDir()
	{
		return $this->compileDir;
	}

	/**
	 * ����ѥ���ǥ��쥯�ȥ�򥻥åȤ���
	 * @access public
	 * @param string $compileDir ����ѥ���ǥ��쥯�ȥ�
	 */
	public function setCompileDir($compileDir)
	{
		$this->compileDir = $compileDir;
	}
	
	/**
	 * �ƥ�ץ졼�ȥǥ��쥯�ȥ���������
	 * @access public
	 * @return string �ƥ�ץ졼�ȥǥ��쥯�ȥ�
	 */
	public function getDirectory()
	{
		return $this->directory;
	}

	/**
	 * �ƥ�ץ졼�ȥǥ��쥯�ȥ�򥻥åȤ���
	 * @access public
	 * @param string $directory �ƥ�ץ졼�ȥǥ��쥯�ȥ�
	 */
	public function setDirectory($directory)
	{
		$this->directory = $directory;
	}
	
	/**
	 * �ƥ�ץ졼�ȥե�����̾���������
	 * @access public
	 * @return string �ƥ�ץ졼�ȥե�����̾
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * �ƥ�ץ졼�ȥե�����̾�򥻥åȤ���
	 * @access public
	 * @param string $directory �ƥ�ץ졼�ȥǥ��쥯�ȥ�
	 */
	public function setTemplate($template)
	{
		if (Toolkit::isPathAbsolute($template)) {
			$this->directory = dirname($template);
			$this->template = basename($template);
		} else {
			$this->template = $template;
		}
	}

	/**
	 * ���ƤΥƥ�ץ졼���ѿ��򥯥ꥢ����
	 * @access public
	 */
	public function clearAttributes()
	{
		$this->smarty->clear_all_assign();
	}

	/**
	 * �ƥ�ץ졼���ѿ�̾�����Ƽ�������
	 * @access public
	 * @return array �ƥ�ץ졼���ѿ�̾
	 */
	public function getAttributeNames()
	{
		return array_keys($this->smarty->get_template_vars());
	}
			
	/**
	 * �ƥ�ץ졼���ѿ��ǡ������������
	 * @access public
	 * @return var �ƥ�ץ졼���ѿ��ǡ���
	 */
	public function & getAttribute($name)
	{
		return $this->smarty->get_template_vars($name);
	}

	/**
	 * �ƥ�ץ졼���ѿ�������
	 * @access public
	 * @return array �ƥ�ץ졼���ѿ�
	 */
	public function & removeAttribute($name)
	{
		$retval = $this->smarty->get_template_vars($name);

		$this->smarty->clear_assign($name);
		
		return $retval;
	}
	
	/**
	 * �ƥ�ץ졼���ѿ��򥻥åȤ���
	 * @access public
	 * @param string $name �ƥ�ץ졼���ѿ�̾
	 * @param var $value �ƥ�ץ졼���ѿ��ǡ���
	 */
	public function setAttribute($name, $value)
	{
		$this->smarty->assign($name, $value);
	}

	/**
	 * �ƥ�ץ졼���ѿ��򥻥åȤ���
	 * @access public
	 * @param string $name �ƥ�ץ졼���ѿ�̾
	 * @param var $value �ƥ�ץ졼���ѿ��ǡ���(���ȥ��ɥ쥹)
	 */
	public function setAttributeByRef($name, &$value)
	{
		$this->smarty->assign_by_ref($name, $value);
	}

	/**
	 * �ƥ�ץ졼���ѿ���ޤȤ�ƥ��åȤ���
	 * @access public
	 * @param array $attributes �ƥ�ץ졼���ѿ�(����)
	 */
	public function setAttributes($attributes)
	{
		$this->smarty->assign($attributes);
	}

	/**
	 * �ƥ�ץ졼���ѿ���ޤȤ�ƥ��åȤ���
	 * @access public
	 * @param array $attributes �ƥ�ץ졼���ѿ�(����)(���ȥ��ɥ쥹)
	 */
	public function setAttributesByRef(&$attributes)
	{
		$this->smarty->assign_by_ref($attributes);
	}

	/**
	 * �ե�����ƥ�ץ졼�Ȥ˥ƥ�ץ졼���ѿ��򥻥åȤ����ƥ����Ȥ���������
	 * @access public
	 * @return �᡼��(�ƥ�����)
	 */
	public function & render()
	{
		// ����ѥ���ǥ��쥯�ȥ꤬̤���åȤξ�硢����ѥ���ǥ��쥯�ȥ�򥻥åȤ���
		if (empty($this->compileDir)) {
			$this->compileDir = MO_WEBAPP_DIR . '/compiled';
		}
		// ����ѥ���ǥ��쥯�ȥ꤬�ʤ���硢����ѥ���ǥ��쥯�ȥ���������
		if (!file_exists($this->compileDir)) {
			mkdir($this->compileDir, 0755, true);
		}
		
		$this->smarty->compile_dir = $this->compileDir;
		$this->smarty->template_dir = $this->directory;
		$retval = $this->smarty->fetch($this->template);

		return $retval;
	}
}
?>