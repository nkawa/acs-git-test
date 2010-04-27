<?php
// $Id: EditCommunityAdmin.tpl.php,v 1.3 2007/03/01 09:01:35 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "EditCommunityAdmin.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "EditCommunityAdmin.tpl.php",'M002') ?>
</div>
<br>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">
<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr><td bgcolor="#ffffff">
<?php
if ($this->_tpl_vars['acs_user_info_row']['is_community_admin']) {
	echo "<input type=\"checkbox\" checked disabled>";
	echo htmlspecialchars($this->_tpl_vars['acs_user_info_row']['user_name']);
	echo " (" . htmlspecialchars($this->_tpl_vars['acs_user_info_row']['community_name']) . ")";
	echo "<br>\n";
}
?>
<?php
foreach ($this->_tpl_vars['community_member_user_info_row_array'] as $user_info_row) {
	echo "<input type=\"checkbox\" name=\"user_community_id_array[]\" value=\"$user_info_row[user_community_id]\"";
	if ($user_info_row['is_community_admin']) {
		echo ' checked';
	}
	echo ">";
	echo htmlspecialchars($user_info_row['user_name']);
	echo " (" . htmlspecialchars($user_info_row['community_name']) . ")";
	echo "<br>\n";
}
?>
</td></tr></table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("Community", "EditCommunityAdmin.tpl.php",'M003') ?>">
</form>
