<?php
require_once(MO_PEAR_DIR . '/Config.php');
require_once(MO_PEAR_DIR . '/HTML/menu.php');
require_once(MO_PEAR_DIR . '/HTML/Menu/DirectRenderer.php');
ini_set("display_errors", 0);
ini_set("error_reporting", false);
require_once(MO_PEAR_DIR . '/HTML/QuickForm/Renderer/ArraySmarty.php');
require_once(MO_PEAR_DIR . '/Pager/Pager.php');
ini_restore("error_reporting");
ini_restore("display_errors");

/**
 * View�γ�ĥ���饹
 * @access public
 * @package webapp/lib
 * @category view
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
abstract class SimpleViewEx extends SmartyView
{
	/**
	 * Controller���֥������� 
	 * @var Controller
	 */
	protected $controller = '';
	
	/**
	 * Request���֥�������
	 * @var Request
	 */
	protected $request = '';
	
	/* user���֥������� */
	private $user = '';
	
	/* HTML_QuickForm��Smarty������ */
	private $quickformSmarty = '';
	
	/* ��å������ǡ��� */
	private $messages = '';

	/* �ڡ���̾ */
	private $pageName = 'page1';
	
	/* ����������Υ⥸�塼��̾ */
	private $moduleName = '';

	/* ����������Υ��������̾ */
	private $actionName = '';

	var $css_file_array = array();
	var $js_file_array = array();
	
	/**
	 * �������
	 * @access public
	 * @param Object $contextt context
	 * @return boolean �������
	 */
	public function initialize ($context)
	{
		parent::initialize($context);

		/* ����ͤ򥻥åȤ��� */
		$this->controller = $context->getController();
		$this->request = $context->getRequest();
		$this->user = $context->getUser();

		$this->moduleName = $context->getModuleName();
		$this->actionName = $context->getActionName();
		$this->messages = &CommonMessages::getInstance();
		
		/* �ե�������Ͽ�ꥹ�ȤΥե������smarty�ˤ��٤ƥ��åȤ��� */
		$o_smarty = &$this->getEngine();
		$o_smarty->register_object("style", $this, array("request", "checkErrorElement"));
		$this->quickformSmarty = 
			new HTML_QuickForm_Renderer_ArraySmarty($this->getEngine());
		$formList = $this->request->getAttribute('formList');
		foreach ($formList as $formName) {
			$this->setForm($formName);
		}

		/* ��å�������smarty�˥��åȤ��� */
		if ($this->request->hasAttribute('messages')) {
			$messages = &$this->request->getAttribute('messages');
			$messages = array_unique($messages);
			$this->setAttributeByRef('messages', $messages);
		}		
		/* ���顼��å�������smarty�˥��åȤ��� */
		if ($this->request->hasErrors()) {
			$errors = &$this->request->getErrors();
			$errors = array_unique($errors);
			$this->setAttributeByRef('errors', $errors);
		}

		
		// ���̤� CSS �򥻥å�
		array_push($this->css_file_array, ACS_SELECTION_CSS_DIR . ACS_DEFAULT_SELECTION_CSS_FILE);
		$this->setAttribute('include_css_array', $this->css_file_array);

		// ���̤� JS �򥻥å�
		array_push($this->js_file_array, ACS_JS_DIR . ACS_COMMON_JS);
		$this->setAttribute('include_script_array', $this->js_file_array);
		
		return true;
	}

	/**
	 * ����ID�򥻥åȤ���
	 * ����ID��ɳ�դ����̥����ȥ�ȥ⥸�塼��̾�ȥ��������̾��smarty�˥��åȤ���
	 * @access public
	 * @param string $id ����ID
	 */
	public function setScreenId($id)
	{
		/* Decorator�ƥ�ץ졼�Ȥ򥻥åȤ��� */
		$this->setDecoratorDirectory(MO_TEMPLATE_DIR);
		$this->setDecoratorTemplate(MO_SIMPLE_TEMPLATE_FILE);

		// �����꡼�󥿥��ȥ���������
		$title = '';
		$screenName = '';
		$screenNameList = MO_CONFIG_DIR . '/' . S4_SCREEN_NAME_LIST;
		$moduleName = '';
		$actionName = '';
		if (file_exists($screenNameList)) {
			$config = new Config();
			$container = $config->parseConfig($screenNameList, 'inifile');
			$child=$container->searchPath(array('ScreenName'));
			$screenList = $child->toArray();
			if (isset($screenList['ScreenName'][$id])) {
				list ($screenName, $moduleName, $actionName) = explode(',', $screenList['ScreenName'][$id]);
			}
			// �����ȥ�С��˲��̥����ȥ�򥻥åȤ���
			$title = $screenName;
		}
		
		// ���̥����ȥ�����ꤹ��
		$this->setAttribute('title', $title);
		$this->setAttribute('screen_name', $screenName);
	}
	
	/**
	 * �ե��������Ͽ����
	 * @access public
	 * @param String $formName �ե�����̾ 
	 * @param Object $form �ե����४�֥�������
	 */
	public function setForm($formName = '', $form = null)
	{
		/* ����ͤ򥻥åȤ��� */
		if (empty($form)) {
			$form = &$this->request->getAttribute($formName);
			if (empty($form)) {
				throw new ApplicationException('Not found form is "' . $formName . '."');
			}
		}
		
		/* ���顼���ܤ��طʿ������ꤹ�� */
		if ($this->request->hasErrors()) {
			$errorNames = &$this->request->getErrorNames();
			$errorNames = array_unique($errorNames);
			foreach ($errorNames as $name) {
				if ($form->elementExists($name)) {
					$element = &$form->getElement($name);
					$this->_setErrorAttribute($element);
				}
			}
		}
		
		/* �ե������smarty�˥��åȤ��� */
		$form->accept($this->quickformSmarty);
		$this->setAttribute($formName, $this->quickformSmarty->toArray());
	}

	/**
	 * ���顼���ܤ��طʿ������ꤹ��
	 * ��QuickForm�ι��ܤ��Ф��ơ��طʿ������ꤹ���
	 * @access public
	 * @param HTML_QuickForm_element $element ���顼����
	 */
	private function _setErrorAttribute(&$element)
	{
		if ('group' == $element->getType()) {
			$elements = $element->getElements();
			foreach ($elements as $value) {
				$this->_setErrorAttribute($value);
			}
		} else {
			$attr = $element->getAttributes();
			$attr['style'] = S4_ERROR_COLUMN_COLOR;
			$element->setAttributes($attr);
		}
	}
	
	/**
	 * ���顼���ܤ�Ƚ�Ǥ���HTML��style�ץ�ѥƥ����ֵѤ���
	 * Smarty���饳���뤵���function�Τ��ᡢ$params�ˤϡ�Ϣ�����������
	 * $params['name']�˥����å��������̾�򥻥åȤ���
	 * ��setErrorMesseage����������ȡ�HTML��name����פ����뤳��
	 * @param array $params Smarty�ѥ�᡼��
	 * @param Smarty &$o_smarty Smarty���֥�������
	 */
	public function checkErrorElement($params, &$o_smarty)
	{
		$retVal = '';
		
		/* ���顼���ܤ��طʿ������ꤹ�� */
		if ($this->request->hasError($params['name'])) {
			$retVal = sprintf('style="%s"', S4_ERROR_COLUMN_COLOR);
		}
		
		return $retVal;
	}
	
	/**
	 * ���̥�å��������������
	 * �ѥ�᡼��������Ϥ��ȡ���å�������%s�˥ѥ�᡼���򥻥åȤ��ֵѤ��ޤ���
	 * @access public
	 * @param string $id ��å�����ID
	 * @param object $args ���̥�å������Υѥ�᡼��(����ޤ��ϡ��ѿ�)
	 * @return string ���̥�å�����
	 */
	public function getMessage($id, $args = null)
	{
		/* ��å��������� */
		$msg = $this->messages->getParameter($id);
		if (empty($msg)) {
			throw new ApplicationException("Not found message! id=$id");
		}
		
		/* �ѥ�᡼�����å� */
		if (is_array($args)) {
			$msg = vsprintf($msg, $args);
		} else {
			$msg = sprintf($msg, $args);
		}
		
		return $msg;
	}

	/**
	 * �ڡ����ǡ���(1�ڡ���ʬ)�򥻥åȤ���
	 * ���ڡ����ǡ���(1�ڡ���ʬ)�������ꡢsmarty��pager�ȥڡ����ǡ����򥻥åȤ��롣
	 *  �ʤ����ڡ����󥰤κݤβ���������ϡ������ǻ��ꤵ�줿���������Ǥ��롣
	 * ����������󤬻��ꤵ��Ƥ��ʤ����ϡ�����ID��ɳ�դ���������󤬥ǥե���ȤȤʤ롣
	 * ��Pager���ݻ���������ܤ�������ϡ�$extraVars�˥����̾�򥻥åȤ��롣
	 * ��Pager���ݻ����������ʤ����ܤ�������ϡ�$excludeVars�˥����̾�򥻥åȤ��롣
	 * @access public
	 * @param array $itemData �ڡ����󥰤Υ����ƥ�����ǡ���
	 * @param int $totalItems ���ڡ����Υ����ƥ���
	 * @param int $perPage �ڡ���������Υ����ƥ��
	 * @param string $pageName �ڡ���̾
	 * @param string $moduleName �⥸�塼��̾
	 * @param string $actionName ���������̾
	 * @param array $extraVars Pager���ݻ����������(�����̾)
	 * @param array $excludeVars Pager���ݻ������ʤ�����(�����̾)
	 */
	public function setPageTotalItems($itemData = null, $perPage = 30, $totalItems = null, $pageName = null, 
		$moduleName = null, $actionName = null, $extraVars = array(), $excludeVars = array())
	{
		/* ����ͤ򥻥åȤ��� */
		if (empty($pageName)) {
			$pageName = $this->getPageName();
		}
		if (empty($moduleName)) {
			$moduleName = $this->moduleName;
		}
		if (empty($actionName)) {
			$actionName = $this->actionName;
		}
		if (empty($perPage)) {
			$perPage = 30;
		}
		/* ������������������ */
		$parameters['module'] = $moduleName;
		$parameters['action'] = $actionName;
		
		$url = $this->controller->genURL(null, $parameters);

		/* �ڡ������������� */
		$params = array("totalItems" => $totalItems,
			"perPage" => $perPage,
			"mode" => "Jumping",
			"httpMethod" => "POST",
			"urlVar" => $pageName,
			"fixFileName" => false,
			"path" => "",
			"fileName" => $url,
			"prevImg" => '&lt;&lt;',
			"nextImg" => '&gt;&gt;',
			"altFirst" => "�ǽ�ڡ���",
			"altPrev" => "���ڡ���",
			"altNext" => "���ڡ���",
			"altLast" => "�ǽ��ڡ���",
			"firstPagePre" => "{",
			"firstPageText" => "�ǽ�",
			"firstPagePost" => "}",
			"lastPagePre" => "{",
			"lastPageText" => "�ǽ�",
			"lastPagePost" => "}",
			"extraVars" => $extraVars,
			"excludeVars" => $excludeVars);
		$pager = Pager::factory($params);
		
		/* �ڡ�����smarty�˥��åȤ��� */
		$this->setAttribute($pageName, $pager);
		if (count($itemData) > 0) {
			$this->setAttribute($pageName . '_data', $itemData);
		} else {
			$this->setAttribute($pageName . '_data', $this->getMessage("MC-006"));
		}
	}
	
	/**
	 * �ڡ����ǡ���(���ƤΥڡ���)�򥻥åȤ���
	 * ���ڡ����ǡ���(���ƤΥڡ���)�������ꡢsmarty��pager�ȥڡ����ǡ����򥻥åȤ��롣
	 *  �ʤ����ڡ����󥰤κݤβ���������ϡ������ǻ��ꤵ�줿���������Ǥ��롣
	 * ����������󤬻��ꤵ��Ƥ��ʤ����ϡ�����ID��ɳ�դ���������󤬥ǥե���ȤȤʤ롣
	 * ��Pager���ݻ���������ܤ�������ϡ�$extraVars�˥����̾�򥻥åȤ��롣
	 * ��Pager���ݻ����������ʤ����ܤ�������ϡ�$excludeVars�˥����̾�򥻥åȤ��롣
	 * @access public
	 * @param array $itemData �ڡ����󥰤Υ����ƥ�����ǡ���
	 * @param int $perPage �ڡ���������Υ����ƥ��
	 * @param string $pageName �ڡ���̾
	 * @param string $moduleName �⥸�塼��̾
	 * @param string $actionName ���������̾
	 * @param array $extraVars Pager���ݻ����������(�����̾)
	 * @param array $excludeVars Pager���ݻ������ʤ�����(�����̾)
	 */
	public function setPageItemData($itemData = null, $perPage = 30, $pageName = null, 
		$moduleName = null, $actionName = null, $extraVars = array(), $excludeVars = array())
	{
		/* ����ͤ򥻥åȤ��� */
		if (empty($pageName)) {
			$pageName = $this->getPageName();
		}
		if (empty($moduleName)) {
			$moduleName = $this->moduleName;
		}
		if (empty($actionName)) {
			$actionName = $this->actionName;
		}
		if (empty($perPage)) {
			$perPage = 30;
		}
		
		/* ��������������ꤹ�� */
		$parameters['module'] = $moduleName;
		$parameters['action'] = $actionName;
		
		$url = $this->controller->genURL(null, $parameters);

		/* �ڡ������������� */
		$params = array ("itemData" => $itemData,
			"perPage" => $perPage,
			"mode" => "Jumping",
			"httpMethod" => "POST",
			"urlVar" => $pageName,
			"fixFileName" => false,
			"path" => "",
			"fileName" => $url,
			"prevImg" => '&lt;&lt;',
			"nextImg" => '&gt;&gt;',
			"altFirst" => "�ǽ�ڡ���",
			"altPrev" => "���ڡ���",
			"altNext" => "���ڡ���",
			"altLast" => "�ǽ��ڡ���",
			"firstPagePre" => "{",
			"firstPageText" => "�ǽ�",
			"firstPagePost" => "}",
			"lastPagePre" => "{",
			"lastPageText" => "�ǽ�",
			"lastPagePost" => "}",
			"extraVars" => $extraVars,
			"excludeVars" => $excludeVars);
		$pager = Pager::factory($params);
		
		/* �ڡ�����smarty�˥��åȤ��� */
		$pageData = array();
		foreach ($pager->getPageData() as $lineData) {
			$pageData[] = $lineData;
		}
		$this->setAttribute($pageName, $pager);
		if (count($pageData) > 0) {
			$this->setAttribute($pageName . '_data', $pageData);
		} else {
			$this->setAttribute($pageName . '_data', $this->getMessage("MC-006"));
		}
	}
	
	/**
	 * �ڡ���̾���������
	 * @access public
	 * @return string �ڡ���̾
	 */
	public function getPageName()
	{
		$pageName = $this->pageName;
		$this->pageName += 1;

		return $pageName;
	}

	/**
	 * �ڡ���ID���������
	 * @access public
	 * @param string $pageName �ڡ���̾
	 * @return int �ڡ���ID
	 */
	public function getPageID($pageName)
	{
		return $this->request->getParameter($pageName, 1);
	}
}
?>
