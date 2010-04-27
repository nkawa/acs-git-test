<?php
/**
 * Serviceオブジェクトの基底クラス
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
	 * データベースコネクション
	 * @var DB_common
	 */
	protected $db = null;
	
	/**
	 * コンストラクタ
	 * @access public
	 */
	function __construct()
	{
		$this->db = ConnectionFactory::getConnection();
	}
	
    /**
     * デストラクタ
     * @access public
     */
	function __destruct()
	{
		$this->db->disconnect();
	}

	/**
	 * トランザクション開始
	 * オートコミットを解除し、トランザクションを開始する。
	 * @access public
	 */
	function begin()
	{
		$this->db->autoCommit(false);
		$this->db->query("BEGIN");
	}
	
	/**
	 * トランザクション終了（コミット）
	 * トランザクションを終了し、オートコミットに設定する。
	 * @access public
	 */
	function end()
	{
		$this->db->query("COMMIT");
		$this->db->autoCommit(true);
	}
	
	/**
	 * トランザクションコミット
	 * トランザクションをコミットし、オートコミットに設定する。
	 * @access public
	 */
	function commit()
	{
		$this->db->query("COMMIT");
		$this->db->autoCommit(true);
	}

	/**
	 * トランザクションロールバック
	 * トランザクションをロールバックし、オートコミットに設定する。
	 * @access public
	 */
	function rollback()
	{
		$this->db->query("ROLLBACK");
		$this->db->autoCommit(true);
	}
}
?>