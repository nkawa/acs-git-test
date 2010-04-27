<?php

// データソース名
define('ACS_DSN', 'pgsql://postgres:postgres@localhost:5432+unix/acsdb');

// logging の設定
define('ACS_LOG_PRIORITY', LEVEL_WARN);
define('ACS_LOG_EXIT_PRIORITY', LEVEL_FATAL);

// システム管理者のユーザID
define('ACS_ADMINISTRATOR_USER_ID', 'admin');

// 一般アクセスのユーザコミュニティID
define('ACS_PUBLIC_USER_COMMUNITY_ID', '0');

// 一般アクセスのユーザ名
define('ACS_PUBLIC_USER_NAME', 'ゲスト');

// 言語の設定
// メール言語のコンテンツタイプコード
define('ACS_MAIL_LANG_CONTENTS_TYPE_CODE', '51');

// デフォルト言語
define('ACS_DEFAULT_LANG', 'ja');

// 切替言語
define('ACS_LANG_LIST', 'ja:Japanese,en:English');

// 外部システム
// LDAPの仕様(0:不使用,1:使用)
define('USE_LDAP_SYSTEM', '0');

/*----- ディレクトリ -----*/

// スクリプトパス
define('SCRIPT_PATH', 'index.php');

// 基本ディレクトリ
define('BASE_DIR', dirname(__FILE__) . '/');

// 共通テンプレートファイル
define('MO_TEMPLATE_FILE', 'acs_base.tpl.php');

// 小画面共通テンプレートファイル
define('MO_SIMPLE_TEMPLATE_FILE', 'acs_simple_base.tpl.php');

// パスワードファイル
define('ACS_PASSWD_FILE', '../../etc/.htpasswd');

// フォルダのファイルを置くディレクトリ (相対パス) (スラッシュで閉じる)
define('ACS_FOLDER_DIR', '../../files/');

// 削除したコミュニティのフォルダを置くディレクトリ
define('ACS_TRASH_FOLDER_DIR', ACS_FOLDER_DIR . 'trash/');

// 仮置きしたコミュニティの掲示板画像ファイルを置くディレクトリ
define('ACS_TEMPORARY_FILE_DIR', ACS_FOLDER_DIR . 'temporary/');

// ACS各クラスファイルのディレクトリ (相対パス) (スラッシュで閉じる)
define('ACS_CLASS_DIR', '../../webapp/lib/class/');

// ACS Validators のディレクトリ (相対パス) (スラッシュで閉じる)
define('ACS_VALIDATORS_DIR', '../../webapp/lib/mojavi/validators/');

// ACS各クラスファイルのディレクトリ (相対パス) (スラッシュで閉じる)
define('ACS_LIB_TEMPLATE_DIR', '../../webapp/lib/template/');

// ACS共通テンプレートファイルのディレクトリ (相対パス) (スラッシュで閉じる)
define('ACS_TEMPLATE_DIR', '../../webapp/templates/');

// ACSメッセージ定義ファイルのディレクトリ (相対パス) (スラッシュで閉じる)
define('ACS_LIB_MESSAGE_DIR', '../../webapp/lib/message/');

// ACSmojaviフィルタークラスファイル(相対パス) (スラッシュで閉じる)
define('ACS_LIB_FILTERS_DIR', '../../webapp/lib/filters/');

// ACS静的HTMLファイルデータのディレクトリ (相対パス) (スラッシュで閉じる)
define('ACS_PAGES_DIR', '../../webapp/pages/');

// 画像ファイルを置くディレクトリ
define('ACS_IMAGE_DIR', './img/');

// CSSファイルを置くディレクトリ
define('ACS_CSS_DIR', './css/');

// JSファイルを置くディレクトリ
define('ACS_JS_DIR', './js/');

// 共通JSファイル
define('ACS_COMMON_JS', 'swap.js');

// コンテンツバックアップファイルを置くディレクトリ
define('ACS_CONTENTS_BACKUP_DIR', ACS_FOLDER_DIR . 'contents_backup/');

// マイページデザイン選択CSSファイルを置くディレクトリ
define('ACS_SELECTION_CSS_DIR', ACS_CSS_DIR . 'selection/');

// デフォルトのマイページデザインCSSファイル
define('ACS_DEFAULT_SELECTION_CSS_FILE', 'default.css');

// デフォルトのユーザ画像ファイル
define('ACS_DEFAULT_USER_IMAGE_FILE', ACS_IMAGE_DIR . 'people.png');
define('ACS_DEFAULT_USER_IMAGE_FILE_THUMB', ACS_IMAGE_DIR . 'people.thumb.png');

// デフォルトのコミュニティ画像ファイル
define('ACS_DEFAULT_COMMUNITY_IMAGE_FILE', ACS_IMAGE_DIR . 'community.png');
define('ACS_DEFAULT_COMMUNITY_IMAGE_FILE_THUMB', ACS_IMAGE_DIR . 'community.thumb.png');

// コミュニティML関連
//	メールアドレスプレフィックスとサフィックス
define('ACS_COMMUNITY_ML_ADDR_PREFIX',  'acs-');
define('ACS_COMMUNITY_ML_ADDR_SUFFIX',  '@xxx.yyy.zz.jp');

// 	メールアドレスＮＧ名(カンマ区切り,小文字で)
define('ACS_COMMUNITY_ML_ADDR_NGNAMES', 'admin,administrator,root,system,mail');

// 	件名プレフィックスフォーマット({BBSID}->bbs_idで置換)
define('ACS_COMMUNITY_ML_SUBJECT_FORMAT', '(ACS) [bbs_id:{BBSID}] ');

// 	件名Re:削除Regex
define('ACS_COMMUNITY_ML_SUBJECT_PREFIX_CLEAR_REGEX', '^[ ]*([R|r][E|e][0-9]*[:][ ]*)+');

// トップページ静的HTML関連
//  静的ページファイルの有効時間範囲(秒)
//  ※指定秒以内に作成されたファイルのみを有効とする
define('ACS_PAGES_EFFECTIVE_SEC', 4000);

// コンテンツバックアップ用圧縮コマンド
define('ACS_BACKUP_COMMAND_ORDER', '/usr/bin/zip -r -j');

// コンテンツバックアップフォルダ名(マイフォルダ,マイダイアリー)
define('ACS_BACKUP_ZIP_DIR_NAME',         'Backup' );
define('ACS_BACKUP_MYFOLDER_SUBDIR_NAME', 'Folder' );
define('ACS_BACKUP_MYDIARY_SUBDIR_NAME',  'Diary' );

// コンテンツバックアップファイル・フォルダ名エンコーディング
define('ACS_BACKUP_NAME_ENCODING', 'SJIS-win');

/*----------------------------------------------------*/

/**
 * The action to be executed when an authenticated user makes a request for
 * an action for which they do not possess the privilege.
 */
define('SECURE_MODULE', 'Common');
define('SECURE_ACTION', 'GlobalSecure');

/**
 * The parameter name used to specify a module.
 */
define('MODULE_ACCESSOR', 'module');

/**
 * The parameter name used to specify an action.
 */
define('ACTION_ACCESSOR', 'action');

// Debug Mode
define('ACS_DEBUG_MODE', 1);

// ExecutionTimeFilter (0...none,1...run)
define('ACS_EXEC_TIMER', 1);

// Default Module & Action
define('DEFAULT_MODULE', 'User');
define('DEFAULT_ACTION', 'Index');


?>
