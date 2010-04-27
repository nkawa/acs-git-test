<?php
/*
 * ���ѥ��饹
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
	 * �ڡ����󥰽����Ѥ�����ΰ�������Ф���
	 *
	 * @param array $array ����
	 * @param int $page �ڡ���
	 * @param int $count ���ڡ���������η��
	 *
	 * @return array ���ꤷ���ڡ���ʬ������
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
	 * smarty��SELECT�ܥå�������˻��Ѥ���������������
	 */
	function createSelectBoxArray($valueArray, $outputArray)
	{
		array_unshift($valueArray, "");
		array_unshift($outputArray, "���򤷤Ʋ�����");
		
		return array("value"=>$valueArray, "output"=>$outputArray);
	}
	
	
	/**
	 * ���û�����
	 * 
	 * @param time unixtimestamp
	 * @param int	�û�������
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
	
		// $date�������ξ�� :  ������ηû�����
		if(mktime (0,0,0,$month + 1 ,0 ,$year) == $date || $day != $resultDay)
		{
			// �׻���η�η������֤�
			$resultDate = mktime (0,0,0,$month + $additionalNumber + 1 ,0 ,$year);
		}

		return $resultDate;
	}
	
	
	/**
	 * ����û�����
	 * 
	 * @param time unixtimestamp
	 * @param int	�û���������
	 * 
	 * @return time unixtimestamp
	 */
	function addDay($date, $addDay)
	{
		$month 	= date("m", $date);
		$day	= date("d", $date) + $addDay; 
		$year	= date("y", $date);	

		// �׻���η�η������֤�
		$result = mktime (0, 0, 0, $month, $day ,$year);
	
		return $result;
	}
	
	
	/**
	 * ������쥯��
	 * 
	 * @param string �⥸�塼��̾
	 * @param string ���������̾
	 * @param string �ղä���ѥ�᡼����
	 * 
	 * @return ̵��
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
	 * �����ͤμ���
	 *
	 * @param	$seqName	��������̾
	 * @return	�������󥹤���
	 * @author	M.Fujijmoto
	 * @date	2005.11.16
	 */
	function getSequence(&$db, $column, $dbName)
	{
		$sql = "SELECT MAX(" .$column. ") AS new_number FROM ".$dbName;

		// SEQUENCE�μ���
		$return = $db->getRow($sql);

		// ���顼�ξ�硢null���֤�
		if (0 == strcasecmp(get_class($result), 'DB_Error'))
		{
			return null;
		}

		return is_null($return['new_number']) ? 0 : $return['new_number'];
	}
	
	
	/**
	 * ���Ĥ������ޡ�������
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
	 * ���ꤵ�줿Ĺ���Υ������ʸ������������ޤ���
	 * 
	 * @author fujiwara
	 * @param int $length ʸ����Ĺ��
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