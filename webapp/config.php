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
 * ���Υե����ޥåȥѥ�᡼��
 * c .... ���饹̾
 * F .... �ե��󥯥����̾
 * l .... ���ֹ�
 * m .... ��å�����
 * N .... ��å�����̾
 * p .... ��å������ѥ�᡼���ֹ�
 * n .... ���ԥ�����
 * r .... ����������
 * t .... ���֥�����
 * T .... ����
 * C .... ���̾��%C{���̾}�Ȥ���ȡ�������ͤ����Ϥ�����
 * d .... ���ա�%d{��}�Ȥ���ȡ����դΥե����ޥåȤ�����Ǥ����
 * f .... �ե�����̾��%f{'file'}�Ȥ���ȡ�basename�����Ϥ��졢%f{'dir'}�Ȥ���ȡ�dirname�����Ϥ�����
 * x .... Ǥ�դ��͡�Logger�˳�Ǽ���줿�ѥ�᡼��̾����ꤹ��ȡ������ͤ����Ϥ�����
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
 ��ɬ�ײս�ˤ��������ܤ����ȡ�̤����ξ�硢�ǥե���Ȥ���Ѥ��롣
*/
// -------------(��������)-------------
define('S4_MAIL_SENDER_ADDR', ''); //S4 �����ԥ��ɥ쥹

// �᡼��������ˡ��mail��php mail������ sendmail:sendmail���� smtp:SMTP����
define('S4_MAIL_DRIVER', 'mail'); //mail, sendmail, smtp

// php mail����
define('S4_MAIL_ARGS', ''); // mail()���ɲåѥ�᡼������5������

// sendmail����
define('S4_SENDMAIL_PATH', ''); //sendmail�ξ��ʥǥե���ȡ�/usr/bin/sendmail��
define('S4_SENDMAIL_ARGS', ''); // sendmai���ɲåѥ�᡼���ʥǥե���ȡ�-i��

// smtp����
define('S4_SMTP_HOST', ''); // SMTP��³������̾�ʥǥե���ȡ�localhost��
define('S4_SMTP_PORT', ''); // SMTP��³����ݡ����ֹ�ʥǥե���ȡ�25�Ǥ���
define('S4_SMTP_AUTH', ''); // SMTPǧ��̵ͭ�ʥǥե���ȡ�false��
define('S4_SMTP_USERNAME', ''); // SMTPǧ�ڥ桼��̾
define('S4_SMTP_PASSWORD', ''); // SMTPǧ�ڥѥ����
define('S4_SMTP_LOCALHOST', ''); // EHLO/HELO�������ѥ�᡼���ʥǥե���ȡ�localhost��
define('S4_SMTP_TIMEOUT', ''); // SMTP��³�Υ����ॢ���ȡʥǥե���ȡ�null(�����ॢ���Ȥ��ʤ�)��
define('S4_SMTP_VERP', ''); // VERP���ѡʥǥե���ȡ�false��
define('S4_SMTP_DEBUG', ''); // SMTP�ǥХå��⡼�ɡʥǥե���ȡ�false��
define('S4_SMTP_PERSIST', ''); // send()�᥽�åɤ�ʣ����ƽФ���SMTP��³���³������ʥǥե���ȡ�false��
// -------------(�����ޤ�)-------------

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
// true = �����������Ȥ˥���å������ΤǼ²�ư���Ϥ��ʤ餺false�ˤ��뤳��
define('MO_DEBUG', false);

// +---------------------------------------------------------------------------+
// | The PHP error reporting level.                                            |
// |                                                                           |
// | See: http://www.php.net/error_reporting                                   |
// +---------------------------------------------------------------------------+
// ���顼��٥������
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
