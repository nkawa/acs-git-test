<?php

require_once(ACS_CLASS_DIR . 'ACSUser.class.php');

/**
 * ��å��������饹
 *
 * @author  teramoto
 * @version $Revision: 1.4 $
 *
 */

define('ACSMSG_LANG_COOKIE_NAME', 'acsmsg_lang');
define('ACSMSG_LANG_COOKIE_EXPIRESEC', (60*60*24*30)); // 30days
define('ACSMSG_LANG_COOKIE_PATH', '/');

$_ACSMSG_LANG = "";
$_ACSMSG_LANG_LIST_STACK = "";
$_ACSMSG_INI_STACK = array();

class ACSMsg
{
	/**
	 * ��������
	 *
	 * ���å���������ϹԤ��ޤ���
	 * 
	 * @param string $lang ���ꤹ�������(ja,en,...)
	 */
	function set_lang($lang) {
		global $_ACSMSG_LANG;
		if (array_key_exists($lang,ACSMsg::get_lang_list_array())) {
			$_ACSMSG_LANG = $lang;
		}
	}

	/**
	 * ���쥯�å�����¸
	 *
	 * ���ߤθ�������򥯥å�������¸���ޤ�
	 * 
	 * @param string $lang ���ꤹ�������(ja,en,...)
	 */
	function set_lang_cookie($lang) {
		if (array_key_exists($lang,ACSMsg::get_lang_list_array())) {
			setcookie( ACSMSG_LANG_COOKIE_NAME, 
					$lang, time() + ACSMSG_LANG_COOKIE_EXPIRESEC, 
					ACSMSG_LANG_COOKIE_PATH);
		}
	}

	/**
	 * ����μ���
	 *
	 * ����μ�����ʲ��μ��ˤƼ¹Ԥ��ޤ���<br>
	 * 
	 * �������줬���Ǥ˼����Ѥߤξ�硢�����ͤ��֤��ޤ���<br>
	 * �������å�������θ�������μ������ߡ���������Ȥ����ͤ��֤��ޤ���<br>
	 * ����ǧ�ںѤǤ��ꡢ���İ����ģ¥��������λ�ߤǼ��Ԥ��Ƥ��ʤ���硢<br>
	 *     �ģ¾�Υ᡼��������꤫���������μ������ߤޤ���<br>
	 * ����acs_define.php �ˤ�������줿�ǥե���ȸ���������֤��ޤ���<br>
	 * 
	 * @param string $is_db_access �ģ¥������������椷�����˻��ꤷ�ޤ�(̤�������TRUE)
	 * @return string ������(ja,en,...)
	 */
	function get_lang($is_db_access=TRUE) {
		global $_ACSMSG_LANG;
		static $is_db_disable;

		if ($_ACSMSG_LANG != "") {
			return $_ACSMSG_LANG;
		}

		// ���å���������
		$_ACSMSG_LANG = $_COOKIE[ACSMSG_LANG_COOKIE_NAME];
		if ($_ACSMSG_LANG != "") {
			ACSMsg::set_lang_cookie($_ACSMSG_LANG); // ͭ�����ֱ�Ĺ
			return $_ACSMSG_LANG;
		}

		// DB������
		if (
			$is_db_access === TRUE && 
			$is_db_disable !== TRUE) {
//				if ($_SERVER['PHP_AUTH_USER'] != "" && 
//				$is_db_access === TRUE && 
//				$is_db_disable !== TRUE) {
//					$_ACSMSG_LANG = ACSUser::get_user_mail_lang($_SERVER['PHP_AUTH_USER']);

			if ($_ACSMSG_LANG != "") {
				ACSMsg::set_lang_cookie($_ACSMSG_LANG);
				return $_ACSMSG_LANG;
			} else {
				$is_db_disable = TRUE;
			}
		}

		$_ACSMSG_LANG = ACS_DEFAULT_LANG;
		return $_ACSMSG_LANG;
	}

	/**
	 * ����桼���Υ᡼��������
	 * 
	 * @param string $user_id �桼����ID
	 * @return string �᡼����������(ja,en,...)
	 */
	function get_mail_lang($user_id) {
		$lang = ACSUser::get_user_mail_lang($user_id);
		return $lang == "" ? ACS_DEFAULT_LANG : $lang;
	}

	/**
	 * �桼�����󤫤�Υ᡼��������
	 * 
	 * @param array $user_info_row �桼����������
	 * @return string �᡼����������(ja,en,...)
	 */
	function get_mail_lang_by_inforow(&$inforow) {
		$lang =& $inforow['contents_row_array']['mail_lang']['contents_value'];
		return $lang == "" ? ACS_DEFAULT_LANG : $lang;
	}

