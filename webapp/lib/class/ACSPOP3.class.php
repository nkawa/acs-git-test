<?php
// $Id: ACSPOP3.class.php,v 1.1 2006/12/18 07:41:48 w-ota Exp $

// PEAR
require_once 'Net/POP3.php';
require_once 'Mail/mimeDecode.php';
require_once 'Mail/RFC822.php';

/**
 * POP3アクセスクラス
 */
class ACSPOP3 {
	/**
	 * POP3サーバにアクセスしてメールを取得する (メイン処理)
	 *
	 * @param $do_dele_flag 1=受信後にメールを削除する / 0=削除しない
	 * @return $mail_row_array メール情報($mail_row)の配列
	 */
	static function get_mail_row_array($params, $do_dele_flag = 0) {
		// 戻り値
		$mail_row_array = array();


		//----------------
		// POP3サーバ接続
		//----------------

		// POP3接続ハンドル
		$pop3 =& new Net_POP3;

		// POP3サーバに接続
		if (PEAR::isError($pop3->connect($params['POP_SERVER'], $params['POP_PORT']))) {
			echo "error: connect()\n";
			exit;
		}
		// POP3ログイン
		if (PEAR::isError($pop3->login($params['POP_USER'], $params['POP_PASSWD']))) {
			echo "error: login()\n";
			exit;
		}

		//------------
		// メール取得
		//------------

		// メール一覧を取得
		$mail_array = $pop3->getListing();

		// 全件のメールについてループ
		foreach ($mail_array as $mail) {

			// ヘッダを含めたメール全文を取得
			$msg = $pop3->getMsg($mail['msg_id']);

			// 1通のメール解析
			$mail_row = ACSPOP3::parse_mail($msg);

			// 戻り値の配列にpush
			array_push($mail_row_array, $mail_row);

			// DB登録に成功したメールは削除する
			if ($do_dele_flag) {
				$pop3->deleteMsg($mail['msg_id']);
			}
		}

		//----------------
		// POP3サーバ切断
		//----------------
		$pop3->disconnect();

		return $mail_row_array;
	}


	/**
	 * アドレスの羅列から名前とメールアドレスの連想配列を取得する
	 *
	 * @param $str メールヘッダに含まれるメールアドレスの羅列
	 * @param メールアドレスの配列
	 */
	static function get_mail_addr_array($str) {
		// メールアドレスリストとしてparse
		$mail_addr_obj_array = Mail_RFC822::parseAddressList($str);

		// メールアドレスを格納する配列
		$mail_addr_array = array();
		foreach ($mail_addr_obj_array as $mail_addr_obj) {
			// メールアドレス
			$mail_addr = $mail_addr_obj->mailbox . '@' . $mail_addr_obj->host;
			array_push($mail_addr_array, $mail_addr);
		}

		return $mail_addr_array;
	}


	/**
	 * 1通のメールを解析する
	 *
	 * @param Net_POP3::getMsg($msg_id)で取得したオブジェクト (1通のメールデータ)
	 * @return 連想配列形式で情報を保持した1通のメール
	 */
	static function parse_mail($msg) {
		// メール解析結果を格納する連想配列
		$mail_row = array();

		$params = array();
		$params['include_bodies'] = true;
		$params['decode_bodies']  = false;
		$params['decode_headers'] = false;
		$params['input'] = $msg;

		// MIMEデコードされたオブジェクトを取得
		$mail_obj = Mail_mimeDecode::decode($params);

		// From
		if (isset($mail_obj->headers['from'])) {
			$mail_row['from_array'] = ACSPOP3::get_mail_addr_array($mail_obj->headers['from']);
		}
		// To
		if (isset($mail_obj->headers['to'])) {
			$mail_row['to_array'] = ACSPOP3::get_mail_addr_array($mail_obj->headers['to']);
		}
		// Cc
		if (isset($mail_obj->headers['cc'])) {
			$mail_row['cc_array'] = ACSPOP3::get_mail_addr_array($mail_obj->headers['cc']);
		}
		// 件名(Subject)
		if (isset($mail_obj->headers['subject'])) {
			$mail_row['subject'] = mb_decode_mimeheader($mail_obj->headers['subject']);
		}
		// 日付(Date)
		if (isset($mail_obj->headers['date'])) {
			// ロケールに沿った日付 (YYYY/MM/DD HH:MM:SS)
			$t = strtotime($mail_obj->headers['date']);
			$mail_row['date'] = strftime('%G/%m/%d %T', $t);
		}

		// 本文(Body) (内部エンコーディングに変換)
		if (isset($mail_obj->parts)) {
			// MIMEパートが有るメール
			foreach ($mail_obj->parts as $index => $parts) {
				if ($parts->ctype_primary == 'text' && !isset($parts->headers['content-disposition']) && !isset($mail_row['body'])) {
					// マルチパート内の本文
					$mail_row['body'] = mb_convert_encoding($parts->body, mb_internal_encoding(), 'auto');
					break;
				}
			}
		} else {
			// MIMEパートが無いメール
			$mail_row['body'] = mb_convert_encoding($mail_obj->body, mb_internal_encoding(), 'auto');
		}

		return $mail_row;
	}
}

?>
