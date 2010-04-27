<?
// $Id: CommunityMemberList.tpl.php,v 1.7 2007/03/14 04:28:17 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "CommunityMemberList.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "CommunityMemberList.tpl.php",'M002') ?>
(<?= $this->_tpl_vars['community_member_user_info_row_array_num'] ?>¿Í)
</div><br><br>

<?php
if ($this->_tpl_vars['friends_group_list_url'] != '') {
	echo "<a href=\"$this->_tpl_vars[friends_group_list_url]\">".ACSMsg::get_msg("Community", "CommunityMemberList.tpl.php",'M003')."</a><br><br>\n";
}
?>

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr><td bgcolor="#ffffff">
<table>
<?php
if (count($this->_tpl_vars['community_member_user_info_row_array'])) {
	$count = 0;
	foreach ($this->_tpl_vars['community_member_user_info_row_array'] as $user_info_row) {
		if ($count % 4 == 0) {
			echo "<tr>";
		}
		echo "<td align=\"center\">";
		echo "<a href=\"$user_info_row[top_page_url]\"><img src=\"$user_info_row[image_url]\" border=\"0\"></a><br>";
		echo "<a href=\"$user_info_row[top_page_url]\">" . htmlspecialchars($user_info_row['community_name']) . "</a>";
		echo "($user_info_row[friends_row_array_num])<br>";
		echo "</td>";
		if ($count % 4 == 3) {
			echo "</tr>\n";
		}
		$count++;
	}
} else {
	echo "<tr>";
	echo "<td>".ACSMsg::get_msg("Community", "CommunityMemberList.tpl.php",'M004')."</td>";
	echo "</tr>\n";
}
?>
</table>
</tr></td>
</table>
<br>
