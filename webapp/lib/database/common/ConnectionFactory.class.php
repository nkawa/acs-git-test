<?php
require_once(MO_PEAR_DIR . "/DB.php");

class ConnectionFactory
{
	function getConnection()
	{
		$dsn = array (
			'phptype'  => DB_PHPTYPE,
			'hostspec' => DB_HOSTSPEC,
			'port'     => DB_PORT,
			'database' => DB_DATABASE,
			'username' => DB_USERNAME,
			'password' => DB_PASSWORD
		);

		$options = array (
			'debug' => 2,
		);
		
		$db =& DB::connect($dsn, $options);
		if (DB::isError($db)) {
			throw new ApplicationException($db->getMessage());
		}

		return $db;
	}
}
?>