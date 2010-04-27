<?php
// $Id: DeleteFriendsGroup.tpl.php,v 1.2 2006/11/20 08:44:26 w-ota Exp $
?>

<span class="sub_title"><?= ACSMsg::get_msg("User", "DeleteFriendsGroup.tpl.php",'M001') ?></span><br>
<br>
<?= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "DeleteFriendsGroup.tpl.php",'DELC'),array(
			"{USER_NAME}" => $this->_tpl_vars['friends_group_row']['community_name'])); ?><br>
<br>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">
<input type="submit" value=" OK ">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("User", "DeleteFriendsGroup.tpl.php",'M003') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">
</form>
