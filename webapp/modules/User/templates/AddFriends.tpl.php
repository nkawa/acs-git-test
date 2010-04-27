<?php
// $Id: AddFriends.tpl.php,v 1.4 2006/11/20 08:44:26 w-ota Exp $
?>

<span class="sub_title"><?= ACSMsg::get_msg("User", "AddFriends.tpl.php",'M001') ?></span><br><br>

<?php
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "AddFriends.tpl.php",'ADF'), array(
		"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
?><br>
<br>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<?= ACSMsg::get_msg("User", "AddFriends.tpl.php",'M002') ?> :<br>
<textarea name="message" cols="50" rows="4"></textarea><br>
<br>
<input type="submit" value="<?= ACSMsg::get_msg("User", "AddFriends.tpl.php",'M003') ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("User", "AddFriends.tpl.php",'M004') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">

</form>
