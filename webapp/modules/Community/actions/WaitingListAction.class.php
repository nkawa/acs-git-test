<?php
// $Id: WaitingListAction.class.php,v 1.7 2006/11/20 08:44:12 w-ota Exp $

class WaitingListAction extends BaseAction
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

		// 対象となるユーザコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);

		// 待機種別コード
		$waiting_type_code = $request->ACSgetParameter('waiting_type_code');
		// 待機状態コード
		$waiting_status_code = $request->ACSgetParameter('waiting_status_code');

		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		$waiting_type_name = $waiting_type_master_array[$waiting_type_code];
		$waiting_status_name = $waiting_status_master_array[$waiting_status_code];

		// 待機
		$waiting_row_array = ACSWaiting::get_waiting_row_array($community_id, $waiting_type_name, $waiting_status_name);
		if ($waiting_type_name == ACSMsg::get_mst('waiting_type_master','D40') || $waiting_type_name == ACSMsg::get_mst('waiting_type_master','D50')) {
			foreach ($waiting_row_array as $index => $waiting_row) {
				$waiting_row_array[$index]['entry_user_info_row'] = ACSUser::get_user_info_row_by_user_community_id($waiting_row['entry_user_community_id']);
			}
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('waiting_type_name', $waiting_type_name);
		$request->setAttribute('waiting_row_array', $waiting_row_array);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$form = $request->ACSgetParameters();

		// 待機種別マスタ
		$waiting_type_master_array = ACSDB::get_master_array('waiting_type');
		// 待機状態マスタ
		$waiting_status_master_array = ACSDB::get_master_array('waiting_status');

		// 待機情報
		$waiting_row = ACSWaiting::get_waiting_row($form['waiting_id']);

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($waiting_row['community_id']);

		// forward
		$done_obj = new ACSDone();


		if ($form['accept_button']) {
			// 承認ボタン押下時 //
			ACSDB::_do_query("BEGIN");

			if ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D20')) {
				// コミュニティメンバ追加
				$community_member_form = array();
				$community_member_form['community_id'] = $waiting_row['community_id'];
				$community_member_form['user_community_id'] = $waiting_row['waiting_community_id'];
				$ret = ACSCommunity::set_community_member($community_member_form);
			} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D40')) {
				// コミュニティ間リンク設定
				$ret = ACSCommunity::set_community_link($waiting_row['community_id'], $waiting_row['waiting_community_id']);
			} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D50')) {
				// コミュニティ間リンク設定
				$ret = ACSCommunity::set_community_link($waiting_row['waiting_community_id'], $waiting_row['community_id']);
			}

			if ($ret) {
				// 承認済みをセット
				$ret = ACSWaiting::update_waiting_waiting_status_code($form['waiting_id'], ACSMsg::get_mst('waiting_status_master','D20'), $acs_user_info_row['user_community_id'], $form['reply_message']);
				if ($ret) {
					ACSDB::_do_query("COMMIT");

					// 整合性保持
					if ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D20')) {
						// 参加の重複を解除
						ACSWaiting::update_waiting_for_join_community($waiting_row['community_id'], $waiting_row['waiting_community_id']);
						// 招待の重複を解除
						ACSWaiting::update_waiting_for_invite_to_community($waiting_row['waiting_community_id'], $waiting_row['community_id']);
					} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D40')) {
						// 親コミュニティ追加の重複解除
						ACSWaiting::update_waiting_for_parent_community_link($waiting_row['community_id'], $waiting_row['waiting_community_id']);
						// サブコミュニティ追加の重複解除
						ACSWaiting::update_waiting_for_sub_community_link($waiting_row['waiting_community_id'], $waiting_row['community_id']);
					} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D50')) {
						// サブコミュニティ追加の重複解除
						ACSWaiting::update_waiting_for_sub_community_link($waiting_row['community_id'], $waiting_row['waiting_community_id']);
						// 親コミュニティ追加の重複解除
						ACSWaiting::update_waiting_for_parent_community_link($waiting_row['waiting_community_id'], $waiting_row['community_id']);
					}

					// 返信メッセージ
					if ($form['reply_message'] != '') {
						$ret = ACSWaiting::send_admission_accept_notify_mail($form['waiting_id']);
					}

					$done_obj->set_title(
							ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'WaitingListAction.class.php', 'FIN_ADM'),
							array("{WAITING_TYPE_NAME}" => $waiting_row[waiting_type_name])));
					$done_obj->set_message(ACSMsg::get_msg('Community', 'WaitingListAction.class.php', 'M001'));
				} else {
					$done_obj->set_message(ACSMsg::get_msg('Community', 'WaitingListAction.class.php', 'M002'));
				}
			} else {
				ACSDB::_do_query("ROLLBACK");
			}

		} elseif ($form['reject_button']) {
			// 拒否ボタン押下時
			$ret = ACSWaiting::update_waiting_waiting_status_code($form['waiting_id'], ACSMsg::get_mst('waiting_status_master','D30'), $acs_user_info_row['user_community_id']);
			if ($ret) {
				$done_obj->set_title(ACSMsg::get_tag_replace(
						ACSMsg::get_msg('Community', 'WaitingListAction.class.php', 'FIN_DIS'),
						array("{WAITING_TYPE_NAME}" => $waiting_row[waiting_type_name])));
				$done_obj->set_message(
						ACSMsg::get_msg('Community', 'WaitingListAction.class.php', 'M001'));
			} else {
				$done_obj->set_message(
						ACSMsg::get_msg('Community', 'WaitingListAction.class.php', 'M002'));
			}
		}

		$done_obj->add_link(ACSMsg::get_tag_replace(
				ACSMsg::get_msg('Community', 'WaitingListAction.class.php', 'BACK_TO_CM'),
				array("{COMMUNITY_NAME}" => $community_row['community_name'])),
				$this->getControllerPath('Community', DEFAULT_ACTION) 
					. '&community_id=' . $community_row['community_id']);

		$request->setAttribute('done_obj', $done_obj);
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
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
