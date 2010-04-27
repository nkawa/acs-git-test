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

		// �ù�
		foreach ($new_community_row_array as $index => $new_community_row) {
			// ���ߥ�˥ƥ��Υȥåץڡ���URL
			$new_community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $new_community_row['community_id'];
			// ���ߥ�˥ƥ��μ̿�
			$new_community_row_array[$index]['image_url'] = ACSCommunity::get_image_url($new_community_row['community_id'], 'thumb');

			// ���ߥ�˥ƥ����п�
			$new_community_row_array[$index]['community_member_num'] = ACSCommunity::get_community_member_num($new_community_row['community_id']);
		}

		// set
		$this->setAttribute('new_community_row_array', $new_community_row_array);

		// �ƥ�ץ졼�Ȥ򥻥åȤ���
		$this->setTemplate('NewCommunity.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("NewCommunity", $this->render());

		return parent::execute();
		
	}
}

?>
