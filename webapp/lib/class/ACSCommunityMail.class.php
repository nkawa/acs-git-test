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
	 * ���󥹥ȥ饯��
	 *
	 * @param $community_id
	 * @param $acs_user_info_row ���������Ծ���
	 * @param $folder_id
	 */
	function ACSCommunityMail ($community_id, $acs_user_info_row, $folder_id) {
	}

	/****************************
	 * �����ƥ��å��ե��󥯥����
	 ****************************/

	/**
	 * ���ߥ�˥ƥ����ФؤΥ᡼������(����ۿ�)
	 *
	 * define('ACS_COMMUNITY_ML_SERVER') �ΣӣͣԣХ����Ф��������ޤ���
	 * �����Ԥϥ��ߥ�˥ƥ������ꤵ�줿�᡼�륢�ɥ쥹�Ȥʤ�ޤ���
	 *
	 * ��å��������Ф��ưʲ��Υץ졼���ե�������ִ����ޤ���
	 * {USER_COMMUNITY_NAME}
	 * {SYSTEM_BASE_URL}
	 * {SYSTEM_SCRIPT_PATH}
	 * {SYSTEM_MODULE_ACCESSOR}
	 * {SYSTEM_ACTION_ACCESSOR}
	 * {COMMUNITY_ID}
	 * {COMMUNITY_NAME}
	 *
	 * @param string $community_id ���ߥ�˥ƥ�id
	 * @param string $from_address �����ԥ��ɥ쥹
	 *                          (From:�Τ�Reply-to:�ϥ��ߥ�˥ƥ�ML���ɥ쥹����ư��Ϳ�����)
	 * @param string $subject ��̾
	 * @param string $msg ��å�������ʸ
	 * @param string $mmb_id_array �оݥ桼�����ߥ�˥ƥ�id����(̤�����������)
	 * @return mixed true...����/PearError ���֥�������...�۾�
	 */
	static function send_community_mailing_list (
			$community_id,$from_address,$subject,$msg,$mmb_id_array='') {

		// ���ߥ�˥ƥ�ML���ɥ쥹�μ���
		$ml_address_row = ACSCommunity::get_contents_row(
				$community_id, ACSMsg::get_mst('contents_type_master','D61'));

		// ���ߥ�˥ƥ�ML���ơ������μ���
		$ml_status_row = ACSCommunity::get_contents_row(
				$community_id, ACSMsg::get_mst('contents_type_master','D62'));

		$ml_address = $ml_address_row['contents_value'];
		$ml_status = $ml_status_row['contents_value'];

		if ($ml_address == '' || $ml_status != 'ACTIVE') {
			return false;
		}

		// BBS�����ƥ�᡼�륢�ɥ쥹�μ���
		$bbs_system_mailaddr = ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D03'),
					'COMMUNITY_ML_MAIL_ADDR');

		// ���ߥ�˥ƥ����������ξ�������
		$mmb_info_rows =& ACSCommunity::get_community_member_mail_address_row_array(
				$community_id);

		// $mmb_id_array �Υ���ǥå�����
		$mmb_array = array();
		if (is_array($mmb_id_array)) {
			foreach ($mmb_id_array as $mmb_id) {
				$mmb_array[$mmb_id] = TRUE;
			}
		}

		// Bcc������
		$bcc = '';
		foreach ($mmb_info_rows as $mmb_info) {
			if ($mmb_id_array == '' || $mmb_array[$mmb_info['user_community_id']]) {
				$bcc .= $bcc == '' ? '' : ',';
				$bcc .= $mmb_info['mail_address'];
			}
		}

		// �����ƥ��Ϣ�ץ졼���ե�������ִ�
		$msg = ACSCommunityMail::get_community_tag_replace($community_id, $msg);

		// Bcc�᡼������
		return ACSLib::send_mail(
				$from_address, 
				$bcc,             // Repicients�ˤ�bcc�����桼���Τ߻���
				'', 
				$subject, 
				$msg, 
				array(
					'Sender'      => $bbs_system_mailaddr,
					'Bcc'         => $bcc,
					'Return-Path' => $ml_address,
					'Reply-to'    => $ml_address,
					// ����������To:�ˤ�ML���ɥ쥹������
					'To'          => $ml_address)); 
	}

	/**
	 * ���ߥ�˥ƥ����ФؤΥ᡼������(�����ۿ�)
	 *
	 * ���ƥ桼���θ�������˽��äƥ������ץ졼�����ޤ���
	 * �������ƥ�����ΣӣͣԣФ��������ޤ���
	 * ��å��������Ф��ưʲ��Υץ졼���ե�������ִ����ޤ���
	 * {USER_COMMUNITY_NAME}
	 * {SYSTEM_BASE_URL}
	 * {SYSTEM_SCRIPT_PATH}
	 * {SYSTEM_MODULE_ACCESSOR}
	 * {SYSTEM_ACTION_ACCESSOR}
	 * {COMMUNITY_ID}
	 * {COMMUNITY_NAME}
	 *
	 * @param string $community_id ���ߥ�˥ƥ�id
	 * @param string $from ������
	 * @param mixed $subject ����ξ�硧��̾(array([lang] => [��̾]))
	 *                   ʸ����ξ�硢���Ƥθ����Ʊ��η�̾
	 * @param mixed $msg ����ξ�硧��å�������ʸ(array([lang] => [��ʸ]))
	 *                   ʸ����ξ�硢���Ƥθ����Ʊ��Υ�å�������ʸ
	 * @param string $mmb_id_array �оݥ桼�����ߥ�˥ƥ�id����(̤�����������)
	 * @return mixed true...����/PearError ���֥�������...�۾�
	 */
	static function send_community_mail ($community_id,$from,$subject,$msg,$mmb_id_array='') {

		// ���ߥ�˥ƥ����������ξ�������
		$mmb_info_rows =& ACSCommunity::get_community_member_mail_address_row_array(
				$community_id);

		// ����ǥå����˥桼�����ߥ�˥ƥ�id����Ѥ������������
		$mmb_info_array = array();
		$is_all = $mmb_id_array == '' ? TRUE : FALSE;
		foreach ($mmb_info_rows as $mmb_info) {
			$mmb_info_array[$mmb_info['user_community_id']] = $mmb_info;
			if ($is_all) {
				$mmb_id_array[] = $mmb_info['user_community_id'];
			}
		}

		// ��������̤˥����ƥ��Ϣ�ץ졼���ե�������ִ�
		$msg_lang = array();
		$lang_list =& ACSMsg::get_lang_list_array();
		foreach ($lang_list as $lang => $lang_disp) {
			$msg_lang[$lang] = ACSCommunityMail::get_community_tag_replace(
					$community_id, (is_array($msg) ? $msg[$lang] : $msg));
		}

		// �оݥ桼���˥᡼�������
		if (is_array($mmb_id_array)) {

			foreach ($mmb_id_array as $mmb_id) {

				$mmb_info =& $mmb_info_array[$mmb_id];

				$lang = $mmb_info['mail_lang'] == '' ? 
						ACS_DEFAULT_LANG : $mmb_info['mail_lang'];

				$mmb_msg = $msg_lang[$lang];

				$mmb_subject = is_array($subject) ? $subject[$lang] : $subject;

				// ̾���ץ졼���ե�������ִ�
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
	 * ���ߥ�˥ƥ����󥿥��ִ�
	 *
	 * ��å��������Ф��ưʲ��Υץ졼���ե�������ִ����ޤ���
	 * {SYSTEM_BASE_URL}
	 * {SYSTEM_SCRIPT_PATH}
	 * {SYSTEM_MODULE_ACCESSOR}
	 * {SYSTEM_ACTION_ACCESSOR}
	 * {COMMUNITY_ID}
	 * {COMMUNITY_NAME}
	 *
	 * @param string $community_id ���ߥ�˥ƥ�id
	 * @param string $msg ��å�������ʸ
	 * @return true / false
	 */
	static function get_community_tag_replace ($community_id,$msg) {

		$community_info = array();

		// �����ƥ�URL
		$system_group = ACSMsg::get_mst('system_config_group','D01');

		$community_info['system_base_url'] = ACSSystemConfig::get_keyword_value(
				$system_group, 'SYSTEM_BASE_URL');
		$community_info['system_base_login_url'] = ACSSystemConfig::get_keyword_value(
				$system_group, 'SYSTEM_BASE_LOGIN_URL');

		// ���ߥ�˥ƥ�����μ���
		$community_row =& ACSCommunity::get_community_row($community_id);
		$community_info['community_name'] = $community_row['community_name'];

		// ���ߥ�˥ƥ���URL
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
	 * ML�ؤΥե����륢�åץ������Υ᡼������
	 *
	 * �ƥ桼���θ�����б����ƥ᡼�����������
	 *
	 * @return true / false
	 */
	static function send_fileupload_mail($community_id, &$user_info, &$folder, &$file) {

		// �ե�����ܺٲ���URL
		$file_detail_url  = "{SYSTEM_SCRIPT_PATH}" .
			"?{SYSTEM_MODULE_ACCESSOR}=Community" .
			"&{SYSTEM_ACTION_ACCESSOR}=FileDetail" .
			"&community_id={$community_id}" .
			"&folder_id={$folder->folder_id}" .
			"&file_id={$file->file_id}";
	
		// ���ߥ�˥ƥ�����μ���
		$community_row =& ACSCommunity::get_community_row($community_id);

		// �Ƹ����ѤΥ�å����������
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

		// �����ƥ�Υ᡼�륢�ɥ쥹�����
		$system_mail_addr = ACSSystemConfig::get_keyword_value(
		ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');

		// ��̾
		$subject = ACSMsg::get_mdmsg(__FILE__,'M001');

		// �᡼������
		return ACSCommunityMail::send_community_mail(
				$community_id, $system_mail_addr, $subject, $msgs);
	}

	/**
	 * �ؤΥ��ߥ�˥ƥ��ե�����ץå����Υ᡼��
	 *
	 * �ƥ桼���θ�����б����ƥ᡼�����������
	 *
	 * @return true / false
	 */
	static function send_putfolder_mail(&$user_info, &$folder, $community_id) {

		// �ե�������ե������������URL
		$folder_list_url  = "{SYSTEM_SCRIPT_PATH}" .
			"?{SYSTEM_MODULE_ACCESSOR}=Community" .
			"&{SYSTEM_ACTION_ACCESSOR}=Folder" .
			"&community_id=" . $community_id .
			"&folder_id=" . $folder['folder_id'];

		// ���ߥ�˥ƥ�����μ���
		$community_row =& ACSCommunity::get_community_row($community_id);

		// �Ƹ����ѤΥ�å����������
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

			// subject���Խ�
			$subjects[$lang] = ACSMsg::get_mdmsg(__FILE__, 'M005');
		}
		ACSMsg::set_lang($org_lang);

		// �����ƥ�Υ᡼�륢�ɥ쥹�����
		$system_mail_addr = ACSSystemConfig::get_keyword_value(
		ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');

		// ��̾
		//$subject = ACSMsg::get_mdmsg(__FILE__,'M005');

		// �᡼������
		return ACSCommunityMail::send_community_mail(
				$community_id, $system_mail_addr, $subjects, $msgs);
	}

	/**
	 * �������塼�����Υ᡼�������
	 *
	 * �ƥ桼���θ�����б����ƥ������塼���Ϣ��<br>
	 * ���Υ᡼�����������
	 *
	 * @param object $schedule �������塼�륤�󥹥���(ACSSchedule)
	 * @param string $serial_msg_key_fmg ���ꥢ���å����������ѥե����ޥå�(��:"RMD%03d")
	 * @param string $subject_msg_key ��̾��å����������ѥ���(��:"M001")
	 * @param array  $additional_tags �ɲä����ִ��ץ졼���ե����(̤������)
	 * @param array  $additional_message_tags �ɲä����ִ��ץ졼���ե����(��å�����id����)
	 *               (̤������) �᡼�������б������ִ�����ޤ�
	 * @return boolean true/false
	 */
	static function send_schedule_announce_mail (
			&$schedule, 
			$serial_msg_key_fmg, 
			$subject_msg_key, 
			$additional_tags = "",
			$additional_message_tags = "") {

		// �������塼��գң�
		$schedule_url  = "{SYSTEM_SCRIPT_PATH}" .
				"?{SYSTEM_MODULE_ACCESSOR}=Community" .
				"&{SYSTEM_ACTION_ACCESSOR}=AnswerSchedule" .
				"&community_id={$schedule->community_id}" .
				"&schedule_id={$schedule->schedule_id}";

		$user_community_row =& ACSUser::get_user_profile_row($schedule->user_community_id);

		// �����ƥ�Υ᡼�륢�ɥ쥹�����
		$system_mail_addr = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');

		// �Ƹ����ѤΥ�å����������
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

			// subject���Խ�
			$subjects[$lang] = ACSMsg::get_mdmsg(__FILE__, $subject_msg_key);

		}
		ACSMsg::set_lang($org_lang);

		// �᡼�������(�����ߥ�˥ƥ�����)
		return ACSCommunityMail::send_community_mail(
				$schedule->community_id, $system_mail_addr, $subjects, $msgs);
	}

}
?>
