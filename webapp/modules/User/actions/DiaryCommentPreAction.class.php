<?php
/**
 * 日記　登録・表示機能　actionクラス
 * 日記コメント情報　確認・登録処理
 * @package  acs/webapp/modules/User/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  $Revision: 1.6 $ $Date: 2006/03/02
 */
// $Id: DiaryCommentPreAction.class.php,v 1.6 2006/11/20 08:44:25 w-ota Exp $


class DiaryCommentPreAction extends BaseAction
{
	//field
	var $form;
	
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$user_community_id = $acs_user_info_row['user_community_id'];
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);
		// 対象となるUserIDを取得
		$user_id = $request->getParameter('user_id');
		// Diary情報
		$diary_row_array = $request->getAttribute('diary_row_array');

		// set
		$request->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('diary_row_array', $diary_row_array);

		return View::SUCCESS;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		//mode　画面の遷移を取得する
		$move_id = $request->getParameter('move_id');
		// ユーザー情報
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		/* 入力画面より */
	   if($move_id==1){
			//☆☆　ここからほぼ同じ
			// 画面上のフォーム情報を取得する
			$form['body'] = $request->getParameter('body');												//内容：body
			$form['user_community_id'] = $acs_user_info_row['user_community_id'];

			$user->setAttribute('new_form_obj',$form);
			$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($form['user_community_id']);
			$request->setAttribute('target_user_info_row', $target_user_info_row);
			//☆☆　ここまでほぼ同じ
			return View::SUCCESS;

		/* 登録確定ボタン「はい」より */
		} else if($move_id==2) {
			$user_id = $request->getParameter('id');
			// 対象のdiary_idを取得
			$diary_id = $request->getParameter('diary_id');
			//☆☆　ここからほぼ同じ
			// 画面上のフォーム情報を取得する
			$form = $user->getAttribute('new_form_obj');
			$form['diary_id'] = $diary_id;
			//☆☆　ここまでほぼ同じ
			// DBへの書き込み等
			ACSDB::_do_query("BEGIN");
			//DiaryCommentテーブル情報
			$ret = ACSDiary::set_diary_comment($form);
			if(!$ret){
				ACSDB::_do_query("ROLLBACK");
				echo "ERROR: Insert dairy comment failed.";
				return;
			}
			ACSDB::_do_query("COMMIT");
			// 書き込み後、GETの処理へ
			$diary_comment_top_page_url = $this->getControllerPath('User', 'DiaryComment') . '&id=' . $acs_user_info_row['user_community_id'] . '&diary_id=' . $diary_id .'&move_id=4';
			header("Location: $diary_comment_top_page_url");
		}
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods() {
		return Request::POST;
	}	

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$request =  $context->getRequest();
		$move_id = $request->getParameter('move_id');

		// 入力画面からの場合、入力チェック
		if ($move_id == 1){
			parent::regValidateName($validatorManager, 
					"body", 
					true, 
					ACSMsg::get_msg('User', 'DiaryCommentPreAction.class.php', 'M001'));
		}
	}

	function handleError () {
		// 入力画面表示
		return $this->getDefaultView();
	}

	function getCredential() {
		return array('EXECUTE');
	}

	function get_execute_privilege (&$controller, &$request, &$user) {

		// 公開範囲情報取得
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($request->getParameter('id'));
		$diary_row = ACSDiary::get_diary_row($request->ACSgetParameter('diary_id'));
		if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
			$diary_row['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
		}

		// アクセス制御判定
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		$ret = ACSAccessControl::is_valid_user_for_user_community($acs_user_info_row, $role_array, $diary_row);

		return $ret;
	}
}

?>
