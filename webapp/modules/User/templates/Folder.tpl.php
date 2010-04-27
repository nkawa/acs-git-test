<?php
if ($this->_tpl_vars['is_self_page']) {
	$title = ACSMsg::get_msg("User", "Folder.tpl.php",'M001');
} else {
	$title  = '<a href="' . $this->_tpl_vars['target_user_info_row']['top_page_url'] . '">';
	//$title .= htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) . "さん";
	$title .= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "Folder.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	$title .= '</a>';
	$title .= ' :: '.ACSMsg::get_msg("User", "Folder.tpl.php",'M002');
}
?>
<div class="ttl"><?= $title ?>&nbsp;&nbsp;[<a href="<?= $this->_tpl_vars['search_folder_url'] ?>"><?= ACSMsg::get_msg("User", "Folder.tpl.php",'M003') ?></a>]</div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<?php
	// パス情報出力
	$path = "";
	foreach ($this->_tpl_vars['path_folder_obj_row_array'] as $path_folder_obj) {
		if ($path != "") {
			$path .= " / ";
		}
		$path .= '<a href="' . $path_folder_obj['link_url'] . '">';
		$path .= htmlspecialchars($path_folder_obj['name']);
		$path .= '</a>';
	}

	print "<p>\n";
	print $path . "\n";
	print "</p>\n";
?>

<?php
	// 公開範囲表示
	$target_folder_open_level_row = $this->_tpl_vars['target_folder_open_level_row'];

	// 閲覧許可コミュニティ取得
	$trusted_community_info_array = array();
	// 本人のみ表示
	if ($this->_tpl_vars['is_self_page']) {
		foreach ($target_folder_open_level_row['trusted_community_row_array'] as $trusted_community_row) {
			$trusted_community_name = htmlspecialchars($trusted_community_row['community_name']);
			if (!$trusted_community_name) {
				continue;
			}

			array_push($trusted_community_info_array, $trusted_community_name);
		}
		// 閲覧コミュニティが設定されていない場合、「対象なし」
		if ($target_folder_open_level_row['name'] == ACSMsg::get_mst('open_level_master','D05')
			&& count($target_folder_open_level_row['trusted_community_row_array']) == 0) {

			//$trusted_community_info_array[0] = '*対象なし';
			$trusted_community_info_array[0] = ACSMsg::get_msg("User", "Folder.tpl.php",'M025');
		}
	}
	print '<table class="open_level_table"><tr><td>';
	print ACSMsg::get_msg("User", "Folder.tpl.php",'M004')." : ";
	print $target_folder_open_level_row['name'];
	if (count($trusted_community_info_array) > 0) {
		$trusted_community_str = implode(", ", $trusted_community_info_array);
		print " ($trusted_community_str)";
	}
	print '</td></tr></table>';
	print "\n";
?>


<?php if ($this->_tpl_vars['is_self_page']) { ?>
	<form name="upload_file" action="<?= $this->_tpl_vars['upload_file_url'] ?>" method="POST" enctype="multipart/form-data">
	<p>
	<span style="border-style: solid; border-width: 1px; border-color: #555555; background: #FFF5AA; padding: 10px; margin: 5px 0 5px 0;">
	<input type="file" name="new_file" size="30">
	<input type="submit" value="<?= ACSMsg::get_msg("User", "Folder.tpl.php",'M026') ?>">
	<a href="<?= $this->_tpl_vars['upload_file_url'] ?>"><?= ACSMsg::get_msg("User", "Folder.tpl.php",'M005') ?></a>
	</span>
	</p>
	</form>

	<p>
	<form name="operation" action="" method="POST">
	<input type="button" value="<?= ACSMsg::get_msg("User", "Folder.tpl.php",'M006') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['move_folder_url'] ?>')">
	<input type="button" value="<?= ACSMsg::get_msg("User", "Folder.tpl.php",'M007') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['rename_folder_url'] ?>')">
	<input type="button" value="<?= ACSMsg::get_msg("User", "Folder.tpl.php",'M008') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['delete_folder_url'] ?>')">
	<input type="button" value="<?= ACSMsg::get_msg("User", "Folder.tpl.php",'M009') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['edit_folder_url'] ?>')">
	</p>

	<script type="text/javascript">
	<!--
		function submit_operation (form_obj, submit_url) {
			form_obj.action = submit_url;
			form_obj.submit();
		}
	//-->
	</script>
<?php } ?>

