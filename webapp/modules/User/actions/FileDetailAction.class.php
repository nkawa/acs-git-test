<?php
/**
 * �ե�����ܺپ���
 * $Id: FileDetailAction.class.php,v 1.8 2007/03/29 01:55:17 w-ota Exp $
 */

class FileDetailAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_user_community_id = $request->getParameter('id');
		// �оݤȤʤ�ե����ID�����
		$target_user_community_folder_id = $request->getParameter('folder_id');
		// �ܺپ����ɽ������ե�����ID�����
		$file_id = $request->getParameter('file_id');

		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);

		// �ե�����������
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
				$acs_user_info_row,
				$target_user_community_folder_id);
		$folder_obj = $user_folder_obj->get_folder_obj();

		// �ե�����θ����ϰϤǥ�����������
		if (!$user_folder_obj->has_privilege($target_user_info_row)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �롼�ȥե����ľ���Υե�������ܿͰʳ����������Բ�
		$privilege_array = $this->getCredential();
		//if ($folder_obj->get_is_root_folder() && !in_array('USER_PAGE_OWNER', $privilege_array)) {
		if ($folder_obj->get_is_root_folder() && !$this->get_execute_privilege()) {
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

		// ­�׾������
		$footprint_url = $this->getControllerPath('User', 'FileDetail')
						. "&id=" . $target_user_community_id
						. "&file_id=" . $file_obj->get_file_id()
						. "&folder_id=" . $user_folder_obj->folder_obj->get_folder_id();
		$where  = "foot.contents_link_url = '" . $footprint_url . "'";
		$where .= " AND foot.visitor_community_id = '" . $acs_user_info_row['user_community_id'] . "'";
		$footprint_info = ACSUser::get_footprint_list($target_user_community_id, $where);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('file_obj', $file_obj);
		$request->setAttribute('user_folder_obj', $user_folder_obj);
		$request->setAttribute('file_detail_info_row', $file_detail_info_row);
		$request->setAttribute('file_history_row_array', $file_history_row_array);
		$request->setAttribute('footprint_info', $footprint_info);

		return View::SUCCESS;
	}
	
	function isSecure () {
		return false;
	}
	function getCredential () {
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
