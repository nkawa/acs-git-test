<?php
/**
 * �ե���� ��ư����
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2006/11/20 08:44:25 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class MoveFolderAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_user_community_id = $request->getParameter('id');
		// �оݤȤʤ�ե����ID�����
		$target_user_community_folder_id = $request->getParameter('folder_id');

		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// �ե�����������
		$user_folder_obj = new ACSUserFolder(
				$target_user_community_id,
				$acs_user_info_row,
				$target_user_community_folder_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);

		// ��ư��ե����ID
		$move_target_folder_id = $request->getParameter('selected_move_folder_id');

		/* -------- */
		/* ��ư���� */
		/* -------- */
		ACSDB::_do_query("BEGIN");

		// ��ư�褬�롼�ȥե�����ξ��ϡ������ϰϤ򥻥åȤ���ɬ�פ����뤿�ᡢ
		// �롼�ȥե�����ξ����������Ƥ���
		$root_folder_obj = ACSFolder::get_folder_instance(
				$user_folder_obj->get_root_folder_row($user_folder_obj->get_community_id()));

		// �ե����
		$folder_row_array = array();
		$selected_folder_id_array = $request->getParameter('selected_folder');
		if ($selected_folder_id_array) {
			foreach ($selected_folder_id_array as $folder_id) {
				// ��ư����ե��������
				$_folder_obj = $user_folder_obj->folder_obj->get_folder_obj($folder_id);

				// ��ư����
				$ret = $_folder_obj->move_folder($move_target_folder_id);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					print "ERROR: Move folder failed.";
					exit;
				}

				// �����ϰϤ򹹿�
				if ($move_target_folder_id == $root_folder_obj->get_folder_id()) {
					// �롼�ȥե�����ذ�ư�ξ�硢�����ϰϤ򥻥å�
					$new_open_level_code = $user_folder_obj->folder_obj->get_open_level_code();
					$new_trusted_community_row_array = $user_folder_obj->folder_obj->get_trusted_community_row_array();

				} else {
					// �롼�ȥե�����ʳ��ذ�ư�ξ�硢�����ϰϤ�ꥻ�å�
					$new_open_level_code = "";
					$new_trusted_community_row_array = array();
				}
				$ret = $_folder_obj->update_open_level_code($new_open_level_code, $new_trusted_community_row_array);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					print "ERROR: Move folder failed.";
					exit;
				}

				// �롼�ȥե�����ʳ��ؤذ�ư�ξ�硢�ץåȲ��(=�ץåȾ�������ƺ��)
				if ($move_target_folder_id != $root_folder_obj->get_folder_id()) {
					$ret = ACSFolderModel::delete_put_community_by_folder_id($_folder_obj->get_folder_id());
					if (!$ret) {
						ACSDB::_do_query("ROLLBACK");
						print "ERROR: Move folder failed.";
						exit;
					}
				}
			}
		}

		// �ե�����
		$file_row_array = array();
		$selected_file_id_array = $request->getParameter('selected_file');
		if ($selected_file_id_array) {
			foreach ($selected_file_id_array as $file_id) {

				// ��ư����
				$file_obj = $user_folder_obj->folder_obj->get_file_obj($file_id);
				$ret = $user_folder_obj->folder_obj->move_file($file_obj, $move_target_folder_id);
				//$ret = $_file_obj->rename_display_file_name($new_file_name);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK;");
					print "ERROR: Move file failed.";
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

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* ɬ�ܥ����å� */
		parent::regValidateName($validatorManager, 
				"selected_move_folder_id", 
				true, 
				ACSMsg::get_msg('User', 'MoveFolderAction.class.php', 'M001'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		// ��ư�������������ƤӽФ�
		$controller->forward('User', 'MoveFolderList');
	}

	function isSecure () {
		return false;
	}

	function getCredential() {		
		return array('USER_PAGE_OWNER');
	}
}
?>
