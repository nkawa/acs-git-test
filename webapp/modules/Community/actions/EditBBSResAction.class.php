<?php
// $Id: EditBBSResAction.class.php,v 1.2 2006/11/20 08:44:12 w-ota Exp $

class EditBBSResAction extends BaseAction
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
		$bbs_res_id = $request->getParameter('bbs_res_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		// BBS記事一覧
		$bbs_res_row = ACSBBS::get_bbs_res_row($bbs_res_id);

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('bbs_res_row', $bbs_res_row);


		// (投稿者本人 or コミュニティ管理者)のみがアクセスできる
		if ($acs_user_info_row['user_community_id'] != $bbs_res_row['user_community_id']
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
		$request = $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$community_id = $request->getParameter('community_id');
		$bbs_id = $request->getParameter('bbs_id');
		$bbs_res_id = $request->getParameter('bbs_res_id');

		// 入力データ
		$form = $request->ACSGetParameters();
		$form['user_community_id'] = $acs_user_info_row['user_community_id'];

		// bbs更新
		$ret = ACSBBS::update_bbs_res($form);

		$bbs_res_url = $this->getControllerPath('Community', 'BBSRes')
			 . '&community_id=' . $community_id . '&bbs_id=' . $bbs_id;
		header("Location: $bbs_res_url");
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
		/* 必須チェック */
		parent::regValidateName($validatorManager, 
				"subject", 
				true, 
				ACSMsg::get_msg('Community', 'EditBBSResAction.class.php', 'M001'));
		parent::regValidateName($validatorManager, 
				"body", 
				true, 
				ACSMsg::get_msg('Community', 'EditBBSResAction.class.php', 'M002'));
	}

	function handleError () {
		$context = $this->getContext();
		$request = $context->getRequest();
		
		// 入力値を set
		$form = $request->ACSGetParameters();
		$request->setAttribute('form', $form);

		// 入力画面表示
		return $this->getDefaultView();
	}
}

?>
