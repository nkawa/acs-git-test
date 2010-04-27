<?php
// $Id: StaticIndexAction.class.php,v 1,0 2007/03/01 09:01:37 w-ota Exp $

class StaticIndexAction extends BaseAction
{
	// GET
	function execute() {

		$context = &$this->getContext();
		$controller = $context->getController();
		$user = $context->getUser();
		$request = $context->getRequest();

		$pagefile = ACS_PAGES_DIR . "index.html." . ACSMsg::get_lang();
		$lockfile = $pagefile.".locked";

		// ��Ū�ե�����񤭴�����ξ��(0.5���Ԥ�)
		if (is_readable($lockfile)) {
			usleep(500000);
		}

		// �񤭴�����Ǥʤ�����Ū�ե����뤬¸�ߤ�����
		if (!is_readable($lockfile) && is_readable($pagefile)) {

			// ��Ū�ե�����������֤�ͭ�������ϰ���ξ��
			if ((time() - filemtime($pagefile)) <= ACS_PAGES_EFFECTIVE_SEC) {

				// ��Ū�ȥåפ�ɸ�����
				mb_http_output('pass');
				readfile($pagefile);
				return;

			}
		}
		$request->setAttribute('force_realtime', 1);
		$controller->forward("Public", "Index");
	}

	/**
	 * ǧ�ڥ����å���Ԥ���
	 * ����������¹Ԥ������ˡ�ǧ�ڥ����å���ɬ�פ����ꤹ��
	 * @access  public
	 * @return  boolean ǧ�ڥ����å�̵ͭ��true:ɬ�ס�false:���ס�
	 */
	public function isSecure()
	{
		return false;
	}
}

?>
