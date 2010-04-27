<?php
// $Id: SearchUser.tpl.php,v 1.12 2007/03/14 04:28:21 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "SearchUser.tpl.php",'M001') ?></div>

<p>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="get">
<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
<input type="hidden" name="search" value="1">

<table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
 <tr>
  <td bgcolor="#FFF5AA">
<table class="layout_table">
<tr>
<td><?= ACSMsg::get_msg("User", "SearchUser.tpl.php",'M002') ?></td>
<td><input type="text" name="q" value="<?= htmlspecialchars($this->_tpl_vars['form']['q']) ?>" size="30"></td>
<td><input type="submit" value="<?= ACSMsg::get_msg("User", "SearchUser.tpl.php",'M003') ?>"></td>
</tr>
<tr>
<td><?= ACSMsg::get_msg("User", "SearchUser.tpl.php",'M004') ?></td>
<td>
<?php
unset($selected);
if ($this->_tpl_vars['form']['order'] == 'community_name' || $this->_tpl_vars['form']['order'] == 'friends_num' || $this->_tpl_vars['form']['order'] == 'community_num') {
	$selected[$this->_tpl_vars['form']['order']] = ' selected';
} else {
	$selected['user_id'] = ' selected';
}
?>
<select name="order">
<option value="user_id"<?= $selected['user_id'] ?>><?= ACSMsg::get_msg("User", "SearchUser.tpl.php",'M011') ?>
<option value="community_name"<?= $selected['community_name'] ?>><?= ACSMsg::get_msg("User", "SearchUser.tpl.php",'M006') ?>
<option value="friends_num"<?= $selected['friends_num'] ?>><?= ACSMsg::get_msg("User", "SearchUser.tpl.php",'M007') ?>
<option value="community_num"<?= $selected['community_num'] ?>><?= ACSMsg::get_msg("User", "SearchUser.tpl.php",'M008') ?>
</select>
</td>
</tr>
</table>
</td></tr></table>

</form>
</p>

<?php
if (count($this->_tpl_vars['user_info_row_array'])) {
	// ページング表示
	ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);

	echo "<table class=\"common_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";
	foreach ($this->_tpl_vars['user_info_row_array'] as $user_info_row) {
		echo "<tr>";
		// 写真
		echo "<td align=\"center\" bgcolor=\"#ffffff\">";
		echo "<a href=\"$user_info_row[top_page_url]\"><img src=\"$user_info_row[image_url]\" border=\"0\"></a><br>";
		echo "<a href=\"$user_info_row[top_page_url]\">" . htmlspecialchars($user_info_row['community_name']) . "</a>";
		echo "(" . $user_info_row['friends_row_array_num'] . ")";
		echo "</td>";
		// 自己紹介
		echo "<td valign=\"top\" bgcolor=\"#ffffff\">".ACSMsg::get_msg("User", "SearchUser.tpl.php",'M009')."<br>";
		echo nl2br(htmlspecialchars($user_info_row['contents_row_array']['community_profile']['contents_value']));
		echo "</td>";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "<br>\n";

	// ページング表示
	ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);

} else {
	if ($this->_tpl_vars['form']['q'] != '') {
		echo ACSMsg::get_msg("User", "SearchUser.tpl.php",'M010')."<br>\n";
	}
}
?>
