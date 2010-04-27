<?php
// $Id: FriendsGroupList.tpl.php,v 1.4 2006/11/20 08:44:26 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "FriendsGroupList.tpl.php",'M001') ?>
 (<?= $this->_tpl_vars['friends_group_row_array_num'] ?><?= ACSMsg::get_msg("User", "FriendsGroupList.tpl.php",'M002') ?>)</div><br>

<a class="ichiran3" href="<?= $this->_tpl_vars['create_friends_group_url'] ?>"><?= ACSMsg::get_msg("User", "FriendsGroupList.tpl.php",'M003') ?></a><br>
<br>

<?php
foreach ($this->_tpl_vars['friends_group_row_array'] as $friends_group_row) {
?>

<table border="0" cellpadding="4" cellspacing="1" bgcolor="#CCCC33" style="margin:10px;">
<tr>
<td bgcolor="#F6F2B8" id="myttl" style="padding:5px;">

<?php
	echo htmlspecialchars($friends_group_row['community_name']);
	echo " (" . $friends_group_row['friends_row_array_num'] . ACSMsg::get_msg("User", "FriendsGroupList.tpl.php",'M004').")";
?>

</td>
</tr>
<tr>
<td bgcolor="#FFFFFF">
<table border="0" cellspacing="10" cellpadding="5">

<?php
$ccount = 0;
	if (count($friends_group_row['friends_row_array'])) {
		$count = 0;
		foreach ($friends_group_row['friends_row_array'] as $user_info_row) {
			if ($count % 6 == 0) {
				echo "<tr>";
			}
			$ccount++;
			echo "<td class=\"mytbl\" align=\"center\">";
			echo "<a href=\"$user_info_row[top_page_url]\"><img style=\"border:none\" src=\"$user_info_row[image_url]\"><br>" . htmlspecialchars($user_info_row['community_name']) . "</a><br>";
			echo "</td>";
			if ($count % 6 == 5) {
				echo "</tr>\n";
			}
			$count++;
		}
	} else {
		echo "<tr>";
		echo "<td>".ACSMsg::get_msg("User", "FriendsGroupList.tpl.php",'M005')."</td>";
		echo "</tr>\n";
	}
?>
<tr>
<td clospan="<?= $ccount ?>" style="padding:5px;" bgcolor="ffffff">
<?php
//echo "[ ";
	echo "<a href=\"$friends_group_row[edit_friends_group_member_url]\">".ACSMsg::get_msg("User", "FriendsGroupList.tpl.php",'M006')."</a>";
	echo " | ";
	echo "<a href=\"$friends_group_row[delete_friends_group_url]\">".ACSMsg::get_msg("User", "FriendsGroupList.tpl.php",'M007')."</a>";
//echo " ]";
	echo "<br>\n";
?>

</table>
</td>
</tr>
</table>
<?php
}
?>
