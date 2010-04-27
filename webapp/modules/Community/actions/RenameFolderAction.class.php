<?php
/**
 * �ե���� ̾���ѹ�����
 *
 * @author  kuwayama
 * @version $Revision: 1.4 $ $Date: 2006/11/20 08:44:12 $
 */
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class RenameFolderAction extends BaseAction
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
		$user_folder_obj = new ACSCommunityFolder($target_community_id,
												  $acs_user_info_row,
												  $target_community_folder_id);

		// set
		$request->setAttribute('target_community_info_row', $target_community_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);


		/* ------------- */
		/* ̾���ѹ����� */
		/* ------------- */
		// ������̾���ǹ���
		ACSDB::_do_query("BEGIN");
		// �ե����
		$folder_row_array = array();
		$new_folder_name_array = $request->getParameter('new_folder_name');
		if ($new_folder_name_array) {
			foreach ($new_folder_name_array as $folder_id => $new_folder_name) {
				// �ե����̾ɬ�ܥ����å�
				if (!$new_folder_name) {
					ACSDB::_do_query("ROLLBACK;");
					// ���顼�ξ�硢������λ
					return $this->setError($controller, $request, $user, 'new_folder_name', ACSMsg::get_msg('Community', 'RenameFolderAction.class.php', 'M001'));
				} elseif (mb_strlen($new_folder_name) > 100) {
					ACSDB::_do_query("ROLLBACK;");
					// ���顼�ξ�硢������λ
					return $this->setError($controller, $request, $user, 'new_file_name', ACSMsg::get_msg('Community', 'RenameFolderAction.class.php', 'M002'));
				}

				// folder_id Ƭ���Ρ�'�פ���
				$folder_id = trim($folder_id, "'");

				// ̾����������
				$_folder_obj = $user_folder_obj->folder_obj->get_folder_obj($folder_id);
				$ret = $_folder_obj->rename_folder_name($new_folder_name);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK;");
					print "ERROR: Rename folder failed.";
					exit;
				}
			}
		}

		// �ե�����
		$file_row_array = array();
		$new_file_name_array = $request->getParameter('new_file_name');
		if ($new_file_name_array) {
			foreach ($new_file_name_array as $file_id => $new_file_name) {
				// �ե�����̾ɬ�ܥ����å�
				if (!$new_file_name) {
					ACSDB::_do_query("ROLLBACK;");
					// ���顼�ξ�硢������λ
					return $this->setError($controller, $request, $user, 'new_file_name', ACSMsg::get_msg('Community', 'RenameFolderAction.class.php', 'M001'));
				}

				// file_id Ƭ���Ρ�'�פ���
				$file_id = trim($file_id, "'");

				// ̾����������
				$_file_obj = $user_folder_obj->folder_obj->get_file_obj($file_id);
				$ret = $_file_obj->rename_display_file_name($new_file_name);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK;");
					print "ERROR: Rename file failed.";
					exit;
				}
			}
		}

		ACSDB::_do_query("COMMIT;");

		// �ե����ɽ�����������ƤӽФ�
		$folder_action  = $this->getControllerPath('Community', 'Folder');
		$folder_action .= '&community_id=' . $target_community_id;
		$folder_action .= '&folder_id=' . $target_community_folder_id;

		header("Location: $folder_action");
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();

		// ̾���ѹ��������������ƤӽФ�
		$controller->forward('Community', 'RenameFolderList');
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_MEMBER');
	}
}
?>
