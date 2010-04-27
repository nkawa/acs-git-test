<?php
/**
 * マイページ機能　Viewクラス
 * デザイン選択画面
 * @package  acs/webapp/modules/User/views
 * ViewSelectDesign_success
 * @author   teramoto
 * @since	PHP 4.0
 */
// $Id: SelectDesignView::INPUT.class.php,v 1.1 2007/03/27 02:12:45 w-ota Exp $

class SelectDesignInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row =& $user->getAttribute('acs_user_info_row');
		$select_design_row_array =& $request->getAttribute('select_design_row_array');
		$user_community_id = $request->getAttribute('user_community_id');
		$style_url = $request->getAttribute('style_url');

		// top
		$top_page_url = $this->getControllerPath('User', 'Index') . 
							'&id=' . $user_community_id;

		// set
		$this->setAttribute('style_url', $style_url);
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);
		$this->setAttribute('top_page_url', $top_page_url);
		$this->setAttribute('select_design_row_array', $select_design_row_array);
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));
		$this->setAttribute('selection_css', $request->getAttribute('selection_css'));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SelectDesign.tpl.php');

		return parent::execute();
	}
}

?>
