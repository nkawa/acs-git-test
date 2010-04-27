<?php
/**
 * �桼���Υե���� �ե����륢�åץ���
 *
 * @author  $Author: w-ota $
 * @version $Revision: 1.8 $ $Date: 2006/11/20 08:44:25 $
 */
//require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class UploadFileAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_community_id = $request->getParameter('id');
		$target_user_community_folder_id = $request->getParameter('folder_id');

		// ¾�桼���Υǡ����������ʤ��褦�����å�
		if (!$this->get_execute_privilege()) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
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
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row			   = $user->getAttribute('acs_user_info_row');
		$target_user_community_id		= $request->getParameter('id');
		$target_user_community_folder_id = $request->getParameter('folder_id');

		// form
		$form = $request->ACSGetParameters();

		/* �ե����륢�åץ��ɽ��� */

		$ret = 0;
		if ($_FILES['new_file']['tmp_name'] != '') {
			// �ե�����������
			$file_obj = ACSFile::get_upload_file_info_instance($_FILES['new_file'],
					$target_user_community_id,
					$acs_user_info_row['user_community_id']);

			// �ե�����˥ե������ɲý���
			$user_folder_obj = new ACSUserFolder($target_user_community_id,
					$acs_user_info_row,
					$target_user_community_folder_id);
			$folder_obj = $user_folder_obj->get_folder_obj();
			$ret = $folder_obj->add_file($file_obj);
		}

		if (!$ret) {
			print "ERROR: Upload file failed.";
		}

		if ($ret) {
			// ������Ͽ�����ե�����ID
			$file_id = $file_obj->get_file_id();
		}

		// �ե��������������Ͽ
		if ($ret) {
			$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
			$ret = ACSFileHistory::set_file_history(
					$file_info_row, 
					$acs_user_info_row['user_community_id'], 
					$form['comment'], 
					ACSMsg::get_mst('file_history_operation_master','D0101'));
		}

		// �ե�����ܺپ�����Ͽ
		if ($form['file_category_code'] != '' && $ret) {
			$file_contents_type_list_row_array = ACSFileDetailInfo::get_file_contents_type_list_row_array($form['file_category_code']);
			$file_contents_form_array = array();
			foreach ($file_contents_type_list_row_array as $file_contents_type_list_row) {
				$file_contents_form = array(
						'file_id' => $file_id,
						'file_contents_type_code' => $file_contents_type_list_row['file_contents_type_code'],
						'file_contents_value' => $form['file_contents_array'][$file_contents_type_list_row['file_contents_type_code']]
				);
				array_push($file_contents_form_array, $file_contents_form);
			}

			$ret = ACSFileDetailInfo::set_file_detail_info($file_id, $form['file_category_code'], $file_contents_form_array);
		}


		// �ե����ɽ�����������ƤӽФ�
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$folder_action = $this->getControllerPath('User', 'Folder');
		$folder_action .= '&id=' . $target_user_community_id;
		$folder_action .= '&folder_id=' . $target_user_community_folder_id;

		header("Location: $folder_action");
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �������桼�����ܿͰʳ���NG
		if ($user->hasCredential('PUBLIC_USER')
				 || !$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
	}
}
?>
