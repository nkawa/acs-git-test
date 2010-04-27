<?php

/**
 * マイフレンズのフォルダ新着情報
 *
 * @author  z-satosi
 * @version $Revision: 1.5 y-yuki Exp $
 */
class NewFriendsFolderAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$controller = $context->getController();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		if ($user_community_id == null || $user_community_id == '') {
			$user_community_id = $request->getAttribute("id");
		}

		// 他ユーザのデータが見えないようチェック
		if (!$this->get_execute_privilege()
				&& $acs_user_info_row["user_community_id"] != $user_community_id) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// インライン表示の場合: 1(true)
		$inline_mode = $request->ACSgetParameter('inline_mode');
		if ($inline_mode == null || $inline_mode == '') {
			$inline_mode = $request->getAttribute("inline_mode");
		}

		// 取得範囲の指定
		$get_days = ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D02'), 
					($inline_mode ? 'NEW_INFO_TOP_TERM' : 'NEW_INFO_LIST_TERM'));
		$request->setAttribute('get_days', $get_days);

		// マイフレンズの新着ファイル一覧を取得する
		if ($inline_mode) {
			$new_file_row_array = 
					ACSUserFolder::get_new_friends_file_row_array($user_community_id, $get_days, true);
			
		} else {
			$new_file_row_array = 
					ACSUserFolder::get_new_friends_file_row_array($user_community_id, $get_days);			
		}

		// 表示件数取得
		if ($inline_mode) {
			$display_count =
					ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D02'), 
					'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
		} else {
			$display_count = count($new_file_row_array);
		}
		$request->setAttribute('display_count', $display_count);

		//
		// マイページ新着、新着一覧ともに表示件数分の情報を取得
		//
		$rec_cnt = 0;
		$_new_file_row_array = array();
		foreach ($new_file_row_array as $index => $new_file_row) {

			// 表示件数に達している場合は終了
			if ($rec_cnt >= $display_count) {
				break;
			}

			$target_folder_obj = new ACSUserFolder(
					$new_file_row['owner_community_id'], 
					$acs_user_info_row, 
					$new_file_row['folder_id']);

			$target_file_obj = new ACSFile($new_file_row);

			$new_file_row['is_root_folder'] = 
					$target_folder_obj->folder_obj->get_is_root_folder();

			// 公開レベル
			$new_file_row['open_level_code'] = 
					$target_folder_obj->folder_obj->get_open_level_code();

			$new_file_row['open_level_name'] = 
					$target_folder_obj->folder_obj->get_open_level_name();

			$open_level_master_row = ACSAccessControl::get_open_level_master_row(
					$new_file_row['open_level_code']);

			$new_file_row = array_merge(
					$new_file_row, $open_level_master_row);

			$new_file_row['trusted_community_row_array'] = 
					$target_folder_obj->folder_obj->get_trusted_community_row_array();

			// パス
			$path_folder_obj_array = $target_folder_obj->get_path_folder_obj_array();
			$path_array = array();
			foreach ($path_folder_obj_array as $path_folder_obj_index => $path_folder_obj) {
				if ($path_folder_obj_index != 0) {
					array_push($path_array, $path_folder_obj->get_folder_name());
				}
			}
			array_push($path_array, $new_file_row['display_file_name']);
			$new_file_row['path_array'] = $path_array;

			//
			// アクセス権限の判定
			//
			$is_access_ok = TRUE;

			// マイフレンズは本人以外なのでis_root_folderのファイルを閲覧できない
			if ($new_file_row['is_root_folder']) {

				$is_access_ok = FALSE;

			// ファイルのアクセス権限をチェックする
			} else {

				// ファイルのオーナー情報取得
				$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id(
						$new_file_row['owner_community_id']);

				// 権限情報取得
				$role_array = ACSAccessControl::get_user_community_role_array(
								$acs_user_info_row, $target_user_info_row);

				// アクセス判定
				if (!ACSAccessControl::is_valid_user_for_user_community(
						$acs_user_info_row, $role_array, $new_file_row)) {
					$is_access_ok = FALSE;
				}
			}

			if ($is_access_ok) {
				// ファイル詳細情報URL
				$new_file_row['file_detail_url'] =
						$this->getControllerPath(DEFAULT_MODULE, 'FileDetail') .
						'&id=' . $new_file_row['owner_community_id'] .
						'&folder_id=' . $new_file_row['folder_id'] .
						'&file_id=' . $new_file_row['file_id'];

				$new_file_row['is_unread'] =
						ACSLib::get_boolean($new_file_row['is_unread']);

				array_push($_new_file_row_array, $new_file_row);
				$rec_cnt = $rec_cnt + 1;
			}
		}

		$new_file_row_array = $_new_file_row_array;

		// set
		$request->setAttribute('user_community_id', $user_community_id);
		$request->setAttribute('new_file_row_array', $new_file_row_array);

		if ($inline_mode) {
			return View::INPUT;
		} else {
			return VIEW::SUCCESS;
		}
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// 本人の場合はOK
		if (!$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
	}
}

?>
