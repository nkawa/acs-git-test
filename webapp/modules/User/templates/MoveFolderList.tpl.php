<div class="sub_title"><?= ACSMsg::get_msg("User", "MoveFolderList.tpl.php",'M001') ?></div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<form name="rename_folder_list" method="POST" action="<?= $this->_tpl_vars['action_url'] ?>">

<p>
<table class="file_list_table">
<?php
	// �ѹ��оݤΥե����
	if ($this->_tpl_vars['folder_row_array']) {
		foreach ($this->_tpl_vars['folder_row_array'] as $folder_row) {
			print '<tr>';

			// ��������
			print '<td>';
			print '<table class="layout_table"><tr>';
			print '<td>';
			print '<img src="' . ACS_IMAGE_DIR . 'folder.png">';
			print '</td>';

			// �ե����̾
			print '<td>';
			print '<input type="hidden" name="selected_folder[]" value="' . $folder_row['folder_id'] . '">';
			print htmlspecialchars($folder_row['folder_name']);
			print '</td>';
			print '</tr></table>';

			print '</tr>'. "\n";
		}
	}

	// �ѹ��оݤΥե�����
	if ($this->_tpl_vars['file_row_array']) {
		foreach ($this->_tpl_vars['file_row_array'] as $file_row) {
			print '<tr>';

			// ��������
			print '<td>';
			print '<table class="layout_table"><tr>';
			print '<td>';
			print '<img src="' . ACS_IMAGE_DIR . 'file.gif">';
			print '</td>';

			// �ե�����̾
			print '<td>';
			print '<input type="hidden" name="selected_file[]" value="' . $file_row['file_id'] . '">';
			print htmlspecialchars($file_row['file_name']);
			print '</td>';
			print '</tr></table>';

			print '</tr>'. "\n";
		}
	}
?>
</table>
</p>

<p>
<?php
	// ��ư��ե������������
	if (count($this->_tpl_vars['folder_tree'])) {
		print ACSMsg::get_msg("User", "MoveFolderList.tpl.php",'M002').'��';
		print '<select name="selected_move_folder_id">';
		print '<option value="">'.ACSMsg::get_msg("User", "MoveFolderList.tpl.php",'M003');
		foreach ($this->_tpl_vars['folder_tree'] as $folder_row) {
			$selected_str = "";
			if ($folder_row['is_selected']) {
				$selected_str = " selected";
			}
			print '<option value="' . $folder_row['folder_id'] . '"' . $selected_str . '>';
			print str_repeat("-", $folder_row['tree_level']);
			print ' ' . htmlspecialchars($folder_row['folder_name']);
			print "\n";
		}
		print '</select>';
		print "\n";

	} else {
		// �ե�������ʤ����
		print ACSMsg::get_msg("User", "MoveFolderList.tpl.php",'M004');
	}
?>
</p>

<input type="submit" value="<?= ACSMsg::get_msg("User", "MoveFolderList.tpl.php",'M005') ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("User", "MoveFolderList.tpl.php",'M006') ?>" onClick="location.href='<?= $this->_tpl_vars['cancel_url'] ?>'">
</form>
