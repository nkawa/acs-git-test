<?php
// $Id: index_login.php,v 2.0 2009/06/25 11:00:00 acs Exp $

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to our webapp/config.php script.              |
// +---------------------------------------------------------------------------+
require_once("../../webapp/config.php");

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to the mojavi/mojavi.php script.              |
// +---------------------------------------------------------------------------+
require_once(MO_APP_DIR . "/mojavi.php");
require_once(MO_WEBAPP_DIR . "/lib/acs_define.php");
require_once(MO_PEAR_DIR . '/DB.php');

// +---------------------------------------------------------------------------+
// | Create our controller. For this file we're going to use a front           |
// | controller pattern. This pattern allows us to specify module and action   |
// | GET/POST parameters and it automatically detects them and finds the       |
// | expected action.                                                          |
// +---------------------------------------------------------------------------+
$controller = Controller::newInstance('FrontWebController');

// +---------------------------------------------------------------------------+
// | Dispatch our request.                                                     |
// +---------------------------------------------------------------------------+
$controller->dispatch();
?>
