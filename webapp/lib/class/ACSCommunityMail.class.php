<?php
/**
 * ACS Community Mail
 *
 * @author  acs
 * @version $Revision: 1.3 $
 */

class ACSCommunityMail
{
	/**
	 * コンストラクタ
	 *
	 * @param $community_id
	 * @param $acs_user_info_row アクセス者情報
	 * @param $folder_id
	 */
	function ACSCommunityMail ($community_id, $acs_user_info_row, $folder_id) {
	}

	/****************************
	 * スタティックファンクション
	 ****************************/

	/**
	 * コミュニティメンバへのメール送信(一括配信)
	 *
	 * define('ACS_COMMUNITY_ML_SERVER') のＳＭＴＰサーバで送信します。
	 * 送信者はコミュニティに設定されたメールアドレスとなります。
	 *
	 * メッセージに対して以下のプレースフォルダを置換します。
	 * {USER_COMMUNITY_NAME}
	 * {SYSTEM_BASE_URL}
	 * {SYSTEM_SCRIPT_PATH}
	 * {SYSTEM_MODULE_ACCESSOR}
	 * {SYSTEM_ACTION_ACCESSOR}
	 * {COMMUNITY_ID}
	 * {COMMUNITY_NAME}
	 *
	 * @param string $community_id コミュニティid
	 * @param string $from_address 送信者アドレス
	 *                          (From:のみReply-to:はコミュニティMLアドレスが自動付与される)
	 * @param string $subject 件名
	 * @param string $msg メッセージ本文
	 * @param string $mmb_id_array 対象ユーザコミュニティid配列(未指定時は全員)
	 * @return mixed true...正常/PearError オブジェクト...異常
	 */
	static function send_community_mailing_list (
			$community_id,$from_address,$subject,$msg,$mmb_id_array='') {

		// コミュニティMLアドレスの取得
		$ml_address_row = ACSCommunity::get_contents_row(
				$community_id, ACSMsg::get_mst('contents_type_master','D61'));

		// コミュニティMLステータスの取得
		$ml_status_row = ACSCommunity::get_contents_row(
				$community_id, ACSMsg::get_mst('contents_type_master','D62'));

		$ml_address = $ml_address_row['contents_value'];
		$ml_status = $ml_status_row['contents_value'];

		if ($ml_address == '' || $ml_status != 'ACTIVE') {
			return false;
		}

		// BBSシステムメールアドレスの取得
		$bbs_system_mailaddr = ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D03'),
					'COMMUNITY_ML_MAIL_ADDR');

		// コミュニティメンバ全員の情報を取得
		$mmb_info_rows =& ACSCommunity::get_community_member_mail_address_row_array(
				$community_id);

		// $mmb_id_array のインデックス化
		$mmb_array = array();
		if (is_array($mmb_id_array)) {
			foreach ($mmb_id_array as $mmb_id) {
				$mmb_array[$mmb_id] = TRUE;
			}
		}

		// Bccの生成
		$bcc = '';
		foreach ($mmb_info_rows as $mmb_info) {
			if ($mmb_id_array == '' || $mmb_array[$mmb_info['user_community_id']]) {
				$bcc .= $bcc == '' ? '' : ',';
				$bcc .= $mmb_info['mail_address'];
			}
		}

		// システム関連プレースフォルダの置換
		$msg = ACSCommunityMail::get_community_tag_replace($community_id, $msg);

