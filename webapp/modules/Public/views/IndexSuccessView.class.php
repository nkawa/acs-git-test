<?php
// $Id: IndexView::SUCCESS.class.php,v 1.1 2006/03/10 11:45:41 w-ota Exp $

class IndexSuccessView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$controller = $context->getController();
		
		// ACS�桼����������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		/*--------------- ����������� ---------------*/
		// ���ߤΥ������⡼�ɤ����
		$renderMode = $controller->getRenderMode();

		//�������⡼�ɤ��� �ʲ��̽��Ϥ򥪥դˤ��Ƥ��
		$controller->setRenderMode(View::RENDER_VAR);
		$this->inlineFlg = true;

		// �ե����¦��Ƚ�Ǥ���
		$request->setAttribute("inline_mode", "1");

		// �ե���ɤ���
		
		// 1.����ѥ֥�å���꡼��
		$controller->forward("Public", "NewPressRelease");
		$this->setAttribute("NewPressRelease", $request->getAttribute("NewPressRelease"));

		// 2.����������꡼
		$controller->forward("Public", "NewOpenDiary");
		$this->setAttribute("NewOpenDiary", $request->getAttribute("NewOpenDiary"));

		// 3.���女�ߥ�˥ƥ�
		$controller->forward("Public", "NewCommunity");
		$this->setAttribute("NewCommunity", $request->getAttribute("NewCommunity"));
		
		// 4.���ߥ�˥ƥ���󥭥�
		$controller->forward("Public", "CommunityRanking");
		$this->setAttribute("CommunityRanking", $request->getAttribute("CommunityRanking"));

		// 5.�桼����󥭥�
		$controller->forward("Public", "UserRanking");
		$this->setAttribute("UserRanking", $request->getAttribute("UserRanking"));

		// �������⡼�ɤ򸵤��᤹
		$controller->setRenderMode($renderMode); 
		$this->inlineFlg = false;

		/*----------------------------------------------*/
		
		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('Index.php');

		return parent::execute();
	}
}

?>
