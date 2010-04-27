<?php
ini_set("display_errors", 0);
ini_set("error_reporting", false);
require_once(MO_PEAR_DIR . "/Mail/Queue.php");
require_once(MO_SMARTY_DIR.'/libs/Smarty.class.php');
ini_restore("error_reporting");
ini_restore("display_errors");

/**
 * 共通テキスト作成クラス
 * テンプレート(Smarty)からテキストを作成する。
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
	
	/* コンパイルディレクトリ */
	protected $compileDir;
	
	/* テンプレートディレクトリ */
	protected $directory;

	/* テンプレートファイル名 */
	protected $template;
	
	/**
	 * コンストラクタ
	 * @access public
	 */
	function __construct()
	{
		/* Smarty設定 */
		$this->smarty = new Smarty();
		$this->smarty->config_dir = MO_CONFIG_DIR;
		$this->smarty->cache_dir = MO_CACHE_DIR;
	}

	/**
	 * コンパイルディレクトリを取得する
	 * @access public
	 * @return string コンパイルディレクトリ
	 */
	public function getCompileDir()
	{
		return $this->compileDir;
	}

	/**
	 * コンパイルディレクトリをセットする
	 * @access public
	 * @param string $compileDir コンパイルディレクトリ
	 */
	public function setCompileDir($compileDir)
	{
		$this->compileDir = $compileDir;
	}
	
	/**
	 * テンプレートディレクトリを取得する
	 * @access public
	 * @return string テンプレートディレクトリ
	 */
	public function getDirectory()
	{
		return $this->directory;
	}

	/**
	 * テンプレートディレクトリをセットする
	 * @access public
	 * @param string $directory テンプレートディレクトリ
	 */
	public function setDirectory($directory)
	{
		$this->directory = $directory;
	}
	
	/**
	 * テンプレートファイル名を取得する
	 * @access public
	 * @return string テンプレートファイル名
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * テンプレートファイル名をセットする
	 * @access public
	 * @param string $directory テンプレートディレクトリ
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
	 * 全てのテンプレート変数をクリアする
	 * @access public
	 */
	public function clearAttributes()
	{
		$this->smarty->clear_all_assign();
	}

	/**
	 * テンプレート変数名を全て取得する
	 * @access public
	 * @return array テンプレート変数名
	 */
	public function getAttributeNames()
	{
		return array_keys($this->smarty->get_template_vars());
	}
			
	/**
	 * テンプレート変数データを取得する
	 * @access public
	 * @return var テンプレート変数データ
	 */
	public function & getAttribute($name)
	{
		return $this->smarty->get_template_vars($name);
	}

	/**
	 * テンプレート変数を除去する
	 * @access public
	 * @return array テンプレート変数
	 */
	public function & removeAttribute($name)
	{
		$retval = $this->smarty->get_template_vars($name);

		$this->smarty->clear_assign($name);
		
		return $retval;
	}
	
	/**
	 * テンプレート変数をセットする
	 * @access public
	 * @param string $name テンプレート変数名
	 * @param var $value テンプレート変数データ
	 */
	public function setAttribute($name, $value)
	{
		$this->smarty->assign($name, $value);
	}

	/**
	 * テンプレート変数をセットする
	 * @access public
	 * @param string $name テンプレート変数名
	 * @param var $value テンプレート変数データ(参照アドレス)
	 */
	public function setAttributeByRef($name, &$value)
	{
		$this->smarty->assign_by_ref($name, $value);
	}

	/**
	 * テンプレート変数をまとめてセットする
	 * @access public
	 * @param array $attributes テンプレート変数(配列)
	 */
	public function setAttributes($attributes)
	{
		$this->smarty->assign($attributes);
	}

	/**
	 * テンプレート変数をまとめてセットする
	 * @access public
	 * @param array $attributes テンプレート変数(配列)(参照アドレス)
	 */
	public function setAttributesByRef(&$attributes)
	{
		$this->smarty->assign_by_ref($attributes);
	}

	/**
	 * ファイルテンプレートにテンプレート変数をセットし、テキストを生成する
	 * @access public
	 * @return メール(テキスト)
	 */
	public function & render()
	{
		// コンパイルディレクトリが未セットの場合、コンパイルディレクトリをセットする
		if (empty($this->compileDir)) {
			$this->compileDir = MO_WEBAPP_DIR . '/compiled';
		}
		// コンパイルディレクトリがない場合、コンパイルディレクトリを作成する
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