<?php
// $Id: DeleteSystemAnnounce.tpl.php,v 1.3 2007/03/01 09:01:41 w-ota Exp $
?>

<div class="ttl"><a href="<?= $this->_tpl_vars['system_announce_list_url'] ?>"><?= ACSMsg::get_msg("System", "DeleteSystemAnnounce.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("System", "DeleteSystemAnnounce.tpl.php",'M002') ?></div>
<br>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<?= ACSMsg::get_msg("System", "DeleteSystemAnnounce.tpl.php",'M003') ?><br>
<br>

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "DeleteSystemAnnounce.tpl.php",'M004') ?></td>
<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['system_announce_row']['subject']) ?></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "DeleteSystemAnnounce.tpl.php",'M005') ?></td>
<td bgcolor="#ffffff"><?= nl2br(htmlspecialchars($this->_tpl_vars['system_announce_row']['body'])) ?></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "DeleteSystemAnnounce.tpl.php",'M006') ?></td>
<td bgcolor="#ffffff"><?= $this->_tpl_vars['system_announce_row']['expire_date'] ?></td>
</tr>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("System", "DeleteSystemAnnounce.tpl.php",'M007') ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("System", "DeleteSystemAnnounce.tpl.php",'M008') ?>" onclick="location.href='<?= $this->_tpl_vars['system_announce_list_url'] ?>'">
</form>
