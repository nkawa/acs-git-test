<?php
/**
 * �������꡼ ������ �����������������󥯥饹
 * DeleteDiaryCommentAction.class.php
 *
 * @author  $Author: w-ota $
 * @revision ver1.0 2006/03/02
 */
// $Id: DeleteDiaryCommentAction.class.php,v 1.5 2006/11/20 08:44:25 w-ota Exp $

class DeleteDiaryCommentAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// get parameter
		$target_community_id       = $request->getParameter('id');
		$diary_id                  = $request->getParameter('diary_id');
		$diary_comment_id          = $request->getParameter('diary_comment_id');

		$comment_back_url = $this->getControllerPath('User','DiaryComment');
			$comment_back_url = $comment_back_url . '&id=' . $target_community_id .'&diary_id=' .$diary_id ;

		$delete_diary_comment_url = $this->getControllerPath('User','DeleteDiaryComment');
			$delete_diary_comment_url = $delete_diary_comment_url . '&id=' .$target_community_id . '&diary_id=' . $diary_id .'&diary_comment_id=' .$diary_comment_id;

		$request->setAttribute('delete_diary_comment_url', $delete_diary_comment_url);
		$request->setAttribute('comment_back_url', $comment_back_url);
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
		$diary_comment_id          = $request->getParameter('diary_comment_id');
		//��������Ȥϡ�id������Ǽ����Ϥ�
		$diary_comment_id_array = array();
		array_push($diary_comment_id_array,$diary_comment_id);
		//�ե��������ơ��֥�Υǡ������
		$ret =ACSDiary::delete_diary_comment($diary_comment_id_array);
		if (!$ret) {
			echo "ERROR: Delete diary comment failed.";
		}

		//ɽ��
		$diary_change_url = $this->getControllerPath('User','DiaryComment');
		$diary_change_url .= '&id=' . $target_community_id .'&diary_id=' .$diary_id ;
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
		
		return array('EXECUTE');
	}

	function get_execute_privilege (&$controller, &$request, &$user) {
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �ܿͤ�OK
		if ($user->hasCredential('USER_PAGE_OWNER')) {
			return true;
		}

		// ������ܿͤ�OK
		$diary_comment_row = ACSDiary::get_diary_comment_row($request->getParameter('diary_comment_id'));
		if ($acs_user_info_row['user_community_id'] == $diary_comment_row['user_community_id']) {
			return true;
		}

		return false;
	}
}
?>
