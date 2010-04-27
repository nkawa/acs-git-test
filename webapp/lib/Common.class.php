<?php
/*
 * 汎用クラス
 * @access public
 * @package lib
 * @category Common
 * @author Masahiko Fujimoto <fujimoto@icraft.jp>
 * @sourcefile
 *
 */
class Common
{
	/**
	 * ページング処理用に配列の一部を取り出す。
	 *
	 * @param array $array 配列
	 * @param int $page ページ
	 * @param int $count １ページあたりの件数
	 *
	 * @return array 指定したページ分の配列
	 */
	function offset($array, $page, $count)
	{
		if (!is_array($array) || !is_numeric($page) || !is_numeric($count))
		{
			return null;
		}

		$start = $count * ($page - 1);
		$end = $count * $page;

		$offsettedArray = null;

		foreach ($array as $no => $value)
		{
			if ($start <= $no && $no < $end)
			{
				$offsettedArray[] = $value;
			}
		}

		return $offsettedArray;
	}
	
	
	/*
	 * smartyのSELECTボックス描画に使用する配列を取得する
	 */
	function createSelectBoxArray($valueArray, $outputArray)
	{
		array_unshift($valueArray, "");
		array_unshift($outputArray, "選択して下さい");
		
		return array("value"=>$valueArray, "output"=>$outputArray);
	}
	
	
	/**
	 * 月を加算する
	 * 
	 * @param time unixtimestamp
	 * @param int	加算する月数
	 * 
	 * @return time unixtimestamp
	 */
	function addMonth($date, $additionalNumber)
	{
		$month 	= date("m", $date);
		$day	= date("d", $date); 
		$year	= date("y", $date);	
		
		$resultDate 	= mktime(0,0,0,$month + $additionalNumber, $day, $year);
//		$resultMonth 	= date("m", $resultDate);
		$resultDay		= date("d", $resultDate); 
//		$resultYear	= date("y", $resultDate);	
	
		// $dateが月末の場合 :  月を翌月の月が短い場合
		if(mktime (0,0,0,$month + 1 ,0 ,$year) == $date || $day != $resultDay)
		{
			// 計算後の月の月末を返す
			$resultDate = mktime (0,0,0,$month + $additionalNumber + 1 ,0 ,$year);
		}

		return $resultDate;
	}
	
	
	/**
	 * 日を加算する
	 * 
	 * @param time unixtimestamp
	 * @param int	加算する日数
	 * 
	 * @return time unixtimestamp
	 */
	function addDay($date, $addDay)
	{
		$month 	= date("m", $date);
		$day	= date("d", $date) + $addDay; 
		$year	= date("y", $date);	

		// 計算後の月の月末を返す
		$result = mktime (0, 0, 0, $month, $day ,$year);
	
		return $result;
	}
	
	
	/**
	 * リダイレクト
	 * 
	 * @param string モジュール名
	 * @param string アクション名
	 * @param string 付加するパラメーター
	 * 
	 * @return 無し
	 */
	function redirect($modName, $actName, $opt = null)
	{
		$params = array(MODULE_ACCESSOR => $modName, ACTION_ACCESSOR => $actName);
		if (is_array($opt)) 
		{
			$params = array_merge($params, $opt);	
		}
		Controller::redirect(Controller::genURL($params));
	}
	
	
	/**
	 * 採番値の取得
	 *
	 * @param	$seqName	シーケンス名
	 * @return	シーケンスの値
	 * @author	M.Fujijmoto
	 * @date	2005.11.16
	 */
	function getSequence(&$db, $column, $dbName)
	{
		$sql = "SELECT MAX(" .$column. ") AS new_number FROM ".$dbName;

		// SEQUENCEの取得
		$return = $db->getRow($sql);

		// エラーの場合、nullを返す
		if (0 == strcasecmp(get_class($result), 'DB_Error'))
		{
			return null;
		}

		return is_null($return['new_number']) ? 0 : $return['new_number'];
	}
	
	
	/**
	 * 三つの配列をマージする
	 * 
	 * @param $array1
	 * @param $array2
	 * @param $array3
	 * @param array
	 */
	function margeTreeArray($array1, $array2, $array3)
	{
		if(!empty($array1))
		{
			$errorMessage = $array1;
		}
		
		if(!empty($array2))
		{
			if(!empty($errorMessage))
			{
				$errorMessage = array_merge($errorMessage, $array2);
			}
			else
			{
				$errorMessage = $array2;
			}
		}
		
		if(!empty($array3))
		{
			if(!empty($errorMessage))
			{
				$errorMessage = array_merge($errorMessage, $array3);
			}
			else
			{
				$errorMessage = $array3;
			}
		}
		
		return $errorMessage;
	}
	
	/**
	 * 指定された長さのランダムな文字列を生成します。
	 * 
	 * @author fujiwara
	 * @param int $length 文字の長さ
	 * @return string
	 */
	public static function createRandomString($length)
	{
		$createdString = "";
		
		$sCharArray = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n'
						,	'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
						,	'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'
						,	'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
						,	'1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
		
		for ($i = 1; $i <= $length; $i++) {
			$createdString .= $sCharArray[rand(0 ,61)];
		}
		
		return $createdString;
	}
}
?>