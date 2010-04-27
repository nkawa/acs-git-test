<?php
/**
 * ���ߥ�˥ƥ��Υե���� �ե�������������
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2006/12/18 07:42:11 $
 */
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');

class DownloadFileAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row		  = $user->getAttribute('acs_user_info_row');

		$target_community_id		= $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');
		$view_mode = $request->getParameter('mode');

		$target_community_row = ACSCommunity::get_community_row($target_community_id);

		// �ե������������ɽ���
		$target_file_id = $request->getParameter('file_id');
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
													   $acs_user_info_row,
													   $target_community_folder_id);
		$folder_obj = $community_folder_obj->get_folder_obj();

		// �ե�����θ����ϰϤǥ�����������
		if (!$community_folder_obj->has_privilege($target_community_row)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �롼�ȥե����ľ���Υե�����ϥ��ߥ�˥ƥ����аʳ����������Բ�
		if ($folder_obj->get_is_root_folder() 
			   && $user->hasCredential('COMMUNITY_MEMBER')) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �ե����륢������������Ͽ
		if ($acs_user_info_row['is_acs_user']) {
			ACSFile::set_file_access_history($acs_user_info_row['user_community_id'], $target_file_id);
		}

		if ($view_mode == 'thumb') {
			$file_obj = ACSFile::get_file_info_instance($target_file_id);
			$ret = $file_obj->view_image($view_mode);
		} else {
			$folder_obj->download_file($target_file_id);
		}
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods () {
		return Request::GET;
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
