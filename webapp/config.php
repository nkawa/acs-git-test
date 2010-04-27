<?php
// System Installation Directory
define('MO_BASE_DIR', '/home/acsuser/acs/');

// PEAR Installation Directory
define('MO_PEAR_DIR', MO_BASE_DIR . '/pear');

// FPDF Font Installation Directory
define('FPDF_FONTPATH', 'font/');

// Log output Directory
define('MO_LOG_DIR', MO_BASE_DIR . '/logs');

// Define level
define('LEVEL_DEBUG', 1000);
define('LEVEL_INFO', 2000);
define('LEVEL_ERROR', 4000);
define('LEVEL_WARN', 3000);
define('LEVEL_FATAL', 5000);


/**
 * ログのフォーマットパラメータ
 * c .... クラス名
 * F .... ファンクション名
 * l .... 行番号
 * m .... メッセージ
 * N .... メッセージ名
 * p .... メッセージパラメータ番号
 * n .... 改行コード
 * r .... 復帰コード
 * t .... タブコード
 * T .... 時刻
 * C .... 定数名（%C{定数名}とすると、定数の値が出力される）
 * d .... 日付（%d{書式}とすると、日付のフォーマットが指定できる）
 * f .... ファイル名（%f{'file'}とすると、basenameが出力され、%f{'dir'}とすると、dirnameが出力される）
 * x .... 任意の値（Loggerに格納されたパラメータ名を指定すると、その値が出力される）
 */
// Log filename
define('MO_LOG_FILENAME', 'mojavi_%d{Ymd}.log');

// Log Pattern Layout
define('MO_LOG_PATTERN_LAYOUT', '[%N] %d{Y/m/d H:i:s} [%x{ip_address} - %x{login_id}] %m %c::%F() %f:%l %x{data}%n');

// Database Default Configuration
define('DB_PHPTYPE', 'mysqli');
define('DB_HOSTSPEC', 'localhost');
define('DB_PORT', '3306');
define('DB_DATABASE', '');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');

// Error column Color
define('S4_ERROR_COLUMN_COLOR', 'background:lightpink;');

// Screen Name List
define('S4_SCREEN_NAME_LIST', 'screen.ini');

/*
 Mail Configuration 
 ※必要箇所にだけ設定を施すこと。未設定の場合、デフォルトを使用する。
*/
// -------------(ここから)-------------
define('S4_MAIL_SENDER_ADDR', ''); //S4 送信者アドレス

// メール送信方法：mail（php mail送信） sendmail:sendmail送信 smtp:SMTP送信
define('S4_MAIL_DRIVER', 'mail'); //mail, sendmail, smtp

// php mail設定
define('S4_MAIL_ARGS', ''); // mail()の追加パラメータ（第5引数）

// sendmail設定
define('S4_SENDMAIL_PATH', ''); //sendmailの場所（デフォルト：/usr/bin/sendmail）
define('S4_SENDMAIL_ARGS', ''); // sendmaiの追加パラメータ（デフォルト：-i）

// smtp設定
define('S4_SMTP_HOST', ''); // SMTP接続サーバ名（デフォルト：localhost）
define('S4_SMTP_PORT', ''); // SMTP接続するポート番号（デフォルト：25です）
define('S4_SMTP_AUTH', ''); // SMTP認証有無（デフォルト：false）
define('S4_SMTP_USERNAME', ''); // SMTP認証ユーザ名
define('S4_SMTP_PASSWORD', ''); // SMTP認証パスワード
define('S4_SMTP_LOCALHOST', ''); // EHLO/HELOの送信パラメータ（デフォルト：localhost）
define('S4_SMTP_TIMEOUT', ''); // SMTP接続のタイムアウト（デフォルト：null(タイムアウトしない)）
define('S4_SMTP_VERP', ''); // VERP使用（デフォルト：false）
define('S4_SMTP_DEBUG', ''); // SMTPデバッグモード（デフォルト：false）
define('S4_SMTP_PERSIST', ''); // send()メソッドの複数回呼出しでSMTP接続を持続させる（デフォルト：false）
// -------------(ここまで)-------------

// Mail Queue table name
define('S4_MAIL_TABLE', 'mail_queue');

// +---------------------------------------------------------------------------+
// | Should we run the system in debug mode? When this is on, there may be     |
// | various side-effects. But for the time being it only deletes the cache    |
// | upon start-up.                                                            |
// |                                                                           |
// | This should stay on while you're developing your application, because     |
// | many errors can stem from the fact that you're using an old cache file.   |
// +---------------------------------------------------------------------------+
// true = アクセスごとにキャッシュを作るので実稼動時はかならずfalseにすること
define('MO_DEBUG', false);

// +---------------------------------------------------------------------------+
// | The PHP error reporting level.                                            |
// |                                                                           |
// | See: http://www.php.net/error_reporting                                   |
// +---------------------------------------------------------------------------+
// エラーレベルの設定
//;define('MO_ERROR_REPORTING', E_ALL | E_STRICT);
define('MO_ERROR_REPORTING', E_ALL);

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to the mojavi package. This directory         |
// | contains all the Mojavi packages.                                         |
// +---------------------------------------------------------------------------+
//;define('MO_APP_DIR', '<REPLACE ME>/mojavi');
define('MO_APP_DIR', MO_BASE_DIR . '/mojavi');

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to your web application directory. This       |
// | directory is the root of your web application, which includes the core    |
// | configuration files and related web application data.                     |
// +---------------------------------------------------------------------------+
//;define('MO_WEBAPP_DIR', '<REPLACE ME>/webapp');
define('MO_WEBAPP_DIR', MO_BASE_DIR . '/webapp');

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to the directory where cache files will be    |
// | stored.                                                                   |
// |                                                                           |
// | NOTE: If you're going to use a public temp directory, make sure this is a |
// |       sub-directory of the temp directory. The cache system will attempt  |
// |       to clean up *ALL* data in this directory.                           |
// +---------------------------------------------------------------------------+
define('MO_CACHE_DIR', MO_WEBAPP_DIR . '/cache');

// Smarty Installation Directory
define('MO_SMARTY_DIR', MO_BASE_DIR . '/smarty');

?>
