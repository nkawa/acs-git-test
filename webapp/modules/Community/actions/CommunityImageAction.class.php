<?php
/**
 * �ץ�ե�����̿�ɽ��
 *
 * @author  kuwayama
 * @version $Revision: 1.3 $ $Date: 2007/03/28 02:51:43 $
 */
class CommunityImageAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser(); 
		$community_id      = $request->getParameter('community_id');
		$view_mode         = $request->getParameter('mode');
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$community_row = ACSCommunity::get_community_profile_row($community_id);

		$is_permitted = false;
		/* ���������������å� */
		// ������ǽ�������å�����
		// ����ե饰�����Τθ����ϰϤ�����å�

		/* �̿�ɽ�� */
		// �ե�����������
		$image_file_id = $community_row['file_id'];
		if ($image_file_id) {
			$file_obj = ACSFile::get_file_info_instance($image_file_id);
			$ret = $file_obj->view_image($view_mode);
		} else {
			$image_url = ACSCommunity::get_default_image_url($view_mode);
			header("Location: $image_url");
		}

	}

	function getRequestMethods () {
		return Request::GET;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();  
		// ��������ߥ�˥ƥ��ϥ��ФΤߥ���������ǽ
		$community_self_info_row = ACSCommunity::get_contents_row($request->getParameter('community_id'), ACSMsg::get_mst('contents_type_master','D00'));
		if ($community_self_info_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
			return array('COMMUNITY_MEMBER');
		}
		return array();
	}
}
?>
