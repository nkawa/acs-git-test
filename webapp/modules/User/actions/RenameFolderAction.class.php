<?php
/**
 * フォルダ 名前変更処理
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2006/11/20 08:44:25 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class RenameFolderAction extends BaseAction
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
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
				$acs_user_info_row,
				$target_user_community_folder_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);


		/* ------------- */
		/* 名前変更処理 */
		/* ------------- */
		// 新しい名前で更新
		ACSDB::_do_query("BEGIN");
		// フォルダ
		$folder_row_array = array();
		$new_folder_name_array = $request->getParameter('new_folder_name');
		if ($new_folder_name_array) {
			foreach ($new_folder_name_array as $folder_id => $new_folder_name) {
				// フォルダ名必須チェック
				if (!$new_folder_name) {
					ACSDB::_do_query("ROLLBACK;");
					// エラーの場合、処理終了
					return $this->setError($controller, $request, $user, 'new_folder_name', ACSMsg::get_msg('User', 'RenameFolderAction.class.php' ,'M001'));
				} elseif (mb_strlen($new_folder_name) > 100) {
					ACSDB::_do_query("ROLLBACK;");
					// エラーの場合、処理終了
					return $this->setError($controller, $request, $user, 'new_file_name', ACSMsg::get_msg('User', 'RenameFolderAction.class.php' ,'M002'));
				}

				// folder_id 頭尾の「'」を削除
				$folder_id = trim($folder_id, "'");
				//$folder_id = str_replace("\'", "", $folder_id);

				// 名前更新処理
				$_folder_obj = $user_folder_obj->folder_obj->get_folder_obj($folder_id);
				$ret = $_folder_obj->rename_folder_name($new_folder_name);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK;");
					print "ERROR: Rename folder failed.";
					exit;
				}
			}
		}

		// ファイル
		$file_row_array = array();
		$new_file_name_array = $request->getParameter('new_file_name');
		if ($new_file_name_array) {
			foreach ($new_file_name_array as $file_id => $new_file_name) {
				// ファイル名必須チェック
				if (!$new_file_name) {
					ACSDB::_do_query("ROLLBACK;");
					// エラーの場合、処理終了
					return $this->setError($controller, $request, $user, 'new_file_name', ACSMsg::get_msg('User', 'RenameFolderAction.class.php' ,'M001'));
				}

				// file_id 頭尾の「'」を削除
				$file_id = trim($file_id, "'");
				//$file_id = str_replace("\'", "", $file_id);

				// 名前更新処理
				$_file_obj = $user_folder_obj->folder_obj->get_file_obj($file_id);
				$ret = $_file_obj->rename_display_file_name($new_file_name);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK;");
					print "ERROR: Rename file failed.";
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

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		// 名前変更一覧アクション呼び出し
		$controller->forward('User', 'RenameFolderList');
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('USER_PAGE_OWNER');
	}
}
?>
