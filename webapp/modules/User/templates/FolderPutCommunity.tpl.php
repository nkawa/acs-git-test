<div class="ttl"><?= ACSMsg::get_msg("User", "FolderPutCommunity.tpl.php",'M001') ?></div>

<div class="msg">
<?= ACSMsg::get_msg("User", "FolderPutCommunity.tpl.php",'M002') ?><br>
</div>

<p>
<form name="select_put_community" method="POST" action="<?= $this->_tpl_vars['put_community_url'] ?>">
<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
foreach ($this->_tpl_vars['select_community_row_array'] as $select_community_row) {
	print '<tr>';

	// コミュニティ名
	print '<td bgcolor="#ffffff">';
	print '<a href="' . $select_community_row['top_page_url'] . '" target="_blank">';
	print htmlspecialchars($select_community_row['community_name']);
	print '</a>';
	print '</td>';
	print "\n";

	// フォルダ選択の select ボックス
	print '<td bgcolor="#ffffff">';
	if (count($select_community_row['folder_tree'])) {
		print '<select name="selected_put_folder_id[' . $select_community_row['community_id'] . ']">';
		print '<option value="">';
		foreach ($select_community_row['folder_tree'] as $folder_row) {
			$selected_str = "";
			if ($folder_row['is_selected']) {
				$selected_str = " selected";
			}
			print '<option value="' . $folder_row['folder_id'] . '"' . $selected_str . '>';
			print str_repeat("-", $folder_row['tree_level']);
			print htmlspecialchars($folder_row['folder_name']);
			print "\n";
		}
		print '</select>';
		print "\n";

	} else {
		// コミュニティにフォルダがない場合
		print ACSMsg::get_msg("User", "FolderPutCommunity.tpl.php",'M003');
	}
	print '</td>';

	print '</tr>' . "\n";
}
?>
</table>
<input type="checkbox" value="t" name="send_announce_mail" <?= $this->_tpl_vars['send_annouce_mail_checked'] ?>>
    <?= ACSMsg::get_msg("User", "FolderPutCommunity.tpl.php",'M005') ?>
</p>

<p>
<input type="submit" value="<?= ACSMsg::get_msg("User", "FolderPutCommunity.tpl.php",'M004') ?>">
</p>
</form>
