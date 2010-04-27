<?php
	$title  = '<a href="' . $this->_tpl_vars['community_top_page_url'] . '">';
	$title .= htmlspecialchars($this->_tpl_vars['target_user_community_name']);
	$title .= ' '.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M001');
	$title .= '</a>';
	$title .= " :: ".ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M002');
?>
<div class="ttl"><?= $title ?>&nbsp;&nbsp;[<a href="<?= $this->_tpl_vars['search_folder_url'] ?>"><?= ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M003') ?></a>]</div>

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
	foreach ($target_folder_open_level_row['trusted_community_row_array'] as $trusted_community_row) {
		$trusted_community_info = "";
		$trusted_community_name = htmlspecialchars($trusted_community_row['community_name']);

		$trusted_community_info  = '<a href="' . $trusted_community_row['community_top_page_url'] . '">';
		$trusted_community_info .= htmlspecialchars($trusted_community_row['community_name']);
		$trusted_community_info .= '</a>';

		array_push($trusted_community_info_array, $trusted_community_info);
	}

	print '<table class="open_level_table"><tr><td>';
	print ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M004')." : ";
	print $target_folder_open_level_row['name'];
	if (count($trusted_community_info_array) > 0) {
		$trusted_community_str = implode(", ", $trusted_community_info_array);
		print " ($trusted_community_str)";
	}
	print '</td></tr></table>';
	print "\n";
?>

<?php if ($this->_tpl_vars['is_community_member']) { ?>
	<form name="upload_file" action="<?= $this->_tpl_vars['upload_file_url'] ?>" method="POST" enctype="multipart/form-data">
	<p>
	<span style="border-style: solid; border-width: 1px; border-color: #555555; background: #FFF5AA; padding: 10px; margin: 5px 0 5px 0;">
	<input type="file" name="new_file" size="30">
	<input type="submit" value="<?= ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M005') ?>">
	<a href="<?= $this->_tpl_vars['upload_file_url'] ?>"><?= ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M006') ?></a>
	</span>
	</p>
	</form>

	<p>
	<form name="operation" action="" method="POST">
	<input type="button" value="<?= ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M007') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['move_folder_url'] ?>')">
	<input type="button" value="<?= ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M008') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['rename_folder_url'] ?>')">
	<input type="button" value="<?= ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M009') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['delete_folder_url'] ?>')">
	<?php
	if ($this->_tpl_vars['edit_folder_url']) {
		print '<input type="button" value="'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M010').'" onClick="submit_operation(this.form, \'' . $this->_tpl_vars['edit_folder_url'] . '\')">';
	}
	?>
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
	echo "[ <a href=\"$this->_tpl_vars[folder_url]\">".ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M011')."</a> | ".ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M012')." ]<br><br>";

	if (count($this->_tpl_vars['folder_row_array']) > 0) {
		print '<table class="file_list_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">' . "\n";
		print '<tr>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M013').'</th>' . "\n";
		if ($this->_tpl_vars['is_community_member']) {
			// 操作用チェックボックス分
			print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M014').'</th>' . "\n";
		}
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M015').'</th>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M016').'</th>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M017').'</th>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M018').'</th>' . "\n";
		if ($this->_tpl_vars['is_root_folder']) {
			print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M004').'</th>' . "\n";
		}
		print '</tr>' . "\n";
	}

	foreach ($this->_tpl_vars['folder_row_array'] as $folder_row ) {
		print "<tr>\n";

		/* フォルダ詳細 */
		print '<td align="center" bgcolor="#ffffff" class=\"nowrap\">';
		print '[<a href="' . $folder_row['detail_url'] . '">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M020').'</a>]';
		print "</td>\n";

		/* 操作用のチェックボックス */
		if ($this->_tpl_vars['is_community_member']) {
			if ($folder_row['is_put']) {
				$disabled_str = " disabled";
			} else {
				$disabled_str = "";
			}
			print '<td bgcolor="#ffffff" align="center">';
			print '<input type="checkbox" name="selected_folder[]" value="' . $folder_row['folder_id'] . '"' . $disabled_str . '>';
			print "</td>\n";
		}

		/* フォルダ画像 */
		if ($folder_row['is_put']) {
			$folder_img = ACS_IMAGE_DIR . 'put_folder.png';
		} else {
			$folder_img = ACS_IMAGE_DIR . 'folder.png';
		}
		print '<td bgcolor="#ffffff">';
		print '<table class="layout_table"><tr>';
		print "<td>";
		print '<img src="' . $folder_img . '">';
		print "</td>";

		/* フォルダ名 (リンク) */
		print "<td>";
		print '<a href="' . $folder_row['link_url'] . '">';
		print htmlspecialchars($folder_row['name']);
		print '</a>';
		print "</td>";
		print "</tr></table>";
		print "</td>\n";

		/* 更新者 */
		print '<td bgcolor="#ffffff">';
		print '<a href="' . $folder_row['update_user_community_link_url'] . '">';
		print htmlspecialchars($folder_row['update_user_community_name']);
		print '</a>';
		print "</td>\n";

		/* 更新日 */
		print '<td bgcolor="#ffffff">';
		print htmlspecialchars($folder_row['update_date']);
		print "</td>\n";

		/* サイズ */
		print "<td bgcolor=\"#ffffff\"><br></td>\n";

		/* 公開範囲 */
		if ($this->_tpl_vars['is_root_folder']) {
			// 閲覧許可コミュニティ取得
			$trusted_community_info_array = array();
			foreach ($folder_row['trusted_community_row_array'] as $trusted_community_row) {
				$trusted_community_info = "";
				$trusted_community_name = htmlspecialchars($trusted_community_row['community_name']);

				$trusted_community_info  = '<a href="' . $trusted_community_row['community_top_page_url'] . '">';
				$trusted_community_info .= htmlspecialchars($trusted_community_row['community_name']);
				$trusted_community_info .= '</a>';

				array_push($trusted_community_info_array, $trusted_community_info);
			}

			print '<td bgcolor="#ffffff">';
			print $folder_row['open_level_name'];
			if (count($trusted_community_info_array) > 0) {
				$trusted_community_str = implode(", ", $trusted_community_info_array);
				print " ($trusted_community_str)";
			}
			print "<br>";
			print "</td>\n";
		}
		print "</tr>\n";
	}
	if (count($this->_tpl_vars['folder_row_array']) > 0) {
		print "</table><br>\n";
	}
