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

$topimgFolder = 'img/';

$topimgAry = null;
$topimgbAry = null;


if (ACSMsg::get_lang(FALSE)=='en') {

    $topimgAry = array($topimgFolder . 'mn01e.gif', 
                            $topimgFolder . 'mn02e.gif', 
                            $topimgFolder . 'mn03e.gif', 
                            $topimgFolder . 'mn04e.gif', 
                            $topimgFolder . 'mn05e.gif', 
                            $topimgFolder . 'mn06e.gif');
    $topimgbAry = array($topimgFolder. 'mn01eb.gif', 
                            $topimgFolder . 'mn02eb.gif', 
                            $topimgFolder . 'mn03eb.gif', 
                            $topimgFolder . 'mn04eb.gif', 
                            $topimgFolder . 'mn05eb.gif', 
                            $topimgFolder . 'mn06eb.gif');
} else {

    $topimgAry = array($topimgFolder . 'mn01.gif', 
                            $topimgFolder . 'mn02.gif', 
                            $topimgFolder . 'mn03.gif', 
                            $topimgFolder . 'mn04.gif', 
                            $topimgFolder . 'mn05.gif', 
                            $topimgFolder . 'mn06.gif');
    $topimgbAry = array($topimgFolder . 'mn01b.gif', 
                            $topimgFolder . 'mn02b.gif', 
                            $topimgFolder . 'mn03b.gif', 
                            $topimgFolder . 'mn04b.gif', 
                            $topimgFolder . 'mn05b.gif', 
                            $topimgFolder . 'mn06b.gif');
}

?>
<title><?= ACSMsg::get_msg('templates','acs_base.tpl.php','M001') ?></title>

</head>
<body onLoad="MM_preloadImages('<?=$topimgbAry[0] ?>','<?=$topimgbAry[1] ?>','<?=$topimgbAry[2] ?>','<?=$topimgbAry[3] ?>','<?=$topimgbAry[4] ?>','<?=$topimgbAry[5] ?>')">

<?php
$logout_complete = $this->_tpl_vars['logout_complete'];
?>

<div align="center">
<br>
<?php
if ($this->_tpl_vars['is_login_user'] && $logout_complete != 1) {
    echo "<a href=\n" . $this->_tpl_vars['public_index_url'] . "\n>\n";
} else {
    echo "<a href=\n" . $this->_tpl_vars['public_index_url_not_login'] . "\n>\n";
}
?>
<img style="border-style:none;" src="img/head.jpg" alt="<?= ACSMsg::get_msg('templates','acs_base.tpl.php','M002') ?>" width="730" height="78">
</a>

<table width="730" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFCC33">
<tr>
<?php
if (!$this->_tpl_vars['is_login_user']) {
?>
    <td><a href="<?= $this->_tpl_vars[login_url] ?>"><?= ACSMsg::get_msg('templates','acs_base.tpl.php','M003') ?></a></td>
<?php
}
?>
    <td align="right">
        <table border="0" cellspacing="0" cellpadding="0">
        <tr>
<?php
if ($this->_tpl_vars['is_login_user'] && $logout_complete != 1) {
?>
            <td><a href="./" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image2','','<?=$topimgbAry[0] ?>',1)"><img src="<?=$topimgAry[0] ?>" alt="<?= ACSMsg::get_msg('templates','acs_base.tpl.php','M009') ?>" name="Image2" width="70" height="16" hspace="2" border="0"></a></td>
<?php
}
?>
            <td><a href="<?= $this->_tpl_vars['community_menu_url'] ?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image3','','<?=$topimgbAry[1] ?>',1)"><img src="<?=$topimgAry[1] ?>" alt="<?= ACSMsg::get_msg('templates','acs_base.tpl.php','M010') ?>" name="Image3" width="70" height="16" hspace="2" border="0"></a></td>
            <td><a href="<?= $this->_tpl_vars['search_user_url'] ?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image4','','<?=$topimgbAry[2] ?>',1)"><img src="<?=$topimgAry[2] ?>" alt="<?= ACSMsg::get_msg('templates','acs_base.tpl.php','M011') ?>" name="Image4" width="70" height="16" hspace="2" border=""></a></td>
<?php
if ($this->_tpl_vars['system_manage_menu_url']) {
?>
            <td><a href="<?= $this->_tpl_vars['system_manage_menu_url'] ?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image7','','<?=$topimgbAry[5] ?>',1)"><img src="<?=$topimgAry[5] ?>" alt="<?= ACSMsg::get_msg('templates','acs_base.tpl.php','M014') ?>" name="Image7" width="70" height="16" hspace="2" border="0"></a></td>
<?
}
?>
            <td><a href="<?= $this->_tpl_vars['faq_url'] ?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image5','','<?=$topimgbAry[3] ?>',1)"><img src="<?=$topimgAry[3] ?>" alt="<?= ACSMsg::get_msg('templates','acs_base.tpl.php','M012') ?>" name="Image5" width="70" height="16" hspace="2" border="0"></a></td>
<?php
// add Logout process
if ($this->_tpl_vars['is_login_user'] && $logout_complete != 1) {
?>
            <td><a href="<?= $this->_tpl_vars['logout_url'] ?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image8','','<?=$topimgbAry[4] ?>',1)"><img src="<?=$topimgAry[4] ?>" alt="<?= ACSMsg::get_msg('templates','acs_base.tpl.php','M013') ?>" name="Image8" width="70" height="16" hspace="2" border="0"></a></td>
<?php
}
?>
        </tr>
        </table>
    </td>
</tr>
</table>

<table width="730" border="0" cellspacing="0" cellpadding="8">
<tr>
    <td>
<?php
echo ACSMsg::get_tag_replace(ACSMsg::get_msg('templates','acs_base.tpl.php', 'WELCOME'),
    array("{USER_NAME}" => htmlspecialchars($this->_tpl_vars['acs_user_info_row']['user_name'])));
?>
    </td>
    <td align="right">
<?php
if (defined('ACS_LANG_LIST')) {
    $langs_array = ACSMsg::get_lang_list_array();
    foreach ($langs_array as $lang => $lang_name) {
        if (ACSMsg::get_lang(FALSE) != $lang) {
            $url = ACSMsg::get_lang_url($lang);
            printf('&nbsp<a href="%s">%s</a>', $url, $lang_name);
        }
    }
}
?>
    </td>
</tr>
</table>

<table width="730" border="0" cellspacing="0" cellpadding="0" id="main_window">
<tr> 
    <td id="topleft"><img src="img/waku_blank.gif" width="12" height="20"></td>
    <td width="706" id="over">&nbsp</td>
    <td id="topright"><img src="img/waku_blank.gif" width="12" height="20"></td>
</tr>
<tr> 
    <td width="12" id="left"> &nbsp; &nbsp; </td>
    <td width="706" style="padding:10px;">
        <?php echo $this->_tpl_vars['content']; ?>
    </td>
    <td width="12" id="right"> &nbsp; &nbsp; </td>
</tr>
<tr> 
    <td id="underleft"><img src="img/waku_blank.gif" width="12" height="20"></td>
    <td width="706" id="under">&nbsp</td>
    <td id="underright"><img src="img/waku_blank.gif" width="12" height="20"></td>
</tr>
</table>

<br>
<font color="#666666">Copyright(C) Academic Community System All Rights Reserved.</font></div>
<br>

</body>
</html>