	/**
	 * ��å���������ե�����ѥ�����
	 * 
	 * @return string ��å���������ե�����Υѥ�
	 */
	function get_messages_dir() {
		return ACS_LIB_MESSAGE_DIR . ACSMsg::get_lang();
	}

	/**
	 * ����ģ¥ơ��֥��������
	 * 
	 * ����˴�Ϣ����ģ¥ơ��֥�ΰ������֤��ޤ���<br>
	 * �̾�Υơ��֥�̾��������'_(������)'���ղä��줿�ơ��֥뤬<br>
	 * �Ƹ����ѤΥơ��֥�̾�Ȥʤ�ޤ���<br>
	 * 
	 * ���en �ξ��<br>
	 * community_type_master -> community_type_master_en <br>
	 * 
	 * @return array �ơ��֥�̾����
	 */
	function get_lang_tables_array() {
		return array(
				'community_type_master',
				'community_member_type_master',
				'category_group_master',
				'category_master',
				'contents_type_master',
				'open_level_master',
				'waiting_type_master',
				'waiting_status_master',
				'file_category_master',
				'file_contents_type_master',
				'file_history_operation_master',
				'system_config_group'
		);
	}

	/**
	 * ����������� 
	 * 
	 * �����ǽ�ʸ���ΰ������֤��ޤ���<br>
	 * �����ǽ�ʸ���� acs_define.ini �� ACS_LANG_LIST ��������Ƥ���ޤ���<br>
	 * 
	 * @return string ��å���������ե�����Υѥ�
	 */
	function get_lang_list_array() {
		global $_ACSMSG_LANG_LIST_STACK;

		if (!defined('ACS_LANG_LIST')) {
			return array();
		}
		if (is_array($_ACSMSG_LANG_LIST_STACK)) {
			reset($_ACSMSG_LANG_LIST_STACK);
			return $_ACSMSG_LANG_LIST_STACK;
		}
		$langs_list = array();
		$langs_array = explode(",",ACS_LANG_LIST);
		foreach($langs_array as $langset) {
			$langset_array = explode(":",$langset);
			$langs_list[$langset_array[0]] = $langset_array[1];
		}
		$_ACSMSG_LANG_LIST_STACK = $langs_list;

		return $langs_list;
	}

	/**
	 * ���������ѣգң�����
	 * 
	 * ���������ѤΣգң̤��������ޤ���<br>
	 * ���ߤΣգң̤� �ѥ�᡼�� acsmsg ���ɲä�������������ޤ���<br>
	 * ��POST�ѥ�᡼����Ƹ����뤳�ȤϤǤ��ޤ���Τ���դ��Ƥ���������<br>
	 * 
	 * @param string $lang ������Υ�����ʸ��(ja,en,...)
	 * @param string $base_url �գң�(̤����ξ��ϸ��ߤΣգң̤�Ŭ�Ѥ����)
	 * @return string ���������գң�
	 */
	function get_lang_url($lang, $base_url="") {
		$base = $base_url == "" ? $_SERVER['REQUEST_URI'] : $base_url;

		list($url_host,$url_query) = explode("?",$base);

		if ($url_query == "") {
			$base .= "?acsmsg=".$lang;
		} else {
			if (ereg("([?&])(acsmsg=)",$base)) {
				$base = mb_ereg_replace(
					"([?&])(acsmsg=)([a-zA-Z]*)([&]|$)",
					"\\1\\2".$lang."\\4",$base);
			} else {
				$base .= "&acsmsg=".$lang;
			}
		}
		return $base;
	}

	/**
	 * �����ִ�
	 * 
	 * ʸ��Υ���(�ץ졼���ե����)���ִ����ޤ���<br>
	 * 
	 * @param string $fmt �������������ʸ����
	 * @param array $tags_array ����(��������)�Ȥ����ͤ�Ϣ������
	 * @return string �ִ�����ʸ����
	 */
	function get_tag_replace($fmt, $tags_array) {
		$replace = $fmt;
		foreach ($tags_array as $tag => $value) {

			// {}�ϴ֤˲��Ԥ����äƤ��ޤ�����
			$tag = str_replace("\n", "", $tag);
			$replace = str_replace($tag, $value, $replace);	

		}
		return $replace;
	}

	/**
	 * �⥸�塼���ѥ�å���������
	 * 
	 * �����Υե�����ѥ��Σ����ؾ�Υǥ��쥯�ȥ�̾��⥸�塼��̾�Ȥ���<br>
	 * �ե�����̾�򥻥������̾�Ȥ��ƥ�å�������������롣<br>
	 * <br>
	 * get_msg('.../NamedModule/actions/NamedAction.class.php','MSG999')<br>
	 *   -> ����ե����롧NamedModule_messages.ini ���������[NamedAction.class.php]<br>
	 * <br>
	 * ���⥸�塼��̾�Τ�ľ�ܻ��ꤹ�뤳�Ȥ��<br>
	 * get_msg('.../NamedModule/actions/NamedAction.class.php','MSG999','special')<br>
	 *   -> ����ե����롧special_messages.ini ���������[NamedAction.class.php]<br>
	 *
	 * @param string $file_path 
	 * @param string $msg_id
	 * @param string $module_name
	 * @return string ��å�����
	 */
	function get_mdmsg($file_path,$msg_id,$module_name="") {
		$module = ($module_name == "" ? basename(dirname(dirname($file_path))) : $module_name);
		$section = basename($file_path);
		return ACSMsg::get_msg($module,$section,$msg_id);
	}

