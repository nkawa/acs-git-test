<?php
/**
 * ダイアリー親　削除画面 Viewクラス
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

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DeleteDiary.tpl.php');
		$back_url = $request->getAttribute('back_url');
		$delete_diary_url = $request->getAttribute('delete_diary_url');

		// parameter set
		$this->setAttribute('delete_diary_url', $delete_diary_url);
		$this->setAttribute('back_url', $back_url);

		//表示
		return parent::execute();
	}
}
?>
