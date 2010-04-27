<?php
/**
 * �������꡼�ơ�������� View���饹
 * @package  acs/webapp/modules/User/views
 * DeleteDiaryView
 * @author   akitsu
 * @since    PHP 4.0
 * @revision ver1.0 2006/03/02
 */
// $Id: DeleteDiaryView_confirm.class.php,v 1.1 2006/03/02 10:13:50 z-akitsu Exp $
class DeleteDiarySuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('DeleteDiary.tpl.php');
		$back_url = $request->getAttribute('back_url');
		$delete_diary_url = $request->getAttribute('delete_diary_url');

		// parameter set
		$this->setAttribute('delete_diary_url', $delete_diary_url);
		$this->setAttribute('back_url', $back_url);

		//ɽ��
		return parent::execute();
	}
}
?>
