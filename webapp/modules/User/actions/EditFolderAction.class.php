<?php
/**
 * �ե���� �������ѹ�
 *
 * @author  kuwayama
 * @version $Revision: 1.8 $ $Date: 2007/03/01 09:01:42 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class EditFolderAction extends BaseAction
{
	/**
	 * ���ϲ���ɽ��
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_user_community_id = $request->getParameter('id');
		// �оݤȤʤ�ե����ID�����
		$target_user_community_folder_id = $request->getParameter('folder_id');
		$edit_folder_id = $request->getParameter('edit_folder_id');

		// ¾�桼���Υǡ����������ʤ��褦�����å�
		if (!$this->get_execute_privilege()) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// �ե�����������
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
											 $acs_user_info_row,
											 $target_user_community_folder_id);


		// ������������Ƚ��
		if ($request->getParameter('edit_folder_id')) {
			$edit_folder_id = $request->getParameter('edit_folder_id');
			$view_mode = 'update';
		} else {
			$view_mode = 'create';
		}

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);
		$request->setAttribute('view_mode', $view_mode);
		$request->setAttribute('edit_folder_id', $edit_folder_id);


		// ���顼�ǸƤФ줿���ϡ������ͤ����
		// hasErrors �ؿ�������
		if ($this->hasErrors($controller, $request, $user)) {

			// �ǥե�����ͤȤ���ɽ�������ͤ� row �˥��å�
			$default_data_row['folder_id']	   = $request->getParameter('folder_id');
			$default_data_row['folder_name']	 = $request->getParameter('folder_name');
			$default_data_row['comment']		 = $request->getParameter('comment');
			$default_data_row['open_level_code'] = $request->getParameter('open_level_code');
			$default_data_row['trusted_community_flag']	 = $request->getParameter('trusted_community_flag');
			$default_data_row['trusted_community_id_array'] = $request->getParameter('trusted_community');

			// set
			$request->setAttribute('default_data_row', $default_data_row);
			return View::INPUT;
		}

		// �����ξ��ϡ������оݤΥե����ID�����
		if ($view_mode == 'update') {
			// ���ɽ���ξ�硢�����оݤΥե�����������
			if (!$this->hasErrors($controller, $request, $user)) {

				// �����оݤΥե�����������
				$update_user_folder_obj = new ACSUserFolder($request->getParameter('id'),
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
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_user_community_id = $request->getParameter('id');
		// �оݤȤʤ�ե����ID�����
		$target_user_community_folder_id = $request->getParameter('folder_id');
		// �����μ���
		$action_type = $request->getParameter('action_type');
		// get
		$form = $request->ACSGetParameters();


		// Validator�ǽ���ʤ����顼�����å���Ԥ� //
		if (mb_strlen($form['folder_name']) > 100) {
			$this->setError($controller, $request, $user, 'folder_name', ACSMsg::get_msg('User', 'EditFolderAction.class.php', 'M001'));
			return $this->handleError(&$controller, &$request, &$user);
		}


		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// �ե�����������
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
											 $acs_user_info_row,
											 $target_user_community_folder_id);


		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);

		/* ----------------- */
		/* ���ϲ���ɽ������ */
		/* ----------------- */
		// action_type (create or update) ��¸�ߤ������顼�����ܤ��Ƥ��Ƥ��ʤ����
		if (!$action_type || $this->hasErrors($controller, $request, $user)) {
			return $this->getDefaultView();
		}


		// ���򤵤줿�����ϰϤ�Ƚ�̤Τ���Υǡ�������
		$open_level_master_row_array = ACSAccessControl::get_all_open_level_master_row_array();

		/* ---------- */
		/* �����ͼ��� */
		/* ---------- */
		$edit_folder_id = $request->getParameter('edit_folder_id');

		$input_folder_row = array();
		$input_folder_row['folder_name']	 = $request->getParameter('folder_name');
		$input_folder_row['comment']		 = $request->getParameter('comment');
		$input_folder_row['open_level_code'] = $request->getParameter('open_level_code');

		$open_level_row = $open_level_master_row_array[$input_folder_row['open_level_code']];
		$open_level_name = $open_level_row['open_level_name'];


		if ($open_level_name == ACSMsg::get_mst('open_level_master','D05')) {
			if ($request->getParameter('trusted_community_flag') == '0') {
				// ���Ƥ�ͧ�ͤ򥻥å�
				$friends_community_id = ACSUser::get_friends_community_id($target_user_community_id);
				$input_folder_row['trusted_community_id_array'] = array($friends_community_id);
			} else {
				// �ޥ��ե�󥺥��롼�פξ��ϡ����ꤵ��Ƥ���ޥ��ե�󥺥��롼��ID�򥻥å�
				$input_folder_row['trusted_community_id_array'] = $request->getParameter('trusted_community');
			}
		}

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
				return $this->setError($controller, $request, $user, 'folder_name', ACSMsg::get_msg('User', 'EditFolderAction.class.php' ,'M003').'[' . $input_folder_row['folder_name'] . ']');
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
				print "ERROR: �ե����������Ǥ��ޤ���Ǥ�����";
				exit;
			}

		} elseif ($action_type == 'update') {
		/* -------- */
		/* �������� */
		/* -------- */
			// �����оݤΥե�����������
			$update_user_folder_obj = new ACSUserFolder($request->getParameter('id'),
														$acs_user_info_row,
														$edit_folder_id);

			$ret = $update_user_folder_obj->folder_obj->update_folder($input_folder_row);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK;");
				print "ERROR: �ե����������ѹ��Ǥ��ޤ���Ǥ�����";
				exit;
			}
		}
		ACSDB::_do_query("COMMIT;");

		/* -------------------- */
		/* �ե������������ɽ�� */
		/* -------------------- */
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$folder_action = $this->getControllerPath('User',
														'Folder');
		$folder_action .= '&id=' . $target_user_info_row['user_community_id'];
		$folder_action .= '&folder_id=' . $target_user_community_folder_id;

		header("Location: $folder_action");
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$request =  $context->getRequest();
		// �������ѹ������ξ��Τߡ����ϥ����å��򤹤�
		if ($request->getParameter('action_type')) {
			/* ɬ�ܥ����å� */
			parent::regValidateName($validatorManager, 
					"folder_name", 
					true, 
					ACSMsg::get_msg('User', 'EditFolderAction.class.php', 'M002'));
		}
	}

	function handleError () {
		// ���ϲ���ɽ��
		return $this->getDefaultView();
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
