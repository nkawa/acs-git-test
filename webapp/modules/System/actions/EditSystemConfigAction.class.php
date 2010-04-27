<?php
/**
 * �����ƥ������Խ�
 *
 * @author  kuwayama
 * @version $Revision: 1.8 $ $Date: 2007/03/01 09:01:39 $
 */
class EditSystemConfigAction extends BaseAction
{
	// GET
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		// �����Ԥ��ɤ�����ǧ
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// get
		// �����ƥ�����ǡ��������������
		$system_config_obj = new ACSSystemConfig();

		// set
		$request->setAttribute('system_config_obj', $system_config_obj);

		return View::INPUT;
	}

	// POST
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		// �����Ԥ��ɤ�����ǧ
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$form = $request->ACSGetParameters();

		// �����ƥ�����ǡ��������������
		$system_config_obj = new ACSSystemConfig();


		// Validator�ǽ���ʤ����顼�����å���Ԥ� //
		$err_flg = false;
		foreach ($system_config_obj->get_system_config_keyword_data_obj_array() as $get_system_config_keyword_data_obj) {
			$keyword = $get_system_config_keyword_data_obj->get_keyword();
			$system_config_group_name = $get_system_config_keyword_data_obj->get_system_config_group_name();
			$name = $get_system_config_keyword_data_obj->get_name();
			$type = $get_system_config_keyword_data_obj->get_type();
			// number�ι��ܤϼ�����
			if ($type == 'number' && !ACSErrorCheck::is_natural_number($form[$keyword])) {
				//$this->setError($controller, $request, $user, $keyword, "[$system_config_group_name] -> [$name] ���ͤ�����������ޤ���(1�ʾ�)");
				$this->setError($controller, $request, $user, $keyword, 
					ACSMsg::get_tag_replace(ACSMsg::get_msg('System', 'EditSystemConfigAction.class.php', 'M_WRONG_VALUE'),
						array( "{GROUP_NAME}" => $system_config_group_name, 
							"{NAME}" => $name,
							"{VAL}" => 1)));
				$err_flg = true;
			}
			// number0�ι��ܤϼ�����(����ޤ�)
			if ($type == 'number0' && 
					!ACSErrorCheck::is_natural_number($form[$keyword],true)) {
				$this->setError($controller, $request, $user, $keyword, 
					ACSMsg::get_tag_replace(ACSMsg::get_msg('System', 'EditSystemConfigAction.class.php', 'M_WRONG_VALUE'),
						array( "{GROUP_NAME}" => $system_config_group_name, 
							"{NAME}" => $name,
							"{VAL}" => 0)));
				$err_flg = true;
			}
		}
		if ($err_flg) {
			return $this->handleError();
		}


		$ret = true;
		ACSDB::_do_query("BEGIN");

		foreach ($system_config_obj->get_system_config_keyword_data_obj_array() as $system_config_keyword_data_obj) {
			$_system_config_row = array();
			$param_key = $system_config_keyword_data_obj->get_keyword();
			//$param_key = $system_config_keyword_data_obj->get_system_config_group() . ',' . $system_config_keyword_data_obj->get_keyword();

			// parameter ����
			$update_value = $request->getParameter($param_key, 'NO_KEY');
			if ($update_value == 'NO_KEY') {
				// POST �˥������ʤ���硢̵�뤹��
				continue;
			}

			// ɬ�ܥ����å�
			//if (!$update_value) {
			if (trim($update_value)=='') {
				$ret = false;
				break;
			}

			// �ͥ����å�
			// number ���Υǡ����ξ�硢���ͥ����å���1 �ʾ�ˤ�Ԥ�
			if ($system_config_keyword_data_obj->get_type() == 'number') {
				if (!is_numeric($update_value) or ($update_value < 1)) {
					$ret = false;
					break;
				}
			}
			// number0 ���Υǡ����ξ�硢���ͥ����å���0 �ʾ�ˤ�Ԥ�
			if ($system_config_keyword_data_obj->get_type() == 'number0') {
				if (!is_numeric($update_value) or ($update_value < 0)) {
					$ret = false;
					break;
				}
			}

			// ��������
			$ret = ACSSystemConfig::update_value($system_config_keyword_data_obj->get_system_config_group_name(),
												 $system_config_keyword_data_obj->get_keyword(),
												 $update_value);
			if (!$ret) {
				break;
			}
		}
		if (!$ret) {
			// rollback
			ACSDB::_do_query("ROLLBACK");
			print "ERROR: Update configuration failed.";
			exit;
		}

		// commit
		ACSDB::_do_query("COMMIT");

		// ����Ͽ: �����ƥ������ѹ�
		ACSLog::set_log($acs_user_info_row, 'Change System Settings', $ret);

		// ��λ����ɽ��
		// �������å�
		$message = ACSMsg::get_msg('System', 'EditSystemConfigAction.class.php', 'M005');
		$system_config_url	   = $this->getControllerPath('System', 'EditSystemConfig');
		$system_config_link_name = ACSMsg::get_msg('System', 'EditSystemConfigAction.class.php', 'M002');

		$system_top_page_url	   = $this->getControllerPath('System', DEFAULT_ACTION);
		$system_top_page_link_name = ACSMsg::get_msg('System', 'EditSystemConfigAction.class.php', 'M003');

		$done_obj = new ACSDone();

		$done_obj->set_title(ACSMsg::get_msg('System', 'EditSystemConfigAction.class.php', 'M004'));
		$done_obj->set_message($message);
		$done_obj->add_link($system_config_link_name, $system_config_url);
		$done_obj->add_link($system_top_page_link_name, $system_top_page_url);

		$request->setAttribute('done_obj', $done_obj);

		// ���̸ƤӽФ�
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('SYSTEM_ADMIN_USER');
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		$system_config_obj = new ACSSystemConfig();

		/* ɬ�ܥ����å� */
		foreach ($system_config_obj->get_system_config_keyword_data_obj_array() as $get_system_config_keyword_data_obj) {
			$keyword = $get_system_config_keyword_data_obj->get_keyword();
			$system_config_group_name = $get_system_config_keyword_data_obj->get_system_config_group_name();
			$name = $get_system_config_keyword_data_obj->get_name();

			parent::regValidateName($validatorManager, 
					$keyword, 
					true, 
					ACSMsg::get_tag_replace(
							ACSMsg::get_msg('System', 'EditSystemConfigAction.class.php', 'M_EMPTY_VALUE'),
									array("{GROUP_NAME}" => $system_config_group_name,
									"{NAME}" => $name)));
		}
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		// �����ͤ� set
		$form = $request->ACSGetParameters();
		$request->setAttribute('form', $form);

		// ���ϲ���ɽ��
		return $this->getDefaultView();
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �����Ԥξ���OK
		if ($user->hasCredential('SYSTEM_ADMIN_USER')) {
			return true;
		}
		return false;
	}
}
?>
