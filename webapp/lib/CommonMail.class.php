<?php
/**
 * ���̥᡼�륯�饹
 * �᡼��ƥ�ץ졼��(Smarty)����᡼��(�ƥ�����)��������ơ��᡼����������롣
 * �᡼���������ˡ�Ȥ��ơ�Ʊ��/��Ʊ���Υ᡼������I/F���Ѱդ���Ƥ��롣
 * 1.sendMail�ˤ��Ʊ���᡼������
 *   �᡼��ƥ�ץ졼�Ȥ˥ǡ����򥻥åȤ����᡼�����������
 * 2.sendQueueMail�ˤ����Ʊ���᡼������
 *   �᡼��ƥ�ץ졼�Ȥ˥ǡ����򥻥åȤ����᡼���mail_queue�˳�Ǽ��addQueueMail�ˤ��Ƥ���
 *   ��������
 * @access public
 * @package webapp/lib
 * @category utility
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
class CommonMail extends CommonMakeText 
{
	/* ����ƥ� (�᡼�륹�ȥ졼��) ���ץ���� */
	private $container_opt;
	
	/* �᡼�顼���ץ���� */
	private $mail_opt;

	/* �᡼�顼 */
	private $mail;

	/**
	 * ���󥹥ȥ饯��
	 * @access public
	 */
	function __construct()
	{
		parent::__construct();
			
		/* ����ƥ� (�᡼�륹�ȥ졼��) ���ץ�������� */
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
		
		/* �᡼�顼���ץ�������� */
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
		
		/* �᡼�顼���� */
		$options = $this->mail_opt;
		unset($options['driver']);
		$this->mail = &Mail::factory($this->mail_opt['driver'], $options);
	}
	
	/**
	 * �᡼������������Ʊ�������ѡ�
	 * ���᡼��ƥ�ץ졼�Ȥ˥ƥ�ץ졼���ѿ��򥻥åȤ����᡼����������롣
	 * ���᡼���BCC�ǥ᡼����������롣TO�ˤ�FROM�Υ᡼�륢�ɥ쥹�����ꤹ�롣
	 * ��ʣ����������˥᡼�����������硢�᡼�륢�ɥ쥹��,(���ڤ�ʸ���ˤǶ��ڤ�
	 * �����ꤹ�뤫���᡼�륢�ɥ쥹������˳�Ǽ���뤳�ȡ�
	 * ���ʤ���������᡼�륢�ɥ쥹��Ϣ������ξ�硢�����񼰤ǥ᡼�륢�ɥ쥹�����ꤹ�롣
	 * ����Ϣ������Υ�����������ΰ�̾�����ꤷ���᡼����������ǽ�Ǥ����
	 * ���񼰡�"$key" <$value>
	 * 
	 * @access public
	 * @param string $from �������᡼�륢�ɥ쥹
	 * @param string/array $to ������᡼�륢�ɥ쥹
	 * @param string $subject ��̾
	 * @param array $headers �᡼��إå���Ϣ�������
	 * @return boolean �������
	 */
	public function sendMail ($from, $to, $subject, $headers = null)
	{
		/* �᡼��ƥ�ץ졼�Ȥ���᡼��ʥƥ����ȡˤ��������� */
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

		/* �᡼����������� */
		$ret = $this->mail->send($to, $headers, $body);

		if (PEAR::isError($ret)) {
			throw new ApplicationException($ret->getMessage());
		}

		return $ret;
	}

	/**
	 * �᡼���mail_queue�˳�Ǽ�������Ʊ�������ѡ�
	 * ���᡼��ƥ�ץ졼�Ȥ˥ƥ�ץ졼���ѿ��򥻥åȤ����᡼���mail_queue�˳�Ǽ���롣
	 * ���᡼���BCC�ǥ᡼����������롣TO�ˤ�FROM�Υ᡼�륢�ɥ쥹�����ꤹ�롣
	 * ��ʣ����������˥᡼�����������硢�᡼�륢�ɥ쥹��,(���ڤ�ʸ���ˤǶ��ڤ�
	 * �����ꤹ�뤫���᡼�륢�ɥ쥹������˳�Ǽ���뤳�ȡ�
	 * ���ʤ���������᡼�륢�ɥ쥹��Ϣ������ξ�硢�����񼰤ǥ᡼�륢�ɥ쥹�����ꤹ�롣
	 * ����Ϣ������Υ�����������ΰ�̾�����ꤷ���᡼����������ǽ�Ǥ����
	 * ���񼰡�"$key" <$value>
	 * ���᡼����ãͽ�������ϡ������ॹ�����(mktime()�ˤ����ꤹ�뤳��
	 * 
	 * @access public
	 * @param string $from �������᡼�륢�ɥ쥹
	 * @param string/array $to ������᡼�륢�ɥ쥹
	 * @param string $subject ��̾
	 * @param array $headers �᡼��إå���Ϣ�������
	 * @param int $reservetime �᡼����ãͽ������
	 * @return int �᡼��ɣ�
	 */
	public function addQueueMail ($from, $to, $subject, $headers = null, $reservetime = null)
	{
		/* ������� */
		$mailID = null;
		$mail_queue = &new Mail_Queue($this->container_opt, $this->mail_opt);

		/* �᡼��ƥ�ץ졼�Ȥ���᡼��ʥƥ����ȡˤ��������� */
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
			// �᡼���mail_queue�˳�Ǽ����
			$mailID = $mail_queue->put($from, $to, $headers, $body);
		} else {
			// �᡼����ã��������ꤷ���᡼���mail_queue�˳�Ǽ����
			$wait = $reservetime - time();
			$mailID = $mail_queue->put($from, $to, $headers, $body, $wait);
		}

		return $mailID;
	}

	/**
	 * �᡼���mail_queue�������������Ʊ�������ѡ�
	 * @access public
	 * @param int $mailID �᡼��ɣ�
	 */
	public function delQueueMail ($mailID)
	{
		$mail_queue =& new Mail_Queue($this->container_opt, $this->mail_opt);
		$mail_queue->deleteMail($mailID);
	}

	/**
	 * �᡼��������������Ʊ�������ѡ�
	 * ��mail_queue�˳�Ǽ���줿�᡼��򤹤٤��������롣
	 * ���᡼����ãͽ��������ã���Ƥ��ʤ��᡼�����������ʤ���mail_queue�˻Ĥ롣
	 * �����̤Υ᡼�������������ϡ�����᡼��������������ꤹ�뤳�ȡ�
	 * 
	 * @access public
	 * @param int $limit ����᡼���������
	 * @return boolean �������
	 */
	public function sendQueueMail ($limit = MAILQUEUE_ALL)
	{
		/* ������� */
		$mail_queue = &new Mail_Queue($this->container_opt, $this->mail_opt);

		/* �᡼����������� */
		$ret = $mail_queue->sendMailsInQueue($limit);
		if (PEAR::isError($ret)) {
			throw new ApplicationException($ret->getMessage());
		}

		return $ret;
	}

	/**
	 * ʸ�����ʸ�������ɤ�JIS�˥��󥳡��ɤ���
	 * @access public
	 * @param string $str ʸ����
	 * @param string $from_charset �Ѵ���ʸ��������
	 * @return string ʸ����(JIS������)
	 */
	public function encodeJIS($str = null, $from_charset = 'EUC-JP'){
		/* �Ѵ���ʸ������������ */
		if (empty($from_charset)) {
			$from_charset = mb_detect_encoding($str);
		}
		
		/* ʸ�����JIS�˥��󥳡��ɤ��� */
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