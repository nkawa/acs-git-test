<?php
// $Id: InviteToCommunity.tpl.php,v 1.5 2007/03/01 09:01:35 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "InviteToCommunity.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "InviteToCommunity.tpl.php",'M002') ?>
</div>
<br>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<?= ACSMsg::get_msg("Community", "InviteToCommunity.tpl.php",'M003') ?><br>
<br>


<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "InviteToCommunity.tpl.php",'M004') ?></td>
<td bgcolor="#ffffff">
<?php
foreach ($this->_tpl_vars['friends_row_array'] as $user_info_row) {
	echo "<input type=\"checkbox\" name=\"user_community_id_array[]\" value=\"$user_info_row[user_community_id]\"";
	if ($user_info_row['is_community_member']) {
		echo " disabled";

	} elseif ($this->_tpl_vars['form']['user_community_id_array']) {
		// エラー時の入力値復元処理
		if (in_array($user_info_row['user_community_id'], $this->_tpl_vars['form']['user_community_id_array'])) {
			echo " checked";
		}
	}
	echo ">";
	echo htmlspecialchars($user_info_row['user_name']);
	echo " (" . htmlspecialchars($user_info_row['community_name']) . ")";
	echo "<br>\n";
}
?>
</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "InviteToCommunity.tpl.php",'M006') ?></td>
<td bgcolor="#ffffff"><textarea name="message" cols="50" rows="4"><?= $this->_tpl_vars['form']['message'] ?></textarea></td>
</tr>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("Community", "InviteToCommunity.tpl.php",'M005') ?>">
</form>
