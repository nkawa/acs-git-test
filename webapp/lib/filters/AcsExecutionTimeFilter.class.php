<?php

/**
 * �¹Ի��ַ�¬�ե��륿�����饹
 *
 * @author  teramoto
 * @version $Revision: 1.1 $
 *
 */

class AcsExecutionTimeFilter extends Filter
{

	/**
	 * ���󥹥ȥ饯��
	 */
	function AcsExecutionTimeFilter ()
	{
	}

	/**
	 * �¹Ի��ַ�¬�ե��륿���μ¹�
	 *
	 * @param FilterChain Mojavi FilterChain ���󥹥���
	 * @param Controller  Mojavi Controller ���󥹥���
	 * @param Request	 Mojavi Request ���󥹥���
	 * @param User		Mojavi User ���󥹥���
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
