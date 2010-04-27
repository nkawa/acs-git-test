<?php
/**
 * ���ߥ�˥ƥ��Υե����ɽ��
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2006/11/20 08:44:12 $
 */
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class FolderAction extends BaseAction
{
	/**
	 * �������
	 * GET�᥽�åɤξ�硢�ƤФ��
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$target_community_id = $request->getParameter('community_id');
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_community_folder_id = $request->getParameter('folder_id');
		$mode = $request->getParameter('mode'); // ɽ���⡼��

		$community_folder_obj = new ACSCommunityFolder($target_community_id,
													   $acs_user_info_row,
													   $target_community_folder_id);

		$target_community_row = ACSCommunity::get_community_row($request->getParameter('community_id'));

		// ���롼��ɽ��
		$file_detail_info_row_array = array();
		if ($mode == 'group') {
			// �ե����륪�֥������Ȥ�����
			$target_folder_obj	= $community_folder_obj->get_folder_obj();
			$file_obj_array	   = $target_folder_obj->get_file_obj_array();

			foreach ($file_obj_array as $file_obj) {
				$file_detail_info_row = ACSFileDetailInfo::get_file_detail_info_row($file_obj->get_file_id());
				if (!$file_detail_info_row['file_id']) {
					// �ե�����ܺپ������ꤵ��Ƥʤ����
					$file_detail_info_row['file_id'] = $file_obj->get_file_id();
				}
				$file_detail_info_row['display_file_name'] = $file_obj->get_display_file_name();
				$file_detail_info_row['thumbnail_server_file_name'] = $file_obj->get_thumbnail_server_file_name();
				if ($file_obj->get_owner_community_id() == $target_community_row['community_id']) {
					$file_detail_info_row['is_put'] = false;
				} else {
					$file_detail_info_row['is_put'] = true;
				}
				array_push($file_detail_info_row_array, $file_detail_info_row);
			}

			// �ե����륫�ƥ��ꥳ���ɤ��ȤΥե����륳��ƥ�ļ��̤�Ϣ��������������
			$file_contents_type_master_row_array_array = ACSFileDetailInfo::get_file_contents_type_master_row_array_array();
		}


		// �ե�����θ����ϰϤǥ�����������
		if (!$community_folder_obj->has_privilege($target_community_row)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$request->setAttribute('target_community_row', $target_community_row);
		$request->setAttribute('community_folder_obj', $community_folder_obj);
		$request->setAttribute('error_row', $error_row);

		$request->setAttribute('mode', $mode);
		if ($mode == 'group') {
			$request->setAttribute('file_detail_info_row_array', $file_detail_info_row_array);
			$request->setAttribute('file_contents_type_master_row_array_array', $file_contents_type_master_row_array_array);
		}

		return View::SUCCESS;
	}

	function execute () {
		return $this->getDefaultView();
	}
	
	function isSecure () {
		return false;
	}

	function getRequestMethods () {
		return Request::POST | Request::GET;
	}

	// ���������������
	function get_access_control_info(&$controller, &$request, &$user) {
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->getParameter('community_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// ��������������� //
		$folder_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D31'));
		$folder_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $folder_contents_row['contents_type_code'], $folder_contents_row['open_level_code']);
		$access_control_info = array(
									 'role_array' => ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row),
									 'contents_row_array' => array($folder_contents_row)
									 );

		return $access_control_info;
	}
}
?>
