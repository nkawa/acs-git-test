<?php
// $Id: ACSPOP3.class.php,v 1.1 2006/12/18 07:41:48 w-ota Exp $

// PEAR
require_once 'Net/POP3.php';
require_once 'Mail/mimeDecode.php';
require_once 'Mail/RFC822.php';

/**
 * POP3�����������饹
 */
class ACSPOP3 {
	/**
	 * POP3�����Ф˥����������ƥ᡼���������� (�ᥤ�����)
	 *
	 * @param $do_dele_flag 1=������˥᡼��������� / 0=������ʤ�
	 * @return $mail_row_array �᡼�����($mail_row)������
	 */
	static function get_mail_row_array($params, $do_dele_flag = 0) {
		// �����
		$mail_row_array = array();


		//----------------
		// POP3��������³
		//----------------

		// POP3��³�ϥ�ɥ�
		$pop3 =& new Net_POP3;

		// POP3�����Ф���³
		if (PEAR::isError($pop3->connect($params['POP_SERVER'], $params['POP_PORT']))) {
			echo "error: connect()\n";
			exit;
		}
		// POP3������
		if (PEAR::isError($pop3->login($params['POP_USER'], $params['POP_PASSWD']))) {
			echo "error: login()\n";
			exit;
		}

		//------------
		// �᡼�����
		//------------

		// �᡼����������
		$mail_array = $pop3->getListing();

		// ����Υ᡼��ˤĤ��ƥ롼��
		foreach ($mail_array as $mail) {

			// �إå���ޤ᤿�᡼����ʸ�����
			$msg = $pop3->getMsg($mail['msg_id']);

			// 1�̤Υ᡼�����
			$mail_row = ACSPOP3::parse_mail($msg);

			// ����ͤ������push
			array_push($mail_row_array, $mail_row);

			// DB��Ͽ�����������᡼��Ϻ������
			if ($do_dele_flag) {
				$pop3->deleteMsg($mail['msg_id']);
			}
		}

		//----------------
		// POP3����������
		//----------------
		$pop3->disconnect();

		return $mail_row_array;
	}


	/**
	 * ���ɥ쥹�����󤫤�̾���ȥ᡼�륢�ɥ쥹��Ϣ��������������
	 *
	 * @param $str �᡼��إå��˴ޤޤ��᡼�륢�ɥ쥹������
	 * @param �᡼�륢�ɥ쥹������
	 */
	static function get_mail_addr_array($str) {
		// �᡼�륢�ɥ쥹�ꥹ�ȤȤ���parse
		$mail_addr_obj_array = Mail_RFC822::parseAddressList($str);

		// �᡼�륢�ɥ쥹���Ǽ��������
		$mail_addr_array = array();
		foreach ($mail_addr_obj_array as $mail_addr_obj) {
			// �᡼�륢�ɥ쥹
			$mail_addr = $mail_addr_obj->mailbox . '@' . $mail_addr_obj->host;
			array_push($mail_addr_array, $mail_addr);
		}

		return $mail_addr_array;
	}


	/**
	 * 1�̤Υ᡼�����Ϥ���
	 *
	 * @param Net_POP3::getMsg($msg_id)�Ǽ����������֥������� (1�̤Υ᡼��ǡ���)
	 * @return Ϣ����������Ǿ�����ݻ�����1�̤Υ᡼��
	 */
	static function parse_mail($msg) {
		// �᡼����Ϸ�̤��Ǽ����Ϣ������
		$mail_row = array();

		$params = array();
		$params['include_bodies'] = true;
		$params['decode_bodies']  = false;
		$params['decode_headers'] = false;
		$params['input'] = $msg;

		// MIME�ǥ����ɤ��줿���֥������Ȥ����
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
		// ��̾(Subject)
		if (isset($mail_obj->headers['subject'])) {
			$mail_row['subject'] = mb_decode_mimeheader($mail_obj->headers['subject']);
		}
		// ����(Date)
		if (isset($mail_obj->headers['date'])) {
			// ������˱�ä����� (YYYY/MM/DD HH:MM:SS)
			$t = strtotime($mail_obj->headers['date']);
			$mail_row['date'] = strftime('%G/%m/%d %T', $t);
		}

		// ��ʸ(Body) (�������󥳡��ǥ��󥰤��Ѵ�)
		if (isset($mail_obj->parts)) {
			// MIME�ѡ��Ȥ�ͭ��᡼��
			foreach ($mail_obj->parts as $index => $parts) {
				if ($parts->ctype_primary == 'text' && !isset($parts->headers['content-disposition']) && !isset($mail_row['body'])) {
					// �ޥ���ѡ��������ʸ
					$mail_row['body'] = mb_convert_encoding($parts->body, mb_internal_encoding(), 'auto');
					break;
				}
			}
		} else {
			// MIME�ѡ��Ȥ�̵���᡼��
			$mail_row['body'] = mb_convert_encoding($mail_obj->body, mb_internal_encoding(), 'auto');
		}

		return $mail_row;
	}
}

?>
