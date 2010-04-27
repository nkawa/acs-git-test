<?php
/**
 * マイページ機能　Viewクラス
 * 足跡確認画面
 * @package  acs/webapp/modules/User/views
 * ViewFootprint_success
 * @author   teramoto
 * @since	PHP 4.0
 */
// $Id: FootprintCheckView::SUCCESS.class.php,v 1.1 2007/03/27 02:12:45 w-ota Exp $

class FootprintCheckSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$footprint_info_row_array = $request->getAttribute('footprint_info_row_array');
		$user_community_id = $request->getAttribute('user_community_id');

		//top
		$top_page_url = $this->getControllerPath('User', 'Index') . 
							'&id=' . $user_community_id;

		foreach($footprint_info_row_array as $index => $footprint_row){

			// 足跡日付
			$footprint_info_row_array[$index]['post_date_disp'] = 
						ACSLib::convert_pg_date_to_str($footprint_row['post_date']);

			// 足跡をつけたユーザのトップページURL
			$footprint_info_row_array[$index]['visitor_url'] = 
						$this->getControllerPath(DEFAULT_MODULE, 'Index') . 
						'&id=' . $footprint_row['visitor_community_id'];

			// コンテンツ日付
			$footprint_info_row_array[$index]['contents_date_disp'] = 
						"(" . ACSLib::convert_pg_date_to_str($footprint_row['contents_date'], 0, 0) . ")";

		}

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(
						ACSMsg::get_mst('system_config_group','D02'), 'FOOTPRINT_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $footprint_info_row_array, $display_count);

		// set
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);
		$this->setAttribute('top_page_url', $top_page_url);
		$this->setAttribute('footprint_info_row_array', $footprint_info_row_array);
		$this->setAttribute('paging_info', $paging_info);
		
		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('FootprintCheck.tpl.php');

		return parent::execute();
	}
}

?>
