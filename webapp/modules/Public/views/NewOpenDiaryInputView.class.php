<?php

class NewOpenDiaryInputView extends BaseView
{
	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// get
		$new_open_diary_row_array = $request->getAttribute('new_open_diary_row_array');

		// 加工
		foreach ($new_open_diary_row_array as $index => $new_open_diary_row) {
			// 投稿者のトップページURL
			$new_open_diary_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $new_open_diary_row['user_community_id'];
			// 投稿者の写真
			$new_open_diary_row_array[$index]['image_url'] = ACSUser::get_image_url($new_open_diary_row['user_community_id'], 'thumb');
			// ダイアリーコメントURL
			$new_open_diary_row_array[$index]['diary_comment_url'] = $this->getControllerPath(DEFAULT_MODULE, 'DiaryComment') . '&id=' . $new_open_diary_row['user_community_id'] . '&diary_id=' . $new_open_diary_row['diary_id'];
			$new_open_diary_row_array[$index]['post_date'] = ACSLib::convert_pg_date_to_str($new_open_diary_row['post_date']);
		}
		// set
		$this->setAttribute('new_open_diary_row_array', $new_open_diary_row_array);

		// テンプレートをセットする
		$this->setTemplate('NewOpenDiary.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("NewOpenDiary", $this->render());

		return parent::execute();

	}

}

?>