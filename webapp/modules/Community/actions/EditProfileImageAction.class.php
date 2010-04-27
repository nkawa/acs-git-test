<?php
/**
 * �ץ�ե�����̿��ѹ����� Action���饹
 * @package  acs/webapp/modules/Community/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  ver1.0  2006/02/14 $
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

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('community_id');
		// �ץ�ե������������
		$target_community_info_row = ACSCommunity::get_community_row($user_community_id);
		// ��������������
		$back_url =  $this->getControllerPath('Community','Index');
		$back_url .= '&community_id=' . $user_community_id;

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ������������Ͽ����������Ƚ�ꤹ�� true:���� false:����
		// �ե���������¸�߳�ǧ

		$image_file_id = $target_community_info_row['file_id'];// �ե�����������
		if ($image_file_id) {
			$image_new_add = false;
		} else {
			$image_new_add = true;
		}
			
		// set
		//user_community_id ��view���饹�����Τ���
		$request->setAttribute('community_id', $user_community_id);
		//target_user_info_row��view���饹�����Τ���
		$request->setAttribute('target_community_info_row', $target_community_info_row);
		//������������Ͽ����������image_new_add��view���饹�����Τ���
		$request->setAttribute('image_new_add',$image_new_add);
		//��������view���饹�����Τ���
		$request->setAttribute('back_url', $back_url);	

		return View::INPUT;
	}

	function getRequestMethods() {
			return Request::GET;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_ADMIN');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// ���ߥ�˥ƥ������Ԥ�OK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		return false;
	}
}
?>
