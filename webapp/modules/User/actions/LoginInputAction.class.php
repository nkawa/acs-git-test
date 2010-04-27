<?php
// $Id: LoginInputAction.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $

class LoginInputAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ɽ���оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		if (empty($user_community_id)) {
			$user_community_id = $acs_user_info_row['user_community_id'];
		}
	
		$user_id = $user->getAttribute('login_user_id');
		if ($user_id == null || $user_id == "") {
			return View::INPUT;
		}
		return View::ERROR;
	}

	/**
	 * ǧ�ڥ����å���Ԥ���
	 * ����������¹Ԥ������ˡ�ǧ�ڥ����å���ɬ�פ����ꤹ��
	 * @access  public
	 * @return  boolean ǧ�ڥ����å�̵ͭ��true:ɬ�ס�false:���ס�
	 */
	public function isSecure()
	{
		return false;
	}
}

?>
