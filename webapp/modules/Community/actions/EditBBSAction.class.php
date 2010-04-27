<?php
// $Id: EditBBSAction.class.php,v 1.2 2006/11/20 08:44:12 w-ota Exp $

class EditBBSAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$community_id = $request->getParameter('community_id');
		$bbs_id = $request->getParameter('bbs_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_row($community_id);

		// BBS��������
		$bbs_row = ACSBBS::get_bbs_row($bbs_id);

		// ����Ѥߥ��ߥ�˥ƥ�����
		$bbs_row['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('bbs_row', $bbs_row);


		// (��Ƽ��ܿ� or ���ߥ�˥ƥ�������)�Τߤ����������Ǥ���
		if ($acs_user_info_row['user_community_id'] != $bbs_row['user_community_id']
			&& !ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $community_row['community_id'])) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser(); 	
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$community_id = $request->getParameter('community_id');
		$bbs_id = $request->getParameter('bbs_id');

		// ���ϥǡ���
		$form = $request->ACSGetParameters();
		$form['user_community_id'] = $acs_user_info_row['user_community_id'];

		// bbs����
		ACSDB::_do_query("BEGIN");
		$ret = ACSBBS::update_bbs($form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			exit;
		}

		ACSDB::_do_query("COMMIT");

		$bbs_url = $this->getControllerPath('Community', 'BBS'). '&community_id=' . $community_id;
		header("Location: $bbs_url");
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function isSecure() {
		return false;
	}

	function getCredential() {
		return array('COMMUNITY_MEMBER');
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* ɬ�ܥ����å� */
		parent::regValidateName($validatorManager, 
				"subject", 
				true, 
				ACSMsg::get_msg('Community', 'EditBBSAction.class.php', 'M001'));
		parent::regValidateName($validatorManager, 
				"body", 
				true, 
				ACSMsg::get_msg('Community', 'EditBBSAction.class.php', 'M002'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		// �����ͤ� set
		$form = $request->ACSGetParameters();
		$request->setAttribute('form', $form);

		// ���ϲ���ɽ��
		return $this->getDefaultView();
	}
}

?>
