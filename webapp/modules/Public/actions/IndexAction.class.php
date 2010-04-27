<?php
// $Id: IndexAction.class.php,v 1.3 2007/03/01 09:01:37 w-ota Exp $

class IndexAction extends BaseAction
{
	// GET
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$controller = $context->getController();

//		$controller = $context->getController();
//		return View::SUCCESS;


		// ưŪ�ȥåץڡ����ξ��
		if ($_SERVER['HTTP_ACS_AGENT'] == 'create_statictop'
			|| array_key_exists('realtime',$_REQUEST)
			|| $request->getAttribute('force_realtime') 
			|| $acs_user_info_row['is_login_user']
			) {

			if ($_SERVER['HTTP_ACS_AGENT'] == 'create_statictop') {
				ini_set("url_rewriter.tags",''); // url�ѥ�᡼�����PHPSESSID��ʤ�
			}

			$controller = $context->getController();
			return View::SUCCESS;

		} else {

			$controller->forward('Public','StaticIndex');
			//return VIEW_NONE;
		}


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
