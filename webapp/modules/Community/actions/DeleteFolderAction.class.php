<?php
/**
 * フォルダ 削除
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2007/03/27 02:12:36 $
 */
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class DeleteFolderAction extends BaseAction
{
	// 確認画面表示
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

				if (!$this->get_execute_privilege()) {
						$controller->forward(SECURE_MODULE, SECURE_ACTION);
						return;
				}

		// 対象となるユーザコミュニティIDを取得
		$target_community_id = $request->getParameter('community_id');

		$community_folder_obj = $request->getAttribute('community_folder_obj');

		// 削除対象を取得
		// フォルダ
		$selected_folder_obj_array = array();   // View にわたす削除対象のフォルダ
		$selected_folder_row_array = array();
		$selected_folder_array = $request->getParameter('selected_folder');
		if ($selected_folder_array) {
			foreach ($selected_folder_array as $selected_folder_id) {
				$_selected_folder_obj = $community_folder_obj->folder_obj->get_folder_obj($selected_folder_id);
				array_push($selected_folder_obj_array, $_selected_folder_obj);
			}
		}

		// ファイル
		$selected_file_obj_array = array();   // View にわたす削除対象のファイル
		$selected_file_row_array = array();
		$selected_file_array = $request->getParameter('selected_file');
		if ($selected_file_array) {
			foreach ($selected_file_array as $selected_file_id) {
				$_selected_file_obj = $community_folder_obj->folder_obj->get_file_obj($selected_file_id);
				array_push($selected_file_obj_array, $_selected_file_obj);
			}
		}

		// アクセス制御: プットフォルダまたはファイルはNG //
		foreach ($selected_folder_obj_array as $selected_folder_obj) {
			if ($selected_folder_obj->get_community_id() != $target_community_id) {
				$controller->forward(SECURE_MODULE, SECURE_ACTION);
			}
		}
		foreach ($selected_file_obj_array as $selected_file_obj) {
			if ($selected_file_obj->get_owner_community_id() != $target_community_id) {
				$controller->forward(SECURE_MODULE, SECURE_ACTION);
			}
		}


		// set
		$request->setAttribute('selected_folder_obj_array', $selected_folder_obj_array);
		$request->setAttribute('selected_file_obj_array', $selected_file_obj_array);

		return View::SUCCESS;
	}

	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

				if (!$this->get_execute_privilege()) {
						$controller->forward(SECURE_MODULE, SECURE_ACTION);
						return;
				}

		// 必須チェック
		//	Validator でできないチェックはここで行う
		if (!$request->getParameter('selected_folder') && !$request->getParameter('selected_file')) {
			// エラーの場合、処理終了
			return $this->setError($controller, $request, $user, 'selected_folder', 
				 ACSMsg::get_msg('Community', 'DeleteFolderAction.class.php', 'M001'));
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_community_id = $request->getParameter('community_id');
		// 対象となるフォルダIDを取得
		$target_community_folder_id = $request->getParameter('folder_id');

		// 表示するページの所有者情報取得
		$target_community_info_row = ACSCommunity::get_community_row($target_community_id);
		// フォルダ情報取得
		$community_folder_obj = new ACSCommunityFolder($target_community_id,
											 $acs_user_info_row,
											 $target_community_folder_id);

		// set
		$request->setAttribute('target_community_info_row', $target_community_info_row);
		$request->setAttribute('community_folder_obj', $community_folder_obj);

		/* ------------ */
		/* 確認画面表示 */
		/* ------------ */
		if ($request->getParameter('action_type') == 'confirm') {
			return $this->getDefaultView();
		}

		/* -------- */
		/* 削除処理 */
		/* -------- */
		elseif ($request->getParameter('action_type') == 'delete') {
			ACSDB::_do_query("BEGIN");
			// フォルダ
			$folder_row_array = array();
			$delete_folder_id_array = $request->getParameter('selected_folder');
			if ($delete_folder_id_array) {
				foreach ($delete_folder_id_array as $folder_id) {
					// 削除処理
					$_folder_obj = $community_folder_obj->folder_obj->get_folder_obj($folder_id);
					$ret = $community_folder_obj->delete_folder($_folder_obj);
					if (!$ret) {
						ACSDB::_do_query("ROLLBACK;");
						print "ERROR: Remove folder failed.";
						exit;
					}
				}
			}

			// ファイル
			$file_row_array = array();
			$delete_file_id_array = $request->getParameter('selected_file');
			if ($delete_file_id_array) {
				foreach ($delete_file_id_array as $file_id) {
					// 公開用ファイル情報
					ACSFileDetailInfo::delete_file_public_access($file_id);

					// 削除処理
					$_file_obj = $community_folder_obj->folder_obj->get_file_obj($file_id);
					$ret = $_file_obj->delete_file();
					if (!$ret) {
						ACSDB::_do_query("ROLLBACK;");
						print "ERROR: Remove file failed.";
						exit;
					}
				}
			}

			ACSDB::_do_query("COMMIT;");

			// フォルダ表示アクション呼び出し
			$folder_action  = $this->getControllerPath('Community', 'Folder');
			$folder_action .= '&community_id=' . $target_community_id;
			$folder_action .= '&folder_id=' . $target_community_folder_id;

			header("Location: $folder_action");
		}
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		/* エラーメッセージをセッションにセット */
		$this->sendError($controller, $request, $user);

		// フォルダ表示アクション呼び出し
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_community_id = $request->getParameter('community_id');
		$target_community_folder_id = $request->getParameter('folder_id');

		$folder_action = $this->getControllerPath('Community', 'Folder');
		$folder_action .= '&community_id=' . $target_community_id;
		$folder_action .= '&folder_id=' . $target_community_folder_id;
		header("Location: $folder_action");
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_MEMBER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// コミュニティメンバはOK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}
		return false;
	}

}
?>
