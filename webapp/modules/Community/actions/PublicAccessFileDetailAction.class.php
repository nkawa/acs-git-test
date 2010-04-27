<?php
/**
 * �ե������������
 * $Id: PublicAccessFileDetailAction.class.php,v 1.1 2007/03/28 08:59:09 w-ota Exp $
 */

class PublicAccessFileDetailAction extends BaseAction
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

		// �ե�����������
		$file_obj = ACSFile::get_file_info_instance($file_id);

		// �ե������������
		$submit_kind = $request->getParameter('submit_kind');

		// �ץåȥե�����Ǥʤ����
		if ($file_obj->get_owner_community_id() == $target_community_id) {
			if($submit_kind != "" && $is_community_admin){
				// �ե��������URL����
				if($submit_kind == "insert"){
					$form['folder_id'] = $target_community_folder_id;
					$form['community_id'] = $target_community_id;
					ACSFileDetailInfo::insert_file_public_access($file_id, $form);

				// �ե��������URL���
				}else if($submit_kind == "delete"){
					ACSFileDetailInfo::delete_file_public_access($file_id);

				// �ե�������������������ꥻ�å�
				}else if($submit_kind == "reset"){
					$form['access_count'] = 0;
					$form['access_start_date'] = "'now'";
					ACSFileDetailInfo::update_file_public_access($file_id, $form);
				}
			}
		}

		$contents_link_url = 
						$this->getControllerPath('Community', 'FileDetail') . 
						"&community_id=" . $target_community_id . 
						"&file_id=" . $file_obj->get_file_id() . 
						"&folder_id=" . $community_folder_obj->folder_obj->get_folder_id();

		header("Location: $contents_link_url");

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
}

?>
