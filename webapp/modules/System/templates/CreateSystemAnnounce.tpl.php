<?php
// $Id: CreateSystemAnnounce.tpl.php,v 1.3 2007/03/01 09:01:41 w-ota Exp $
?>

<div class="ttl"><a href="<?= $this->_tpl_vars['system_announce_list_url'] ?>"><?= ACSMsg::get_msg("System", "CreateSystemAnnounce.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("System", "CreateSystemAnnounce.tpl.php",'M002') ?></div>
<br>

<?= ACSMsg::get_msg("System", "CreateSystemAnnounce.tpl.php",'M003') ?><br>
<?= ACSMsg::get_msg("System", "CreateSystemAnnounce.tpl.php",'M004') ?><br>
<br>

<?php
ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
?>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "CreateSystemAnnounce.tpl.php",'M005') ?></td>
<td bgcolor="#ffffff"><input type="text" name="subject" value="<?= htmlspecialchars($this->_tpl_vars['form']['subject']) ?>" size="50"></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "CreateSystemAnnounce.tpl.php",'M006') ?></td>
<td bgcolor="#ffffff">
<textarea name="body" cols="80" rows="5"><?= htmlspecialchars($this->_tpl_vars['form']['body']) ?></textarea>
</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "CreateSystemAnnounce.tpl.php",'M007') ?></td>
<td bgcolor="#ffffff"><input type="text" name="expire_date" value="<?= htmlspecialchars($this->_tpl_vars['form']['expire_date']) ?>" size="20"> (YYYY/MM/DD)</td>
</tr>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("System", "CreateSystemAnnounce.tpl.php",'M008') ?>"><br>
</form>
<br>

<a href="<?= $this->_tpl_vars['system_announce_list_url'] ?>"><?= ACSMsg::get_msg("System", "CreateSystemAnnounce.tpl.php",'M009') ?></a><br>
