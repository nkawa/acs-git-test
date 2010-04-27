<?php
// $Id: FileHistoryCommentAction.class.php,v 1.5 2006/12/08 05:06:42 w-ota Exp $

class FileHistoryCommentAction extends BaseAction
{
	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_user_community_id = $request->getParameter('id');
		$target_user_community_folder_id = $request->getParameter('folder_id');
		$file_id = $request->getParameter('file_id');
		$file_history_id = $request->getParameter('file_history_id');

		// form
		$form = $request->ACSGetParameters();


		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// �ե�����������
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
				$acs_user_info_row,
				$target_user_community_folder_id);
		$folder_obj = $user_folder_obj->get_folder_obj();


		// �ե�����θ����ϰϤǥ�����������
		if (!$user_folder_obj->has_privilege($target_user_info_row)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
		}

		// �롼�ȥե����ľ���Υե�������ܿͰʳ����������Բ�
		$privilege_array = $this->getCredential();
		if ($folder_obj->get_is_root_folder() && !in_array('USER_PAGE_OWNER', $privilege_array)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
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
		$file_detail_url = $this->getControllerPath('User', 'FileDetail');
		$file_detail_url .= '&id=' . $target_user_community_id;
		$file_detail_url .= '&file_id=' . $file_id;
		$file_detail_url .= '&folder_id=' . $target_user_community_folder_id;
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
				ACSMsg::get_msg('User', 'FileHistoryCommentAction.class.php', 'M001'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		// ��ư�������������ƤӽФ�
		$controller->forward('User', 'FileDetail');
	}
	
	function getCredential () {
		return array('USER_PAGE_OWNER');
	}
}

?>
