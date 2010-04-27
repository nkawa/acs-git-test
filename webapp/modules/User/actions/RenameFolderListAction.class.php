<?php
/**
 * フォルダ 名前変更一覧
 *
 * @author  kuwayama
 * @version $Revision: 1.5 $ $Date: 2006/11/20 08:44:25 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class RenameFolderListAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// 必須チェック
		//    Validator でできないチェックはここで行う
		if (!$request->getParameter('selected_folder') && !$request->getParameter('selected_file')) {
			// エラーの場合、処理終了
			return $this->setError($controller, $request, $user, 'selected_folder', ACSMsg::get_msg('User', 'RenameFolderListAction.class.php' ,'M001'));
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

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

		// 名前変更対象を取得
		// フォルダ
		$selected_folder_obj_array = array();   // View にわたす変更対象のフォルダ
		$selected_folder_row_array = array();
		$selected_folder_array = $request->getParameter('selected_folder');
		if ($selected_folder_array) {
			foreach ($selected_folder_array as $selected_folder_id) {
				$_selected_folder_obj = $user_folder_obj->folder_obj->get_folder_obj($selected_folder_id);
				array_push($selected_folder_obj_array, $_selected_folder_obj);
			}
		}

		// ファイル
		$selected_file_obj_array = array();   // View にわたす変更対象のファイル
		$selected_file_row_array = array();
		$selected_file_array = $request->getParameter('selected_file');
		if ($selected_file_array) {
			foreach ($selected_file_array as $selected_file_id) {
				$_selected_file_obj = $user_folder_obj->folder_obj->get_file_obj($selected_file_id);
				array_push($selected_file_obj_array, $_selected_file_obj);
			}
		}

		// set
		$request->setAttribute('selected_folder_obj_array', $selected_folder_obj_array);
		$request->setAttribute('selected_file_obj_array', $selected_file_obj_array);

		return View::INPUT;
	}

	function handleError () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		/* エラーメッセージをセッションにセット */
		$this->sendError($controller, $request, $user);

		// フォルダ表示アクション呼び出し
		$target_user_community_id = $request->getParameter('id');
		$target_user_community_folder_id = $request->getParameter('folder_id');

		$folder_action = $this->getControllerPath('User', 'Folder');
		$folder_action .= '&id=' . $target_user_community_id;
		$folder_action .= '&folder_id=' . $target_user_community_folder_id;
		header("Location: $folder_action");
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 非ログインユーザ、本人以外はNG
		if ($user->hasCredential('PUBLIC_USER')
				 || !$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
	}
}
?>
