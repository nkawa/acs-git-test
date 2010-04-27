<?php
/**
 * 掲示板　投稿機能　actionクラス
 * 返信投稿情報　確認・登録処理
 * @package  acs/webapp/modules/Community/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  $Revision: 1.7 $ $Date: 2006/02/28
 */
// $Id: BBSResPreAction.class.php,v 1.7 2006/12/19 10:17:26 w-ota Exp $


class BBSResPreAction extends BaseAction
{
	//field
	var $form;
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// 対象となるコミュニティIDを取得
		$community_id = $request->getParameter('community_id');
		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);
		// 公開範囲
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D42'));

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);

		return View::SUCCESS;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		//mode　画面の遷移を取得する
		$move_id = $request->getParameter('move_id');
		// 公開範囲
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D42'));
		// ユーザー情報
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// 対象となるコミュニティIDを取得
		$community_id = $request->getParameter('community_id');
		$bbs_id = $request->getParameter('bbs_id');

		/* 入力画面より */
		if($move_id==1){
			// 画面上のフォーム情報を取得する
			$form['subject'] = $request->getParameter('subject');//件名：subject
			// 本文
			$form['body'] = $request->getParameter('body');		//内容：body
	
			$form['community_id'] = $community_id;				//当コミュニティのID
			$form['user_community_id'] = $acs_user_info_row['user_community_id']; 		// 投稿者print "form_row:";
			$form['bbs_id'] = $bbs_id; 		// 投稿者print "form_row:";
			
			$user->setAttribute('new_form_obj',$form);
			return View::SUCCESS;

		/* 登録確定ボタン「はい」より */
		}else if($move_id==2){
			// 画面上のフォーム情報を取得する
			$form = $user->getAttribute('new_form_obj');
			// DBへの書き込み等
			$ret = ACSBBS::set_bbs_res($form);

			if($ret){
		
				// 掲示板アクセス履歴へ記録
				ACSBBS::set_bbs_access_history(
						$acs_user_info_row['user_community_id'], $form['bbs_id']);
		
				// bbs情報取得
				$bbs_row = ACSBBS::get_bbs_row($request->getParameter('bbs_id'));
		
				// MLオプションありの場合
				if ($bbs_row['ml_send_flag']=='t') {

					// MLステータスの取得
					$ml_status_row = ACSCommunity::get_contents_row(
					$community_id, ACSMsg::get_mst('contents_type_master','D62'));
					$ml_status = $ml_status_row['contents_value'];
			
					// ML有りの場合メールを送信
					if ($ml_status == 'ACTIVE') {
		
						// "Re:"の削除
						$subject_msg = mb_ereg_replace( 
								ACS_COMMUNITY_ML_SUBJECT_PREFIX_CLEAR_REGEX,
								'', $form['subject']);
		
						// 件名編集
						$subject = "Re: ".str_replace('{BBSID}', $bbs_id,
								ACS_COMMUNITY_ML_SUBJECT_FORMAT) . $subject_msg;
			
						// ML送信
						ACSCommunityMail::send_community_mailing_list(
								$community_id, $acs_user_info_row['mail_addr'],
								$subject, $form['body']);
					}
				}
		
			} else {
				echo ACSMsg::get_msg('Community', 'BBSResPreAction.class.php', 'M001');
			}
		
			// 書き込み後、BBS Top 表示の処理へ
			$action_url  = $this->getControllerPath('Community', 'BBS'). '&community_id=' . $community_id. '&move_id=4';
			header("Location: $action_url");
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
					"subject", 
					true, 
					ACSMsg::get_msg('Community', 'BBSResPreAction.class.php', 'M002'));
			parent::regValidateName($validatorManager, 
					"body", 
					true, 
					ACSMsg::get_msg('Community', 'BBSResPreAction.class.php', 'M003'));
		}
	}

	function handleError () {
		// 入力画面表示
		return $this->execute();
	}

	function getCredential () {
		return array('EXECUTE');
	}


	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// コミュニティメンバはOK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}
		return false;
	}
}

?>
