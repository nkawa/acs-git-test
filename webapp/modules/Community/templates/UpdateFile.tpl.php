<?php
// $Id: UpdateFile.tpl.php,v 1.4 2007/03/01 09:01:35 w-ota Exp $
?>

<?php
	$title  = '<a href="' . $this->_tpl_vars['target_community_row']['top_page_url'] . '">';
	$title .= htmlspecialchars($this->_tpl_vars['target_community_row']['community_name']) . " コミュニティ";
	$title .= '</a>';
?>
<div class="ttl"><?= $title ?> :: <?= ACSMsg::get_msg("Community", "UpdateFile.tpl.php",'M001') ?> :: <?= ACSMsg::get_msg("Community", "UpdateFile.tpl.php",'M002') ?> :: <?= ACSMsg::get_msg("Community", "UpdateFile.tpl.php",'M003') ?></div>
<br>

<form name="upload_file" action="<?= $this->_tpl_vars['action_url'] ?>" method="POST" enctype="multipart/form-data">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "UpdateFile.tpl.php",'M004') ?></td>
<td bgcolor="#ffffff"><input type="file" name="new_file" size="50"> <input type="submit" value="<?= ACSMsg::get_msg("Community", "UpdateFile.tpl.php",'M005') ?>"></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "UpdateFile.tpl.php",'M006') ?></td>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("Community", "UpdateFile.tpl.php",'M007') ?><br><textarea name="comment" cols="50" rows="5"></textarea></td>
</tr>
</table>
<input type="checkbox" value="t" name="send_announce_mail" <?= $this->_tpl_vars['send_annouce_mail_checked'] ?>>
    <?= ACSMsg::get_msg("Community", "UpdateFile.tpl.php",'M009') ?>
</form>
<br>

<a href="<?= $this->_tpl_vars['file_detail_url'] ?>"><?= ACSMsg::get_msg("Community", "UpdateFile.tpl.php",'M008') ?></a>
