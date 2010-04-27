<?php

require_once(ACS_CLASS_DIR . 'ACSUser.class.php');

/**
 * メッセージクラス
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
	 * 言語設定
	 *
	 * クッキーの設定は行いません
	 * 
	 * @param string $lang 設定するロケール(ja,en,...)
	 */
	function set_lang($lang) {
		global $_ACSMSG_LANG;
		if (array_key_exists($lang,ACSMsg::get_lang_list_array())) {
			$_ACSMSG_LANG = $lang;
		}
	}

	/**
	 * 言語クッキー保存
	 *
	 * 現在の言語設定をクッキーに保存します
	 * 
	 * @param string $lang 設定するロケール(ja,en,...)
	 */
	function set_lang_cookie($lang) {
		if (array_key_exists($lang,ACSMsg::get_lang_list_array())) {
			setcookie( ACSMSG_LANG_COOKIE_NAME, 
					$lang, time() + ACSMSG_LANG_COOKIE_EXPIRESEC, 
					ACSMSG_LANG_COOKIE_PATH);
		}
	}

	/**
	 * 言語の取得
	 *
	 * 言語の取得を以下の手順にて実行します。<br>
	 * 
	 * １．言語がすでに取得済みの場合、その値を返します。<br>
	 * ２．クッキーからの言語設定の取得を試み、成功するとその値を返します。<br>
	 * ３．認証済であり、かつ以前ＤＢアクセスの試みで失敗していない場合、<br>
	 *     ＤＢ上のメール言語設定から言語設定の取得を試みます。<br>
	 * ４．acs_define.php にて定義されたデフォルト言語設定を返します。<br>
	 * 
	 * @param string $is_db_access ＤＢアクセスを制御した場合に指定します(未設定時はTRUE)
	 * @return string ロケール(ja,en,...)
	 */
	function get_lang($is_db_access=TRUE) {
		global $_ACSMSG_LANG;
		static $is_db_disable;

		if ($_ACSMSG_LANG != "") {
			return $_ACSMSG_LANG;
		}

		// クッキーより取得
		$_ACSMSG_LANG = $_COOKIE[ACSMSG_LANG_COOKIE_NAME];
		if ($_ACSMSG_LANG != "") {
			ACSMsg::set_lang_cookie($_ACSMSG_LANG); // 有効期間延長
			return $_ACSMSG_LANG;
		}

		// DBより取得
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
	 * 指定ユーザのメール言語取得
	 * 
	 * @param string $user_id ユーザーID
	 * @return string メール言語ロケール(ja,en,...)
	 */
	function get_mail_lang($user_id) {
		$lang = ACSUser::get_user_mail_lang($user_id);
		return $lang == "" ? ACS_DEFAULT_LANG : $lang;
	}

	/**
	 * ユーザ情報からのメール言語取得
	 * 
	 * @param array $user_info_row ユーザ情報配列
	 * @return string メール言語ロケール(ja,en,...)
	 */
	function get_mail_lang_by_inforow(&$inforow) {
		$lang =& $inforow['contents_row_array']['mail_lang']['contents_value'];
		return $lang == "" ? ACS_DEFAULT_LANG : $lang;
	}

	/**
	 * メッセージ定義ファイルパス取得
	 * 
	 * @return string メッセージ定義ファイルのパス
	 */
	function get_messages_dir() {
		return ACS_LIB_MESSAGE_DIR . ACSMsg::get_lang();
	}

	/**
	 * 言語ＤＢテーブル一覧取得
	 * 
	 * 言語に関連するＤＢテーブルの一覧を返します。<br>
	 * 通常のテーブル名の末尾に'_(ロケール)'が付加されたテーブルが<br>
	 * 各言語用のテーブル名となります。<br>
	 * 
	 * 例）en の場合<br>
	 * community_type_master -> community_type_master_en <br>
	 * 
	 * @return array テーブル名一覧
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
	 * 言語一覧取得 
	 * 
	 * 指定可能な言語の一覧を返します。<br>
	 * 指定可能な言語は acs_define.ini の ACS_LANG_LIST で定義してあります。<br>
	 * 
	 * @return string メッセージ定義ファイルのパス
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
	 * 言語切替用ＵＲＬ生成
	 * 
	 * 言語切替用のＵＲＬを生成します。<br>
	 * 現在のＵＲＬに パラメータ acsmsg を追加する形で生成します。<br>
	 * ※POSTパラメータを再現することはできませんので注意してください。<br>
	 * 
	 * @param string $lang 切替先のロケール文字(ja,en,...)
	 * @param string $base_url ＵＲＬ(未指定の場合は現在のＵＲＬが適用される)
	 * @return string 生成したＵＲＬ
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
	 * タグ置換
	 * 
	 * 文中のタグ(プレースフォルダ)を置換します。<br>
	 * 
	 * @param string $fmt タグを埋め込んだ文字列
	 * @param array $tags_array タグ(キー項目)とその値の連想配列
	 * @return string 置換した文字列
	 */
	function get_tag_replace($fmt, $tags_array) {
		$replace = $fmt;
		foreach ($tags_array as $tag => $value) {

			// {}は間に改行が入ってしまうため
			$tag = str_replace("\n", "", $tag);
			$replace = str_replace($tag, $value, $replace);	

		}
		return $replace;
	}

	/**
	 * モジュール用メッセージ取得
	 * 
	 * 引数のファイルパスの２階層上のディレクトリ名をモジュール名とし、<br>
	 * ファイル名をセクション名としてメッセージを取得する。<br>
	 * <br>
	 * get_msg('.../NamedModule/actions/NamedAction.class.php','MSG999')<br>
	 *   -> 定義ファイル：NamedModule_messages.ini セクション：[NamedAction.class.php]<br>
	 * <br>
	 * ※モジュール名のみ直接指定することも可<br>
	 * get_msg('.../NamedModule/actions/NamedAction.class.php','MSG999','special')<br>
	 *   -> 定義ファイル：special_messages.ini セクション：[NamedAction.class.php]<br>
	 *
	 * @param string $file_path 
	 * @param string $msg_id
	 * @param string $module_name
	 * @return string メッセージ
	 */
	function get_mdmsg($file_path,$msg_id,$module_name="") {
		$module = ($module_name == "" ? basename(dirname(dirname($file_path))) : $module_name);
		$section = basename($file_path);
		return ACSMsg::get_msg($module,$section,$msg_id);
	}

	/**
	 * メッセージ取得
	 * 
	 * @param string $module  モジュール名。定義ファイルは 'モジュール名'_message.ini となる
	 * @param string $section セクション名。
	 * @param string $msg_id  メッセージID
	 * @param mixed $value_not_exists 定義が存在しない場合の戻り値
	 * @return mixed 取得したメッセージ
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
	 * 連番連結メッセージ取得
	 * 
	 * 連続した番号で構成されるメッセージIDのメッセージを取得。<br>
	 * 次の番号が存在しない場合か最大値に達するまで連番メッセージを取得する。<br>
	 * 最後に指定した文字列で連結して返す。<br>
	 * 
	 * @param string $module  モジュール名。定義ファイルは 'モジュール名'_message.ini となる
	 * @param string $section セクション名。
	 * @param string $msgid_format  メッセージID生成用printfフォーマット 例) "MSG%03d"
	 * @param int    $glue  連結する文字列(未指定の場合改行文字)
	 * @param int    $start 開始番号(未指定の場合1)
	 * @param int    $end   最大番号(未指定の場合999)
	 * @return string 取得したメッセージ
	 */
	function get_serial_msg($module,$section,$msgid_format,$glue="\n",$start=1,$end=999) {
		$arr =& ACSMsg::get_serial_msg_array($module,$section,$msgid_format,$start,$end);
		return implode($glue,$arr);
	}

	/**
	 * 連番メッセージ配列取得
	 * 
	 * 連続した番号で構成されるメッセージIDのメッセージを取得。<br>
	 * 次の番号が存在しない場合か最大値に達するまで連番メッセージを取得する。<br>
	 * 
	 * @param string $module  モジュール名。定義ファイルは 'モジュール名'_message.ini となる
	 * @param string $section セクション名。
	 * @param string $msgid_format  メッセージID生成用printfフォーマット 例) "MSG%03d"
	 * @param int    $start 開始番号(未指定の場合1)
	 * @param int    $end   最大番号(未指定の場合999)
	 * @return string 取得したメッセージ
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
	 * マスタ定義内容の取得
	 * 
	 * create_masterini.sh にて作成されたマスタデータiniファイルの内容を取得する。
	 * 
	 * @param string $table テーブル名
	 * @param string $id    ID
	 * @param string $module  モジュール名(デフォルトは master となる)
	 * @return string 取得した内容
	 */
	function get_mst($table,$id,$module="master") {
		return ACSMsg::get_msg($module,$table,$id);
	}
}
?>
