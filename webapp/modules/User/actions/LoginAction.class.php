<?php
// $Id: LoginAction.class.php,v 1.8 2008/04/24 16:00:00 y-yuki Exp $

class LoginAction extends BaseAction
{
	function execute() {
				
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$user_id = $user->getAttribute('login_user_id');
		if ($user_id == null || $user_id == "") {
			if (ACSSystem::check_connect_outside() != "0") {
				// LDAP��³���顼�ξ�硢������λ
				$this->setError($controller, $request, $user, 'login_input', 
							ACSMsg::get_msg('User', 'LoginAction.class.php' ,'M002'));
			}
			else if ($_POST['userid'] != NULL && $_POST['userid'] != "") {
				// ���顼�ξ�硢������λ
				$this->setError($controller, $request, $user, 'login_input', 
							ACSMsg::get_msg('User', 'LoginAction.class.php' ,'M001'));
			}

			return View::INPUT;
		}

		if ($acs_user_info_row['is_acs_user']) {
			// ����Ͽ: ������
			ACSLog::set_log($acs_user_info_row, 'Login', true, "[UserID:{$acs_user_info_row['user_id']}]");

			// �饹�ȥ�������Ͽ
			ACSUser::set_last_login($acs_user_info_row);
			header("Location: ./");
		}
		return View::NONE;
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
