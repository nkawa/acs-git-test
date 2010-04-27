<?php
// $Id: SystemAnnounceList.tpl.php,v 1.4 2007/03/01 09:01:41 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M001') ?></div><br>

<a href="<?= $this->_tpl_vars['create_system_announce_url'] ?>"><?= ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M002') ?></a><br>
<br>

<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M003') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M004') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M005') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M006') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M007') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M008') ?></th>
</tr>
<?php
foreach ($this->_tpl_vars['system_announce_row_array'] as $system_announce_row) {
	echo "<tr>";
	echo "<td class=\"nowrap\" bgcolor=\"#ffffff\">";
	echo $system_announce_row['post_date'];
	echo "</td>";
	echo "<td class=\"nowrap\" bgcolor=\"#ffffff\">";
	echo "<a href=\"$system_announce_row[top_page_url]\">" . htmlspecialchars($system_announce_row['community_name']) . "</a>";
	echo "</td>";
	echo "<td bgcolor=\"#ffffff\">";
	echo $system_announce_row['subject'];
	echo "</td>";
	echo "<td bgcolor=\"#ffffff\">";
	echo nl2br(htmlspecialchars($system_announce_row['body']));
	echo "</td>";
	echo "<td bgcolor=\"#ffffff\">";
	echo $system_announce_row['expire_date'];
	echo "</td>";
	echo "<td class=\"nowrap\" align=\"center\" bgcolor=\"#ffffff\">";
	if ($system_announce_row['system_announce_delete_flag']) {
		// 途中で掲載中止した場合
		echo "<font color=\"#ff0000\"><b>".ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M009')."</b></font>";
	} else {
		if ($system_announce_row['is_expire']) {
			// 掲載期限に達した場合
			echo ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M010');
		} else {
			// 現在掲載中の場合
			echo "<font color=\"#00aa00\"><b>".ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M011')."</b></font>";
			echo " <input type=\"button\" value=\"".ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M012')."\" onclick=\"location.href='$system_announce_row[delete_system_announce_url]'\">";
		}
	}
	echo "</td>";
	echo "</tr>\n";
}

if (count($this->_tpl_vars['system_announce_row_array']) == 0) {
	echo "<tr><td colspan=\"6\" bgcolor=\"#ffffff\">".ACSMsg::get_msg("System", "SystemAnnounceList.tpl.php",'M013')."</td></tr>\n";
}
?>
</table>
<br>
