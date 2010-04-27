<?php
/**
 * 共通メールクラス
 * メールテンプレート(Smarty)からメール(テキスト)を作成して、メールを送信する。
 * メールの送信方法として、同期/非同期のメール送信I/Fが用意されている。
 * 1.sendMailによる同期メール送信
 *   メールテンプレートにデータをセットし、メールを送信する
 * 2.sendQueueMailによる非同期メール送信
 *   メールテンプレートにデータをセットし、メールをmail_queueに格納（addQueueMail）してから
 *   送信する
 * @access public
 * @package webapp/lib
 * @category utility
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
class CommonMail extends CommonMakeText 
{
	/* コンテナ (メールストレージ) オプション */
	private $container_opt;
	
	/* メーラーオプション */
	private $mail_opt;

	/* メーラー */
	private $mail;

	/**
	 * コンストラクタ
	 * @access public
	 */
	function __construct()
	{
		parent::__construct();
			
		/* コンテナ (メールストレージ) オプション設定 */
		$this->container_opt = array (
			'type' => 'db',
			'dsn'  => array('phptype'  => DB_PHPTYPE,
							'hostspec' => DB_HOSTSPEC,
							'port'	 => DB_PORT,
							'database' => DB_DATABASE,
							'username' => DB_USERNAME,
							'password' => DB_PASSWORD),
			'mail_table' => S4_MAIL_TABLE
		);
		
		/* メーラーオプション設定 */
		if ('mail' == strtolower(S4_MAIL_DRIVER)) {
			$this->mail_opt['driver'] = S4_MAIL_DRIVER;
			if (S4_MAIL_ARGS != '') {
				$this->mail_opt['param'] = S4_MAIL_ARGS;
			}
		} elseif ('sendmail' == strtolower(S4_MAIL_DRIVER)) {
			$this->mail_opt['driver'] = S4_MAIL_DRIVER;
			if (S4_SENDMAIL_PATH != '') {
				$this->mail_opt["sendmail_path"] = S4_SENDMAIL_PATH;
			}
			if (S4_SENDMAIL_ARGS != '') {
				$this->mail_opt["sendmail_args"] = S4_SENDMAIL_ARGS;
			}
		} elseif ('smtp' == strtolower(S4_MAIL_DRIVER)) {
			if (S4_MAIL_DRIVER != '') {
				$this->mail_opt['driver'] = S4_MAIL_DRIVER;
			}
			if (S4_SMTP_HOST != '') {
				$this->mail_opt["host"] = S4_SMTP_HOST;
			}
			if (S4_SMTP_PORT != '') {
				$this->mail_opt["port"] = S4_SMTP_PORT;
			}
			if (S4_SMTP_AUTH != '') {
				$this->mail_opt["auth"] = S4_SMTP_AUTH;
			}
			if (S4_SMTP_USERNAME != '') {
				$this->mail_opt["username"] = S4_SMTP_USERNAME;
			}
			if (S4_SMTP_PASSWORD != '') {
				$this->mail_opt["password"] = S4_SMTP_PASSWORD;
			}
			if (S4_SMTP_LOCALHOST != '') {
				$this->mail_opt["localhost"] = S4_SMTP_LOCALHOST;
			}
			if (S4_SMTP_TIMEOUT != '') {
				$this->mail_opt["timeout"] = S4_SMTP_TIMEOUT;
			}
			if (S4_SMTP_VERP != '') {
				$this->mail_opt["verp"] = S4_SMTP_VERP;
			}
			if (S4_SMTP_DEBUG != '') {
				$this->mail_opt["debug"] = S4_SMTP_DEBUG;
			}
			if (S4_SMTP_PERSIST != '') {
				$this->mail_opt["persist"] = S4_SMTP_PERSIST;
			}
		} else {
			throw new ApplicationException('Illegal mail driver! driver=' . S4_MAIL_DRIVER);
		}
		
		/* メーラー取得 */
		$options = $this->mail_opt;
		unset($options['driver']);
		$this->mail = &Mail::factory($this->mail_opt['driver'], $options);
	}
	
	/**
	 * メールを送信する（同期送信用）
	 * ・メールテンプレートにテンプレート変数をセットし、メールを送信する。
	 * ・メールはBCCでメール送信される。TOにはFROMのメールアドレスを設定する。
	 * ・複数の送信先にメール送信する場合、メールアドレスを,(区切り文字）で区切り
	 * 　設定するか、メールアドレスを配列に格納すること。
	 * 　なお、送信先メールアドレスが連想配列の場合、下記書式でメールアドレスを設定する。
	 * 　（連想配列のキーに送信先の宛名を設定し、メール送信が可能である）
	 * 　書式："$key" <$value>
	 * 
	 * @access public
	 * @param string $from 送信元メールアドレス
	 * @param string/array $to 送信先メールアドレス
	 * @param string $subject 件名
	 * @param array $headers メールヘッダ（連想配列）
	 * @return boolean 処理結果
	 */
	public function sendMail ($from, $to, $subject, $headers = null)
	{
		/* メールテンプレートからメール（テキスト）を生成する */
		$message = $this->render();
		$mime = &new Mail_mime();
		$mime->setFrom($from);
		if (!empty($to)) {
			if (is_array($to)) {
				foreach ($to as $val) {
					$mime->addBcc($val);
				}
			} else {
				$mime->addBcc($to);
			}
		}
		$to = $from;

		$subject = mb_encode_mimeheader($subject, "ISO-2022-JP",'B', "\n");
		
		$mime->setSubject($subject);
		$mime->setTXTBody($this->encodeJIS($message));
		$body = $mime->get(array("text_charset" => "ISO-2022-JP", 
			"head_charset" => "ISO-2022-JP", "text_encoding" => "7bit"));
		$headers['To'] = $to;
		$headers = $mime->headers($headers);

		/* メールを送信する */
		$ret = $this->mail->send($to, $headers, $body);

		if (PEAR::isError($ret)) {
			throw new ApplicationException($ret->getMessage());
		}

		return $ret;
	}

	/**
	 * メールをmail_queueに格納する（非同期送信用）
	 * ・メールテンプレートにテンプレート変数をセットし、メールをmail_queueに格納する。
	 * ・メールはBCCでメール送信される。TOにはFROMのメールアドレスを設定する。
	 * ・複数の送信先にメール送信する場合、メールアドレスを,(区切り文字）で区切り
	 * 　設定するか、メールアドレスを配列に格納すること。
	 * 　なお、送信先メールアドレスが連想配列の場合、下記書式でメールアドレスを設定する。
	 * 　（連想配列のキーに送信先の宛名を設定し、メール送信が可能である）
	 * 　書式："$key" <$value>
	 * ・メール配達予約日時は、タイムスタンプ(mktime()）で設定すること
	 * 
	 * @access public
	 * @param string $from 送信元メールアドレス
	 * @param string/array $to 送信先メールアドレス
	 * @param string $subject 件名
	 * @param array $headers メールヘッダ（連想配列）
	 * @param int $reservetime メール配達予約日時
	 * @return int メールＩＤ
	 */
	public function addQueueMail ($from, $to, $subject, $headers = null, $reservetime = null)
	{
		/* 初期処理 */
		$mailID = null;
		$mail_queue = &new Mail_Queue($this->container_opt, $this->mail_opt);

		/* メールテンプレートからメール（テキスト）を生成する */
		$message = $this->render();
		$mime = &new Mail_mime();
		$mime->setFrom($from);
		if (!empty($to)) {
			if (is_array($to)) {
				foreach ($to as $val) {
					$mime->addBcc($val);
				}
			} else {
				$mime->addBcc($to);
			}
		}
		$to = $from;

		$subject = mb_encode_mimeheader($subject, "ISO-2022-JP",'B', "\n");
		
		$mime->setSubject($subject);
		$mime->setTXTBody($this->encodeJIS($message));
		$body = $mime->get(array("text_charset" => "ISO-2022-JP", 
			"head_charset" => "ISO-2022-JP", "text_encoding" => "7bit"));
		$headers['To'] = $to;
		$headers = $mime->headers($headers);

		if (empty($reservetime)) {
			// メールをmail_queueに格納する
			$mailID = $mail_queue->put($from, $to, $headers, $body);
		} else {
			// メール配達日時を指定し、メールをmail_queueに格納する
			$wait = $reservetime - time();
			$mailID = $mail_queue->put($from, $to, $headers, $body, $wait);
		}

		return $mailID;
	}

	/**
	 * メールをmail_queueから削除する（非同期送信用）
	 * @access public
	 * @param int $mailID メールＩＤ
	 */
	public function delQueueMail ($mailID)
	{
		$mail_queue =& new Mail_Queue($this->container_opt, $this->mail_opt);
		$mail_queue->deleteMail($mailID);
	}

	/**
	 * メールを送信する（非同期送信用）
	 * ・mail_queueに格納されたメールをすべて送信する。
	 * ・メール配達予約日時に達していないメールは送信されない。mail_queueに残る。
	 * ・大量のメールを送信する場合は、最大メール送信件数を設定すること。
	 * 
	 * @access public
	 * @param int $limit 最大メール送信件数
	 * @return boolean 処理結果
	 */
	public function sendQueueMail ($limit = MAILQUEUE_ALL)
	{
		/* 初期処理 */
		$mail_queue = &new Mail_Queue($this->container_opt, $this->mail_opt);

		/* メールを送信する */
		$ret = $mail_queue->sendMailsInQueue($limit);
		if (PEAR::isError($ret)) {
			throw new ApplicationException($ret->getMessage());
		}

		return $ret;
	}

	/**
	 * 文字列の文字コードをJISにエンコードする
	 * @access public
	 * @param string $str 文字列
	 * @param string $from_charset 変換前文字コード
	 * @return string 文字列(JISコード)
	 */
	public function encodeJIS($str = null, $from_charset = 'EUC-JP'){
		/* 変換前文字コード設定 */
		if (empty($from_charset)) {
			$from_charset = mb_detect_encoding($str);
		}
		
		/* 文字列をJISにエンコードする */
		if ($from_charset == 'EUC-JP') {
			$str_JIS = '';
			$mode = 0;
			$b = unpack("C*", $str);
			$n = count($b);
			for ($i = 1; $i <= $n; $i++) {
				if ($b[$i] == 0x8E) {
					if ($mode != 2) {
						$mode = 2;
						$str_JIS .= pack("CCC", 0x1B, 0x28, 0x49);
					}
					$b[$i+1] -= 0x80;
					$str_JIS .= pack("C", $b[$i+1]);
					$i++;
				} elseif ($b[$i] > 0x8E) {
					if ($mode != 1){
						$mode = 1;
						$str_JIS .= pack("CCC", 0x1B, 0x24, 0x42);
					}
					$b[$i] -= 0x80; $b[$i+1] -= 0x80;
					$str_JIS .= pack("CC", $b[$i], $b[$i+1]);
					$i++;
				} else {
					if ($mode != 0) {
						$mode = 0;
						$str_JIS .= pack("CCC", 0x1B, 0x28, 0x42);
					}
					$str_JIS .= pack("C", $b[$i]);
				}
			}
			if ($mode != 0) $str_JIS .= pack("CCC", 0x1B, 0x28, 0x42);
		} else {
			$str_JIS  = mb_convert_encoding($str, "ISO-2022-JP", $from_charset);
		}

		return $str_JIS;
	}
}
?>