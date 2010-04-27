<?php
// $Id: EditBBSResInputView.class.php,v 1.1 2006/06/08 05:53:03 w-ota Exp $

class EditBBSResInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$community_row = $request->getAttribute('community_row');
		$bbs_res_row = $request->getAttribute('bbs_res_row');
		$form = $request->getAttribute('form');

		// ���ϥ��顼���Υǡ�������
		if (is_array($form)) {
			$bbs_res_row['subject'] = $form['subject'];
			$bbs_res_row['body'] = $form['body'];
		}

		// form action ��ǧ���̤ؤ�����
		$action_url  = $this->getControllerPath('Community', 'EditBBSRes')
			 . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_res_row['bbs_id'] . '&bbs_res_id=' . $bbs_res_row['bbs_res_id'];

		// ���ߥ�˥ƥ��ȥåץڡ�����URL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];
		// BBS URL
		$bbs_url = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_row['community_id'];
		// BBSRes URL
		$bbs_res_url = $this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_res_row['bbs_id'];

		// set
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('community_row', $request->getAttribute('community_row'));
		$this->setAttribute('bbs_res_row', $bbs_res_row);
		$this->setAttribute('action_url', $action_url);

		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('bbs_url', $bbs_url);
		$this->setAttribute('bbs_res_url', $bbs_res_url);

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('EditBBSRes.tpl.php');
		
		return parent::execute();
	}
}

?>
