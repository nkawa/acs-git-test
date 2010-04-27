<?php
/**
 * ユーザのフォルダプット先コミュニティ設定
 *
 * @author  kuwayama
 * @version $Revision: 1.7 $ $Date: 2006/12/18 07:42:15 $
 */
require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
require_once(ACS_CLASS_DIR . 'ACSFolderModel.class.php');
class FolderPutCommunityAction extends BaseAction
{
	/**
	 * プット先コミュニティ選択画面表示
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		// 対象となるフォルダIDを取得
		$target_folder_id = $request->ACSgetParameter('folder_id');

		// 他ユーザのデータが見えないようチェック
		if (!$this->get_execute_privilege()) {
			// このページへアクセスすることはできません。
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// マイコミュニティ
		$community_row_array = ACSUser::get_community_row_array($user_community_id);
		// マイコミュニティのフォルダツリーを追加
		$community_folder_obj_array = array();
		$community_row_index = 0;
		foreach ($community_row_array as $community_row) {
			$community_folder_obj = array();
			$folder_tree = array();

			// ルートフォルダのインスタンス生成
			$community_folder_obj = new ACSCommunityFolder($community_row['community_id'], $acs_user_info_row, '');
			$folder_tree = $community_folder_obj->get_folder_tree();
			$community_row_array[$community_row_index]['folder_tree'] = $folder_tree;
			$community_row_index++;
		}

		// プット先コミュニティ（設定されているコミュニティ）
		$put_community_row_array = ACSFolderModel::select_put_community($target_folder_id);


		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('target_folder_id', $target_folder_id);
		$request->setAttribute('community_row_array', $community_row_array);
		$request->setAttribute('put_community_row_array', $put_community_row_array);

		return View::INPUT;
	}

	/**
	 * プット先コミュニティ設定処理
	 */
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		// 対象となるフォルダIDを取得
		$target_folder_id = $request->ACSgetParameter('folder_id');

		// ACSUserFolder インスタンス生成
		$user_folder_obj = new ACSUserFolder($user_community_id, $acs_user_info_row, $target_folder_id);
		$target_folder_obj = $user_folder_obj->get_folder_obj();

		// 選択された community_id, folder_id を取得
		$selected_put_folder_row_array = $request->getParameter('selected_put_folder_id');

		// ----------------------------
		// 更新用にデータ加工
		// プット先コミュニティ更新用に、row_array 作成
		$put_community_row_array = array();
		foreach ($selected_put_folder_row_array as $community_id => $folder_id) {
			array_push($put_community_row_array, array('put_community_id' => $community_id, 'put_community_folder_id' => $folder_id));
		}

		// ----------------------------
		// 更新処理
		$ret = $target_folder_obj->update_put_community($target_folder_id, $put_community_row_array);
		if (!$ret) {
			print "ERROR: Update put-community failed.<br>\n";
			exit;
		}

		// ML通知チェックがあればMLにメール送信する
		// コミュニティ情報の取得
		$send_announce_mail		= $request->getParameter('send_announce_mail');
		if($send_announce_mail == "t"){
			foreach ($selected_put_folder_row_array as $community_id => $folder_id) {
				$folder_info = ACSFolderModel::select_folder_row($folder_id);
				ACSCommunityMail::send_putfolder_mail(
							$acs_user_info_row, $folder_info, $community_id);
			}
		}

		// 処理が終わったら、ウィンドウを閉じる
		$controller->forward('Common', 'CloseChildWindow');
	}

	function getRequestMethods () {
		return Request::POST;
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