<p>
<?php
	/* フォルダ一覧表示 */
	echo "[ ".ACSMsg::get_msg("User", "Folder.tpl.php",'M010')." | <a href=\"" . $this->_tpl_vars['folder_group_mode_url'] . "\">".ACSMsg::get_msg("User", "Folder.tpl.php",'M011')."</a> ]<br><br>\n";

	if (count($this->_tpl_vars['folder_row_array']) > 0 or count($this->_tpl_vars['file_row_array']) > 0 ) {
		print '<table class="file_list_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">' . "\n";
		print '<tr>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M012').'<br></th>' . "\n";
		if ($this->_tpl_vars['is_self_page']) {
			// 操作用チェックボックス分
			print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M013').'</th>' . "\n";
		}
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M014').'</th>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M015').'</th>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M016').'</th>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M017').'</th>' . "\n";
		if ($this->_tpl_vars['is_root_folder']) {
			print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M004').'</th>' . "\n";
			print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M019').'</th>' . "\n";
		}
		print '</tr>' . "\n";
	}

	foreach ($this->_tpl_vars['folder_row_array'] as $folder_row ) {
		print "<tr>\n";

		print "<td align=\"center\" bgcolor=\"#ffffff\">";
		/* 公開範囲設定 */
		if ($this->_tpl_vars['is_self_page'] && $this->_tpl_vars['is_root_folder']) {
			print '[<a href="' . $folder_row['folder_put_community_url'] . '" target="_blank">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M020').'</a>]<br>';
		}
		/* フォルダ詳細 */
		print '[<a href="' . $folder_row['detail_url'] . '">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M021').'</a>]';
		print "</td>\n";

		/* 操作用のチェックボックス */
		if ($this->_tpl_vars['is_self_page']) {
			print '<td bgcolor="#ffffff" align="center">';
			print '<input type="checkbox" name="selected_folder[]" value="' . $folder_row['folder_id'] . '">';
			print "</td>\n";
		}

		/* フォルダ画像 */
		print "<td bgcolor=\"#ffffff\">";
		print '<table class="layout_table"><tr>';
		print "<td bgcolor=\"#ffffff\">";
		print '<img src="' . ACS_IMAGE_DIR . 'folder.png">';
		print "</td>";

		/* フォルダ名 (リンク) */
		print "<td bgcolor=\"#ffffff\">";
		print '<a href="' . $folder_row['link_url'] . '">';
		print htmlspecialchars($folder_row['name']);
		print '</a>';
		print "</td>";
		print "</tr></table>";
		print "</td>\n";

		/* 更新者 */
		print "<td bgcolor=\"#ffffff\">";
		print '<a href="' . $folder_row['update_user_community_link_url'] . '">';
		print htmlspecialchars($folder_row['update_user_community_name']);
		print '</a>';
		print "</td>\n";

		/* 更新日 */
		print "<td bgcolor=\"#ffffff\">";
		print $folder_row['update_date'];
		print "</td>\n";

		/* サイズ */
		print "<td bgcolor=\"#ffffff\"><br></td>\n";

		/* 公開範囲 */
		if ($this->_tpl_vars['is_root_folder']) {
			// 閲覧許可コミュニティ取得
			$trusted_community_info_array = array();
			// 本人のみ表示
			if ($this->_tpl_vars['is_self_page']) {
				foreach ($folder_row['trusted_community_row_array'] as $trusted_community_row) {
					$trusted_community_name = htmlspecialchars($trusted_community_row['community_name']);
					if (!$trusted_community_name) {
						continue;
					}

					array_push($trusted_community_info_array, $trusted_community_name);
				}
				// 閲覧コミュニティが設定されていない場合、「対象なし」
				if ($folder_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')
					&& count($folder_row['trusted_community_row_array']) == 0) {

					//$trusted_community_info_array[0] = '*対象なし';
					$trusted_community_info_array[0] = ACSMsg::get_msg("User", "Folder.tpl.php",'M025');
				}
			}

			print "<td bgcolor=\"#ffffff\">";
			print $folder_row['open_level_name'];
			if (count($trusted_community_info_array) > 0) {
				$trusted_community_str = implode(", ", $trusted_community_info_array);
				print " ($trusted_community_str)";
			}
			print "<br>";
			print "</td>\n";
		}

		/* プット先コミュニティ */
		if ($this->_tpl_vars['is_root_folder']) {
			print "<td bgcolor=\"#ffffff\">\n";
			if ($folder_row['put_community_url']) {
				print '<a href="' . $folder_row['put_community_url'] . '" target="_blank">';
				print ACSMsg::get_msg("User", "Folder.tpl.php",'M022');
				print '</a>';
			} else {
				print ACSMsg::get_msg("User", "Folder.tpl.php",'M023');
			}
			print "<br>";
			print "</td>\n";
		}
	}
	foreach ($this->_tpl_vars['file_row_array'] as $file_row ) {
		print "<tr>\n";

		/* ファイル詳細 */
		print '<td align="center" bgcolor="#ffffff">';
		print '[<a href="' . $file_row['detail_url'] . '">'.ACSMsg::get_msg("User", "Folder.tpl.php",'M021').'</a>]';
		print '</td>' . "\n";

		if ($this->_tpl_vars['is_self_page']) {
			print '<td align="center" bgcolor="#ffffff">';
			print '<input type="checkbox" name="selected_file[]" value="' . $file_row['file_id'] . '">';
			print '</td>' . "\n";
		}

		/* ファイル画像 */
		print "<td bgcolor=\"#ffffff\">";
		print '<table class="layout_table"><tr>';
		print "<td bgcolor=\"#ffffff\">";
		print '<img src="' . ACS_IMAGE_DIR . 'file.gif">';
		print "</td>";

		/* ファイル名 (リンク) */
		print "<td bgcolor=\"#ffffff\">";
		print '<a href="' . $file_row['link_url'] . '">';
		print htmlspecialchars($file_row['name']);
		print '</a>';
		print "</td>";
		print "</tr></table>";
		print "</td>\n";

		/* 更新者 */
		print "<td bgcolor=\"#ffffff\">";
		print '<a href="' . $file_row['update_user_community_link_url'] . '">';
		print htmlspecialchars($file_row['update_user_community_name']);
		print '</a>';
		print "</td>\n";

		/* 更新日 */
		print "<td bgcolor=\"#ffffff\">";
		print $file_row['update_date'];
		print "</td>\n";

		/* サイズ */
		print '<td align="right" bgcolor="#ffffff">';
		print $file_row['file_size'];
		print "</td>\n";

		/* 公開範囲 */
		if ($this->_tpl_vars['is_root_folder']) {
			print "<td bgcolor=\"#ffffff\"><br></td>\n";
		}
		/* プット先コミュニティ */
		if ($this->_tpl_vars['is_root_folder']) {
			print "<td bgcolor=\"#ffffff\"><br></td>\n";
		}

		print "</tr>\n";
	}
	if (count($this->_tpl_vars['folder_row_array']) > 0 or count($this->_tpl_vars['file_row_array']) > 0 ) {
		print "</table>\n";
	}

?>
</p>
<?php if ($this->_tpl_vars['is_self_page']) { ?>
</form>
<?php } ?>
