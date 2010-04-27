<?php
/**
 * コミュニティメンバ削除 メンバ一覧表示
 *
 * @author  kuwayama
 * @version $Revision: 1.4 $ $Date: 2006/03/28 02:00:22 $
 */
class DeleteCommunityMemberListAction extends BaseAction
{
	/**
	 * 初期画面
	 * GETメソッドの場合、呼ばれる
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

		$target_community_id = $request->getParameter('community_id');


		/* メンバ情報取得 */
		$target_community_member_info_row_array = ACSCommunity::get_community_member_user_info_row_array($target_community_id);

		$request->setAttribute('target_community_member_info_row_array', $target_community_member_info_row_array);

		$this->set_request_community_info(&$request);

		return View::INPUT;
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

		/* INPUT 画面表示 */
		if ($request->getParameter('action_type') == 'back') {
			// 選択されている user_community_id 取得
			$selected_user_community_id_array = array();
			$selected_user_community_id_array = $request->getParameter('delete_user_community_id_array');

			$request->setAttribute('selected_user_community_id_array', $selected_user_community_id_array);

			return $this->getDefaultView();
		}

		/* CONFIRM 画面表示 */
		else if ($request->getParameter('action_type') == 'confirm') {
			/* エラーを取得 */
			if ($request->hasErrors()) {
				// エラーがある場合は、INPUT 画面表示
				$user->removeAttribute('error_row');
				$request->setAttribute('error_row', $error_row);

				return $this->getDefaultView();
			}
		
			/* POST データ取得処理 */
			$delete_user_community_id_array = $request->getParameter('delete_user_community_id_array');
			$delete_user_info_row_array = $this->get_user_info_row_array($delete_user_community_id_array);

			/* View へ渡す値セット */
			$this->set_request_community_info(&$request);
			$request->setAttribute('delete_user_info_row_array', $delete_user_info_row_array);
			
			return View::SUCCESS;
		}
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {

		parent::regValidateName($validatorManager, 
				"delete_user_community_id_array", 
				true, 
				'選択してください。');
	}

	function handleError () {
		// エラーの場合、INPUT 画面を表示
		return $this->getDefaultView();
	}

	function set_request_community_info (&$request) {
		/* コミュニティ情報取得 */
		$target_community_id = $request->getParameter('community_id');
		$target_community_row = ACSCommunity::get_community_row($request->getParameter('community_id'));

		$request->setAttribute('target_community_row', $target_community_row);
	}

	function get_user_info_row_array ($user_community_id_array) {
		$user_info_row_array = array();
		foreach ($user_community_id_array as $user_community_id) {
			$user_info_row = array();
			$user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

			array_push($user_info_row_array, $user_info_row);
		}

		return $user_info_row_array;
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
