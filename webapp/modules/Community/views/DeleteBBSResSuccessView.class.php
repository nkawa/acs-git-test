<?php
/**
 * �Ǽ��ġ���ƺ������ View���饹
 * @package  acs/webapp/modules/Community/views
 * DeleteBBSResView_confirm
 * @author   akitsu
 * @since    PHP 4.0
 * @revision ver1.0 2006/02/23 
 */

class DeleteBBSResSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('DeleteBBSRes.tpl.php');
		$back_url = $request->getAttribute('back_url');
		$delete_bbs_url = $request->getAttribute('delete_bbs_res_url');
		// parameter set
		$this->setAttribute('delete_bbs_res_url', $delete_bbs_url);
		$this->setAttribute('back_url', $back_url);

		//ɽ��
		return parent::execute();
	}
}
?>
