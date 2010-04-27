<div class="sub_title"><?= ACSMsg::get_msg("Community", "RenameFolderList.tpl.php",'M001') ?></div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<form name="rename_folder_list" method="POST" action="<?= $this->_tpl_vars['action_url'] ?>">

<p>
<table class="file_list_table">
<?php
	// 変更対象のフォルダ
	if ($this->_tpl_vars['folder_row_array']) {
		foreach ($this->_tpl_vars['folder_row_array'] as $folder_row) {
			print '<tr>';

			// アイコン
			print '<td>';
			print '<table class="layout_table"><tr>';
			print '<td>';
			print '<img src="' . ACS_IMAGE_DIR . 'folder.png">';
			print '</td>';

			// フォルダ名
			print '<td>';
			print '<input type="hidden" name="selected_folder[]" value="' . $folder_row['folder_id'] . '">';
			print htmlspecialchars($folder_row['folder_name']);
			print '</td>';
			print '</tr></table>';

			// 新フォルダ名入力フォーム
			print '<td>';
			print '<input type="text" name="new_folder_name[\'' . $folder_row['folder_id'] . '\']">';
			print '</td>';

			print '</tr>'. "\n";
		}
	}

	// 変更対象のファイル
	if ($this->_tpl_vars['file_row_array']) {
		foreach ($this->_tpl_vars['file_row_array'] as $file_row) {
			print '<tr>';

			// アイコン
			print '<td>';
			print '<table class="layout_table"><tr>';
			print '<td>';
			print '<img src="' . ACS_IMAGE_DIR . 'file.gif">';
			print '</td>';

			// ファイル名
			print '<td>';
			print '<input type="hidden" name="selected_file[]" value="' . $file_row['file_id'] . '">';
			print htmlspecialchars($file_row['file_name']);
			print '</td>';
			print '</tr></table>';

			// 新ファイル名入力フォーム
			print '<td>';
			print '<input type="text" name="new_file_name[\'' . $file_row['file_id'] . '\']">';
			print '</td>';

			print '</tr>'. "\n";
		}
	}
?>
</table>
</p>

<input type="submit" value="<?= ACSMsg::get_msg("Community", "RenameFolderList.tpl.php",'M002') ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("Community", "RenameFolderList.tpl.php",'M003') ?>" onClick="location.href='<?= $this->_tpl_vars['cancel_url'] ?>'">
</form>
