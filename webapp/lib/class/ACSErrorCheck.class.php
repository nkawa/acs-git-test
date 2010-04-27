<?php
// $Id: ACSErrorCheck.class.php,v 1.3 2007/03/01 09:01:12 w-ota Exp $

/**
 * エラーチェック関数
 */
class ACSErrorCheck
{
	/**
	 * 日付が正しいか
	 * 
	 * @param 日付を表す文字列 (YYYY/MM/DD)
	 * @return 正(true) / 誤(false)
	 */
	static function is_valid_date($str) {
		list($yyyy, $mm, $dd) = explode('/', $str);
		if (preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', $str) && checkdate($mm, $dd, $yyyy)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 自然数かどうか
	 *
	 * @param $str 判定する文字列
	 * @param $enable_zero ０を含むか否か
	 * @return 正(true) / 誤(false)
	 */
	function is_natural_number($str,$enable_zero=false) {
		$min = $enable_zero===true ? 0 : 1;
		if (preg_match('/^[0-9]+$/', $str) && $str >= $min) {
			return true;
		} else {
			return false;
		}
	}
}

?>
