<?php
// $Id: CreateFriendsGroup.tpl.php,v 1.4 2007/03/01 09:01:43 w-ota Exp $

function is_friends_group($user_community_id, $friends_group_member_row_array) {
	$ret = false;
	foreach ($friends_group_member_row_array as $user_info_row) {
		if ($user_community_id == $user_info_row['user_community_id']) {
			$ret = true;
			break;
		}
	}
	return $ret;
}
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "CreateFriendsGroup.tpl.php",'M001') ?></div><br>
<br>

<?php
ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
?>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">
<table border="0" cellpadding="10" cellspacing="1" bgcolor="#99CC33">
<tr><td bgcolor="#FFFFFF">
<?= ACSMsg::get_msg("User", "CreateFriendsGroup.tpl.php",'M002') ?> : <input type="text" name="community_name" value="<?= htmlspecialchars($this->_tpl_vars['form']['community_name']) ?>" size="30"><br>
<br>
<?php
foreach ($this->_tpl_vars['friends_row_array'] as $user_info_row) {
	echo "<input type=\"checkbox\" name=\"trusted_community_id_array[]\" value=\"$user_info_row[user_community_id]\"";
	if (is_friends_group($user_info_row['user_community_id'], $this->_tpl_vars['friends_group_member_row_array'])) {
		echo ' checked';
	}
	echo ">";
	echo htmlspecialchars($user_info_row['user_name']);
	echo " (" . htmlspecialchars($user_info_row['community_name']) . ")";
	echo "<br>\n";
}
?>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("User", "CreateFriendsGroup.tpl.php",'M003') ?>">
</td></tr></table>
</form>
