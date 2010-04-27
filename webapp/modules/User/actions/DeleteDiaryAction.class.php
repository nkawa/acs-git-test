<?php
/**
 * �������꡼�� �����������������󥯥饹
 * DeleteDiaryAction.class.php
 *
 * @author  $Author: w-ota $
 * @revision ver1.0 2006/03/02
 */
// $Id: DeleteDiaryAction.class.php,v 1.4 2006/11/20 08:44:25 w-ota Exp $

class DeleteDiaryAction extends BaseAction
{
	// GET
	function getDefaultView() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		// get parameter
		$target_community_id	   = $request->getParameter('id');
		$diary_id				  = $request->getParameter('diary_id');

		$back_url = $this->getControllerPath('User','Diary');
		$back_url .= '&id=' . $target_community_id;
		
		$delete_diary_url = $this->getControllerPath('User','DeleteDiary') .'&id=' . $target_community_id .'&diary_id=' .$diary_id;

		$request->setAttribute('delete_diary_url', $delete_diary_url);
		$request->setAttribute('back_url', $back_url);
		// ɽ��
		return View::SUCCESS;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		//���������Ԥ�
		$target_community_id	= $request->getParameter('id');
		$diary_id			= $request->getParameter('diary_id');
		//�ե��������ơ��֥�Υǡ������
		$diary_obj = ACSDiary::get_diary_row($diary_id);
		if(!$diary_obj){
			echo "�����������Ǥ��ޤ���Ǥ���";
		}
		$ret =ACSDiary::delete_diary($diary_id);
		if (!$ret) {
			echo "ERROR: Delete diary failed.";
		}

		//ɽ��
		$diary_change_url = $this->getControllerPath('User','Diary');
	$diary_change_url .= '&id=' . $target_community_id;
		header("Location: $diary_change_url");
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		return array('USER_PAGE_OWNER');
	}
}
?>
