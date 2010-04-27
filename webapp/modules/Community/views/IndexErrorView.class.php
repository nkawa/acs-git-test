<?php
/**
 *コミュニティトップページ (エラー)
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/03/23 10:04:00 $
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
