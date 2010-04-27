<?php
/**
 * ���������Բĥ�å�����ɽ��
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/03/27 07:49:27 $
 */
class GlobalSecureAction extends Action
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$user_id = $user->getAttribute('login_user_id');
		return View::SUCCESS;
	}

	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$user_id = $user->getAttribute('login_user_id');
		if ($user_id == null || $user_id == "") {
			if (ACSSystem::check_connect_outside() != "0") {
				// LDAP��³���顼�ξ�硢������λ
				$this->setError($controller, $request, $user, 'login_input', 
							ACSMsg::get_msg('Common', 'GlobalSecureAction.class.php', 'M002'));
			}
			else if ($_POST['userid'] != NULL && $_POST['userid'] != "") {
				// ���顼�ξ�硢������λ
				$this->setError($controller, $request, $user, 'login_input', 
							ACSMsg::get_msg('Common', 'GlobalSecureAction.class.php', 'M001'));
			}
			return View::INPUT;
		}

		if ($acs_user_info_row['is_acs_user']) {
			// ����Ͽ: ������
			ACSLog::set_log($acs_user_info_row, 'Login', true, 
					"[UserID:" .$acs_user_info_row['user_id']. "]");

			// �饹�ȥ�������Ͽ
			ACSUser::set_last_login($acs_user_info_row);
			header("Location: ./" . $_SERVER['REQUEST_URI']);
		}
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods() {
		return Request::POST;
	}
}
?>
