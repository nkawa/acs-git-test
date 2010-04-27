<?php
// $Id: AddUser.tpl.php,v 1.4 2007/03/01 09:01:41 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("System", "AddUser.tpl.php",'M001') ?></div>
<br><br>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "AddUser.tpl.php",'M002') ?></td>
<td bgcolor="#ffffff"><input type="text" name="user_id" size="30"></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "AddUser.tpl.php",'M003') ?></td>
<td bgcolor="#ffffff">
<input type="password" name="passwd" size="20"><br>
<input type="password" name="passwd2" size="20"> <?= ACSMsg::get_msg("System", "AddUser.tpl.php",'M004') ?><br>
</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "AddUser.tpl.php",'M005') ?></td>
<td bgcolor="#ffffff"><input type="text" name="user_name" size="30"></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "AddUser.tpl.php",'M006') ?></td>
<td bgcolor="#ffffff"><input type="text" name="mail_addr" size="30"></td>
</tr>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("System", "AddUser.tpl.php",'M007') ?>"><br>
</form>
<br>

<a href="<?= $this->_tpl_vars['back_url'] ?>"><?= ACSMsg::get_msg("System", "AddUser.tpl.php",'M008') ?></a><br>
