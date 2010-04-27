<?php
/**
 * �ե���� ���
 *
 * @author  kuwayama
 * @version $Revision: 1.5 $ $Date: 2006/11/20 08:44:25 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class DeleteFolderAction extends BaseAction
{
	// ��ǧ����ɽ��
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		$user_folder_obj = $request->getAttribute('user_folder_obj');

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ����оݤ����
		// �ե����
		$selected_folder_obj_array = array();   // View �ˤ錄������оݤΥե����
		$selected_folder_row_array = array();
		$selected_folder_array = $request->getParameter('selected_folder');
		if ($selected_folder_array) {
			foreach ($selected_folder_array as $selected_folder_id) {
				$_selected_folder_obj = $user_folder_obj->folder_obj->get_folder_obj($selected_folder_id);
				array_push($selected_folder_obj_array, $_selected_folder_obj);
			}
		}

		// �ե�����
		$selected_file_obj_array = array();   // View �ˤ錄������оݤΥե�����
		$selected_file_row_array = array();
		$selected_file_array = $request->getParameter('selected_file');
		if ($selected_file_array) {
			foreach ($selected_file_array as $selected_file_id) {
				$_selected_file_obj = $user_folder_obj->folder_obj->get_file_obj($selected_file_id);
				array_push($selected_file_obj_array, $_selected_file_obj);
			}
		}

		// set
		$request->setAttribute('selected_folder_obj_array', $selected_folder_obj_array);
		$request->setAttribute('selected_file_obj_array', $selected_file_obj_array);

		return View::SUCCESS;
	}

	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		// ɬ�ܥ����å�
		//	Validator �ǤǤ��ʤ������å��Ϥ����ǹԤ�
		if (!$request->getParameter('selected_folder') && !$request->getParameter('selected_file')) {
			// ���顼�ξ�硢������λ
			//return $this->setError($controller, $request, $user, 'selected_folder', '�������ե�������ե���������򤷤Ƥ���������');
			return $this->setError($controller, $request, $user, 'selected_folder', 
					ACSMsg::get_msg('User', 'DeleteFolderAction.class.php' ,'M001'));
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_user_community_id = $request->getParameter('id');
		// �оݤȤʤ�ե����ID�����
		$target_user_community_folder_id = $request->getParameter('folder_id');

		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// �ե�����������
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
											 $acs_user_info_row,
											 $target_user_community_folder_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);

		/* ------------ */
		/* ��ǧ����ɽ�� */
		/* ------------ */
		if ($request->getParameter('action_type') == 'confirm') {
			return $this->getDefaultView();
		}

		/* -------- */
		/* ������� */
		/* -------- */
		elseif ($request->getParameter('action_type') == 'delete') {
			ACSDB::_do_query("BEGIN");
			// �ե����
			$folder_row_array = array();
			$delete_folder_id_array = $request->getParameter('selected_folder');
			if ($delete_folder_id_array) {
				foreach ($delete_folder_id_array as $folder_id) {
					// �������
					$_folder_obj = $user_folder_obj->folder_obj->get_folder_obj($folder_id);
					$ret = $user_folder_obj->delete_folder($_folder_obj);
					if (!$ret) {
						ACSDB::_do_query("ROLLBACK;");
						print "ERROR: Remove folder failed.";
						exit;
					}
				}
			}

			// �ե�����
			$file_row_array = array();
			$delete_file_id_array = $request->getParameter('selected_file');
			if ($delete_file_id_array) {
				foreach ($delete_file_id_array as $file_id) {
					// �������
					$_file_obj = $user_folder_obj->folder_obj->get_file_obj($file_id);
					$ret = $_file_obj->delete_file();
					if (!$ret) {
						ACSDB::_do_query("ROLLBACK;");
						print "ERROR: Remove file failed.";
						exit;
					}
				}
			}

			ACSDB::_do_query("COMMIT;");

			// �ե����ɽ�����������ƤӽФ�
			$folder_action  = $this->getControllerPath('User', 'Folder');
			$folder_action .= '&id=' . $target_user_community_id;
			$folder_action .= '&folder_id=' . $target_user_community_folder_id;

			header("Location: $folder_action");
		}
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		/* ���顼��å������򥻥å����˥��å� */
		$this->sendError($controller, $request, $user);

		// �ե����ɽ�����������ƤӽФ�
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_community_id = $request->getParameter('id');
		$target_user_community_folder_id = $request->getParameter('folder_id');

		$folder_action = $this->getControllerPath('User', 'Folder');
		$folder_action .= '&id=' . $target_user_community_id;
		$folder_action .= '&folder_id=' . $target_user_community_folder_id;
		header("Location: $folder_action");
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
