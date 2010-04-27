<?php
// $Id: Log.tpl.php,v 1.3 2007/03/01 09:01:41 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("System", "Log.tpl.php",'M001') ?></div>

<p>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="get">
<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
<input type="hidden" name="search" value="1">
<table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
 <tr>
  <td bgcolor="#FFF5AA">
キーワード <input type="text" name="q" value="<?= htmlspecialchars($this->_tpl_vars['form']['q']) ?>" size="30">
<input type="submit" value="<?= ACSMsg::get_msg("System", "Log.tpl.php",'M002') ?>">
</tr></td></table>
</form>
</p>

<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33" width="665">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "Log.tpl.php",'M003') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "Log.tpl.php",'M004') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "Log.tpl.php",'M005') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "Log.tpl.php",'M006') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "Log.tpl.php",'M007') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "Log.tpl.php",'M008') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "Log.tpl.php",'M009') ?></th>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "Log.tpl.php",'M010') ?></th>
</td>
</tr>
<?php
foreach ($this->_tpl_vars['log_row_array'] as $log_row) {
	echo "<tr>";
	echo "<td align=\"center\" bgcolor=\"#ffffff\">$log_row[log_id]</td>";
	echo "<td bgcolor=\"#ffffff\">$log_row[log_date]</td>";
	echo "<td bgcolor=\"#ffffff\">$log_row[user_id]</td>";
	echo "<td bgcolor=\"#ffffff\">" . htmlspecialchars($log_row['user_name']) . "</td>";
	echo "<td bgcolor=\"#ffffff\"><a href=\"$log_row[top_page_url]\">" . htmlspecialchars($log_row['community_name']) .  "</a></td>";
	echo "<td bgcolor=\"#ffffff\">" . htmlspecialchars($log_row['user_level_name']) .  "</td>";
	echo "<td bgcolor=\"#ffffff\">" . htmlspecialchars($log_row['message']) .  "</td>";
	echo "<td bgcolor=\"#ffffff\">";
	// 失敗
	if ($log_row['operation_result_name'] == ACSMsg::get_msg("System", "Log.tpl.php",'M011')) {
		echo "<span class=\"err_msg\">" . htmlspecialchars($log_row['operation_result_name']) . "</a>";
	} else {
		echo htmlspecialchars($log_row['operation_result_name']);
	}
	echo "</td>";
	echo "</tr>\n";
}
?>
</table>
<br>
