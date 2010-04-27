<?php
// $Id: CommunityList.tpl.php,v 1.7 2007/03/28 02:51:47 w-ota Exp $
?>

<?php
echo "<div class=\"ttl\">";
if ($this->_tpl_vars['is_self_page']) {
	echo ACSMsg::get_msg("User", "CommunityList.tpl.php",'M001');
} else {
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_top_page_url'] ."\">";
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "CommunityList.tpl.php",'NAME'),array(
		"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	echo "</a> :: ";
	echo ACSMsg::get_msg("User", "CommunityList.tpl.php",'M002');
}

echo " (" .$this->_tpl_vars['community_row_array_num'] .")";
echo "</div><br>\n";

?>

<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>

<table width="650" border="0" cellpadding="8" cellspacing="1" bgcolor="#99CC33">
<tr>
<td bgcolor="#ffffff">
<table width="100%" border="0" cellspacing="10" cellpadding="5">
<?php
if (count($this->_tpl_vars['community_row_array'])) {
	$count = 0;
	foreach ($this->_tpl_vars['community_row_array'] as $community_row) {
		if ($count % 4 == 0) {
			echo "<tr>";
		}
		echo "<td align=\"center\">";
		echo "<a href=\"$community_row[top_page_url]\"><img src=\"$community_row[image_url]\" border=\"0\"></a><br>";
		echo "<a href=\"$community_row[top_page_url]\">" . htmlspecialchars($community_row['community_name']) . "</a>";
		echo "(" . $community_row['community_member_num'] . ")";
		echo "</td>";
		if ($count % 4 == 3) {
			echo "</tr>\n";
		}
		$count++;
	}
} else {
	echo "<tr>";
	echo "<td><?= ACSMsg::get_msg('User', 'CommunityList.tpl.php','M003') ?></td>";
	echo "</tr>\n";
}
?>
</table>
</tr></td>
</table>
<br>
