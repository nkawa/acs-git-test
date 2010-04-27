<?php
// $Id: CreateCommunityAction.class.php,v 1.7 2006/12/28 07:36:13 w-ota Exp $

class CreateCommunityAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$bbs_open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D41'));
		$community_folder_open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D31'));
		$self_open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D00'));

		// カテゴリグループマスタ
		$category_group_master_row_array = ACSCommunity::get_category_group_master_row_array();
		foreach ($category_group_master_row_array as $index => $category_group_master_row) {
			// カテゴリグループごとのカテゴリマスタ
			$category_group_master_row_array[$index]['category_master_row_array'] = ACSCommunity::get_category_master_row_array_by_category_group_code($category_group_master_row['category_group_code']);
		}

		// set
		$request->setAttribute('bbs_open_level_master_row_array', $bbs_open_level_master_row_array);
		$request->setAttribute('community_folder_open_level_master_row_array', $community_folder_open_level_master_row_array);
		$request->setAttribute('category_group_master_row_array', $category_group_master_row_array);
		$request->setAttribute('self_open_level_master_row_array', $self_open_level_master_row_array);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$form = $request->ACSGetParameters();
		$form['user_community_id'] = $acs_user_info_row['user_community_id'];

		if ($form['community_ml_address'] != '') {

			// コミュニティMLにプレフィックス・サフィックスを追加
			$form['community_ml_address'] = 
					ACS_COMMUNITY_ML_ADDR_PREFIX .
					$form['community_ml_address'] .
					ACS_COMMUNITY_ML_ADDR_SUFFIX;
		}

		// コミュニティ作成
		$community_id = ACSCommunity::set_community($form);

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		// forward
		$done_obj = new ACSDone();
		$done_obj->set_title(ACSMsg::get_msg('Community', 'CreateCommunityAction.class.php', 'M001'));
		$done_obj->set_message(ACSMsg::get_msg('Community', 'CreateCommunityAction.class.php', 'M002'));
		$done_obj->add_link($community_row['community_name'] . ' '.ACSMsg::get_msg('Community', 'CreateCommunityAction.class.php', 'M003'), $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id']);

		$request->setAttribute('done_obj', $done_obj);
		$controller->forward('Common', 'Done');
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods() {
		return Request::POST;
	}

	/**
	 * 入力値チェック
	 */
	function validate () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// パラメータ取得
		$params =& $request->getParameters();

		$result = TRUE;

		// メールアドレスのチェック
		if ($params['community_ml_address']) {
			// 英数字-_のチェック
			if (!ereg( "^[a-z|A-Z|0-9|_|\-]+$", $params['community_ml_address'])) {
				$request->setError("community_ml_address",
						ACSMsg::get_msg('Community', 'CreateCommunityAction.class.php', 'M051'));
				$result = FALSE;
			}
			// 英数字始まりのチェック
			if (!ereg( "^[a-z|A-Z|0-9]+", $params['community_ml_address'])) {
				$request->setError("community_ml_address",
						ACSMsg::get_msg('Community', 'CreateCommunityAction.class.php', 'M052'));
				$result = FALSE;
			}
			// 利用できない名前のチェック
			$ng_name_array = explode(",",ACS_COMMUNITY_ML_ADDR_NGNAMES);
			if (in_array(strtolower($params['community_ml_address']),$ng_name_array)) {
				$request->setError("community_ml_address",
						ACSMsg::get_msg('Community', 'CreateCommunityAction.class.php', 'M053').ACS_COMMUNITY_ML_ADDR_NGNAMES);
				$result = FALSE;
			}
			// 既存のメールアドレス
			if (ACSCommunity::is_exists_ml_addr(
					ACS_COMMUNITY_ML_ADDR_PREFIX .
					$params['community_ml_address'] .
					ACS_COMMUNITY_ML_ADDR_SUFFIX)) {
				$request->setError("community_ml_address",
						ACSMsg::get_msg('Community', 'CreateCommunityAction.class.php', 'M054'));
				$result = FALSE;
			}
		}

		return $result;
	}

	/**
	 * 入力値チェック(ValidatorManager使用)
	 */
	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$request =  $context->getRequest();

		$params =& $request->ACSGetParameters();

		// コミュニティ名 [schedule_name]
		parent::regValidateName($validatorManager, 
				"community_name", 
				true, 
				ACSMsg::get_msg('Community', 'CreateCommunityAction.class.php', 'M007'));
	}

	/**
	 * 入力チェックエラー時の対応
	 */
	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// パラメータ取得
		$params =& $request->getParameters();
		$result = $this->getDefaultView();

		if ($result != View::INPUT) {
			return $result;
		}

		$community_row =& $request->getAttribute('community_row');

		// コミュニティ情報の復元
		$community_row['community_name']    = $params['community_name'];
		$community_row['category_code']     = $params['category_code'];
		$community_row['admission_flag']    = 
				$params['admission_flag'] == '1' ? 't' : 'f';
		$community_row['contents_row_array']['community_profile']['contents_value'] =
				$params['community_profile'];
		$community_row['contents_row_array']['bbs']['open_level_code'] =
				$params['bbs_open_level_code'];
		$community_row['contents_row_array']['community_folder']['open_level_code'] =
				$params['community_folder_open_level_code'];
		$community_row['contents_row_array']['self']['open_level_code'] =
				$params['self_open_level_code'];

		// 参加資格の公開範囲の復元
		$join_trusted_community_row_array =& 
				$community_row['join_trusted_community_row_array'];
		$join_trusted_community_row_array = array();
		if (is_array($params['join_trusted_community_id_array'])) {
			foreach ($params['join_trusted_community_id_array'] as $trusted_community_id) {
				$join_trusted_community_row_array[] = 
						ACSCommunity::get_community_row($trusted_community_id);
			}
		}

		// 掲示板の公開範囲の復元
		$bbs_trusted_community_row_array =& 
				$community_row['contents_row_array']['bbs']['trusted_community_row_array'];
		$bbs_trusted_community_row_array = array();
		if (is_array($params['bbs_trusted_community_id_array'])) {
			foreach ($params['bbs_trusted_community_id_array'] as $trusted_community_id) {
				$bbs_trusted_community_row_array[] = 
						ACSCommunity::get_community_row($trusted_community_id);
			}
		}

		// コミュニティフォルダの公開範囲の復元
		$community_folder_trusted_community_row_array =& 
				$community_row['contents_row_array']['community_folder']['trusted_community_row_array'];
		$community_folder_trusted_community_row_array = array();
		if (is_array($params['community_folder_trusted_community_id_array'])) {
			foreach ($params['community_folder_trusted_community_id_array'] as $trusted_community_id) {
				$community_folder_trusted_community_row_array[] = 
						ACSCommunity::get_community_row($trusted_community_id);
			}
		}

		// 編集中のコミュニティメールアドレスの復元
		$request->setAttributeByRef('edit_community_ml_address',
				$params['community_ml_address']);

		$request->setAttributeByRef('community_row',$community_row);

		return View::INPUT;
	}
	
	function getCredential () {
		return array('ACS_USER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 非ログインユーザはNG
		if ($user->hasCredential('PUBLIC_USER')) {
			return false;
		}
		return true;
	}

}

?>
