<?php
// $Id: NewCommunityView_inline.class.php,v 1.3 2006/06/08 05:51:52 w-ota Exp $

class NewCommunityInputView extends BaseView
{
	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();

		// get
		$new_community_row_array = $request->getAttribute('new_community_row_array');

		// 加工
		foreach ($new_community_row_array as $index => $new_community_row) {
			// コミュニティのトップページURL
			$new_community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $new_community_row['community_id'];
			// コミュニティの写真
			$new_community_row_array[$index]['image_url'] = ACSCommunity::get_image_url($new_community_row['community_id'], 'thumb');

			// コミュニティメンバ数
			$new_community_row_array[$index]['community_member_num'] = ACSCommunity::get_community_member_num($new_community_row['community_id']);
		}

		// set
		$this->setAttribute('new_community_row_array', $new_community_row_array);

		// テンプレートをセットする
		$this->setTemplate('NewCommunity.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("NewCommunity", $this->render());

		return parent::execute();
		
	}
}

?>
