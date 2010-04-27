<?php
/**
 * マイページトップページ (エラー)
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/03/13 08:30:33 $
 */
class IndexErrorView extends BaseView
{
	function execute() {

		$this->setScreenId("0001");
		$this->setTemplate('Index_error.tpl.php');
		return parent::execute();
	}
}
?>
