<?php
// $Id: SearchFolder.tpl.php,v 1.6 2007/03/28 08:39:32 w-ota Exp $
?>

<?php
$title  = '<a href="' . $this->_tpl_vars['community_top_page_url'] . '">';
$title .= htmlspecialchars($this->_tpl_vars['community_row']['community_name']);
$title .= ' '.ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M001');
$title .= '</a>';
$title .= ' :: ';
$title .= '<a href="' . $this->_tpl_vars['folder_url'] . '">';
$title .= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M002');
$title .= '</a>';
?>
<div class="ttl"><?= $title ?> :: <?= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M003') ?></div>

<p>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="get">
<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
<input type="hidden" name="community_id" value="<?= $this->_tpl_vars['community_row']['community_id'] ?>">
<input type="hidden" name="search" value="1">

<table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
 <tr>
  <td bgcolor="#FFF5AA">
<table class="layout_table">
<tr>
<td>キーワード</td>
<td><input type="text" name="q" value="<?= htmlspecialchars($this->_tpl_vars['form']['q']) ?>" size="30"></td>
<td><input type="submit" value="<?= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M003') ?>"></td>
</tr>
<tr>
<td><?= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M005') ?></td>
<td>
<?php
unset($selected);
if ($this->_tpl_vars['form']['target'] == 'folder' || $this->_tpl_vars['form']['target'] == 'file') {
	$selected[$this->_tpl_vars['form']['target']] = ' checked';
} else {
	$selected['folder_file'] = ' checked';
}
?>
<input type="radio" name="target" value="folder_file"<?= $selected['folder_file'] ?>><?= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M006') ?>&nbsp;
<input type="radio" name="target" value="folder"<?= $selected['folder'] ?>><?= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M002') ?>&nbsp;
<input type="radio" name="target" value="file"<?= $selected['file'] ?>><?= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M008') ?>
</td>
</tr>
<tr>
<td><?= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M009') ?></td>
<td>
<?php
unset($selected);
if ($this->_tpl_vars['form']['order'] == 'update_date') {
	$selected[$this->_tpl_vars['form']['order']] = ' selected';
} else {
	$selected['name'] = ' selected';
}
?>
<select name="order">
<option value="name"<?= $selected['name'] ?>><?= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M010') ?>
<option value="update_date"<?= $selected['update_date'] ?>><?= ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M011') ?>
</select>
</td>
</tr>
</table>
</td></tr></table>

</form>
</p>


<?php
if ($this->_tpl_vars['form']['search']) {
	//echo "<table class=\"file_list_table\" border>\n";
	echo "<table class=\"file_list_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";
	echo "<tr>\n";
	echo "<th bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M012')."</th>\n";
	echo "<th bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M013')."</th>\n";
	echo "<th bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M014')."</th>\n";
	echo "<th bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("Community", "SearchFolder.tpl.php",'M015')."</th>\n";
	echo "</tr>\n";

	// フォルダ
	if (is_array($this->_tpl_vars['folder_row_array'])) {
		foreach ($this->_tpl_vars['folder_row_array'] as $folder_row) {
			echo "<tr>";
			echo "<td bgcolor=\"#ffffff\">";
			echo "<table class=\"layout_table\"><tr>";
			echo "<td><img src=\"" . ACS_IMAGE_DIR . "folder.png\"></td>";
			echo "<td><a href=\"$folder_row[folder_url]\">" . htmlspecialchars($folder_row['folder_name']) . "</a></td>";
			echo "</tr></table>";
			echo"</td>";
			echo "<td bgcolor=\"#ffffff\">$folder_row[update_date]</td>";
			echo "<td bgcolor=\"#ffffff\">&nbsp;</td>";
			echo "<td bgcolor=\"#ffffff\">" . htmlspecialchars($folder_row['path']) . "</td>";
			echo "</tr>\n";
		}
	}

	// ファイル
	if (is_array($this->_tpl_vars['file_info_row_array'])) {
		foreach ($this->_tpl_vars['file_info_row_array'] as $file_info_row) {
			echo "<tr>";
			echo "<td bgcolor=\"#ffffff\">";
			echo "<table class=\"layout_table\"><tr>";
			echo "<td><img src=\"" . ACS_IMAGE_DIR . "file.gif\"></td>";
			echo "<td><a href=\"$file_info_row[download_file_url]\">" . htmlspecialchars($file_info_row['display_file_name']) . "</a></td>";
			echo "</tr></table>";
			echo"</td>";
			echo "<td class=\"nowrap\" bgcolor=\"#ffffff\">$file_info_row[update_date]</td>";
			echo "<td class=\"nowrap\" bgcolor=\"#ffffff\">$file_info_row[file_size]</td>";
			echo "<td bgcolor=\"#ffffff\">" . htmlspecialchars($file_info_row['path']) . "</td>";
			echo "</tr>\n";
		}
	}

	// プットフォルダ
	if (is_array($this->_tpl_vars['put_folder_row_array'])) {
		foreach ($this->_tpl_vars['put_folder_row_array'] as $folder_row) {
			echo "<tr>";
			echo "<td bgcolor=\"#ffffff\">";
			echo "<table class=\"layout_table\"><tr>";
			echo "<td><img src=\"" . ACS_IMAGE_DIR . "put_folder.png\"></td>";
			echo "<td><a href=\"$folder_row[folder_url]\">" . htmlspecialchars($folder_row['folder_name']) . "</a></td>";
			echo "</tr></table>";
			echo"</td>";
			echo "<td bgcolor=\"#ffffff\">$folder_row[update_date]</td>";
			echo "<td bgcolor=\"#ffffff\">&nbsp;</td>";
			echo "<td bgcolor=\"#ffffff\">" . htmlspecialchars($folder_row['path']) . "</td>";
			echo "</tr>\n";
		}
	}

	// プットファイル
	if (is_array($this->_tpl_vars['put_file_info_row_array'])) {
		foreach ($this->_tpl_vars['put_file_info_row_array'] as $file_info_row) {
			echo "<tr>";
			echo "<td bgcolor=\"#ffffff\">";
			echo "<table class=\"layout_table\"><tr>";
			echo "<td><img src=\"" . ACS_IMAGE_DIR . "put_file.gif\"></td>";
			echo "<td><a href=\"$file_info_row[download_file_url]\">" . htmlspecialchars($file_info_row['display_file_name']) . "</a></td>";
			echo "</tr></table>";
			echo"</td>";
			echo "<td bgcolor=\"#ffffff\">$file_info_row[update_date]</td>";
			echo "<td bgcolor=\"#ffffff\">$file_info_row[file_size]</td>";
			echo "<td bgcolor=\"#ffffff\">" . htmlspecialchars($file_info_row['path']) . "</td>";
			echo "</tr>\n";
		}
	}

	echo "</table>\n";
}
?>
