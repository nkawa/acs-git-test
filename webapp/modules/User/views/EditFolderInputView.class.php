<?php
/**
 * �ե���� �������ѹ�
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2006/11/20 08:44:28 $
 */
require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class EditFolderInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$user_folder_obj = $request->getAttribute('user_folder_obj');
		$edit_folder_id = $request->getAttribute('edit_folder_id');
		$default_data_row = $request->getAttribute('default_data_row');  // �ǥե�����ͤȤ���ɽ��������

		$target_user_community_id   = $target_user_info_row['user_community_id'];
		$view_mode = $request->getAttribute('view_mode');

		$target_user_info = '&id=' . $target_user_community_id;
		$folder_info      = '&folder_id=' . $user_folder_obj->folder_obj->get_folder_id();
		if ($view_mode == 'update') {
			$edit_folder_info = '&edit_folder_id=' . $edit_folder_id;
		} else {
			$edit_folder_info = "";
		}

		// �ե�����ν�ͭ��
		$_target_user_info_row['community_name'] = $target_user_info_row['community_name'];
		$_target_user_info_row['top_page_url']   = $this->getControllerPath('User', DEFAULT_ACTION);
		$_target_user_info_row['top_page_url']  .= $target_community_info;

		// ��Ͽ����������URL
		$action_url = "";
		$action_url  = $this->getControllerPath('User', 'EditFolder');
		$action_url .= $target_user_info;
		$action_url .= $folder_info;
		$action_url .= $edit_folder_info;
		$action_url .= '&action_type=' . $view_mode;

		$cancel_url = "";
		if ($view_mode == 'create') {
			$cancel_url  = $this->getControllerPath('User', 'Folder');
			$cancel_url .= $target_user_info;
			$cancel_url .= $folder_info;
		} elseif ($view_mode == 'update') {
			$cancel_url  = $this->getControllerPath('User', 'FolderDetail');
			$cancel_url .= $target_user_info;
			$cancel_url .= $folder_info;
			$cancel_url .= '&detail_folder_id=' . $edit_folder_id;
		}

		// �����ϰϤ�����Ǥ��뤫�ɤ���
		$is_set_open_level_available = $user_folder_obj->is_set_open_level_available();

		// �����ϰ���������
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D32'));
		// �ǥե����ɽ���ǡ����������硢is_default ���ѹ�����
		if ($default_data_row) {
			$selected_open_level_code = $default_data_row['open_level_code'];
			$index_count = 0;
			foreach ($open_level_master_row_array as $open_level_master_row) {
				if ($open_level_master_row['open_level_code'] == $selected_open_level_code) {
					$open_level_master_row_array[$index_count]['is_default'] = true;
				} else {
					$open_level_master_row_array[$index_count]['is_default'] = false;
				}
				$index_count++;
			}
		}

		// �ޥ��ե�󥺥��롼�׼��� (�����ϰϡ�ͧ�ͤ˸����פ������)
		$friends_group_row_array = ACSUser::get_friends_group_row_array($target_user_info_row['user_community_id']);

		// ͧ�ͤ˸����ξ��Υ��ץ����ǥե�����ͤ��ɲ�
		// default_data_row �� 'trusted_community_flag' ���ɲä���
		$selected_trusted_community_id_array = $default_data_row['trusted_community_id_array'];

		// ��ͧ�ͤ˸����פ� �����ϰϥ����ɼ���
		foreach ($open_level_master_row_array as $open_level_master_row) {
			if ($open_level_master_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
				$friends_open_level_code = $open_level_master_row['open_level_code'];
				break;
			}
		}
		
		if ($default_data_row['trusted_community_flag']) {
			// ���򤵤줿�ͤ��狼�äƤ�����ʥ��顼�ξ���
			// ���Τޤޤ��ͤ���Ѥ���

		} elseif ($default_data_row['open_level_code'] != $friends_open_level_code) {
			// ���Ƥ�ͧ�� ��ǥե���Ȥˤ���
			$default_data_row['trusted_community_flag'] = '0';

		} elseif ($friends_group_row_array && $selected_trusted_community_id_array) {

			// ���ꤵ��Ƥ��륳�ߥ�˥ƥ�ID�����Ĥǡ�community_type ���ޥ��ե�󥺤Ǥʤ��ξ��
			//    �ޥ��ե�󥺥��롼�� �����򤹤�
			if (count($selected_trusted_community_id_array) == 1) {
				$_trusted_community_row = ACSCommunity::get_community_row($selected_trusted_community_id_array[0]);
				if ($_trusted_community_row['community_type_name'] != ACSMsg::get_mst('community_type_master','D20')) {
					$default_data_row['trusted_community_flag'] = '1';
				} else {
					$default_data_row['trusted_community_flag'] = '0';
				}

			} else {
				// ʣ��������ϡ��ޥ��ե�󥺥��롼��
				$default_data_row['trusted_community_flag'] = '1';
			}

		} elseif ($default_data_row['open_level_code'] == $friends_open_level_code && !$selected_trusted_community_id_array) {
			// ͧ�ͤ˸����ǡ��������ĥ��ߥ�˥ƥ����ʤ����� �ޥ��ե�󥺤�����
			$default_data_row['trusted_community_flag'] = '1';
		}


		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('EditFolder.tpl.php');

		// set
		$this->setAttribute('target_user_info_row', $_target_user_info_row);
		$this->setAttribute('view_mode', $view_mode);

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('cancel_url', $cancel_url);

		$this->setAttribute('is_set_open_level_available', $is_set_open_level_available);
		$this->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$this->setAttribute('friends_group_row_array', $friends_group_row_array);

		$this->setAttribute('default_data_row', $default_data_row);


		// ���顼��å�����
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		return parent::execute();
	}
}
?>
