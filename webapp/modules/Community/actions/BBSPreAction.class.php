<?php
/**
 * 掲示板　投稿機能　actionクラス
 * 投稿情報　確認・登録処理
 * @package  acs/webapp/modules/Community/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  $Revision: 1.12 $ $Date: 2006/12/19 10:17:26 $
 */
// $Id: BBSPreAction.class.php,v 1.12 2006/12/19 10:17:26 w-ota Exp $
class BBSPreAction extends BaseAction
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
		//エラーの初期化
		

/* 入力画面より */
	if($move_id==1){
	$err = 'OK';		//エラー値の初期化
//☆☆　ここからほぼ同じ
		// 画面上のフォーム情報を取得する
		$form['subject'] = $request->getParameter('subject');//件名：subject
		$form['body'] = $request->getParameter('body');												//内容：body
		$form['open_level_code'] = $request->getParameter('open_level_code');	//公開範囲コード：open_level_code
			foreach ($open_level_master_row_array as $open_level_master_row) {
				if($open_level_master_row['open_level_code'] == $form['open_level_code']){
					$form['open_level_name'] = htmlspecialchars($open_level_master_row['open_level_name']) ;//公開範囲表示名：open_level_name
				}
			}
		$form['new_file'] = $request->getParameter('new_file');								//画像：new_file
		//ファイル情報のあるなしをチェックしておく
		if (!ini_get('mbstring.encoding_translation')) {
			$form['file_name'] = mb_convert_encoding($_FILES['new_file']['name'], mb_internal_encoding(), mb_http_output());
		} else {
			$form['file_name'] = $_FILES['new_file']['name'];
		}

		if($form['file_name'] != ''){
			/* ディレクトリ存在チェック */
			// ない場合は作成する
			$to_dir  = ACS_TEMPORARY_FILE_DIR;
			if(!file_exists($to_dir)) {mkdir($to_dir); chmod($to_dir, 0777);}
		//ファイルがある場合、仮置き場所を設定する
			$_FILES['new_file']['upload_tmp_dir'] = ACS_TEMPORARY_FILE_DIR;
			//仮置きのファイル名を設定する
			$type_name = session_id();
			$upload = $_FILES['new_file']['upload_tmp_dir'];
			$upload .= $type_name;
			if ( !move_uploaded_file( $_FILES['new_file']['tmp_name'], $upload ) ) {
				echo "ファイルの読み込みに失敗しました\n";
			}
			$_FILES['new_file']['tmp_name'] = $upload;
			$form['file_obj'] = $_FILES['new_file'];
			$user->setAttribute('new_file_info',$upload);
			$user->setAttribute('new_file_obj',$_FILES['new_file']);
		}
		$form['xdate'] = $request->getParameter('xdate');											//掲載最終日：xdate
		//掲載最終日デフォルト値
		if($form['xdate'] == ''){
			$form[xdate] ='';
		}
		//閲覧許可コミュニティ
		$form['trusted_community_id_array'] = $request->getParameter('trusted_community_id_array');
		$form['trusted_community_row_array'] = ACSCommunity::get_each_community_row_array($form['trusted_community_id_array']);

		$form['community_id'] = $community_id;																		//当コミュニティのID
		$form['user_community_id'] = $acs_user_info_row['user_community_id']; 		// 投稿者print "form_row:";

		// ML送信オプション
		$form['is_ml_send'] = $request->getParameter('is_ml_send');								//画像：new_file
//☆☆　ここまでほぼ同じ
		$user->setAttribute('new_form_obj',$form);

		return View::SUCCESS;





/* 登録確定ボタン「はい」より */
	}else if($move_id==2){
//☆☆　ここからほぼ同
		// 画面上のフォーム情報を取得する
		$form = $user->getAttribute('new_form_obj');
		$new_file_obj = $form['file_obj'];
//☆☆　ここまでほぼ同じ
		// DBへの書き込み等
		ACSDB::_do_query("BEGIN");
		if($form['file_name'] != ""){	//ファイル情報があった場合
		//1.ファイル情報取得(新規)
			$file_obj = ACSFile::get_upload_file_info_instance($user->getAttribute('new_file_obj'),$community_id,$form['user_community_id']);
			//form情報へ登録
			$form['new_file'] = $file_obj;
		}
		//2.bbsテーブル情報
		$ret = ACSBBS::set_bbs($form);
		if($ret){
			ACSDB::_do_query("COMMIT");
			// 掲示板アクセス履歴
			ACSBBS::set_bbs_access_history($acs_user_info_row['user_community_id'], $ret);
		}else{
			ACSDB::_do_query("ROLLBACK");
		}
		$bbs_id_seq = $ret;

		// MLオプションありの場合
		if ($form['is_ml_send']=='t') {

			// MLステータスの取得
			$ml_status_row = ACSCommunity::get_contents_row(
					$community_id, ACSMsg::get_mst('contents_type_master','D62'));
			$ml_status = $ml_status_row['contents_value'];

			// ML有りの場合メールを送信
			if ($bbs_id_seq && $ml_status == 'ACTIVE') {

				// 件名編集
				$subject = str_replace('{BBSID}',
						$bbs_id_seq,ACS_COMMUNITY_ML_SUBJECT_FORMAT) . $form['subject'];

				// ML送信
				ACSCommunityMail::send_community_mailing_list(
						$community_id, 
						$acs_user_info_row['mail_addr'],
						$subject, 
						$form['body']);
			}
		}

		$action_url  = $this->getControllerPath('Community', 'BBS'). '&community_id=' . $community_id. '&move_id=4';

		header("Location: $action_url");
	}
}

	function getRequestMethods() {
		return Request::POST;
	}	

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$move_id = $request->getParameter('move_id');

		// 入力画面からの場合のみ、入力チェックをする
		if ($move_id == 1) {
			/* 必須チェック */
			parent::regValidateName($validatorManager, 
					"subject", 
					true, 
					ACSMsg::get_msg('Community', 'BBSPreAction.class.php', 'M001'));
			parent::regValidateName($validatorManager, 
					"body", 
					true, 
					ACSMsg::get_msg('Community', 'BBSPreAction.class.php', 'M002'));
			parent::regValidateName($validatorManager, 
					"open_level_code", 
					true, 
					ACSMsg::get_msg('Community', 'BBSPreAction.class.php', 'M003'));

			/* 日付チェック */
			// 掲載終了日
			$xdate = $request->getParameter('xdate');
			if ($xdate) {
				$validator =& new DateValidator($controller);
				$criteria = array('date_error' => ACSMsg::get_msg('Community', 'BBSPreAction.class.php', 'M004'));
				$validator->initialize($criteria);
				//$validatorManager->register('xdate', $validator);
				$validatorManager->registerValidator('xdate', $validator);
			}
		}
	}

	function handleError () {
		return $this->execute();
	}
	
	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_MEMBER');
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
