<?php
/**
 * �������꡼ �����ȡ�������� View���饹
 * @package  acs/webapp/modules/User/views
 * DeleteCommentDiaryView
 * @author   akitsu
 * @since    PHP 4.0
 * @revision ver1.0 2006/03/02
 */
// $Id: DeleteDiaryCommentView_confirm.class.php,v 1.1 2006/03/02 10:13:50 z-akitsu Exp $
class DeleteDiaryCommentSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('DeleteDiaryComment.tpl.php');
		$comment_back_url = $request->getAttribute('comment_back_url');
		$delete_diary_comment_url = $request->getAttribute('delete_diary_comment_url');

		// parameter set
		$this->setAttribute('delete_diary_comment_url', $delete_diary_comment_url);
		$this->setAttribute('comment_back_url', $comment_back_url);

		//ɽ��
		return parent::execute();
	}
}
?>
