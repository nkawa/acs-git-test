<?php
/**
 * ��å����������̥��饹
 * 
 * ACSMessage.class.php
 * @package  acs/webapp/lib/class
 * @author   acs
 */
// $Id: ACSMessage.class.php,v 1.1 2009/06/19 09:50:00 acs Exp $

/*
 * ��å��������饹
 */
class ACSMessage {

	/**
	 * �����å������ΰ������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @return �����å������ΰ��� (Ϣ�����������)
	 */
	static function get_new_message_row_array($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT message.message_id";
		$sql .= " FROM message, message_receiver";
		$sql .= " WHERE message_receiver.community_id = '$user_community_id'";
		$sql .= "  AND message_receiver.message_id = message.message_id";
		$sql .= "  AND message_receiver.message_delete_flag = 'f'";
		$sql .= "  AND message_receiver.read_flag = 'f'";
		$sql .= " ORDER BY message.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ������å������ΰ������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @return ������å������ΰ��� (Ϣ�����������)
	 */
	static function get_receive_message_row_array($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT message.message_id";
		$sql .= ", message.subject";
		$sql .= ", message.post_date";
		$sql .= ", message_receiver.message_receiver_id";
		$sql .= ", message_receiver.read_flag";
		$sql .= ", community.community_id AS user_id";
		$sql .= ", community.community_name AS user_name";
		$sql .= " FROM message, message_sender, message_receiver, community";
		$sql .= " WHERE message_receiver.community_id = '$user_community_id'";
		$sql .= "  AND message_sender.community_id = community.community_id";
		$sql .= "  AND message_receiver.message_id = message.message_id";
		$sql .= "  AND message.message_id = message_sender.message_id";
		$sql .= "  AND message_receiver.message_delete_flag = 'f'";
		$sql .= " ORDER BY message.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * �����ѥ�å������ΰ������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @return �����ѥ�å������ΰ��� (Ϣ�����������)
	 */
	static function get_send_message_row_array($user_community_id) {
		$user_community_id = pg_escape_string($user_community_id);

		$sql  = "SELECT message.message_id";
		$sql .= ", message.subject";
		$sql .= ", message.post_date";
		$sql .= ", message_sender.message_sender_id";
		$sql .= ", message_receiver.read_flag";
		$sql .= ", community.community_id AS user_id";
		$sql .= ", community.community_name AS user_name";
		$sql .= " FROM message, message_sender, message_receiver, community";
		$sql .= " WHERE message_sender.community_id = '$user_community_id'";
		$sql .= "  AND message_receiver.community_id = community.community_id";
		$sql .= "  AND message_sender.message_id = message.message_id";
		$sql .= "  AND message.message_id = message_receiver.message_id";
		$sql .= "  AND message_sender.message_delete_flag = 'f'";
		$sql .= " ORDER BY message.post_date DESC";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * ������å������ξܺ٤��������
	 *
	 * @param $message_id ��å�����ID
	 * @return ������å������ξܺ� (Ϣ�����������)
	 */
	static function get_receive_message_row($message_id) {
		$message_id = pg_escape_string($message_id);

		$sql  = "SELECT message.*";
		$sql .= ", message_receiver.message_receiver_id";
		$sql .= ", message_receiver.read_flag";
		$sql .= ", community.community_id AS user_id";
		$sql .= ", community.community_name AS user_name";
		$sql .= " FROM message, message_sender, message_receiver, community";
		$sql .= " WHERE message.message_id = '$message_id'";
		$sql .= "  AND message_sender.community_id = community.community_id";
		$sql .= "  AND message_receiver.message_id = message.message_id";
		$sql .= "  AND message.message_id = message_sender.message_id";
		$sql .= "  AND message_receiver.message_delete_flag = 'f'";
		$sql .= " ORDER BY message.post_date DESC";

		$row_array = ACSDB::_get_row($sql);
		return $row_array;
	}

	/**
	 * �����ѥ�å������ξܺ٤��������
	 *
	 * @param $message_id ��å�����ID
	 * @return �����ѥ�å������ξܺ� (Ϣ�����������)
	 */
	static function get_send_message_row($message_id) {
		$message_id = pg_escape_string($message_id);

		$sql  = "SELECT message.*";
		$sql .= ", community.community_id AS user_id";
		$sql .= ", community.community_name AS user_name";
		$sql .= " FROM message, message_sender, message_receiver, community";
		$sql .= " WHERE message.message_id = '$message_id'";
		$sql .= "  AND message_receiver.community_id = community.community_id";
		$sql .= "  AND message_receiver.message_id = message.message_id";
		$sql .= "  AND message.message_id = message_sender.message_id";
		$sql .= "  AND message_sender.message_delete_flag = 'f'";
		$sql .= " ORDER BY message.post_date DESC";

		$row_array = ACSDB::_get_row($sql);
		return $row_array;
	}

	/**
	 * ��å���������ɤˤ���
	 *
	 * @param $message_receiver_id ������å�����ID
	 * @return ����(true) / ����(false)
	 */
	static function read_message($message_receiver_id) {
	
		// BEGIN
		
		//��å�������̤�ɡ����ɥե饰�ѹ�	
		$sql = "UPDATE message_receiver";
		$sql .= " SET read_flag = 't'";
		$sql .= " WHERE message_receiver.message_receiver_id = $message_receiver_id";
		$ret = ACSDB::_do_query($sql);
		if(!$ret){
			echo "ERROR: Delete parent article failed.";
			return false;
		}
	
		// COMMIT
		return true;
	}

	/**
	 * �ֿ����ΰ��ѥ�å��������������
	 *
	 * @param $message_id ��å�����ID
	 * @return ���ѥ�å������ξܺ� (Ϣ�����������)
	 */
	static function get_message_row($message_id) {
		$message_id = pg_escape_string($message_id);

		$sql  = "SELECT message.subject";
		$sql .= ", message.body";
		$sql .= " FROM message";
		$sql .= " WHERE message.message_id = '$message_id'";

		$row_array = ACSDB::_get_row($sql);
		return $row_array;
	}

	/**
	 * ��å���������Ͽ����
	 *
	 * @param $form ��å��������������
	 * @return ����(��Ͽ���줿��å�����ID) / ����(false)
	 */
	static function set_message($form) {
		$org_form = $form;

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		//ACSDB::_do_query("BEGIN");

		$message_id_seq = ACSDB::get_next_seq('message_id_seq');
		$message_sender_id_seq = ACSDB::get_next_seq('message_sender_id_seq');
		$message_receiver_id_seq = ACSDB::get_next_seq('message_receiver_id_seq');

		// messege
		$sql1  = "INSERT INTO message";
		$sql1 .= " (message_id, subject, body)";
		$sql1 .= " VALUES ($message_id_seq, $form[subject], $form[body])";

		$ret = ACSDB::_do_query($sql1);
		if (!$ret) {
			//ACSDB::_do_query("ROLLBACK");
			echo "ERROR: insert message error";
			return $ret;
		}

		$form = $org_form;
		
		// messege_sender
		$sql2  = "INSERT INTO message_sender";
		$sql2 .= " (message_sender_id, message_id, community_id, message_delete_flag)";
		$sql2 .= " VALUES ($message_sender_id_seq, $message_id_seq, $form[acs_user_info_id], 'f')";

		$ret = ACSDB::_do_query($sql2);
		if (!$ret) {
			//ACSDB::_do_query("ROLLBACK");
			echo "ERROR: insert message_sender error";
			return $ret;
		}
		
		$form = $org_form;
		
		// messege_receiver
		$sql3  = "INSERT INTO message_receiver";
		$sql3 .= " (message_receiver_id, message_id, community_id, read_flag, message_delete_flag)";
		$sql3 .= " VALUES ($message_receiver_id_seq, $message_id_seq, $form[user_community_id], 'f', 'f')";

		$ret = ACSDB::_do_query($sql3);
		if (!$ret) {
			//ACSDB::_do_query("ROLLBACK");
			echo "ERROR: insert message_receiver error";
			return $ret;
		}

		if ($ret) {
			return $message_id_seq;
		} else {
			return false;
		}
	}

	/**
	 * ������å�������������
	 *
	 * @param $message_id ��å�����ID
	 * @return ����(true) / ����(false)
	 */
	static function delete_receive_message($message_id) {
	
		// BEGIN
	
		//��å������κ��(����ե饰����)	
		$sql = "UPDATE message_receiver";
		$sql .= " SET message_delete_flag = 't'";
		$sql .= " WHERE message_receiver.message_id = $message_id";
		$ret = ACSDB::_do_query($sql);
		if(!$ret){
			echo "ERROR: Delete receive message failed.";
			return false;
		}
	
		// COMMIT
		return true;
	}
	
	/**
	 * �����ѥ�å�������������
	 *
	 * @param $message_id ��å�����ID
	 * @return ����(true) / ����(false)
	 */
	static function delete_send_message($message_id) {
	
		// BEGIN
	
		//��å������κ��(����ե饰����)	
		$sql = "UPDATE message_sender";
		$sql .= " SET message_delete_flag = 't'";
		$sql .= " WHERE message_sender.message_id = $message_id";
		$ret = ACSDB::_do_query($sql);
		if(!$ret){
			echo "ERROR: Delete send messege failed.";
			return false;
		}
	
		// COMMIT
		return true;
	}
	
	/**
	 * ���Υ᡼�����������
	 *
	 * @param $message_id ��å�����ID
	 * @param $receiver_id ������ID
	 * @param $sender_id ������ID
	 */
	static function send_info_mail($message_id, $receiver_id, $sender_id) {
		$system_group = ACSMsg::get_mst('system_config_group','D01');


		// �����ƥ�URL
		$system_base_url = ACSSystemConfig::get_keyword_value($system_group, 'SYSTEM_BASE_URL');
		// �����ƥ������URL
		$system_base_login_url = ACSSystemConfig::get_keyword_value($system_group, 'SYSTEM_BASE_LOGIN_URL');
		// �����ƥ�Υ᡼�륢�ɥ쥹 (From:)
		$system_mail_addr = ACSSystemConfig::get_keyword_value($system_group, 'SYSTEM_MAIL_ADDR');

		// ���ѼԤθ����������Ū����¸
		$org_lang = ACSMsg::get_lang();

		// �Ƹ���Υ����ȥ�����
		$mail_titles = array();
		foreach (ACSMsg::get_lang_list_array() as $lang_key => $lang_name) {
			ACSMsg::set_lang($lang_key);
			$mail_titles[$lang_key] = 
					ACSMsg::get_serial_msg('lib','ACSWaiting.class.php','MTL%03d')."\n";
		}

		// ������ö�����᤹
		ACSMsg::set_lang($org_lang);

		// ����¦�Υ桼������
		$user_info_row = ACSUser::get_user_profile_row($sender_id);
		// ���ꤵ���¦�Υ桼������
		$target_user_info_row = ACSUser::get_user_profile_row($receiver_id);

		// �Ե���ǧURL
		$message_url  = $system_base_login_url . SCRIPT_PATH;
		$message_url .= "?" . MODULE_ACCESSOR . "=User";
		$message_url .= "&" . ACTION_ACCESSOR . "=MessageShow";
		$message_url .= "&id={$receiver_id}";
		$message_url .= "&message_id={$message_id}";


		$target_lang = ACSMsg::get_mail_lang_by_inforow($target_user_info_row);

		// ��ö���ꤵ���¦�θ�������ꤹ��
		ACSMsg::set_lang($target_lang);

		$body = $mail_titles[$target_lang];
		$body .= ACSMsg::get_tag_replace( 
				ACSMsg::get_serial_msg('lib','ACSMessage.class.php','ADF%03d'),
				array(
					"{TARGET_USER_NAME}"	=> $target_user_info_row['user_name'],
					"{USER_NAME}"			=> $user_info_row['user_name'],
					"{USER_COMMUNITY_NAME}"	=> $user_info_row['community_name'],
					"{MESSAGE_URL}"			=> $message_url,
					"{SYSTEM_BASE_URL}"		=> $system_base_url
				)
		);
		$subject = ACSMsg::get_mdmsg(__FILE__,'M002');

		// ����򸵤��᤹
		ACSMsg::set_lang($org_lang);

		$ret = ACSLib::send_mail($system_mail_addr, 
				$target_user_info_row['mail_addr'], null, $subject, $body);
	}

	/**
	 * ������å������λ����礬�ɤ���Ƚ�Ǥ���
	 *
	 * @param $message_id ��å�����ID
	 * @param $community_id �桼�������ߥ�˥ƥ�ID
	 * @return true or false
	 */
	static function check_message_receiver($message_id, $community_id) {
		$message_id = pg_escape_string($message_id);
		$community_id = pg_escape_string($community_id);

		$sql  = "SELECT count(*)";
		$sql .= " FROM message_receiver ";
		$sql .= " WHERE message_id = '$message_id'";
		$sql .= "  AND community_id = '$community_id'";
		$sql .= "  AND message_delete_flag = 'f'";
	
		$value = ACSDB::_get_value($sql);
		if ($value) {
			return true;
		} else {
			return false;
		}
	}
	
}
?>
