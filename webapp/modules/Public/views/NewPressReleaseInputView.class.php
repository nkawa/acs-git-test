<?php
// $Id: NewPressReleaseView_inline.class.php,v 1.1 2006/03/10 11:45:41 w-ota Exp $

class NewPressReleaseInputView extends BaseView
{
	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$new_bbs_for_press_release_row_array = $request->getAttribute('new_bbs_for_press_release_row_array');

		// 加工
		foreach ($new_bbs_for_press_release_row_array as $index => $new_bbs_row) {
			$new_bbs_for_press_release_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $new_bbs_row['community_id'];
			$new_bbs_for_press_release_row_array[$index]['bbs_res_url'] = $this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $new_bbs_row['community_id'] . '&bbs_id=' . $new_bbs_row['bbs_id'];
			if ($new_bbs_row['file_id']) {
				$new_bbs_for_press_release_row_array[$index]['file_url'] = ACSBBSFile::get_image_url($new_bbs_row['bbs_id'], 'thumb'); // サムネイル
				$new_bbs_for_press_release_row_array[$index]['file_url_alink'] = ACSBBSFile::get_image_url($new_bbs_row['bbs_id'], ''); // ポップアップ用
			}
			$new_bbs_for_press_release_row_array[$index]['post_date'] = ACSLib::convert_pg_date_to_str($new_bbs_row['post_date'], 1, 0);
		}
	
		// set
		$this->setAttribute('new_bbs_for_press_release_row_array', $new_bbs_for_press_release_row_array);

		// テンプレートをセットする
		$this->setTemplate('NewPressRelease.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("NewPressRelease", $this->render());

		return parent::execute();

	}
}

?>
