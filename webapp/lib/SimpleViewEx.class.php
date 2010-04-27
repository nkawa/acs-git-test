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
 * Viewの拡張クラス
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
	 * Controllerオブジェクト 
	 * @var Controller
	 */
	protected $controller = '';
	
	/**
	 * Requestオブジェクト
	 * @var Request
	 */
	protected $request = '';
	
	/* userオブジェクト */
	private $user = '';
	
	/* HTML_QuickFormのSmartyレンダラ */
	private $quickformSmarty = '';
	
	/* メッセージデータ */
	private $messages = '';

	/* ページ名 */
	private $pageName = 'page1';
	
	/* 画面遷移先のモジュール名 */
	private $moduleName = '';

	/* 画面遷移先のアクション名 */
	private $actionName = '';

	var $css_file_array = array();
	var $js_file_array = array();
	
	/**
	 * 初期処理
	 * @access public
	 * @param Object $contextt context
	 * @return boolean 処理結果
	 */
	public function initialize ($context)
	{
		parent::initialize($context);

		/* 初期値をセットする */
		$this->controller = $context->getController();
		$this->request = $context->getRequest();
		$this->user = $context->getUser();

		$this->moduleName = $context->getModuleName();
		$this->actionName = $context->getActionName();
		$this->messages = &CommonMessages::getInstance();
		
		/* フォーム登録リストのフォームをsmartyにすべてセットする */
		$o_smarty = &$this->getEngine();
		$o_smarty->register_object("style", $this, array("request", "checkErrorElement"));
		$this->quickformSmarty = 
			new HTML_QuickForm_Renderer_ArraySmarty($this->getEngine());
		$formList = $this->request->getAttribute('formList');
		foreach ($formList as $formName) {
			$this->setForm($formName);
		}

		/* メッセージをsmartyにセットする */
		if ($this->request->hasAttribute('messages')) {
			$messages = &$this->request->getAttribute('messages');
			$messages = array_unique($messages);
			$this->setAttributeByRef('messages', $messages);
		}		
		/* エラーメッセージをsmartyにセットする */
		if ($this->request->hasErrors()) {
			$errors = &$this->request->getErrors();
			$errors = array_unique($errors);
			$this->setAttributeByRef('errors', $errors);
		}

		
		// 共通の CSS をセット
		array_push($this->css_file_array, ACS_SELECTION_CSS_DIR . ACS_DEFAULT_SELECTION_CSS_FILE);
		$this->setAttribute('include_css_array', $this->css_file_array);

		// 共通の JS をセット
		array_push($this->js_file_array, ACS_JS_DIR . ACS_COMMON_JS);
		$this->setAttribute('include_script_array', $this->js_file_array);
		
		return true;
	}

	/**
	 * 画面IDをセットする
	 * 画面IDに紐付く画面タイトルとモジュール名とアクション名をsmartyにセットする
	 * @access public
	 * @param string $id 画面ID
	 */
	public function setScreenId($id)
	{
		/* Decoratorテンプレートをセットする */
		$this->setDecoratorDirectory(MO_TEMPLATE_DIR);
		$this->setDecoratorTemplate(MO_SIMPLE_TEMPLATE_FILE);

		// スクリーンタイトルを取得する
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
			// タイトルバーに画面タイトルをセットする
			$title = $screenName;
		}
		
		// 画面タイトルを設定する
		$this->setAttribute('title', $title);
		$this->setAttribute('screen_name', $screenName);
	}
	
	/**
	 * フォームを登録する
	 * @access public
	 * @param String $formName フォーム名 
	 * @param Object $form フォームオブジェクト
	 */
	public function setForm($formName = '', $form = null)
	{
		/* 初期値をセットする */
		if (empty($form)) {
			$form = &$this->request->getAttribute($formName);
			if (empty($form)) {
				throw new ApplicationException('Not found form is "' . $formName . '."');
			}
		}
		
		/* エラー項目の背景色を設定する */
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
		
		/* フォームをsmartyにセットする */
		$form->accept($this->quickformSmarty);
		$this->setAttribute($formName, $this->quickformSmarty->toArray());
	}

	/**
	 * エラー項目の背景色を設定する
	 * （QuickFormの項目に対して、背景色を設定する）
	 * @access public
	 * @param HTML_QuickForm_element $element エラー項目
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
	 * エラー項目を判断し、HTMLのstyleプロパティを返却する
	 * Smartyからコールされるfunctionのため、$paramsには、連想配列形式で
	 * $params['name']にチェックする項目名をセットする
	 * ※setErrorMesseageの第二引数と、HTMLのnameを一致させること
	 * @param array $params Smartyパラメータ
	 * @param Smarty &$o_smarty Smartyオブジェクト
	 */
	public function checkErrorElement($params, &$o_smarty)
	{
		$retVal = '';
		
		/* エラー項目の背景色を設定する */
		if ($this->request->hasError($params['name'])) {
			$retVal = sprintf('style="%s"', S4_ERROR_COLUMN_COLOR);
		}
		
		return $retVal;
	}
	
	/**
	 * 共通メッセージを取得する
	 * パラメータを引き渡すと、メッセージの%sにパラメータをセットし返却します。
	 * @access public
	 * @param string $id メッセージID
	 * @param object $args 共通メッセージのパラメータ(配列または、変数)
	 * @return string 共通メッセージ
	 */
	public function getMessage($id, $args = null)
	{
		/* メッセージ取得 */
		$msg = $this->messages->getParameter($id);
		if (empty($msg)) {
			throw new ApplicationException("Not found message! id=$id");
		}
		
		/* パラメータセット */
		if (is_array($args)) {
			$msg = vsprintf($msg, $args);
		} else {
			$msg = sprintf($msg, $args);
		}
		
		return $msg;
	}

	/**
	 * ページデータ(1ページ分)をセットする
	 * ・ページデータ(1ページ分)を受け取り、smartyにpagerとページデータをセットする。
	 *  なお、ページングの際の画面遷移先は、引数で指定されたアクションである。
	 * ・アクションが指定されていない場合は、画面IDに紐付くアクションがデフォルトとなる。
	 * ・Pagerに保持させる項目がある場合は、$extraVarsにカラム名をセットする。
	 * ・Pagerに保持させたくない項目がある場合は、$excludeVarsにカラム名をセットする。
	 * @access public
	 * @param array $itemData ページングのアイテム配列データ
	 * @param int $totalItems 全ページのアイテム合計
	 * @param int $perPage ページあたりのアイテム数
	 * @param string $pageName ページ名
	 * @param string $moduleName モジュール名
	 * @param string $actionName アクション名
	 * @param array $extraVars Pagerに保持させる項目(カラム名)
	 * @param array $excludeVars Pagerに保持させない項目(カラム名)
	 */
	public function setPageTotalItems($itemData = null, $perPage = 30, $totalItems = null, $pageName = null, 
		$moduleName = null, $actionName = null, $extraVars = array(), $excludeVars = array())
	{
		/* 初期値をセットする */
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
		/* 画面遷移先を取得する */
		$parameters['module'] = $moduleName;
		$parameters['action'] = $actionName;
		
		$url = $this->controller->genURL(null, $parameters);

		/* ページを生成する */
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
			"altFirst" => "最初ページ",
			"altPrev" => "前ページ",
			"altNext" => "次ページ",
			"altLast" => "最終ページ",
			"firstPagePre" => "{",
			"firstPageText" => "最初",
			"firstPagePost" => "}",
			"lastPagePre" => "{",
			"lastPageText" => "最終",
			"lastPagePost" => "}",
			"extraVars" => $extraVars,
			"excludeVars" => $excludeVars);
		$pager = Pager::factory($params);
		
		/* ページをsmartyにセットする */
		$this->setAttribute($pageName, $pager);
		if (count($itemData) > 0) {
			$this->setAttribute($pageName . '_data', $itemData);
		} else {
			$this->setAttribute($pageName . '_data', $this->getMessage("MC-006"));
		}
	}
	
	/**
	 * ページデータ(全てのページ)をセットする
	 * ・ページデータ(全てのページ)を受け取り、smartyにpagerとページデータをセットする。
	 *  なお、ページングの際の画面遷移先は、引数で指定されたアクションである。
	 * ・アクションが指定されていない場合は、画面IDに紐付くアクションがデフォルトとなる。
	 * ・Pagerに保持させる項目がある場合は、$extraVarsにカラム名をセットする。
	 * ・Pagerに保持させたくない項目がある場合は、$excludeVarsにカラム名をセットする。
	 * @access public
	 * @param array $itemData ページングのアイテム配列データ
	 * @param int $perPage ページあたりのアイテム数
	 * @param string $pageName ページ名
	 * @param string $moduleName モジュール名
	 * @param string $actionName アクション名
	 * @param array $extraVars Pagerに保持させる項目(カラム名)
	 * @param array $excludeVars Pagerに保持させない項目(カラム名)
	 */
	public function setPageItemData($itemData = null, $perPage = 30, $pageName = null, 
		$moduleName = null, $actionName = null, $extraVars = array(), $excludeVars = array())
	{
		/* 初期値をセットする */
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
		
		/* 画面遷移先を設定する */
		$parameters['module'] = $moduleName;
		$parameters['action'] = $actionName;
		
		$url = $this->controller->genURL(null, $parameters);

		/* ページを生成する */
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
			"altFirst" => "最初ページ",
			"altPrev" => "前ページ",
			"altNext" => "次ページ",
			"altLast" => "最終ページ",
			"firstPagePre" => "{",
			"firstPageText" => "最初",
			"firstPagePost" => "}",
			"lastPagePre" => "{",
			"lastPageText" => "最終",
			"lastPagePost" => "}",
			"extraVars" => $extraVars,
			"excludeVars" => $excludeVars);
		$pager = Pager::factory($params);
		
		/* ページをsmartyにセットする */
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
	 * ページ名を取得する
	 * @access public
	 * @return string ページ名
	 */
	public function getPageName()
	{
		$pageName = $this->pageName;
		$this->pageName += 1;

		return $pageName;
	}

	/**
	 * ページIDを取得する
	 * @access public
	 * @param string $pageName ページ名
	 * @return int ページID
	 */
	public function getPageID($pageName)
	{
		return $this->request->getParameter($pageName, 1);
	}
}
?>
