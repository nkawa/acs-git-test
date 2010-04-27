<?php
// $Id: ACSErrorCheck.class.php,v 1.3 2007/03/01 09:01:12 w-ota Exp $

/**
 * ���顼�����å��ؿ�
 */
class ACSErrorCheck
{
	/**
	 * ���դ���������
	 * 
	 * @param ���դ�ɽ��ʸ���� (YYYY/MM/DD)
	 * @return ��(true) / ��(false)
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
	 * ���������ɤ���
	 *
	 * @param $str Ƚ�ꤹ��ʸ����
	 * @param $enable_zero ����ޤफ�ݤ�
	 * @return ��(true) / ��(false)
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
