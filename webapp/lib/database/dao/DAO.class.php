<?php
/**
 * DAOオブジェクトの基底クラス
 * @access public
 * @package database/dao
 * @category DAO
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
class DAO
{
	/**
	 * データベースコネクション
	 * @var DB_common
	 */
	protected $db = null;
	
    /**
     * コンストラクタ
     * @access public
     * @param Object $db データベースコネクション
     */
	function __construct($db)
	{
		$this->db = $db;
	}
	
    /**
     * SQL文を実行し、配列データを取得する(SELECT専用)
     *	(データ形式) array([0]=>array([0]=>data, ・・・・)
     * ※ループ処理などでは使用しない。
     * ※SQLの解析が繰り返し行なわれるため処理が遅い。
     * (SQLの結果セットを配列データで取得したい場合に使用する)
     * ＜処理内容＞
     * ・SQLの解析(prepare)を行なってからSQL文を実行する
     * ・SQL文の実行エラーが発生したら、トランザクションをロールバックする
     * @access public
     * @param string $sql SQL文
     * @param array $params SQL文にセットするパラメータ
     * @param string $fetchModeフェッチモード(デフォルト：DB_FETCHMODE_ORDERED)
	 *  DB_FETCHMODE_ORDERED・・・配列データをインデックス配列([0][1]…)で取得する
	 *  DB_FETCHMODE_ASSOC・・・配列データを連想配列([id][name]…)で取得する
	 *  DB_FETCHMODE_OBJECT・・・配列データをオブジェクト($data->id…)で取得する
     * @return array SQL文の実行結果(全レコード)
     * 		(Selectが成功した場合、2次元配列オブジェクトをセットする)
     * @throws DatabaseException SQL文の実行エラーが発生した時
     */
	function execQuerySqlAll($sql, $params = array(), 
		$fetchMode = DB_FETCHMODE_DEFAULT)
	{
		$rs = &$this->db->getAll($sql, $params, $fetchMode);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}

    /**
     * SQL文を実行し、連想配列データを取得する(SELECT専用)
     * ※ループ処理などでは使用しない。
     * ※SQLの解析が繰り返し行なわれるため処理が遅い。
     * (SQLの結果セットを連想配列データで取得したい(プルダウンリスト)場合に
     * 使用する)
     * ＜処理内容＞
     * ・selectの先頭カラムをキー(key)に、残りのカラムを値配列(array)として
     *  取得する。
     *	(データ形式) array([key]=>array([0]=>data, ・・・・)
     * ・selectのカラムが2列しかない場合は、値として取得する。
     *	(データ形式) array[key]=>data
     * ・SQLの解析(prepare)を行なってからSQL文を実行する
     * ・SQL文の実行エラーが発生したら、トランザクションをロールバックする
     * @access public
     * @param string $sql SQL文
     * @param boolean $force 連想配列ではなく配列にするか
     * 			(デフォルト：false)
     * @param array $params SQL文にセットするパラメータ
     * @param string $fetchModeフェッチモード(デフォルト：DB_FETCHMODE_ORDERED)
	 *  DB_FETCHMODE_ORDERED・・・配列データをインデックス配列([0][1]…)で取得する
	 *  DB_FETCHMODE_ASSOC・・・配列データを連想配列([id][name]…)で取得する
	 *  DB_FETCHMODE_OBJECT・・・配列データをオブジェクト($data->id…)で取得する
     * @param boolean $group キー重複時に配下の配列を入れ子にするか
     * 			(デフォルト：false)
     * @return array SQL文の実行結果(全レコード)
     * 		(Selectが成功した場合、連想配列オブジェクトをセットする)
     * @throws DatabaseException SQL文の実行エラーが発生した時
     */
	function execQuerySqlAssoc($sql, $force = false, $params = array(), 
		$fetchMode = DB_FETCHMODE_DEFAULT, $group = false)
	{
		$rs = &$this->db->getAssoc($sql, $force, $params, $fetchMode, $group);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}

    /**
     * SQL文を実行し、最初の配列データを取得する(SELECT専用)
     * ※ループ処理などでは使用しない。
     * ※SQLの解析が繰り返し行なわれるため処理が遅い。
     * (SQLの結果セットを配列データで取得したい場合に使用する)
     * ※最初の配列データを取得した後、結果セットを開放する
     * ＜処理内容＞
     * ・SQLの解析(prepare)を行なってからSQL文を実行する
     * ・SQL文の実行エラーが発生したら、トランザクションをロールバックする
     * @access public
     * @param string $sql SQL文
     * @param array $params SQL文にセットするパラメータ
     * @param string $fetchModeフェッチモード(デフォルト：DB_FETCHMODE_ORDERED)
	 *  DB_FETCHMODE_ORDERED・・・配列データをインデックス配列([0][1]…)で取得する
	 *  DB_FETCHMODE_ASSOC・・・配列データを連想配列([id][name]…)で取得する
	 *  DB_FETCHMODE_OBJECT・・・配列データをオブジェクト($data->id…)で取得する
     * @return array SQL文の実行結果(1レコード)
     * 		(Selectが成功した場合、1次元配列オブジェクトをセットする)
     * @throws DatabaseException SQL文の実行エラーが発生した時
     */
	function execQuerySqlRow($sql, $params = array(), 
		$fetchMode = DB_FETCHMODE_DEFAULT)
	{
		$rs = &$this->db->getRow($sql, $params, $fetchMode);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}
			
    /**
     * SQL文を実行する
     * ※ループ処理などでは使用しない。
     * ※SQLの解析が繰り返し行なわれるため処理が遅い。
     * (SQLを何度も繰り返し実行しない場合に使用する)
     * ＜処理内容＞
     * ・SQLの解析(prepare)を行なってからSQL文を実行する
     * ・SQL文の実行エラーが発生したら、トランザクションをロールバックする
     * @access public
     * @param string $sql SQL文
     * @param array $params SQL文にセットするパラメータ
     * @return object SQL文の実行結果
     * 		(Selectが成功した場合、DB_Resultオブジェクトをセットする)
     * 		(INSERT/UPDATE/DELETEが成功した場合、DB_OKをセットする)
     * @throws DatabaseException SQL文の実行エラーが発生した時
     */
	function execQuerySql($sql, $params = array())
	{
		$rs = &$this->db->query($sql, $params);

		if (DB::isError($rs)) {
			$this->db->rollback();

			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}

    /**
     * SQLクエリで取得する行数を指定する(SELECT専用)
     * (SQLクエリで取得する行数を制限する場合に使用する)
     * ＜処理内容＞
     * ・SQLの解析(prepare)を行なってからSQL文を実行する
     * ・SQL文の実行エラーが発生したら、トランザクションをロールバックする
     * @access public
     * @param string $sql SQL文
     * @param int $from 取得開始行
     * @param int $cnt 取得する件数
     * @param array $params SQL文にセットするパラメータ
     * @return object SQL文の実行結果
     * 		(Selectが成功した場合、DB_Resultオブジェクトをセットする)
     * @throws DatabaseException SQL文の実行エラーが発生した時
     */
	function execLimitQuerySql($sql, $from, $cnt, $params = array())
	{
		$rs = &$this->db->limitQuery($sql, $from, $cnt, $params);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}
	
    /**
     * 準備済みSQL文を作成する
     * (executeSQLを実行するため使用する)
     * ＜処理内容＞
     * ・SQL文の解析エラーが発生したら、トランザクションをロールバックする
     * @access public
     * @param string $sql SQL文
     * @return object 準備済みSQL文
     * @throws DatabaseException SQL文の解析エラーが発生した時
     */
	function prepareSql($sql)
	{
		$sth = &$this->db->prepare($sql);

		if (DB::isError($sth)) {
			$this->db->rollback();
			throw new DatabaseException($sth->getMessage());
        }

		return $sth;
	}

    /**
     * 準備済みSQL文を実行する
     * ※ループ処理などにて使用する。
     * ※SQLの解析が繰り返し行なわれないため処理が早い。
     * (同じSQLを繰り返し実行する場合に使用する)
     * ＜処理内容＞
     * ・SQLの解析(prepare)はせずに準備済みのSQL文を実行する
     * ・SQL文の実行エラーが発生したら、トランザクションをロールバックする
     * @access public
     * @param string $sql SQL文
     * @param array $params SQL文にセットするパラメータ
     * @return object SQL文の実行結果
     * 		(Selectが成功した場合、DB_Resultオブジェクトをセットする)
     * 		(INSERT/UPDATE/DELETEが成功した場合、DB_OKをセットする)
     * @throws DatabaseException SQL文の実行エラーが発生した時
     */
	function execSql($sth, $params = array())
	{
		$rs = &$this->db->execute($sth, $params);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
        }

		return $rs;
	}

    /**
     * 準備済みSQL文をまとめて実行する(INSERT/UPDATE/DELETE専用)
     * ※配列データを一括登録するなどに使用する。
     * ※SQLの解析が繰り返し行なわれないため処理が早い。
     * (同じSQLを繰り返し実行する場合に使用する)
     * ＜処理内容＞
	 * ・SQLの解析(prepare)はせずに準備済みのSQL文を実行する
     * ・実行途中で処理が失敗した場合、以降のデータは処理されません。
     * ・SQL文の実行エラーが発生したら、トランザクションをロールバックする
     * @access public
     * @param string $sql SQL文
     * @param array $data SQL文にセットする配列データ
     * @return object SQL文の実行結果
     * 		(INSERT/UPDATE/DELETEが成功した場合、DB_OKをセットする)
     * @throws DatabaseException SQL文の実行エラーが発生した時
     */
	function execMultipleSql($sth, $data = array())
	{
		$rs = &$this->db->executeMultiple($sth, $data);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
        }

		return $rs;
	}
}
?>