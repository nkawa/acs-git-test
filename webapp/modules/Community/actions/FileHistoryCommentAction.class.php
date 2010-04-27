<?php
// $Id: FileHistoryCommentAction.class.php,v 1.3 2006/12/08 05:06:34 w-ota Exp $

class FileHistoryCommentAction extends BaseAction
{
	// POST
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$target_community_id = $request->getParameter('community_id');
		// �оݤȤʤ�ե����ID�����
		$target_community_folder_id = $request->getParameter('folder_id');
		// �ܺپ����ɽ������ե�����ID�����
		$file_id = $request->getParameter('file_id');
		// �ե���������ID
		$file_history_id = $request->getParameter('file_history_id');

		// form
		$form = $request->ACSGetParameters();


		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_community_row = ACSCommunity::get_community_row($target_community_id);
		// �ե�����������
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
												  $acs_user_info_row,
												  $target_community_folder_id);
		$folder_obj = $community_folder_obj->get_folder_obj();


		// �ե�����������
		$file_obj = ACSFile::get_file_info_instance($file_id);

		// �ե�����θ����ϰϤǥ�����������
		if (!$community_folder_obj->has_privilege($target_community_row)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �ե��������򥳥��Ȥ���Ͽ����
		if ($form['comment'] != '') {
			// �ե���������1�����Ͽ����Ƥ��ʤ�����"����"����Ͽ����
			$file_history_row_array = ACSFileHistory::get_file_history_row_array($file_id);
			if (count($file_history_row_array) == 0) {
				$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
				$file_history_id = ACSFileHistory::set_file_history($file_info_row, $file_info_row['entry_user_community_id'], '', ACSMsg::get_mst('file_history_operation_master','D0101'));
			}

			$ret = ACSFileHistoryComment::set_file_history_comment($file_history_id, $acs_user_info_row['user_community_id'], $form['comment']);
		}

		// �ե�����ܺپ��������
		$file_detail_url = $this->getControllerPath('Community', 'FileDetail');
		$file_detail_url .= '&community_id=' . $target_community_id;
		$file_detail_url .= '&file_id=' . $file_id;
		$file_detail_url .= '&folder_id=' . $target_community_folder_id;
		header("Location: $file_detail_url");
	}

	function isSecure () {
		return false;
	}
	
	function getRequestMethods () {
		return Request::POST;
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* ɬ�ܥ����å� */
		parent::regValidateName($validatorManager, 
				"comment", 
				true, 
				ACSMsg::get_msg('Community', 'FileHistoryCommentAction.class.php', 'M001'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		// ��ư�������������ƤӽФ�
		$controller->forward('Community', 'FileDetail');
	}
}

?>
