<?php
/**
 * �ե�����ܺپ���
 * $Id: FileDetailAction.class.php,v 1.5 2007/03/28 08:59:09 w-ota Exp $
 */

class FileDetailAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$target_community_id = $request->getParameter('community_id');
		// �оݤȤʤ�ե����ID�����
		$target_community_folder_id = $request->getParameter('folder_id');
		// �ܺپ����ɽ������ե�����ID�����
		$file_id = $request->getParameter('file_id');

		// ���ߥ�˥ƥ������Ԥ�
		$is_community_admin = false;
		if(ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $target_community_id)){
			$is_community_admin = true;
		}

		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_community_row = ACSCommunity::get_community_row($target_community_id);

		// �ե�����������
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
													   $acs_user_info_row,
													   $target_community_folder_id);
		$folder_obj = $community_folder_obj->get_folder_obj();

		// �ե�����θ����ϰϤǥ�����������
		if (!$community_folder_obj->has_privilege($target_community_row)) {

			// 2010.03.24 ̤���������ͶƳ
			// ������桼���Ǥʤ����ϥ�������̤�
			if ($user->hasCredential('PUBLIC_USER')) {
				$controller->forward("User", "Login");
				return;
			}

			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �롼�ȥե����ľ���Υե�����ϥ��ߥ�˥ƥ����аʳ����������Բ�
		if ($folder_obj->get_is_root_folder() && $user->hasCredential('COMMUNITY_MEMBER')) {

			// 2010.03.24 ̤���������ͶƳ
			// ������桼���Ǥʤ����ϥ�������̤�
			if ($user->hasCredential('PUBLIC_USER')) {
				$controller->forward("User", "Login");
				return;
			}

			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �ե�����������
		$file_obj = ACSFile::get_file_info_instance($file_id);

		// �ե�����ξܺپ���
		$file_detail_info_row = ACSFileDetailInfo::get_file_detail_info_row($file_id);

		// �ե�������������
		$file_history_row_array = ACSFileHistory::get_file_history_row_array($file_id);
		// �ե��������򤴤ȤΥ�����
		foreach ($file_history_row_array as $index => $file_history_row) {
			$file_history_row_array[$index]['file_history_comment_row_array'] = ACSFileHistoryComment::get_file_history_comment_row_array($file_history_row['file_history_id']);
		}

		// �ե����륢������������Ͽ
		if ($acs_user_info_row['is_acs_user']) {
			ACSFile::set_file_access_history($acs_user_info_row['user_community_id'], $file_id);
		}


		// �ץåȥե�����Ǥʤ����
		if ($file_obj->get_owner_community_id() == $target_community_id) {
			// �ե�����θ�������
			$file_public_access_row = ACSFileDetailInfo::get_file_public_access_row($file_id);
		}

		// set
		$request->setAttribute('target_community_row', $target_community_row);
		$request->setAttribute('file_obj', $file_obj);
		$request->setAttribute('community_folder_obj', $community_folder_obj);
		$request->setAttribute('file_detail_info_row', $file_detail_info_row);
		$request->setAttribute('file_history_row_array', $file_history_row_array);
		$request->setAttribute('is_community_admin', $is_community_admin);
		$request->setAttribute('file_public_access_row', $file_public_access_row);

		return View::SUCCESS;
	}
	
	function isSecure () {
		return false;
	}
	
}

?>
