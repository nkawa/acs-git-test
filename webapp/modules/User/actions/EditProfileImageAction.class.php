<?php
/**
 * �ץ�ե�����̿��ѹ����� Action���饹
 * @package  acs/webapp/modules/User/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  ver1.1 $Date: 2008/03/24 07:00:36 $
 */

class EditProfileImageAction extends BaseAction
{
	/**
	 * �������
	 * GET�᥽�åɤξ�硢�ƤФ��
	 */
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		/* ���顼����� */
		//$error_row = $user->getAttribute('error_row');
		//$user->removeAttribute('error_row');

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		// �ץ�ե������������
		$target_user_info_row = ACSUser::get_user_profile_row($user_community_id);

		// ������٥륳����
		$open_level_code_row = array('05', '02', '01');

		//������������Ͽ����������Ƚ�ꤹ�� true:���� false:����
		// �ե���������¸�߳�ǧ
		
		// ��٥�		
		for ($i = 0; $i < count($open_level_code_row); $i++) {
			//
			$image_file_id = $target_user_info_row['file_id_ol' . $open_level_code_row[$i]];
			if ($image_file_id) {
				$image_new_add['file_id_ol' . $open_level_code_row[$i]] = false;
			} else {
				$image_new_add['file_id_ol' . $open_level_code_row[$i]] = true;
			}
		}

		// set
		//user_community_id ��view���饹�����Τ���
		$request->setAttribute('user_community_id', $user_community_id);
		//target_user_info_row��view���饹�����Τ���
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		//������������Ͽ����������image_new_add��view���饹�����Τ���
		$request->setAttribute('image_new_add',$image_new_add);
		$request->setAttribute('open_level_code_row', $open_level_code_row);
//		$request->setAttribute('image_file_label', $image_file_label);

		return View::INPUT;
	}

	function getRequestMethods() {
			return Request::GET;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		// ��ʬ�Υ桼����������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		if ($user_community_id == $acs_user_info_row['user_community_id']) {
			// ���桼���Τ��ѹ�OK
			return true;
		}
		return false;
	}
}
?>
