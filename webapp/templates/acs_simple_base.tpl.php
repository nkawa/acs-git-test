<?php
// $Id: acs_base.tpl.php(for mojavi3),v 1.0 2009/01/23 14:20:00 y-yuki Exp $
// $Id: acs_super_base.tpl.php,v 1.4 2006/11/20 08:44:29 w-ota Exp $
?>
<html>
<head>
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Type" content="text/html; charset=EUC_JP">
<?php
// CSS
foreach ($this->_tpl_vars['include_css_array'] as $include_css) {
    echo '<link rel="stylesheet" href="' . $include_css . '" type="text/css">' . "\n";
}

// JavaScript
foreach ($this->_tpl_vars['include_script_array'] as $include_script) {
    echo '<script src="' . $include_script . '" type="text/javascript"></script>' . "\n";
}
?>
<title><?= ACSMsg::get_msg('templates','acs_base.tpl.php','M001') ?></title>

</head>
<body onLoad="MM_preloadImages('img/mn01b.gif','img/mn02b.gif','img/mn03b.gif','img/mn04b.gif','img/mn05b.gif')">
<div class="main">
        <?php echo $this->_tpl_vars['content']; ?>
</div>
</body>
</html>