	/**
	 * ��å���������
	 * 
	 * @param string $module  �⥸�塼��̾������ե������ '�⥸�塼��̾'_message.ini �Ȥʤ�
	 * @param string $section ���������̾��
	 * @param string $msg_id  ��å�����ID
	 * @param mixed $value_not_exists �����¸�ߤ��ʤ����������
	 * @return mixed ����������å�����
	 */
	function get_msg($module,$section,$msg_id,$value_not_exists='') {

		global $_ACSMSG_INI_STACK;

		$ini_stack =& $_ACSMSG_INI_STACK;
		
		$module_key = ACSMsg::get_lang().".".$module;

		//if (!array_key_exists($module_key,$ini_stack)) {
		if (!is_array($ini_stack) || !array_key_exists($module_key,$ini_stack)) {
			$ini_stack[$module_key] = parse_ini_file(ACSMsg::get_messages_dir() .'/'. $module.'_messages.ini',TRUE);
		}
		
		if (is_array($ini_stack[$module_key][$section])) {
			if (array_key_exists($msg_id,$ini_stack[$module_key][$section])) {
				return $ini_stack[$module_key][$section][$msg_id];
			} else {
				return $value_not_exists;
			}
		} else {
			return "$section";
		}
	}

	/**
	 * Ϣ��Ϣ���å���������
	 * 
	 * Ϣ³�����ֹ�ǹ���������å�����ID�Υ�å������������<br>
	 * �����ֹ椬¸�ߤ��ʤ���礫�����ͤ�ã����ޤ�Ϣ�֥�å�������������롣<br>
	 * �Ǹ�˻��ꤷ��ʸ�����Ϣ�뤷���֤���<br>
	 * 
	 * @param string $module  �⥸�塼��̾������ե������ '�⥸�塼��̾'_message.ini �Ȥʤ�
	 * @param string $section ���������̾��
	 * @param string $msgid_format  ��å�����ID������printf�ե����ޥå� ��) "MSG%03d"
	 * @param int    $glue  Ϣ�뤹��ʸ����(̤����ξ�����ʸ��)
	 * @param int    $start �����ֹ�(̤����ξ��1)
	 * @param int    $end   �����ֹ�(̤����ξ��999)
	 * @return string ����������å�����
	 */
	function get_serial_msg($module,$section,$msgid_format,$glue="\n",$start=1,$end=999) {
		$arr =& ACSMsg::get_serial_msg_array($module,$section,$msgid_format,$start,$end);
		return implode($glue,$arr);
	}

	/**
	 * Ϣ�֥�å������������
	 * 
	 * Ϣ³�����ֹ�ǹ���������å�����ID�Υ�å������������<br>
	 * �����ֹ椬¸�ߤ��ʤ���礫�����ͤ�ã����ޤ�Ϣ�֥�å�������������롣<br>
	 * 
	 * @param string $module  �⥸�塼��̾������ե������ '�⥸�塼��̾'_message.ini �Ȥʤ�
	 * @param string $section ���������̾��
	 * @param string $msgid_format  ��å�����ID������printf�ե����ޥå� ��) "MSG%03d"
	 * @param int    $start �����ֹ�(̤����ξ��1)
	 * @param int    $end   �����ֹ�(̤����ξ��999)
	 * @return string ����������å�����
	 */
	function get_serial_msg_array($module,$section,$msgid_format,$start=1,$end=999) {
		$arr = array();
		for ($i = $start; $i <= $end; $i++) {
			$val = ACSMsg::get_msg($module,$section,sprintf($msgid_format,$i),false);
			if ($val === false) {
				break;
			}
			$arr[$i] = $val;
		}
		return $arr;
	}

	/**
	 * �ޥ���������Ƥμ���
	 * 
	 * create_masterini.sh �ˤƺ������줿�ޥ����ǡ���ini�ե���������Ƥ�������롣
	 * 
	 * @param string $table �ơ��֥�̾
	 * @param string $id    ID
	 * @param string $module  �⥸�塼��̾(�ǥե���Ȥ� master �Ȥʤ�)
	 * @return string ������������
	 */
	function get_mst($table,$id,$module="master") {
		return ACSMsg::get_msg($module,$table,$id);
	}
}
?>
