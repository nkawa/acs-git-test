<?php
/**
 * �ե���� �������ѹ�
 *
 * @author  kuwayama
 * @version $Revision: 1.4 $ $Date: 2006/11/20 08:44:12 $
 */
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class EditFolderAction extends BaseAction
{
	/**
	 * ���ϲ���ɽ��
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$target_community_id = $request->getParameter('community_id');
		// �оݤȤʤ�ե����ID�����
		$target_community_folder_id = $request->getParameter('folder_id');
		$edit_folder_id = $request->getParameter('edit_folder_id');

		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_community_info_row = ACSCommunity::get_community_row($target_community_id);
		// �ե�����������
		$user_folder_obj = new ACSCommunityFolder($target_community_id,
												  $acs_user_info_row,
												  $target_community_folder_id);

				if (!$this->get_execute_privilege()) {
						$controller->forward(SECURE_MODULE, SECURE_ACTION);
						return;
				}

		// ������������Ƚ��
		if ($request->getParameter('edit_folder_id')) {
			$edit_folder_id = $request->getParameter('edit_folder_id');
			$view_mode = 'update';
		} else {
			$view_mode = 'create';
		}

		// ���ɽ������������ĥ��ߥ�˥ƥ������ʿơ����֥��ߥ�˥ƥ���
		$parent_community_row_array = ACSCommunity::get_parent_community_row_array($target_community_id);
		$sub_community_row_array	= ACSCommunity::get_sub_community_row_array($target_community_id);

		// set
		$request->setAttribute('target_community_info_row', $target_community_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);
		$request->setAttribute('view_mode', $view_mode);
		$request->setAttribute('edit_folder_id', $edit_folder_id);
		$request->setAttribute('parent_community_row_array', $parent_community_row_array);
		$request->setAttribute('sub_community_row_array', $sub_community_row_array);



		// ���顼�ǸƤФ줿���ϡ������ͤ����
		// hasErrors �ؿ�������
		if ($this->hasErrors($controller, $request, $user)) {

			// �ǥե�����ͤȤ���ɽ�������ͤ� row �˥��å�
			$default_data_row['folder_id']	   = $request->getParameter('folder_id');
			$default_data_row['folder_name']	 = $request->getParameter('folder_name');
			$default_data_row['comment']		 = $request->getParameter('comment');
			$default_data_row['open_level_code'] = $request->getParameter('open_level_code');
			$default_data_row['trusted_community_flag']	 = $request->getParameter('trusted_community_flag');
			$default_data_row['trusted_community_id_array'] = $request->getParameter('trusted_community_id_array');

			// set
			$request->setAttribute('default_data_row', $default_data_row);
			return View::INPUT;
		}

		// �����ξ��ϡ������оݤΥե����ID�����
		if ($view_mode == 'update') {
			// ���ɽ���ξ�硢�����оݤΥե�����������
			if (!$this->hasErrors($controller, $request, $user)) {

				// �����оݤΥե�����������
				$update_user_folder_obj = new ACSCommunityFolder($request->getParameter('community_id'),
																 $acs_user_info_row,
																 $edit_folder_id);
				$update_folder_obj = $update_user_folder_obj->get_folder_obj();
				// �������ĥ��ߥ�˥ƥ�ID����
				$trusted_community_id_array = array();
				foreach($update_folder_obj->get_trusted_community_row_array() as $trusted_community_row) {
					array_push($trusted_community_id_array, $trusted_community_row['community_id']);
				}

				// �ǥե�����ͤȤ���ɽ�������ͤ� row �˥��å�
				$default_data_row['folder_id']	   = $update_folder_obj->get_folder_id();
				$default_data_row['folder_name']	 = $update_folder_obj->get_folder_name();
				$default_data_row['comment']		 = $update_folder_obj->get_comment();
				$default_data_row['open_level_code'] = $update_folder_obj->get_open_level_code();
				$default_data_row['trusted_community_flag']	 = "";  // view ���ͤ�Ƚ�Ǥ���
				$default_data_row['trusted_community_id_array'] = $trusted_community_id_array;
				$request->setAttribute('default_data_row', $default_data_row);
			}

			// set
			$request->setAttribute('input_data_row', $input_data_row);
			return View::INPUT;
		} elseif ($view_mode == 'create') {
			return View::INPUT;
		}
	}

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

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_community_id = $request->getParameter('community_id');
		// �оݤȤʤ�ե����ID�����
		$target_community_folder_id = $request->getParameter('folder_id');
		// �����μ���
		$action_type = $request->getParameter('action_type');
		// get
		$form = $request->ACSGetParameters();


		// Validator�ǽ���ʤ����顼�����å���Ԥ� //
		if (mb_strlen($form['folder_name']) > 100) {
			$this->setError($controller, $request, $user, 'folder_name', 
					 ACSMsg::get_msg('Community', 'EditFileAction.class.php', 'M001'));
			return $this->handleError();
		}


		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_community_info_row = ACSCommunity::get_community_row($target_community_id);
		// �ե�����������
		$user_folder_obj = new ACSCommunityFolder($target_community_id,
												  $acs_user_info_row,
												  $target_community_folder_id);


		// set
		$request->setAttribute('target_community_info_row', $target_community_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);

		/* ----------------- */
		/* ���ϲ���ɽ������ */
		/* ----------------- */
		// action_type (create or update) ��¸�ߤ������顼�����ܤ��Ƥ��Ƥ��ʤ����
		if (!$action_type || $this->hasErrors($controller, $request, $user)) {
			return $this->getDefaultView();
		}


		/* ---------- */
		/* �����ͼ��� */
		/* ---------- */
		$edit_folder_id = $request->getParameter('edit_folder_id');

		$input_folder_row = array();
		$input_folder_row['folder_name']	 = $request->getParameter('folder_name');
		$input_folder_row['comment']		 = $request->getParameter('comment');
		$input_folder_row['open_level_code'] = $request->getParameter('open_level_code');
		$input_folder_row['trusted_community_id_array'] = $request->getParameter('trusted_community_id_array');

		/* ---------------------- */
		/* �ե����̾��ʣ�����å� */
		/* ---------------------- */
		// �оݤȤʤ�ե�����۲��Υե���������
		$sub_folder_obj_array = $user_folder_obj->folder_obj->get_folder_obj_array();
		foreach ($sub_folder_obj_array as $sub_folder_obj) {
			if ($sub_folder_obj->get_folder_id() == $edit_folder_id) {
				// �����оݤΥե�����ϥ����å��оݤȤ��ʤ�
				continue;
			}

			if ($sub_folder_obj->get_folder_name() == $input_folder_row['folder_name']) {
				// ���顼��å������򥻥åȤ������������Ǥ���
				return $this->setError($controller, $request, $user, 'folder_name', ACSMsg::get_msg('Community', 'EditFolderAction.class.php', 'M003').'[' . $input_folder_row['folder_name'] . ']');
			}
		}

		ACSDB::_do_query("BEGIN");
		/* -------- */
		/* ��Ͽ���� */
		/* -------- */
		if ($action_type == 'create') {
			$ret = $user_folder_obj->folder_obj->create_folder($input_folder_row);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK;");
				print "ERROR: Create folder failed.";
				exit;
			}

		} elseif ($action_type == 'update') {
		/* -------- */
		/* �������� */
		/* -------- */
			// �����оݤΥե�����������
			$update_user_folder_obj = new ACSCommunityFolder($request->getParameter('community_id'),
															 $acs_user_info_row,
															 $edit_folder_id);

			$ret = $update_user_folder_obj->folder_obj->update_folder($input_folder_row);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK;");
				print "ERROR: Create folder information failed.";
				exit;
			}
		}
		ACSDB::_do_query("COMMIT;");


		/* -------------------- */
		/* �ե������������ɽ�� */
		/* -------------------- */
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$folder_action = $this->getControllerPath('Community',
														'Folder');
		$folder_action .= '&community_id=' . $target_community_info_row['community_id'];
		$folder_action .= '&folder_id=' . $target_community_folder_id;

		header("Location: $folder_action");
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$request = $context->getRequest();
		// �������ѹ������ξ��Τߡ����ϥ����å��򤹤�
		if ($request->getParameter('action_type')) {
			/* ɬ�ܥ����å� */
			parent::regValidateName($validatorManager, 
					"folder_name", 
					true, 
					ACSMsg::get_msg('User', 'EditFolderAction.class.php', 'M001'));
		}
	}

	function handleError () {
		// ���ϲ���ɽ��
		return $this->getDefaultView();
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
