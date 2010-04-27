<?php
// $Id: IndexAction.class.php,v 1.12 2006/12/08 05:06:34 w-ota Exp $

class IndexAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$controller = $context->getController();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		
		// 対象となるコミュニティIDを取得
		$community_id = $request->ACSGetParameter('community_id');

		// コミュニティ存在チェック
		$community_row = ACSCommunity::get_community_row($community_id);
		if (!$community_row || $community_row['community_type_name'] != ACSMsg::get_mst('community_type_master','D40')) {
			return View::ERROR;
		}

		// 権限チェック
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// サブコミュニティ情報の一覧
		$sub_community_row_array = ACSCommunity::get_sub_community_row_array($community_id);

		// 親コミュニティ情報の一覧
		$parent_community_row_array = ACSCommunity::get_parent_community_row_array($community_id);

		// コミュニティメンバ, コミュニティ管理者
		$community_member_user_info_row_array = ACSCommunity::get_community_member_user_info_row_array($community_id);
		$community_admin_user_info_row_array = ACSCommunity::get_community_admin_user_info_row_array($community_id);

		// 待機: コミュニティ参加 承認待ち
		$waiting_for_join_community_row_array = ACSWaiting::get_waiting_row_array($community_id, ACSMsg::get_mst('waiting_type_master','D20'), ACSMsg::get_mst('waiting_status_master','D10'));

		// 待機: 親コミュニティ追加, サブコミュニティ追加
		$waiting_for_parent_community_link_row_array = ACSWaiting::get_waiting_row_array($community_id, ACSMsg::get_mst('waiting_type_master','D40'), ACSMsg::get_mst('waiting_status_master','D10'));
		$waiting_for_sub_community_link_row_array = ACSWaiting::get_waiting_row_array($community_id, ACSMsg::get_mst('waiting_type_master','D50'), ACSMsg::get_mst('waiting_status_master','D10'));

		// 最新情報: BBS
		// BBS記事一覧
		$bbs_row_array = ACSBBS::get_bbs_row_array($community_id);
		foreach ($bbs_row_array as $index => $bbs_row) {
			// 信頼済みコミュニティ一覧
			$bbs_row_array[$index]['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('sub_community_row_array', $sub_community_row_array);
		$request->setAttribute('parent_community_row_array', $parent_community_row_array);
		$request->setAttribute('community_member_user_info_row_array', $community_member_user_info_row_array);
		$request->setAttribute('community_admin_user_info_row_array', $community_admin_user_info_row_array);
		$request->setAttribute('waiting_for_join_community_row_array', $waiting_for_join_community_row_array);
		$request->setAttribute('waiting_for_parent_community_link_row_array', $waiting_for_parent_community_link_row_array);
		$request->setAttribute('waiting_for_sub_community_link_row_array', $waiting_for_sub_community_link_row_array);
		$request->setAttribute('bbs_row_array', $bbs_row_array);

		return View::SUCCESS;
	}

	/**
	 * 認証チェックを行うか
	 * アクションを実行する前に、認証チェックが必要か設定する
	 * @access  public
	 * @return  boolean 認証チェック有無（true:必要、false:不要）
	 */
	function isSecure()
	{
		return false;
	}

	function getCredential () {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();

		// 非公開コミュニティはメンバのみアクセス可能
		$community_self_info_row = ACSCommunity::get_contents_row($request->getParameter('community_id'), ACSMsg::get_mst('contents_type_master','D00'));
		if ($community_self_info_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
			return array('COMMUNITY_MEMBER');
		}
		return array();
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// コミュニティメンバはOK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}

		// 非公開コミュニティはコミュニティメンバは不可能
		$community_self_info_row = ACSCommunity::get_contents_row($request->getParameter('community_id'), ACSMsg::get_mst('contents_type_master','D00'));
		if ($community_self_info_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
			return false;
		}
		return true;
	}
}

?>
