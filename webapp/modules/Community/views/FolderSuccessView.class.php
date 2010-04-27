<?php
/**
 * ���ߥ�˥ƥ��Υե����ɽ��
 *
 * @author  kuwayama
 * @version $Revision: 1.20 $ $Date: 2006/12/08 05:06:37 $
 */
class FolderSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row    = $user->getAttribute('acs_user_info_row');
		$target_community_row = $request->getAttribute('target_community_row');
		$community_folder_obj = $request->getAttribute('community_folder_obj');
		$target_folder_obj    = $community_folder_obj->get_folder_obj();
		$folder_obj_array     = $target_folder_obj->get_folder_obj_array();
		$file_obj_array       = $target_folder_obj->get_file_obj_array();

		$mode = $request->getAttribute('mode');
		if ($mode == 'group') {
			$file_detail_info_row_array = $request->getAttribute('file_detail_info_row_array');
			$file_contents_type_master_row_array_array = $request->getAttribute('file_contents_type_master_row_array_array');
		}


		// URL ���ղä��� target_community
		$target_community_info = '&community_id=' . $target_community_row['community_id'];
		// URL ���ղä��� target_folder
		$target_folder_info = '&folder_id=' . $target_folder_obj->get_folder_id();

		// ���ߥ�˥ƥ���URL
		$community_top_page_url  = $this->getControllerPath('Community', 'Index');
		$community_top_page_url .= $target_community_info;

		// �ե�����ѥ�����
		$path_folder_obj_array = $community_folder_obj->get_path_folder_obj_array();
		// ɽ���Ѥ˲ù�
		$path_folder_obj_row_array = $this->make_display_folder_row_array($path_folder_obj_array, $target_community_info, $target_folder_info, $target_community_row, $mode, $controller);

		// �ե�����θ����ϰ�
		$target_folder_open_level_row['name'] = $target_folder_obj->get_open_level_name();
		$target_folder_open_level_row['trusted_community_row_array'] = $this->make_display_trusted_community_row_array($target_folder_obj->get_trusted_community_row_array(), &$controller);

		// ���ߥ�˥ƥ����Ф��ɤ���
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'],
																 $target_community_row['community_id']);

		// �롼�ȥե�������ɤ���
		$is_root_folder = $target_folder_obj->get_is_root_folder();

		// �ץåȥե�������ɤ���
		$is_put_folder = $target_folder_obj->is_put_folder($target_community_row['community_id']);

		// �ե����륢�åץ��� URL
		$upload_file_url = $this->getControllerPath('Community',
														  'UploadFile');
		$upload_file_url .= $target_community_info;
		$upload_file_url .= $target_folder_info;

		/* �ե�������ե�������� */
		// �ե��������
		$display_folder_obj_array = $target_folder_obj->get_display_folder_obj_array_for_community($acs_user_info_row, $target_community_row);
		$folder_row_array = $this->make_display_folder_row_array($display_folder_obj_array, $target_community_info, $target_folder_info, $target_community_row, $mode, $controller);


		if ($mode == 'group') {
			// ���롼��ɽ�� //
			// �ե����륫�ƥ���ޥ���
			$file_category_master_array = ACSDB::get_master_array('file_category');

			// ����ʤ��Υե����륫�ƥ��ꥳ����
			$default_file_category_code = array_search(ACSMsg::get_mst('file_category_master','D0000'), $file_category_master_array);

			$file_detail_info_row_array_array = array();
			foreach ($file_category_master_array as $file_category_code => $file_category_name) {
				$file_detail_info_row_array_array[$file_category_code] = array();
				$file_detail_info_row_array_array[$file_category_code]['file_category_code'] = $file_category_code;
				$file_detail_info_row_array_array[$file_category_code]['file_category_name'] = $file_category_name;
				$file_detail_info_row_array_array[$file_category_code]['file_detail_info_row_array'] = array();
			}


			// Action�Ǽ�������$file_detail_info_row������򡢥ե�������ॳ���ɤ��Ȥ�����˿���ʬ����
			foreach ($file_detail_info_row_array as $file_detail_info_row) {
				// �ե����륫�ƥ��ꥳ����
				$file_category_code = $file_detail_info_row['file_category_code'];

				// ���URL
				$file_detail_info_row['link_url'] = $this->getControllerPath('Community', 'DownloadFile')
					 . $target_community_info . '&file_id=' . $file_detail_info_row['file_id'] . $target_folder_info;
				// �ե�����ܺپ���URL
				$file_detail_info_row['file_detail_url'] = $this->getControllerPath('Community', 'FileDetail')
					 . $target_community_info . '&file_id=' . $file_detail_info_row['file_id'] . $target_folder_info;
				// ����ͥ������URL
				if ($file_category_master_array[$file_category_code] == ACSMsg::get_mst('file_category_master','D0003')) {
					$file_detail_info_row['image_url'] = $this->getControllerPath('Community', 'DownloadFile')
						 . $target_community_info . '&file_id=' . $file_detail_info_row['file_id'] . $target_folder_info . '&mode=thumb';
				}

				// push
				if ($file_category_code == '') {
					// �ե����륫�ƥ��ꥳ���ɤ�¸�ߤ��ʤ����ϥǥե����(����ʤ�)����
					$file_category_code = $default_file_category_code;
				}
				array_push($file_detail_info_row_array_array[$file_category_code]['file_detail_info_row_array'], $file_detail_info_row);
			}

			// �ե���� �̾�ɽ��URL
			$folder_url  = $this->getControllerPath('Community', 'Folder');
			$folder_url .= $target_community_info;
			$folder_url .= $target_folder_info;

		} else {
			// �ե��������
			$file_row_array = array();
			foreach ($file_obj_array as $file_obj) {
				$a_file = array();
				$link_url = "";
				$update_user_community_link_url = "";

				$link_url  = $this->getControllerPath('Community',
															'DownloadFile');
				$link_url .= $target_community_info;
				$link_url .= "&file_id=" . $file_obj->get_file_id();
				$link_url .= $target_folder_info;

				$update_user_community_link_url  = $this->getControllerPath('User', 'Index');
				$update_user_community_link_url .= "&id=" . $file_obj->get_update_user_community_id();

				$a_file['name'] = $file_obj->get_display_file_name();
				$a_file['file_id'] = $file_obj->get_file_id();
				$a_file['link_url'] = $link_url;
				$a_file['file_size'] = $file_obj->get_file_size_kb();
				$a_file['update_user_community_name'] = $file_obj->get_update_user_community_name();
				$a_file['update_user_community_link_url'] = $update_user_community_link_url;
				$a_file['update_date'] = $file_obj->get_update_date_yyyymmddhmi();

				// �ե�����ܺپ���URL
				$a_file['detail_url']  = $this->getControllerPath('Community', 'FileDetail');
				$a_file['detail_url'] .= $target_community_info;
				$a_file['detail_url'] .= "&file_id=" . $file_obj->get_file_id();
				$a_file['detail_url'] .= $target_folder_info;

				// �ץåȥե����뤫�ɤ���
				if ($file_obj->get_owner_community_id() == $target_community_row['community_id']) {
					$a_file['is_put'] = false;
				} else {
					$a_file['is_put'] = true;
				}

				array_push($file_row_array, $a_file);
			}

			// �ե���� ���롼��ɽ��URL
			$folder_group_mode_url  = $this->getControllerPath('Community', 'Folder');
			$folder_group_mode_url .= $target_community_info;
			$folder_group_mode_url .= $target_folder_info;
			$folder_group_mode_url .= '&mode=group';
		}

		// �롼�ȥե�����Υե�����ϡ�������Ȥ��ư���
		if ((!$is_community_member) && $is_root_folder) {
			$file_row_array = array();
			$file_detail_info_row_array_array = array();
		}

		// �ե�������� URL
		if (!$is_put_folder) {
			$edit_folder_url  = $this->getControllerPath('Community', 'EditFolder');
			$edit_folder_url .= $target_community_info;
			$edit_folder_url .= $target_folder_info;
		} else {
			$edit_folder_url = "";
		}

		// ̾���ѹ�URL
		$rename_folder_url = "";
		$rename_folder_url  = $this->getControllerPath('Community', 'RenameFolderList');
		$rename_folder_url .= $target_community_info;
		$rename_folder_url .= $target_folder_info;

		// ���URL
		$delete_folder_url = "";
		$delete_folder_url  = $this->getControllerPath('Community', 'DeleteFolder');
		$delete_folder_url .= $target_community_info;
		$delete_folder_url .= $target_folder_info;
		$delete_folder_url .= "&action_type=confirm";  // ���ܤ�����β��̤ϳ�ǧ����

		// ��ưURL
		$move_folder_url = "";
		$move_folder_url  = $this->getControllerPath('Community', 'MoveFolderList');
		$move_folder_url .= $target_community_info;
		$move_folder_url .= $target_folder_info;

		// ���ߥ�˥ƥ��ե��������URL
		$search_folder_url  = $this->getControllerPath('Community', 'SearchFolder');
		$search_folder_url .= $target_community_info;

		$this->setAttribute('target_folder_open_level_row', $target_folder_open_level_row);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('is_root_folder', $is_root_folder);
		$this->setAttribute('is_put_folder', $is_put_folder);
		$this->setAttribute('upload_file_url', $upload_file_url);
		$this->setAttribute('folder_put_community_url', $folder_put_community_url);

		$this->setAttribute('target_user_community_name', $target_community_row['community_name']);

		$this->setAttribute('path_folder_obj_row_array', $path_folder_obj_row_array);
		$this->setAttribute('folder_row_array', $folder_row_array);
		$this->setAttribute('file_row_array', $file_row_array);

		// ����˥塼
		$this->setAttribute('edit_folder_url', $edit_folder_url);
		$this->setAttribute('rename_folder_url', $rename_folder_url);
		$this->setAttribute('delete_folder_url', $delete_folder_url);
		$this->setAttribute('move_folder_url', $move_folder_url);
		$this->setAttribute('search_folder_url', $search_folder_url);

		$this->setAttribute('mode', $mode);
		if ($mode == 'group') {
			$this->setAttribute('folder_url', $folder_url);
			$this->setAttribute('file_detail_info_row_array_array', $file_detail_info_row_array_array);
			$this->setAttribute('file_contents_type_master_row_array_array', $file_contents_type_master_row_array_array);
		} else {
			$this->setAttribute('folder_group_mode_url', $folder_group_mode_url);
		}

		// ���顼��å�����
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// �ƥ�ץ졼��
		if ($mode == 'group') {
			$this->setScreenId("0001");
			$this->setTemplate('Folder_group.tpl.php');
		} else {
			$this->setScreenId("0001");
			$this->setTemplate('Folder.tpl.php');
		}

		return parent::execute();
	}

	function make_display_folder_row_array ($folder_obj_array,
											$target_community_info,
											$target_folder_info,
											$target_community_row,
											$mode,
											&$controller) {
		$folder_row_array = array();
		foreach ($folder_obj_array as $folder_obj) {
			$folder_row = array();
			$link_url = "";
			$name     = "";
			$update_user_community_link_url = "";

			$link_url  = $this->getControllerPath('Community', 'Folder');
			$link_url .= $target_community_info;
			$link_url .= "&folder_id=" . $folder_obj->get_folder_id();
			if ($mode == 'group') {
				$link_url .= '&mode=' . $mode;
			}


			$update_user_community_link_url  = $this->getControllerPath('User', 'Index');
			$update_user_community_link_url .= "&id=" . $folder_obj->get_update_user_community_id();

			// �ե�����ܺ� URL
			$detail_url  = $this->getControllerPath('Community', 'FolderDetail');
			$detail_url .= $target_community_info;
			$detail_url .= $target_folder_info;
			$detail_url .= "&detail_folder_id=" . $folder_obj->get_folder_id();

			if ($folder_obj->get_is_root_folder()) {
				$name  = $target_community_row['community_name'];
				//$name .= "�Υե����";
				$name = ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'FolderSuccessView.class.php', 'FOLDER_NM'),
					array("{COMMUNITY_NAME}" => $target_community_row['community_name']));
			} else {
				$name = $folder_obj->get_folder_name();
			}

			// �������ĥ��ߥ�˥ƥ�̾����
			$trusted_community_row_array = $folder_obj->get_trusted_community_row_array();
			$new_trusted_community_row_array = $this->make_display_trusted_community_row_array($trusted_community_row_array, &$controller);

			$folder_row['name'] = $name;
			$folder_row['folder_id'] = $folder_obj->get_folder_id();
			$folder_row['link_url'] = $link_url;
			$folder_row['open_level_name'] = $folder_obj->get_open_level_name();
			$folder_row['trusted_community_row_array'] = $new_trusted_community_row_array;
			$folder_row['update_user_community_name'] = $folder_obj->get_update_user_community_name();
			$folder_row['update_user_community_link_url'] = $update_user_community_link_url;
			$folder_row['update_date'] = $folder_obj->get_update_date_yyyymmddhmi();

			$folder_row['detail_url'] = $detail_url;

			// �ץåȥե�������ɤ���
			$folder_row['is_put'] = $folder_obj->is_put_folder($target_community_row['community_id']);

			array_push($folder_row_array, $folder_row);
		}
		return $folder_row_array;
	}

	function make_display_trusted_community_row_array ($trusted_community_row_array, &$controller) {
		$new_trusted_community_row_array = array();
		foreach ($trusted_community_row_array as $trusted_community_row) {
			$new_trusted_community_row = array();
			$community_top_page_url    = "";

			$community_top_page_url  = $this->getControllerPath('Community', 'Index');
			$community_top_page_url .= "&community_id=" . $trusted_community_row['community_id'];

			$new_trusted_community_row['community_name'] = $trusted_community_row['community_name'];
			$new_trusted_community_row['community_top_page_url'] = $community_top_page_url;

			array_push($new_trusted_community_row_array, $new_trusted_community_row);
		}

		return $new_trusted_community_row_array;
	}
}
?>
