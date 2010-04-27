<?php
// �ե����빹��
// $Id: UpdateFileAction.class.php,v 1.4 2006/12/18 07:42:11 w-ota Exp $

class UpdateFileAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_community_id = $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');
		$file_id = $request->getParameter('file_id');

		// ������������ // �ץåȥե�������ե������NG
		$file_obj = ACSFile::get_file_info_instance($file_id);
		if ($file_obj->get_owner_community_id() != $target_community_id) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �ޥ���
		$file_category_master_array = ACSDB::get_master_array('file_category');
		$file_contents_type_master_array = ACSDB::get_master_array('file_contents_type');

		// �ե����륫�ƥ��ꥳ���ɤ��ȤΥե����륳��ƥ�ļ��̤�Ϣ��������������
		$file_contents_type_master_row_array_array = ACSFileDetailInfo::get_file_contents_type_master_row_array_array();

		// set
		$request->setAttribute('file_contents_type_master_row_array_array', $file_contents_type_master_row_array_array);
		$request->setAttribute('file_category_master_array', $file_category_master_array);
		$request->setAttribute('file_contents_type_master_array', $file_contents_type_master_array);

		return View::INPUT;
	}

	// POST
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_community_id = $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');
		$file_id = $request->getParameter('file_id');

		// ������������ // �ץåȥե�������ե������NG
		$file_obj = ACSFile::get_file_info_instance($file_id);
		if ($file_obj->get_owner_community_id() != $target_community_id) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// form
		$form = $request->ACSGetParameters();

		$ret = false;
		// �ե����빹������
		if ($_FILES['new_file']['tmp_name'] != '') {
			// �ե�����obj
			$file_obj = ACSFile::get_upload_file_info_instance_for_update($_FILES['new_file'],
					$target_community_id,
					$acs_user_info_row['user_community_id'],
					$file_id
			);

			// �ե����obj
			$community_folder_obj = new ACSCommunityFolder($target_community_id,
					$acs_user_info_row,
					$target_community_folder_id);
			$folder_obj = $community_folder_obj->get_folder_obj();

			// �ե���������1�����Ͽ����Ƥ��ʤ�����"����"����Ͽ����
			$file_history_row_array = ACSFileHistory::get_file_history_row_array($file_id);
			if (count($file_history_row_array) == 0) {
				$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
				$file_history_id = ACSFileHistory::set_file_history($file_info_row, $file_info_row['entry_user_community_id'], '', ACSMsg::get_mst('file_history_operation_master','D0101'));
			}

			// file_info����, �ե�������¸
			$ret = $folder_obj->update_file($file_obj);
		}

		if (!$ret) {
			print "ERROR: �ե����륢�åץ��ɤ˼��Ԥ��ޤ���";
		}

		// �ե��������������Ͽ
		if ($ret) {
			$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
			$ret = ACSFileHistory::set_file_history(
					$file_info_row, 
					$acs_user_info_row['user_community_id'], 
					$form['comment'], 
					ACSMsg::get_mst('file_history_operation_master','D0201'));

			// 2007.12 �ɲ�
			// ML���Υ����å��������ML�˥᡼����������
			// ���ߥ�˥ƥ�����μ���
			$send_announce_mail		= $request->getParameter('send_announce_mail');
			if($send_announce_mail == "t"){
				ACSCommunityMail::send_fileupload_mail(
							$target_community_id, $acs_user_info_row, $folder_obj, $file_obj);
			}

		}

		// �ե�����ܺپ��������
		$file_detail_url = $this->getControllerPath('Community', 'FileDetail');
		$file_detail_url .= '&community_id=' . $target_community_id;
		$file_detail_url .= '&file_id=' . $file_id;
		$file_detail_url .= '&folder_id=' . $target_community_folder_id;
		header("Location: $file_detail_url");
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_MEMBER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// ���ߥ�˥ƥ����Ф�OK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}
		return false;
	}

}

?>
