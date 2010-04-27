<?php
// $Id: EditBBS.tpl.php,v 1.4 2007/03/01 09:01:35 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "EditBBS.tpl.php", 'M001') ?></a> :: <a href="<?= $this->_tpl_vars['bbs_url'] ?>"><?= ACSMsg::get_msg("Community", "EditBBS.tpl.php", 'M002') ?></a> :: <?= ACSMsg::get_msg("Community", "EditBBS.tpl.php", 'M003') ?>
</div>
<br><br>

<?= ACSMsg::get_msg("Community", "EditBBS.tpl.php", 'M004') ?><br>
<br>

<?php
ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
?>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post" name="bbs_form" enctype="multipart/form-data">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditBBS.tpl.php", 'M006') ?></td>
<td bgcolor="#ffffff"><input type="text" name="subject" value="<?= htmlspecialchars($this->_tpl_vars['bbs_row']['subject']) ?>" size="50" style="width:400px"></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditBBS.tpl.php", 'M007') ?></td>
<td bgcolor="#ffffff"><textarea name="body" cols="60" rows="10" style="width:480px"><?= htmlspecialchars($this->_tpl_vars['bbs_row']['body']) ?></textarea></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditBBS.tpl.php", 'M008') ?></td>
<td bgcolor="#ffffff">
<?php
if ($this->_tpl_vars['bbs_row']['file_url']) {
	echo "<img src=\"{$this->_tpl_vars['bbs_row']['file_url']}\" style=\"margin:5px\"><br>\n";
}
?>
<input type="file" name="new_file" size="50">
</td>
</tr>
</table>
<br>
<input type="submit" value="<?= ACSMsg::get_msg("Community", "EditBBS.tpl.php", 'M005') ?>">
</form>
<br>
