<?php
// +----------------------------------------------------------------------+
// | PHP version 5 & mojavi version 3                                     |
// +----------------------------------------------------------------------+
// | Authors: y-yuki v 1.0 2009/01/23 14:40:00                            |
// +----------------------------------------------------------------------+
// $Id: LoginInput.tpl.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg('User', 'LoginInput.tpl.php','M001') ?></div>
<?php
    // ログインエラー時
    echo ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
?>
<p>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">
<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
<input type="hidden" name="search" value="1">

<table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
<tr>
    <td bgcolor="#FFF5AA">
        <table class="layout_table">
        <tr>
            <td><?= ACSMsg::get_msg('User', 'LoginInput.tpl.php','M002') ?></td>
            <td><input type="text" name="userid" value="<?= htmlspecialchars($_POST['userid']) ?>" size="30" tabindex="1" style="width:200px"></td>
        </tr>
        <tr>
            <td><?= ACSMsg::get_msg('User', 'LoginInput.tpl.php','M003') ?></td>
            <td><input type="password" name="passwd" value="<?= htmlspecialchars($_POST['passwd']) ?>" size="30" tabindex="2" style="width:200px"></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" value="<?= ACSMsg::get_msg('User', 'LoginInput.tpl.php','M004') ?>" tabindex="3">
            </td>
        </tr>
        </table>
    </td>
</tr>
</table>

</form>
</p>