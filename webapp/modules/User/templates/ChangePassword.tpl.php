<?php
// $Id: ChangePassword.tpl.php,v 1.3 2007/03/01 09:01:43 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "ChangePassword.tpl.php",'M001') ?></div>
<br><br>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "ChangePassword.tpl.php",'M002') ?></td>
<td bgcolor="#ffffff">
<input type="password" name="passwd" size="20"><br>
<input type="password" name="passwd2" size="20"> <?= ACSMsg::get_msg("User", "ChangePassword.tpl.php",'M003') ?><br>
</td>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("User", "ChangePassword.tpl.php",'M004') ?>"><br>
</form>
<br>
