<?php
/**
 * �ե���� ��ư������
 *
 * @author  kuwayama
 * @version $Revision: 1.4 $ $Date: 2006/11/20 08:44:12 $
 */
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class MoveFolderListAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
				$controller->forward(SECURE_MODULE, SECURE_ACTION);
						return;
		}

		// ɬ�ܥ����å�
		//	Validator �ǤǤ��ʤ������å��Ϥ����ǹԤ�
		if (!$request->getParameter('selected_folder') && !$request->getParameter('selected_file')) {
			// ���顼�ξ�硢������λ
			return $this->setError($controller, $request, $user, 'selected_folder', 
					ACSMsg::get_msg('Community', 'MoveFolderListAction.class.php', 'M001'));
		}

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

		// ��ư�������ѤΥե������������
		$community_folder_tree = array();
		$community_folder_tree = $community_folder_obj->get_folder_tree();

		// ��ư�оݤ����
		// �ե����
		$selected_folder_obj_array = array();   // View �ˤ錄����ư�оݤΥե����
		$selected_folder_row_array = array();
		$selected_folder_array = $request->getParameter('selected_folder');
		if ($selected_folder_array) {
			foreach ($selected_folder_array as $selected_folder_id) {
				$_selected_folder_obj = $community_folder_obj->folder_obj->get_folder_obj($selected_folder_id);
				array_push($selected_folder_obj_array, $_selected_folder_obj);
			}
		}

		// �ե�����
		$selected_file_obj_array = array();   // View �ˤ錄����ư�оݤΥե�����
		$selected_file_row_array = array();
		$selected_file_array = $request->getParameter('selected_file');
		if ($selected_file_array) {
			foreach ($selected_file_array as $selected_file_id) {
				$_selected_file_obj = $community_folder_obj->folder_obj->get_file_obj($selected_file_id);
				array_push($selected_file_obj_array, $_selected_file_obj);
			}
		}


		// ������������: �ץåȥե�����ޤ��ϥե������NG //
		foreach ($selected_folder_obj_array as $selected_folder_obj) {
			if ($selected_folder_obj->get_community_id() != $target_community_id) {
				$controller->forward(SECURE_MODULE, SECURE_ACTION);
				return;
			}
		}
		foreach ($selected_file_obj_array as $selected_file_obj) {
			if ($selected_file_obj->get_owner_community_id() != $target_community_id) {
				$controller->forward(SECURE_MODULE, SECURE_ACTION);
				return;
			}
		}

		// set
		$request->setAttribute('community_folder_tree', $community_folder_tree);
		$request->setAttribute('selected_folder_obj_array', $selected_folder_obj_array);
		$request->setAttribute('selected_folder_id_array', $selected_folder_array);
		$request->setAttribute('selected_file_obj_array', $selected_file_obj_array);

		return View::INPUT;
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		/* ���顼��å��������Ϥ� */
		$this->sendError($controller, $request, $user);

		// �ե����ɽ�����������ƤӽФ�
		$target_community_id = $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');

		$folder_action = $this->getControllerPath('Community', 'Folder');
		$folder_action .= '&community_id=' . $target_community_id;
		$folder_action .= '&folder_id=' . $target_community_folder_id;
		header("Location: $folder_action");
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
