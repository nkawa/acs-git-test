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

		// �ù�
		foreach ($new_open_diary_row_array as $index => $new_open_diary_row) {
			// ��ƼԤΥȥåץڡ���URL
			$new_open_diary_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $new_open_diary_row['user_community_id'];
			// ��ƼԤμ̿�
			$new_open_diary_row_array[$index]['image_url'] = ACSUser::get_image_url($new_open_diary_row['user_community_id'], 'thumb');
			// �������꡼������URL
			$new_open_diary_row_array[$index]['diary_comment_url'] = $this->getControllerPath(DEFAULT_MODULE, 'DiaryComment') . '&id=' . $new_open_diary_row['user_community_id'] . '&diary_id=' . $new_open_diary_row['diary_id'];
			$new_open_diary_row_array[$index]['post_date'] = ACSLib::convert_pg_date_to_str($new_open_diary_row['post_date']);
		}
		// set
		$this->setAttribute('new_open_diary_row_array', $new_open_diary_row_array);

		// �ƥ�ץ졼�Ȥ򥻥åȤ���
		$this->setTemplate('NewOpenDiary.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("NewOpenDiary", $this->render());

		return parent::execute();

	}

}

?>