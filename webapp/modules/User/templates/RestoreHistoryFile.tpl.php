<?php
// $Id: RestoreHistoryFile.tpl.php,v 1.5 2007/03/28 08:39:34 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "RestoreHistoryFile.tpl.php",'M001') ?></div>
<br>

<?= ACSMsg::get_msg("User", "RestoreHistoryFile.tpl.php",'M002') ?><br><br>

<form name="upload_file" action="<?= $this->_tpl_vars['action_url'] ?>" method="POST">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "RestoreHistoryFile.tpl.php",'M003') ?></td>
	<td bgcolor="#ffffff">
	<table class="inner_layout_table"><tr>
	<td><img src="<?= ACS_IMAGE_DIR . "file.gif" ?>"></td>
	<td>
	<a href="<?= $this->_tpl_vars['file_history_row']['download_history_file_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['file_history_row']['display_file_name']) ?></a>&nbsp;&nbsp;(<?= htmlspecialchars($this->_tpl_vars['file_history_row']['mime_type']) ?>)
	</td>
	</tr></table>
	</td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "RestoreHistoryFile.tpl.php",'M004') ?></td>
	<td bgcolor="#ffffff"><?= $this->_tpl_vars['file_history_row']['file_size_kb'] ?> (<?= $this->_tpl_vars['file_history_row']['file_size'] ?> <?= ACSMsg::get_msg("User", "RestoreHistoryFile.tpl.php",'M005') ?>)</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "RestoreHistoryFile.tpl.php",'M006') ?></td>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "RestoreHistoryFile.tpl.php",'M007') ?><br><textarea name="comment" cols="50" rows="5"></textarea></td>
</tr>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("User", "RestoreHistoryFile.tpl.php",'M008') ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("User", "RestoreHistoryFile.tpl.php",'M009') ?>" onclick="location.href='<?= $this->_tpl_vars['file_detail_url'] ?>'">

</form>
