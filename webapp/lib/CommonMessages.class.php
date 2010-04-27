<?php
/**
 * 共通メッセージクラス
 * アプリケーションで使用する共通メッセージを管理する
 * @access public
 * @package webapp/lib
 * @category utility
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
class CommonMessages extends ParameterHolder
{
	/* シングルトンオブジェクト */
	private static $singleton = null;
	
	/**
	 * コンストラクタ
	 * @access public
	 */
	private function __construct()
	{
		$this->parameters = array();
	}
	
	/**
	 * シングルトンオブジェクトを取得する
	 * @access public
	 * @return CommonMessages インスタンス
	 */
	public static function getInstance() {
		if (CommonMessages::$singleton == null) {
			CommonMessages::$singleton = new CommonMessages();
		}
		return CommonMessages::$singleton;
	}
	
	/**
	 * 初期処理
	 * 連想配列データ(key：コード、value：共通メッセージ)を受け取り、共通メッセージを更新する
	 * @access public
	 * @param array $parameters 共通メッセージ(連想配列データ)
	 * @return boolean 処理結果
	 */
	public function initialize ($parameters = null)
	{
		if ($parameters != null) {
			$this->parameters = array_merge($this->parameters, $parameters);
		}
		
		return true;
	}
}
