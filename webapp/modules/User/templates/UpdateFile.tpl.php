<?php
// $Id: UpdateFile.tpl.php,v 1.4 2007/03/01 09:01:43 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "UpdateFile.tpl.php",'M001') ?></div>
<br>

<form name="upload_file" action="<?= $this->_tpl_vars['action_url'] ?>" method="POST" enctype="multipart/form-data">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "UpdateFile.tpl.php",'M002') ?></td>
<td bgcolor="#ffffff"><input type="file" name="new_file" size="50"> <input type="submit" value="<?= ACSMsg::get_msg("User", "UpdateFile.tpl.php",'M003') ?>"></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "UpdateFile.tpl.php",'M004') ?></td>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "UpdateFile.tpl.php",'M005') ?><br><textarea name="comment" cols="50" rows="5"></textarea></td>
</tr>
</table>

</form>
<br>

<a href="<?= $this->_tpl_vars['file_detail_url'] ?>"><?= ACSMsg::get_msg("User", "UpdateFile.tpl.php",'M006') ?></a>
