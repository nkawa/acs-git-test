<?php
/**
 * �ե���� ��ư����
 *
 * @author  kuwayama
 * @version $Revision: 1.5 $ $Date: 2007/03/27 02:12:36 $
 */
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class MoveFolderAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_community_id = $request->getParameter('community_id');
		// �оݤȤʤ�ե����ID�����
		$target_community_folder_id = $request->getParameter('folder_id');

		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_community_info_row = ACSCommunity::get_community_row($target_community_id);
		// �ե�����������
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
													   $acs_user_info_row,
													   $target_community_folder_id);

		// set
		$request->setAttribute('target_community_info_row', $target_community_info_row);
		$request->setAttribute('community_folder_obj', $community_folder_obj);

		// ��ư��ե����ID
		$move_target_folder_id = $request->getParameter('selected_move_folder_id');

		/* -------- */
		/* ��ư���� */
		/* -------- */
		ACSDB::_do_query("BEGIN");

		// ��ư�褬�롼�ȥե�����ξ��ϡ������ϰϤ򥻥åȤ���ɬ�פ����뤿�ᡢ
		// �롼�ȥե�����ξ����������Ƥ���
		$root_folder_obj = ACSFolder::get_folder_instance($community_folder_obj->get_root_folder_row($community_folder_obj->get_community_id()));

		// �ե����
		$folder_row_array = array();
		$selected_folder_id_array = $request->getParameter('selected_folder');
		if ($selected_folder_id_array) {
			foreach ($selected_folder_id_array as $folder_id) {
				// ��ư����ե��������
				$_folder_obj = $community_folder_obj->folder_obj->get_folder_obj($folder_id);

				// ��ư����
				$ret = $_folder_obj->move_folder($move_target_folder_id);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK;");
					print "ERROR: Move folder failed.";
					exit;
				}

				// �����ϰϤ򹹿�
				if ($move_target_folder_id == $root_folder_obj->get_folder_id()) {
					// �롼�ȥե�����ذ�ư�ξ�硢�����ϰϤ򥻥å�
					$new_open_level_code = $community_folder_obj->folder_obj->get_open_level_code();
					$new_trusted_community_row_array = $community_folder_obj->folder_obj->get_trusted_community_row_array();

				} else {
					// �롼�ȥե�����ʳ��ذ�ư�ξ�硢�����ϰϤ�ꥻ�å�
					$new_open_level_code = "";
					$new_trusted_community_row_array = array();
				}
				$ret = $_folder_obj->update_open_level_code($new_open_level_code, $new_trusted_community_row_array);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK;");
					print "ERROR: Move folder failed.";
					exit;
				}
			}
		}

		// �ե�����
		$file_row_array = array();
		$selected_file_id_array = $request->getParameter('selected_file');
		if ($selected_file_id_array) {
			foreach ($selected_file_id_array as $file_id) {

				// ��ư����
				$file_obj = $community_folder_obj->folder_obj->get_file_obj($file_id);
				$ret = $community_folder_obj->folder_obj->move_file($file_obj, $move_target_folder_id);
				//$ret = $_file_obj->rename_display_file_name($new_file_name);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK;");
					print "ERROR: Move file failed.";
					exit;
				}
			}
		}

		ACSDB::_do_query("COMMIT;");

		// �����ѥե�������󹹿�
		$form['folder_id'] = $move_target_folder_id;
		ACSFileDetailInfo::update_file_public_access($file_id, $form);

		// �ե����ɽ�����������ƤӽФ�
		$folder_action  = $this->getControllerPath('Community', 'Folder');
		$folder_action .= '&community_id=' . $target_community_id;
		$folder_action .= '&folder_id=' . $target_community_folder_id;

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
				ACSMsg::get_msg('Community', 'MoveFolderAction.class.php', 'M001'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		// ��ư�������������ƤӽФ�
		$controller->forward('Community', 'MoveFolderList');
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_MEMBER');
	}
}
?>