?>
<?php
// グループ表示


foreach ($this->_tpl_vars['file_contents_type_master_row_array_array'] as $file_contents_type_master_row_array) {
	$file_category_code = $file_contents_type_master_row_array['file_category_code'];
	$file_category_name = $file_contents_type_master_row_array['file_category_name'];

	// ファイルカテゴリコードごとのファイル詳細情報の連想配列が1件以上ある場合
	if (count($this->_tpl_vars['file_detail_info_row_array_array'][$file_category_code]['file_detail_info_row_array']) > 0) {
		echo "<div class=\"subsub_title\">";
		echo $file_contents_type_master_row_array['file_category_name'];
		echo "</div>\n";

		echo '<table class="file_list_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">';
		echo "<tr>";
		if ($this->_tpl_vars['is_community_member']) {
			echo '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M013')."</th>";
		}
		echo '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M014')."</th>";
		echo '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M015')."</th>";

		// ファイルコンテンツごとのタイトル表示
		foreach ($this->_tpl_vars['file_contents_type_master_row_array_array'][$file_category_code]['file_contents_type_master_row_array']
				 as $file_contents_type_master_row) {
			echo '<th id="myttl" bgcolor="#DEEEBD">';
			echo htmlspecialchars($file_contents_type_master_row['file_contents_type_name']);
			echo "</th>";
		}
		echo "</tr>\n";

		// ファイル詳細情報
		foreach ($this->_tpl_vars['file_detail_info_row_array_array'][$file_category_code]['file_detail_info_row_array'] as $file_detail_info_row) {
			echo "<tr>\n";

			// 基本情報
			echo "<td align=\"center\" bgcolor=\"#ffffff\" class=\"nowrap\">[<a href=\"$file_detail_info_row[file_detail_url]\">".ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M020')."</a>]</td>\n";

			if ($file_detail_info_row['is_put']) {
				$disabled_str = " disabled";
			} else {
				$disabled_str = "";
			}
			if ($this->_tpl_vars['is_community_member']) {
				echo "<td bgcolor=\"#ffffff\" align=\"center\"><input type=\"checkbox\" name=\"selected_file[]\" value=\"$file_detail_info_row[file_id]\"$disabled_str></td>\n";
			}

			if ($file_category_name == ACSMsg::get_msg("Community", "Folder_group.tpl.php",'M025')) {
				echo "<td bgcolor=\"#ffffff\" align=\"center\">";
				echo "<img src=\"$file_detail_info_row[image_url]\"><br>";
				echo "<a href=\"$file_detail_info_row[link_url]\">$file_detail_info_row[display_file_name]</a>";
				echo "</td>\n";
			} else {
				echo "<td bgcolor=\"#ffffff\"><a href=\"$file_detail_info_row[link_url]\">$file_detail_info_row[display_file_name]</a></td>\n";
			}

			// ファイルコンテンツごとのコンテンツ表示
			foreach ($this->_tpl_vars['file_contents_type_master_row_array_array'][$file_category_code]['file_contents_type_master_row_array']
					 as $file_contents_type_master_row) {
				echo "<td bgcolor=\"#ffffff\">";
				$file_contents_value = $file_detail_info_row['file_contents_row_array'][$file_contents_type_master_row['file_contents_type_code']]['file_contents_value'];
				if ($file_contents_value != '') {
					echo nl2br(htmlspecialchars($file_contents_value));
				} else {
					echo "&nbsp;";
				}
				echo "</td>";
			}

			echo "</tr>\n";
		}

		echo "</table><br>\n";
	}
}

?>
</p>
<?php if ($this->_tpl_vars['is_community_member']) { ?>
</form>
<?php } ?>
