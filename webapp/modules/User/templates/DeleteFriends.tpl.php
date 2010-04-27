<?php
// $Id: DeleteFriends.tpl.php,v 1.4 2007/03/14 04:28:21 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "DeleteFriends.tpl.php","M001") ?></div>

<div class="confirm_msg">
<?= ACSMsg::get_msg("User", "DeleteFriends.tpl.php","M002") ?><br>
<?= ACSMsg::get_msg("User", "DeleteFriends.tpl.php","M003") ?><br>
</div>


<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td align="center" bgcolor="#ffffff">
<a href="<?= $this->_tpl_vars['delete_user_info_row']['top_page_url'] ?>"><img src="<?= $this->_tpl_vars['delete_user_info_row']['image_url'] ?>" border="0"></a><br>
<a href="<?= $this->_tpl_vars['delete_user_info_row']['top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['delete_user_info_row']['community_name']) ?></a>
</td>
</tr>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("User", "DeleteFriends.tpl.php","M004") ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("User", "DeleteFriends.tpl.php","M005") ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">

</form>
