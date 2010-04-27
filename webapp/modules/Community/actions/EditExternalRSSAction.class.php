<?php
// 外部RSS情報自動取り込み設定
// $Id: EditExternalRSSAction.class.php,v 1.1 2007/03/28 05:58:18 w-ota Exp $

class EditExternalRSSAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// get
		$external_rss_url_open_level_master_row_array = 
			ACSAccessControl::get_open_level_master_row_array(
				ACSMsg::get_mst('community_type_master','D40'), 
				ACSMsg::get_mst('contents_type_master','D63'));

		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);
		$community_row = ACSExternalRSS::add_contents_row_array($community_row);


		// 入力エラー時の復元 //
		$form = $request->ACSGetParameters();
		if ($form['is_error']) {
			$community_row['contents_row_array']['external_rss_url']['contents_value'] = $form['external_rss_url'];
			$community_row['contents_row_array']['external_rss_url']['open_level_code'] = $form['external_rss_url_open_level_code'];
			$community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'] = array();
			if (is_array($form['external_rss_url_trusted_community_id_array'])) {
				foreach ($form['external_rss_url_trusted_community_id_array'] as $trusted_community_id) {
					array_push($community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'],
							   array('community_id' => $trusted_community_id));
				}
			}
			$community_row['contents_row_array']['external_rss_post_user']['contents_value'] = $form['external_rss_post_user'];
			$community_row['contents_row_array']['external_rss_public_release_expire_term']['contents_value'] = $form['external_rss_public_release_expire_term'];
			$community_row['contents_row_array']['external_rss_ml_send_flag']['contents_value'] = $form['external_rss_ml_send_flag'];
		}

		// 初期値 //
		// 投稿者
		if (!isset($community_row['contents_row_array']['external_rss_post_user']['contents_value'])) {
			$community_row['contents_row_array']['external_rss_post_user']['contents_value'] = $acs_user_info_row['user_community_id'];
		}
		// 公開範囲
		if (!isset($community_row['contents_row_array']['external_rss_url']['open_level_code'])) {
			$community_row['contents_row_array']['external_rss_url']['open_level_code'] =
				 $community_row['contents_row_array']['bbs']['open_level_code'];
			$community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'] =
				 $community_row['contents_row_array']['bbs']['trusted_community_row_array'];
		}

		// コミュニティ管理者情報
		$community_admin_user_info_row_array = ACSCommunity::get_community_admin_user_info_row_array($community_row['community_id']);

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('community_admin_user_info_row_array', $community_admin_user_info_row_array);
		$request->setAttribute('external_rss_url_open_level_master_row_array', $external_rss_url_open_level_master_row_array);

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

		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		$form = $request->ACSGetParameters();

		$ret = ACSExternalRSS::set_external_rss_contents($community_id, $form);

		// forward
		$done_obj = new ACSDone();

		if ($ret) {
			$done_obj->set_title(ACSMsg::get_msg('Community', 'EditExternalRSSAction.class.php', 'M001'));
			$done_obj->set_message(ACSMsg::get_msg('Community', 'EditExternalRSSAction.class.php', 'M002'));
			$done_obj->add_link($community_row['community_name'] . ' ' . ACSMsg::get_msg('Community', 'EditExternalRSSAction.class.php','M003'), $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id']);
		} else {
			$done_obj->set_title(ACSMsg::get_msg('Community', 'EditExternalRSSAction.class.php','M004'));
		}

		$request->setAttribute('done_obj', $done_obj);
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function validate() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$open_level_master_array = ACSDB::get_master_array('open_level');

		$params =& $request->getParameters();
		$result = true;

		// RSS URL
		if ($params['external_rss_url'] != '' && !preg_match('/^http:\/\/.+/', $params['external_rss_url'])) {
			$request->setError('external_rss_url', ACSMsg::get_msg('Community', 'EditExternalRSSAction.class.php', 'M005'));
			$result = false;
		}

		// 公開範囲
		if ($open_level_master_array[$params['external_rss_url_open_level_code']] == ACSMsg::get_mst('open_level_master','D06')) {
			if ($params['external_rss_public_release_expire_term'] != ''
				&& (!preg_match('/^[0-9]+/', $params['external_rss_public_release_expire_term']) || intval($params['external_rss_public_release_expire_term']) < 1)) {
				$request->setError('external_rss_public_release_expire_term', ACSMsg::get_msg('Community', 'EditExternalRSSAction.class.php', 'M006'));
				$result = false;
			}
		}

		return $result;
	}

	function registerValidators(&$validatorManager) {
		// 必須チェック
		parent::regValidateName($validatorManager, 
				"external_rss_post_user", 
				true, 
				ACSMsg::get_msg('Community', 'EditExternalRSSAction.class.php', 'M007'));
	}

	function handleError() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		// 入力値をset
		$form = $request->ACSGetParameters();

		// 入力エラー時のデータ復元
		$request->setAttribute('is_error', 1);
		$request->setAttribute('external_rss_url', $form['external_rss_url']);
		$request->setAttribute('external_rss_post_user', $form['external_rss_post_user']);
		$request->setAttribute('external_rss_url_open_level_code', $form['external_rss_url_open_level_code']);
		$request->setAttribute('external_rss_url_trusted_community_id_array', $form['external_rss_url_trusted_community_id_array']);
		$request->setAttribute('external_rss_public_release_expire_term', $form['external_rss_public_release_expire_term']);
		$request->setAttribute('external_rss_ml_send_flag', $form['external_rss_ml_send_flag']);

		// 入力画面表示
		return $this->getDefaultView();
	}

	function isSecure() {
		return false;
	}

	function getCredential() {
		return array('COMMUNITY_ADMIN');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// コミュニティ管理者はOK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		return false;
	}
}

?>
