<?php

/**
 * マイコミュニティのフォルダ新着情報
 *
 * @author  z-satosi
 * @version $Revision: 1.4 y-yuki Exp $
 */
class NewCommunityFolderAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$controller = $context->getController();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$form = $request->ACSgetParameters();
	
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

		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		if ($inline_mode) {
			// マイコミュニティの新着フォルダ一覧を取得する
			$new_folder_row_array = 
					ACSCommunityFolder::get_new_community_folder_row_array($user_community_id, $get_days, true);

			// マイコミュニティの新着プットフォルダ一覧を取得する
			$new_put_folder_row_array = 
					ACSCommunityFolder::get_new_community_put_folder_row_array($user_community_id, $form, $get_days, true);
		} else {
			// マイコミュニティの新着フォルダ一覧を取得する
			$new_folder_row_array = 
					ACSCommunityFolder::get_new_community_folder_row_array($user_community_id, $get_days);

			// マイコミュニティの新着プットフォルダ一覧を取得する
			$new_put_folder_row_array = 
					ACSCommunityFolder::get_new_community_put_folder_row_array($user_community_id, $form, $get_days);
			
		}

		// コミュニティ名取得バッファ
		$this->community_name_buffer = array();

		// ソート用配列の初期化
		$sort_folder_row_array = array();

		// ソート用に加工(コミュニティのファイル)
		foreach ($new_folder_row_array as $index => $new_folder_row) {
			$sort_index = $new_folder_row['update_date'] . " " .
							sprintf("%06d", $new_folder_row['file_id']) . "c";
			$sort_folder_row_array[$sort_index] = $new_folder_row;
		}

		// ソート用に加工(プットファイル)
		// (複数コミュニティへのput情報が重複しないようcounterを付加)
		// (※複数コミュニティは第一階層のファイルのみが対応されている)
		$counter = 0;
		foreach ($new_put_folder_row_array as $index => $new_folder_row) {

			// PUTであることのフラグを設定をしておく
			$new_folder_row['is_put_icon'] = TRUE;

			$sort_index = $new_folder_row['update_date'] . " " .
						sprintf("%06d", $new_folder_row['file_id']) . "p" . $counter;
			$sort_folder_row_array[$sort_index] = $new_folder_row;

			$counter++;
		}

		// ソート実施
		krsort($sort_folder_row_array);

		// 表示件数制御 //
		if ($inline_mode) {
			$display_count =
					ACSSystemConfig::get_keyword_value(ACSMsg::get_mst(
						'system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
		} else {
			// viewでページングするので全件取得(0=全件取得)
			$display_count = 0;
		}

		// 表示用に整える
		$new_folder_row_array = array();
		foreach ($sort_folder_row_array as $key => $folder_row) {

			// 表示件数に達している場合は処理しない
			if (count($new_folder_row_array) >= $display_count && $display_count != 0) {
				break;
			}

			// putファイルの場合
			if ($folder_row['is_put_icon']) {

				// フォルダ情報の取得
				// (※複数のフォルダ情報の取得する場合も有)
				$add_folder_row_array =&
						$this->getPutFolderRows($acs_user_info_row, $folder_row);

			// コミュニティファイルの場合
			} else {
				// (必要なフォルダ情報は取得済み)
				$folder_row['url_community_id'] = $folder_row['owner_community_id'];
				$folder_row['url_folder_id'] = $folder_row['folder_id'];
				$add_folder_row_array = array($folder_row);
			}

			// 表示用情報の設定
			foreach ($add_folder_row_array as $add_folder_row) {

				// 表示件数に達している場合は処理しない
				if (count($new_folder_row_array) >= $display_count &&
						$display_count != 0) {
					break;
				}

				// 既読フラグの設定
				$add_folder_row['is_unread'] =
						ACSLib::get_boolean($add_folder_row['is_unread']);

				// コミュニティ名をバッファリングしておく(再利用するため)
				$this->community_name_buffer[$add_folder_row['owner_community_id']] =
						$add_folder_row['community_name'];

				// ファイル詳細情報URLの生成
				$add_folder_row['file_detail_url'] =
						$this->getControllerPath('Community', 'FileDetail') .
						'&community_id=' . $add_folder_row['url_community_id'] .
						'&folder_id=' . $add_folder_row['url_folder_id'] .
						'&file_id=' . $add_folder_row['file_id'];

				array_push($new_folder_row_array, $add_folder_row);
			}
		}

		// set
		$request->setAttribute('user_community_id', $user_community_id);
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('new_folder_row_array', $new_folder_row_array);
		//$request->setAttribute('new_put_folder_row_array', $new_put_folder_row_array);

		if ($inline_mode) {
			return View::INPUT;
		} else {
			return View::SUCCESS;
		}
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('USER_PAGE_OWNER');
	}

	/*
	 * put先のフォルダ情報を取得する(複数put対応)
	 */
	function & getPutFolderRows(&$acs_user_info_row, &$folder_row) {

		$put_community_id = $folder_row['put_community_id'];

		$put_folder_rows = array();

		// ２階層以上深い階層のputファイルの場合
		// (put先のコミュニティフォルダが取得できていない)
		// (put先が複数の場合あり)
		if ($put_community_id=='') {

			// ユーザフォルダobj取得
			$user_folder_obj = new ACSUserFolder(
					$folder_row['owner_community_id'],
					$acs_user_info_row,
					$folder_row['folder_id'] );

			// パス情報取得
			$path_folder_obj_array = $user_folder_obj->get_path_folder_obj_array();

			// 第1階層フォルダＩＤ取得
			$second_folder_obj =& $path_folder_obj_array[1];

			// 第1階層フォルダからプット先のコミュニティ情報を取得(複数の場合有)
			$put_community_array =& $second_folder_obj->get_put_community_row_array();
			foreach ($put_community_array as $put_community) {

				$add_folder_row = $folder_row;
				$add_folder_row['url_community_id'] = $put_community['community_id'];
				$add_folder_row['url_folder_id'] = $folder_row['folder_id'];
				$add_folder_row['community_name'] = $put_community['community_name'];

				$put_folder_rows[] = $add_folder_row;
			}


		// １階層目のputファイルの場合
		// (put先のコミュニティフォルダが取得できている)
		} else {

			// バッファにコミュニティ名が無い場合は問い合わせる
			if ($this->community_name_buffer[$put_community_id]=='') {
				$community_row =& ACSCommunity::get_community_row(
						$put_community_id);
				$this->community_name_buffer[$put_community_id] =
						$community_row['community_name'];
			}

			// 表示用にコミュニティ名を設定しておく
			$folder_row['community_name'] =
					$this->community_name_buffer[$put_community_id];
			$folder_row['url_community_id'] = $folder_row['put_community_id'];
			$folder_row['url_folder_id'] = $folder_row['put_community_folder_id'];

			$put_folder_rows = array($folder_row);
		}
		return $put_folder_rows;
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
