<?php
/**
 * Valueeオブジェクトの基底クラス
 * (Valueは予約語のため、Valueeと命名している)
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
	 * 暗号化フラグ
	 * 個人情報、機密情報などデータを暗号化する場合、
	 * 暗号化フラグをONにする
	 * ・暗号化フラグをONにする方法は、encryptFlagON()を実行するか、
	 * 　コンストラクタの引数にtrueをセットする。
	 * ・暗号化フラグをOFFにする方法は、encryptFlagOFF()を実行する。
	 * @var boolean true:暗号化する、false:暗号化しない
	 */
	private $encryptFlag;
	
	/* 暗号化クラス */
	private $encryption;
	
    /**
     * コンストラクタ
     * @access public
     * @param boolean $encryptFlag 暗号化フラグ（デフォルト：true）
     */
	public function __construct($encryptFlag = true)
	{
		$this->encryptFlag = $encryptFlag;
		$this->encryption = CommonEncryption::getInstance();
	}
	
	/**
	 * 暗号化フラグをONにする
	 * @access public
	 */
	public function encryptFlagON()
	{
		$this->encryptFlag = true;
	}

	/**
	 * 暗号化フラグをOFFにする
	 * @access public
	 */
	public function encryptFlagOFF()
	{
		$this->encryptFlag = false;
	}
	
	/**
	 * データを暗号化する
	 * ・暗号化フラグがONの時、引数のデータを暗号化する。
	 * ・暗号化フラグがOFFの時、引数のデータをそのまま戻す。
	 * @access  public
	 * @param mixed $data データ
	 * @return string 暗号化データ
	 */	
	public function encrypt($data)
	{
		// 暗号化ロジックを実装する
		$retVal = $data;
			
		return $retVal;
	}
	
	/**
	 * データを復号化する
	 * ・暗号化フラグがONの時、引数のデータを復号化する。
	 * ・暗号化フラグがOFFの時、引数のデータをそのまま戻す。
	 * @access  public
	 * @param mixed $data データ
	 * @return string 復号化データ
	 */	
	public function decrypt($data)
	{
		// 複合化ロジックを実装する
		$retVal = $data;
			
		return $retVal;
	}

	/**
	 * 配列データを暗号化する
	 * ・暗号化フラグがONの時、引数の配列データを暗号化する。
	 * ・暗号化フラグがOFFの時、引数の配列データをそのまま戻す。
	 * ・配列データの一部を暗号化する場合は、暗号化する配列データの処理インデックスを引き渡すこと。
	 * @access  public
	 * @param array $data 配列データ
	 * @param array $indexs 処理インデックス
	 * @return array 復号化配列データ
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
	 * 配列データを復号化する
	 * ・暗号化フラグがONの時、引数の配列データを復号化する。
	 * ・暗号化フラグがOFFの時、引数の配列データをそのまま戻す。
	 * ・配列データの一部を復号化する場合は、復号化する配列データの処理インデックスを引き渡すこと。
	 * @access  public
	 * @param array $data 配列データ
	 * @param array $indexs 処理インデックス
	 * @return array 復号化配列データ
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