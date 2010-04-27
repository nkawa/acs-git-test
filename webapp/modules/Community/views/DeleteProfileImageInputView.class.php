<?php
/**
 * プロフィール写真削除画面 Viewクラス
 * @package  acs/webapp/modules/Community/views
 * DeleteProfileImageView::INPUT
 * @author   akitsu
 * @since    PHP 4.0
 * @revision ver1.0 2006/02/16 
 */

class DeleteProfileImageInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DeleteProfileImage.tpl.php');
		$back_url = $request->getAttribute('back_url');

		// parameter set
		$this->setAttribute('delete_image_url', $delete_image_url);
		$this->setAttribute('back_url', $back_url);

		//表示
		return parent::execute();
	}
}
?>
