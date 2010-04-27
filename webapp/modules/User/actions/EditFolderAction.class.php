<?php
/**
 * フォルダ 作成・変更
 *
 * @author  kuwayama
 * @version $Revision: 1.8 $ $Date: 2007/03/01 09:01:42 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class EditFolderAction extends BaseAction
{
	/**
	 * 入力画面表示
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_user_community_id = $request->getParameter('id');
		// 対象となるフォルダIDを取得
		$target_user_community_folder_id = $request->getParameter('folder_id');
		$edit_folder_id = $request->getParameter('edit_folder_id');

		// 他ユーザのデータが見えないようチェック
		if (!$this->get_execute_privilege()) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// 表示するページの所有者情報取得
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// フォルダ情報取得
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
											 $acs_user_info_row,
											 $target_user_community_folder_id);


		// 作成・更新の判別
		if ($request->getParameter('edit_folder_id')) {
			$edit_folder_id = $request->getParameter('edit_folder_id');
			$view_mode = 'update';
		} else {
			$view_mode = 'create';
		}

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);
		$request->setAttribute('view_mode', $view_mode);
		$request->setAttribute('edit_folder_id', $edit_folder_id);


		// エラーで呼ばれた場合は、入力値を取得
		// hasErrors 関数作成？
		if ($this->hasErrors($controller, $request, $user)) {

			// デフォルト値として表示する値を row にセット
			$default_data_row['folder_id']	   = $request->getParameter('folder_id');
			$default_data_row['folder_name']	 = $request->getParameter('folder_name');
			$default_data_row['comment']		 = $request->getParameter('comment');
			$default_data_row['open_level_code'] = $request->getParameter('open_level_code');
			$default_data_row['trusted_community_flag']	 = $request->getParameter('trusted_community_flag');
			$default_data_row['trusted_community_id_array'] = $request->getParameter('trusted_community');

			// set
			$request->setAttribute('default_data_row', $default_data_row);
			return View::INPUT;
		}

		// 更新の場合は、更新対象のフォルダIDを取得
		if ($view_mode == 'update') {
			// 初期表示の場合、更新対象のフォルダ情報取得
			if (!$this->hasErrors($controller, $request, $user)) {

				// 更新対象のフォルダ情報取得
				$update_user_folder_obj = new ACSUserFolder($request->getParameter('id'),
															$acs_user_info_row,
															$edit_folder_id);
				$update_folder_obj = $update_user_folder_obj->get_folder_obj();
				// 閲覧許可コミュニティID取得
				$trusted_community_id_array = array();
				foreach($update_folder_obj->get_trusted_community_row_array() as $trusted_community_row) {
					array_push($trusted_community_id_array, $trusted_community_row['community_id']);
				}

				// デフォルト値として表示する値を row にセット
				$default_data_row['folder_id']	   = $update_folder_obj->get_folder_id();
				$default_data_row['folder_name']	 = $update_folder_obj->get_folder_name();
				$default_data_row['comment']		 = $update_folder_obj->get_comment();
				$default_data_row['open_level_code'] = $update_folder_obj->get_open_level_code();
				$default_data_row['trusted_community_flag']	 = "";  // view で値を判断する
				$default_data_row['trusted_community_id_array'] = $trusted_community_id_array;
				$request->setAttribute('default_data_row', $default_data_row);
			}

			// set
			$request->setAttribute('input_data_row', $input_data_row);
			return View::INPUT;
		} elseif ($view_mode == 'create') {
			return View::INPUT;
		}
	}

	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$target_user_community_id = $request->getParameter('id');
		// 対象となるフォルダIDを取得
		$target_user_community_folder_id = $request->getParameter('folder_id');
		// 処理の種類
		$action_type = $request->getParameter('action_type');
		// get
		$form = $request->ACSGetParameters();


		// Validatorで出来ないエラーチェックを行う //
		if (mb_strlen($form['folder_name']) > 100) {
			$this->setError($controller, $request, $user, 'folder_name', ACSMsg::get_msg('User', 'EditFolderAction.class.php', 'M001'));
			return $this->handleError(&$controller, &$request, &$user);
		}


		// 表示するページの所有者情報取得
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// フォルダ情報取得
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
											 $acs_user_info_row,
											 $target_user_community_folder_id);


		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);

		/* ----------------- */
		/* 入力画面表示処理 */
		/* ----------------- */
		// action_type (create or update) が存在し、エラーで遷移してきていない場合
		if (!$action_type || $this->hasErrors($controller, $request, $user)) {
			return $this->getDefaultView();
		}


		// 選択された公開範囲の判別のためのデータ取得
		$open_level_master_row_array = ACSAccessControl::get_all_open_level_master_row_array();

		/* ---------- */
		/* 入力値取得 */
		/* ---------- */
		$edit_folder_id = $request->getParameter('edit_folder_id');

		$input_folder_row = array();
		$input_folder_row['folder_name']	 = $request->getParameter('folder_name');
		$input_folder_row['comment']		 = $request->getParameter('comment');
		$input_folder_row['open_level_code'] = $request->getParameter('open_level_code');

		$open_level_row = $open_level_master_row_array[$input_folder_row['open_level_code']];
		$open_level_name = $open_level_row['open_level_name'];


		if ($open_level_name == ACSMsg::get_mst('open_level_master','D05')) {
			if ($request->getParameter('trusted_community_flag') == '0') {
				// 全ての友人をセット
				$friends_community_id = ACSUser::get_friends_community_id($target_user_community_id);
				$input_folder_row['trusted_community_id_array'] = array($friends_community_id);
			} else {
				// マイフレンズグループの場合は、指定されているマイフレンズグループIDをセット
				$input_folder_row['trusted_community_id_array'] = $request->getParameter('trusted_community');
			}
		}

		/* ---------------------- */
		/* フォルダ名重複チェック */
		/* ---------------------- */
		// 対象となるフォルダ配下のフォルダを取得
		$sub_folder_obj_array = $user_folder_obj->folder_obj->get_folder_obj_array();
		foreach ($sub_folder_obj_array as $sub_folder_obj) {
			if ($sub_folder_obj->get_folder_id() == $edit_folder_id) {
				// 更新対象のフォルダはチェック対象としない
				continue;
			}

			if ($sub_folder_obj->get_folder_name() == $input_folder_row['folder_name']) {
				// エラーメッセージをセットし、処理を中断する
				return $this->setError($controller, $request, $user, 'folder_name', ACSMsg::get_msg('User', 'EditFolderAction.class.php' ,'M003').'[' . $input_folder_row['folder_name'] . ']');
			}
		}

		ACSDB::_do_query("BEGIN");
		/* -------- */
		/* 登録処理 */
		/* -------- */
		if ($action_type == 'create') {
			$ret = $user_folder_obj->folder_obj->create_folder($input_folder_row);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK;");
				print "ERROR: フォルダを作成できませんでした。";
				exit;
			}

		} elseif ($action_type == 'update') {
		/* -------- */
		/* 更新処理 */
		/* -------- */
			// 更新対象のフォルダ情報取得
			$update_user_folder_obj = new ACSUserFolder($request->getParameter('id'),
														$acs_user_info_row,
														$edit_folder_id);

			$ret = $update_user_folder_obj->folder_obj->update_folder($input_folder_row);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK;");
				print "ERROR: フォルダ情報を変更できませんでした。";
				exit;
			}
		}
		ACSDB::_do_query("COMMIT;");

		/* -------------------- */
		/* フォルダ一覧画面表示 */
		/* -------------------- */
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$folder_action = $this->getControllerPath('User',
														'Folder');
		$folder_action .= '&id=' . $target_user_info_row['user_community_id'];
		$folder_action .= '&folder_id=' . $target_user_community_folder_id;

		header("Location: $folder_action");
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$request =  $context->getRequest();
		// 作成・変更処理の場合のみ、入力チェックをする
		if ($request->getParameter('action_type')) {
			/* 必須チェック */
			parent::regValidateName($validatorManager, 
					"folder_name", 
					true, 
					ACSMsg::get_msg('User', 'EditFolderAction.class.php', 'M002'));
		}
	}

	function handleError () {
		// 入力画面表示
		return $this->getDefaultView();
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
