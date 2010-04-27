<?php
/**
 * コミュニティ削除処理
 *
 * @author  kuwayama
 * @version $Revision: 1.7 $ $Date: 2006/11/20 08:44:12 $
 */
// アップロード先ディレクトリ取得に必要
//require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
require_once(ACS_CLASS_DIR . 'ACSDone.class.php');
class DeleteCommunityAction extends BaseAction
{
	/**
	 * 確認画面表示
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		/* コミュニティ情報取得 */
		$target_community_id = $request->getParameter('community_id');
		$target_community_row = ACSCommunity::get_community_row($request->getParameter('community_id'));

		// コミュニティ概要取得
		$target_community_row['community_profile'] = 
				ACSCommunity::get_contents_row($target_community_id, ACSMsg::get_mst('contents_type_master','D07'));

		$request->setAttribute('target_community_row', $target_community_row);

		return View::SUCCESS;
	}

	/**
	 * コミュニティ削除処理
	 */
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		/* コミュニティ情報取得 */
		$target_community_id = $request->getParameter('community_id');
		$target_community_row = ACSCommunity::get_community_row($request->getParameter('community_id'));

		/* 削除処理 */
		ACSDB::_do_query("BEGIN");
		$ret = ACSCommunity::delete_community($target_community_id);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			"ERROR : delete community failed";
			exit;
		}

		// フォルダのディレクトリ移動
		$from_dir  = ACS_FOLDER_DIR . "/";
		$from_dir .= ACSFile::get_upload_file_save_path($target_community_id);

		if (file_exists($from_dir)) {
			$to_dir  = ACS_TRASH_FOLDER_DIR . "/";
			$to_dir .= ACSFile::get_upload_file_save_path($target_community_id);
			if (!file_exists(ACS_TRASH_FOLDER_DIR)) {
				// 置き場所ディレクトリがなければ作成する
				mkdir(ACS_TRASH_FOLDER_DIR);
				chmod(ACS_TRASH_FOLDER_DIR, 0777);
			}

			$ret = rename($from_dir, $to_dir);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				"ERROR : delete community failed";
				exit;
			}
		}

		ACSDB::_do_query("COMMIT");


		/* 完了画面表示 */
		// 引数セット
		$message = ACSMsg::get_tag_replace(
				ACSMsg::get_msg('Community', 'DeleteCommunityAction.class.php', 'DELETECM'),
				array('{TARGET_COMMUNITY_NAME}' => $target_community_row['community_name']));
		$top_page_url  = $this->getControllerPath('User', 'Index');
		$top_page_link_name = ACSMsg::get_msg('Community', 'DeleteCommunityAction.class.php', 'M001');

		$done_obj = new ACSDone();

		$done_obj->set_title(ACSMsg::get_msg('Community', 'DeleteCommunityAction.class.php', 'M002'));
		$done_obj->set_message($message);
		$done_obj->add_link($top_page_link_name, $top_page_url);

		$request->setAttribute('done_obj', $done_obj);

		// 画面呼び出し
		$controller->forward('Common', 'Done');
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
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