		// Bccメール送信
		return ACSLib::send_mail(
				$from_address, 
				$bcc,             // Repicientsにはbcc送信ユーザのみ指定
				'', 
				$subject, 
				$msg, 
				array(
					'Sender'      => $bbs_system_mailaddr,
					'Bcc'         => $bcc,
					'Return-Path' => $ml_address,
					'Reply-to'    => $ml_address,
					// 見せかけのTo:にはMLアドレスを設定
					'To'          => $ml_address)); 
	}

	/**
	 * コミュニティメンバへのメール送信(個別配信)
	 *
	 * ※各ユーザの言語設定に従ってタグをリプレースします。
	 * ※システム設定のＳＭＴＰで送信します。
	 * メッセージに対して以下のプレースフォルダを置換します。
	 * {USER_COMMUNITY_NAME}
	 * {SYSTEM_BASE_URL}
	 * {SYSTEM_SCRIPT_PATH}
	 * {SYSTEM_MODULE_ACCESSOR}
	 * {SYSTEM_ACTION_ACCESSOR}
	 * {COMMUNITY_ID}
	 * {COMMUNITY_NAME}
	 *
	 * @param string $community_id コミュニティid
	 * @param string $from 送信者
	 * @param mixed $subject 配列の場合：件名(array([lang] => [件名]))
	 *                   文字列の場合、全ての言語で同一の件名
	 * @param mixed $msg 配列の場合：メッセージ本文(array([lang] => [本文]))
	 *                   文字列の場合、全ての言語で同一のメッセージ本文
	 * @param string $mmb_id_array 対象ユーザコミュニティid配列(未指定時は全員)
	 * @return mixed true...正常/PearError オブジェクト...異常
	 */
	static function send_community_mail ($community_id,$from,$subject,$msg,$mmb_id_array='') {

		// コミュニティメンバ全員の情報を取得
		$mmb_info_rows =& ACSCommunity::get_community_member_mail_address_row_array(
				$community_id);

		// インデックスにユーザコミュニティidを使用した配列を生成
		$mmb_info_array = array();
		$is_all = $mmb_id_array == '' ? TRUE : FALSE;
		foreach ($mmb_info_rows as $mmb_info) {
			$mmb_info_array[$mmb_info['user_community_id']] = $mmb_info;
			if ($is_all) {
				$mmb_id_array[] = $mmb_info['user_community_id'];
			}
		}

		// 言語種類別にシステム関連プレースフォルダの置換
		$msg_lang = array();
		$lang_list =& ACSMsg::get_lang_list_array();
		foreach ($lang_list as $lang => $lang_disp) {
			$msg_lang[$lang] = ACSCommunityMail::get_community_tag_replace(
					$community_id, (is_array($msg) ? $msg[$lang] : $msg));
		}

		// 対象ユーザにメールを送信
		if (is_array($mmb_id_array)) {

			foreach ($mmb_id_array as $mmb_id) {

				$mmb_info =& $mmb_info_array[$mmb_id];

				$lang = $mmb_info['mail_lang'] == '' ? 
						ACS_DEFAULT_LANG : $mmb_info['mail_lang'];

				$mmb_msg = $msg_lang[$lang];

				$mmb_subject = is_array($subject) ? $subject[$lang] : $subject;

				// 名前プレースフォルダの置換
				$mmb_msg = ACSMsg::get_tag_replace( $mmb_msg, 
						array('{USER_COMMUNITY_NAME}'=>$mmb_info['user_community_name']));
				$ret = ACSLib::send_mail($from, $mmb_info['mail_address'],
						'', $mmb_subject, $mmb_msg, array('Sender'=>$from));
				if (Pear::IsError($ret)) {
					return $ret;
				} 
			}
		}
		return true;
	}

	/**
	 * コミュニティ情報タグ置換
	 *
	 * メッセージに対して以下のプレースフォルダを置換します。
	 * {SYSTEM_BASE_URL}
	 * {SYSTEM_SCRIPT_PATH}
	 * {SYSTEM_MODULE_ACCESSOR}
	 * {SYSTEM_ACTION_ACCESSOR}
	 * {COMMUNITY_ID}
	 * {COMMUNITY_NAME}
	 *
	 * @param string $community_id コミュニティid
	 * @param string $msg メッセージ本文
	 * @return true / false
	 */
	static function get_community_tag_replace ($community_id,$msg) {

		$community_info = array();

		// システムURL
		$system_group = ACSMsg::get_mst('system_config_group','D01');

		$community_info['system_base_url'] = ACSSystemConfig::get_keyword_value(
				$system_group, 'SYSTEM_BASE_URL');
		$community_info['system_base_login_url'] = ACSSystemConfig::get_keyword_value(
				$system_group, 'SYSTEM_BASE_LOGIN_URL');

		// コミュニティ情報の取得
		$community_row =& ACSCommunity::get_community_row($community_id);
		$community_info['community_name'] = $community_row['community_name'];

		// コミュニティーURL
		$community_info['community_url'] = 
				$community_info['system_base_login_url'] . SCRIPT_PATH .
				"?" . MODULE_ACCESSOR . "=Community" .
				"&" . ACTION_ACCESSOR . "=Index" .
				"&community_id=".$community_id;

		return ACSMsg::get_tag_replace($msg, array(
				'{SYSTEM_BASE_URL}'			=> $community_info['system_base_url'],
				'{SYSTEM_SCRIPT_PATH}'		=> 
						$community_info['system_base_login_url'] . SCRIPT_PATH,
				'{SYSTEM_MODULE_ACCESSOR}'	=> MODULE_ACCESSOR,
				'{SYSTEM_ACTION_ACCESSOR}'	=> ACTION_ACCESSOR,
				'{COMMUNITY_ID}'			=> $community_id,
				'{COMMUNITY_NAME}'			=> $community_info['community_name'],
				'{COMMUNITY_URL}'			=> $community_info['community_url']
		));
	}

	/**
	 * MLへのファイルアップロード通知メール送信
	 *
	 * 各ユーザの言語に対応してメールを送信する
	 *
	 * @return true / false
	 */
	static function send_fileupload_mail($community_id, &$user_info, &$folder, &$file) {

		// ファイル詳細画面URL
		$file_detail_url  = "{SYSTEM_SCRIPT_PATH}" .
			"?{SYSTEM_MODULE_ACCESSOR}=Community" .
			"&{SYSTEM_ACTION_ACCESSOR}=FileDetail" .
			"&community_id={$community_id}" .
			"&folder_id={$folder->folder_id}" .
			"&file_id={$file->file_id}";
	
		// コミュニティ情報の取得
		$community_row =& ACSCommunity::get_community_row($community_id);

		// 各言語用のメッセージを準備
		$msgs = array();
		$org_lang = ACSMsg::get_lang();
		foreach (ACSMsg::get_lang_list_array() as $lang => $lang_disp) {

			ACSMsg::set_lang($lang);
			$msgs[$lang] = ACSMsg::get_serial_msg('lib',basename(__FILE__),'UPL%03d');
			$msgs[$lang] = ACSMsg::get_tag_replace($msgs[$lang], array(
					'{COMMUNITY_NAME}'              => $community_row['community_name'],
					'{USER_NAME}'                   => $user_info['user_name'],
					'{USER_COMMUNITY_NAME}'         => $user_info['community_name'],
					'{FILE_DETAIL_URL}'             => $file_detail_url,
					'{UPLOAD_FILE_NAME}'          	=> $file->display_file_name
			));
		}
		ACSMsg::set_lang($org_lang);

		// システムのメールアドレスを取得
		$system_mail_addr = ACSSystemConfig::get_keyword_value(
		ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');

		// 件名
		$subject = ACSMsg::get_mdmsg(__FILE__,'M001');

		// メール送信
		return ACSCommunityMail::send_community_mail(
				$community_id, $system_mail_addr, $subject, $msgs);
	}

	/**
	 * へのコミュニティフォルダプット通知メール
	 *
	 * 各ユーザの言語に対応してメールを送信する
	 *
	 * @return true / false
	 */
	static function send_putfolder_mail(&$user_info, &$folder, $community_id) {

		// フォルダ・ファイル一覧画面URL
		$folder_list_url  = "{SYSTEM_SCRIPT_PATH}" .
			"?{SYSTEM_MODULE_ACCESSOR}=Community" .
			"&{SYSTEM_ACTION_ACCESSOR}=Folder" .
			"&community_id=" . $community_id .
			"&folder_id=" . $folder['folder_id'];

		// コミュニティ情報の取得
		$community_row =& ACSCommunity::get_community_row($community_id);

		// 各言語用のメッセージを準備
		$msgs = array();
		$org_lang = ACSMsg::get_lang();
		foreach (ACSMsg::get_lang_list_array() as $lang => $lang_disp) {

			ACSMsg::set_lang($lang);
			$msgs[$lang] = ACSMsg::get_serial_msg('lib',basename(__FILE__),'PUT%03d');
			$msgs[$lang] = ACSMsg::get_tag_replace($msgs[$lang], array(
					'{USER_NAME}'                   => $user_info['user_name'],
					'{USER_COMMUNITY_NAME}'         => $user_info['community_name'],
					'{FOLDER_LIST_URL}'             => $folder_list_url,
					'{PUT_FOLDER_NAME}'          	=> $folder['folder_name']
			));

			// subjectの編集
			$subjects[$lang] = ACSMsg::get_mdmsg(__FILE__, 'M005');
		}
		ACSMsg::set_lang($org_lang);

		// システムのメールアドレスを取得
		$system_mail_addr = ACSSystemConfig::get_keyword_value(
		ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');

		// 件名
		//$subject = ACSMsg::get_mdmsg(__FILE__,'M005');

		// メール送信
		return ACSCommunityMail::send_community_mail(
				$community_id, $system_mail_addr, $subjects, $msgs);
	}

	/**
	 * スケジュール通知メールの送信
	 *
	 * 各ユーザの言語に対応してスケジュール関連の<br>
	 * 通知メールを送信する
	 *
	 * @param object $schedule スケジュールインスタンス(ACSSchedule)
	 * @param string $serial_msg_key_fmg シリアルメッセージ取得用フォーマット(例:"RMD%03d")
	 * @param string $subject_msg_key 件名メッセージ取得用キー(例:"M001")
	 * @param array  $additional_tags 追加する置換プレースフォルダ(未指定も可)
	 * @param array  $additional_message_tags 追加する置換プレースフォルダ(メッセージid指定)
	 *               (未指定も可) メール言語に対応して置換されます
	 * @return boolean true/false
	 */
	static function send_schedule_announce_mail (
			&$schedule, 
			$serial_msg_key_fmg, 
			$subject_msg_key, 
			$additional_tags = "",
			$additional_message_tags = "") {

		// スケジュールＵＲＬ
		$schedule_url  = "{SYSTEM_SCRIPT_PATH}" .
				"?{SYSTEM_MODULE_ACCESSOR}=Community" .
				"&{SYSTEM_ACTION_ACCESSOR}=AnswerSchedule" .
				"&community_id={$schedule->community_id}" .
				"&schedule_id={$schedule->schedule_id}";

		$user_community_row =& ACSUser::get_user_profile_row($schedule->user_community_id);

		// システムのメールアドレスを取得
		$system_mail_addr = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');

		// 各言語用のメッセージを準備
		$msgs = array();
		$subjects = array();
		$org_lang = ACSMsg::get_lang();
		foreach (ACSMsg::get_lang_list_array() as $lang => $lang_disp) {

			ACSMsg::set_lang($lang);

			$tags_array = array(
					'{USER_NAME}'					=> $user_community_row['user_name'],
					'{USER_COMMUNITY_NAME}'			=> $user_community_row['community_name'],
					'{SCHEDULE_NAME}'				=> $schedule->schedule_name,
					'{SCHEDULE_DETAIL}'				=> $schedule->schedule_detail,
					'{SCHEDULE_CLOSING_DATETIME}'	=> ACSLib::convert_pg_date_to_str(
							$schedule->schedule_closing_datetime),
					'{SCHEDULE_URL}'			=> $schedule_url
			);

			if (is_array($additional_tags)) {
				$tags_array = array_merge($tags_array, $additional_tags);
			}

			if (is_array($additional_message_tags)) {
				$msg_array = array();
				foreach ($additional_message_tags as $tag => $msg_key) {
					$msg_array[$tag] = ACSMsg::get_mdmsg(__FILE__, $msg_key);
				}
				$tags_array = array_merge($tags_array, $msg_array);
			}

			$msgs[$lang] = ACSMsg::get_serial_msg(
					'lib',basename(__FILE__), $serial_msg_key_fmg);
			$msgs[$lang] = ACSMsg::get_tag_replace($msgs[$lang], $tags_array);

			// subjectの編集
			$subjects[$lang] = ACSMsg::get_mdmsg(__FILE__, $subject_msg_key);

		}
		ACSMsg::set_lang($org_lang);

		// メールの送信(全コミュニティメンバ)
		return ACSCommunityMail::send_community_mail(
				$schedule->community_id, $system_mail_addr, $subjects, $msgs);
	}

}
?>
