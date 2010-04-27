<?php

// �ǡ���������̾
define('ACS_DSN', 'pgsql://postgres:postgres@localhost:5432+unix/acsdb');

// logging ������
define('ACS_LOG_PRIORITY', LEVEL_WARN);
define('ACS_LOG_EXIT_PRIORITY', LEVEL_FATAL);

// �����ƥ�����ԤΥ桼��ID
define('ACS_ADMINISTRATOR_USER_ID', 'admin');

// ���̥��������Υ桼�����ߥ�˥ƥ�ID
define('ACS_PUBLIC_USER_COMMUNITY_ID', '0');

// ���̥��������Υ桼��̾
define('ACS_PUBLIC_USER_NAME', '������');

// ���������
// �᡼�����Υ���ƥ�ĥ����ץ�����
define('ACS_MAIL_LANG_CONTENTS_TYPE_CODE', '51');

// �ǥե���ȸ���
define('ACS_DEFAULT_LANG', 'ja');

// ���ظ���
define('ACS_LANG_LIST', 'ja:Japanese,en:English');

// ���������ƥ�
// LDAP�λ���(0:�Ի���,1:����)
define('USE_LDAP_SYSTEM', '0');

/*----- �ǥ��쥯�ȥ� -----*/

// ������ץȥѥ�
define('SCRIPT_PATH', 'index.php');

// ���ܥǥ��쥯�ȥ�
define('BASE_DIR', dirname(__FILE__) . '/');

// ���̥ƥ�ץ졼�ȥե�����
define('MO_TEMPLATE_FILE', 'acs_base.tpl.php');

// �����̶��̥ƥ�ץ졼�ȥե�����
define('MO_SIMPLE_TEMPLATE_FILE', 'acs_simple_base.tpl.php');

// �ѥ���ɥե�����
define('ACS_PASSWD_FILE', '../../etc/.htpasswd');

// �ե�����Υե�������֤��ǥ��쥯�ȥ� (���Хѥ�) (����å�����Ĥ���)
define('ACS_FOLDER_DIR', '../../files/');

// ����������ߥ�˥ƥ��Υե�������֤��ǥ��쥯�ȥ�
define('ACS_TRASH_FOLDER_DIR', ACS_FOLDER_DIR . 'trash/');

// ���֤��������ߥ�˥ƥ��ηǼ��Ĳ����ե�������֤��ǥ��쥯�ȥ�
define('ACS_TEMPORARY_FILE_DIR', ACS_FOLDER_DIR . 'temporary/');

// ACS�ƥ��饹�ե�����Υǥ��쥯�ȥ� (���Хѥ�) (����å�����Ĥ���)
define('ACS_CLASS_DIR', '../../webapp/lib/class/');

// ACS Validators �Υǥ��쥯�ȥ� (���Хѥ�) (����å�����Ĥ���)
define('ACS_VALIDATORS_DIR', '../../webapp/lib/mojavi/validators/');

// ACS�ƥ��饹�ե�����Υǥ��쥯�ȥ� (���Хѥ�) (����å�����Ĥ���)
define('ACS_LIB_TEMPLATE_DIR', '../../webapp/lib/template/');

// ACS���̥ƥ�ץ졼�ȥե�����Υǥ��쥯�ȥ� (���Хѥ�) (����å�����Ĥ���)
define('ACS_TEMPLATE_DIR', '../../webapp/templates/');

// ACS��å���������ե�����Υǥ��쥯�ȥ� (���Хѥ�) (����å�����Ĥ���)
define('ACS_LIB_MESSAGE_DIR', '../../webapp/lib/message/');

// ACSmojavi�ե��륿�����饹�ե�����(���Хѥ�) (����å�����Ĥ���)
define('ACS_LIB_FILTERS_DIR', '../../webapp/lib/filters/');

// ACS��ŪHTML�ե�����ǡ����Υǥ��쥯�ȥ� (���Хѥ�) (����å�����Ĥ���)
define('ACS_PAGES_DIR', '../../webapp/pages/');

// �����ե�������֤��ǥ��쥯�ȥ�
define('ACS_IMAGE_DIR', './img/');

// CSS�ե�������֤��ǥ��쥯�ȥ�
define('ACS_CSS_DIR', './css/');

// JS�ե�������֤��ǥ��쥯�ȥ�
define('ACS_JS_DIR', './js/');

// ����JS�ե�����
define('ACS_COMMON_JS', 'swap.js');

// ����ƥ�ĥХå����åץե�������֤��ǥ��쥯�ȥ�
define('ACS_CONTENTS_BACKUP_DIR', ACS_FOLDER_DIR . 'contents_backup/');

// �ޥ��ڡ����ǥ���������CSS�ե�������֤��ǥ��쥯�ȥ�
define('ACS_SELECTION_CSS_DIR', ACS_CSS_DIR . 'selection/');

// �ǥե���ȤΥޥ��ڡ����ǥ�����CSS�ե�����
define('ACS_DEFAULT_SELECTION_CSS_FILE', 'default.css');

// �ǥե���ȤΥ桼�������ե�����
define('ACS_DEFAULT_USER_IMAGE_FILE', ACS_IMAGE_DIR . 'people.png');
define('ACS_DEFAULT_USER_IMAGE_FILE_THUMB', ACS_IMAGE_DIR . 'people.thumb.png');

// �ǥե���ȤΥ��ߥ�˥ƥ������ե�����
define('ACS_DEFAULT_COMMUNITY_IMAGE_FILE', ACS_IMAGE_DIR . 'community.png');
define('ACS_DEFAULT_COMMUNITY_IMAGE_FILE_THUMB', ACS_IMAGE_DIR . 'community.thumb.png');

// ���ߥ�˥ƥ�ML��Ϣ
//	�᡼�륢�ɥ쥹�ץ�ե��å����ȥ��ե��å���
define('ACS_COMMUNITY_ML_ADDR_PREFIX',  'acs-');
define('ACS_COMMUNITY_ML_ADDR_SUFFIX',  '@xxx.yyy.zz.jp');

// 	�᡼�륢�ɥ쥹�Σ�̾(����޶��ڤ�,��ʸ����)
define('ACS_COMMUNITY_ML_ADDR_NGNAMES', 'admin,administrator,root,system,mail');

// 	��̾�ץ�ե��å����ե����ޥå�({BBSID}->bbs_id���ִ�)
define('ACS_COMMUNITY_ML_SUBJECT_FORMAT', '(ACS) [bbs_id:{BBSID}] ');

// 	��̾Re:���Regex
define('ACS_COMMUNITY_ML_SUBJECT_PREFIX_CLEAR_REGEX', '^[ ]*([R|r][E|e][0-9]*[:][ ]*)+');

// �ȥåץڡ�����ŪHTML��Ϣ
//  ��Ū�ڡ����ե������ͭ�������ϰ�(��)
//  �������ð���˺������줿�ե�����Τߤ�ͭ���Ȥ���
define('ACS_PAGES_EFFECTIVE_SEC', 4000);

// ����ƥ�ĥХå����å��Ѱ��̥��ޥ��
define('ACS_BACKUP_COMMAND_ORDER', '/usr/bin/zip -r -j');

// ����ƥ�ĥХå����åץե����̾(�ޥ��ե����,�ޥ��������꡼)
define('ACS_BACKUP_ZIP_DIR_NAME',         'Backup' );
define('ACS_BACKUP_MYFOLDER_SUBDIR_NAME', 'Folder' );
define('ACS_BACKUP_MYDIARY_SUBDIR_NAME',  'Diary' );

// ����ƥ�ĥХå����åץե����롦�ե����̾���󥳡��ǥ���
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
