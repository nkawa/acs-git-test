<?php

/**
 * 実行時間計測フィルタークラス
 *
 * @author  teramoto
 * @version $Revision: 1.1 $
 *
 */

class AcsExecutionTimeFilter extends Filter
{

	/**
	 * コンストラクタ
	 */
	function AcsExecutionTimeFilter ()
	{
	}

	/**
	 * 実行時間計測フィルターの実行
	 *
	 * @param FilterChain Mojavi FilterChain インスタンス
	 * @param Controller  Mojavi Controller インスタンス
	 * @param Request	 Mojavi Request インスタンス
	 * @param User		Mojavi User インスタンス
	 */
	function execute (&$filterChain, &$controller, &$request, &$user)
	{

		$loaded = TRUE;

		ob_start();

		$stimer = explode(' ', microtime());
		$stimer = $stimer[1] + $stimer[0];

		$filterChain->execute($controller, $request, $user);

		$etimer = explode(' ', microtime());
		$etimer = $etimer[1] + $etimer [0];
		$time   = round(($etimer - $stimer), 3);

		$content = str_replace('%EXEC_TIME%', $time, ob_get_contents());

		ob_clean();

		echo "$content\n<!-- [".
				$controller->getCurrentModule().".".
				$controller->getCurrentAction().
				"] Page was processed in $time seconds -->";
	}
}

?>
