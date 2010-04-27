<?php
/**
 * フォルダ 移動処理
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2006/11/20 08:44:25 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class MoveFolderAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_user_community_id = $request->getParameter('id');
		// 対象となるフォルダIDを取得
		$target_user_community_folder_id = $request->getParameter('folder_id');

		// 表示するページの所有者情報取得
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// フォルダ情報取得
		$user_folder_obj = new ACSUserFolder(
				$target_user_community_id,
				$acs_user_info_row,
				$target_user_community_folder_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);

		// 移動先フォルダID
		$move_target_folder_id = $request->getParameter('selected_move_folder_id');

		/* -------- */
		/* 移動処理 */
		/* -------- */
		ACSDB::_do_query("BEGIN");

		// 移動先がルートフォルダの場合は、公開範囲をセットする必要があるため、
		// ルートフォルダの情報を取得しておく
		$root_folder_obj = ACSFolder::get_folder_instance(
				$user_folder_obj->get_root_folder_row($user_folder_obj->get_community_id()));

		// フォルダ
		$folder_row_array = array();
		$selected_folder_id_array = $request->getParameter('selected_folder');
		if ($selected_folder_id_array) {
			foreach ($selected_folder_id_array as $folder_id) {
				// 移動するフォルダ取得
				$_folder_obj = $user_folder_obj->folder_obj->get_folder_obj($folder_id);

				// 移動処理
				$ret = $_folder_obj->move_folder($move_target_folder_id);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					print "ERROR: Move folder failed.";
					exit;
				}

				// 公開範囲を更新
				if ($move_target_folder_id == $root_folder_obj->get_folder_id()) {
					// ルートフォルダへ移動の場合、公開範囲をセット
					$new_open_level_code = $user_folder_obj->folder_obj->get_open_level_code();
					$new_trusted_community_row_array = $user_folder_obj->folder_obj->get_trusted_community_row_array();

				} else {
					// ルートフォルダ以外へ移動の場合、公開範囲をリセット
					$new_open_level_code = "";
					$new_trusted_community_row_array = array();
				}
				$ret = $_folder_obj->update_open_level_code($new_open_level_code, $new_trusted_community_row_array);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					print "ERROR: Move folder failed.";
					exit;
				}

				// ルートフォルダ以外へへ移動の場合、プット解除(=プット情報を全て削除)
				if ($move_target_folder_id != $root_folder_obj->get_folder_id()) {
					$ret = ACSFolderModel::delete_put_community_by_folder_id($_folder_obj->get_folder_id());
					if (!$ret) {
						ACSDB::_do_query("ROLLBACK");
						print "ERROR: Move folder failed.";
						exit;
					}
				}
			}
		}

		// ファイル
		$file_row_array = array();
		$selected_file_id_array = $request->getParameter('selected_file');
		if ($selected_file_id_array) {
			foreach ($selected_file_id_array as $file_id) {

				// 移動処理
				$file_obj = $user_folder_obj->folder_obj->get_file_obj($file_id);
				$ret = $user_folder_obj->folder_obj->move_file($file_obj, $move_target_folder_id);
				//$ret = $_file_obj->rename_display_file_name($new_file_name);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK;");
					print "ERROR: Move file failed.";
					exit;
				}
			}
		}

		ACSDB::_do_query("COMMIT;");

		// フォルダ表示アクション呼び出し
		$folder_action  = $this->getControllerPath('User', 'Folder');
		$folder_action .= '&id=' . $target_user_community_id;
		$folder_action .= '&folder_id=' . $target_user_community_folder_id;

		header("Location: $folder_action");
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		/* 必須チェック */
		parent::regValidateName($validatorManager, 
				"selected_move_folder_id", 
				true, 
				ACSMsg::get_msg('User', 'MoveFolderAction.class.php', 'M001'));
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		// 移動一覧アクション呼び出し
		$controller->forward('User', 'MoveFolderList');
	}

	function isSecure () {
		return false;
	}

	function getCredential() {		
		return array('USER_PAGE_OWNER');
	}
}
?>
