<?php
if ($this->_tpl_vars['is_self_page']) {
	$title = ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M001');
} else {
	$title  = '<a href="' . $this->_tpl_vars['target_user_info_row']['top_page_url'] . '">';
	$title .= htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) . "さん";
	$title .= '</a>';
	$title .= ' :: <a href="' . $this->_tpl_vars['back_url'] . '">'.ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M002').'</a>';
}
?>
<div class="ttl"><?= $title ?> :: <?= ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M003') ?></div>

<?php
	// パス情報出力
	$path = "";
	foreach ($this->_tpl_vars['path_folder_row_array'] as $path_folder) {
		if ($path != "") {
			$path .= " / ";
		}
		$path .= '<a href="' . $path_folder['link_url'] . '">';
		$path .= htmlspecialchars($path_folder['folder_name']);
		$path .= '</a>';
	}

	print "<p>\n";
	print $path . "\n";
	print "</p>\n";
?>

<p>
<!-- layout_table start //-->
<table class="layout_table">
<tr>
	<td align="right">
	<?php
		if ($this->_tpl_vars['menu']['edit_folder_url']) {
			print '[<a href="' . $this->_tpl_vars['menu']['edit_folder_url'] . '">'.ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M004').'</a>]&nbsp;';
		}
		if ($this->_tpl_vars['menu']['move_folder_url']) {
			print '[<a href="' . $this->_tpl_vars['menu']['move_folder_url'] . '">'.ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M005').'</a>]&nbsp;';
		}
		if ($this->_tpl_vars['menu']['delete_folder_url']) {
			print '[<a href="' . $this->_tpl_vars['menu']['delete_folder_url'] . '">'.ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M006').'</a>]';
		}
	?>
	</td>
</tr>

<tr><td>
<table class="file_list_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M007') ?></td>
	<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['detail_folder_row']['folder_name']) ?><br></td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M008') ?></td>
	<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['detail_folder_row']['comment']) ?><br></td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M009') ?></td>
	<td bgcolor="#ffffff">
	<?php
		// 閲覧許可コミュニティ取得
		$trusted_community_info_array = array();
		if ($this->_tpl_vars['detail_folder_row']['trusted_community_row_array']) {
			foreach ($this->_tpl_vars['detail_folder_row']['trusted_community_row_array'] as $trusted_community_row) {
				$trusted_community_name = htmlspecialchars($trusted_community_row['community_name']);
				if (!$trusted_community_name) {
					continue;
				}

				array_push($trusted_community_info_array, $trusted_community_name);
			}
		}
		// 閲覧コミュニティが設定されていない場合、「対象なし」
		if ($this->_tpl_vars['is_self_page']
		    && $this->_tpl_vars['detail_folder_row']['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')
			&& count($this->_tpl_vars['detail_folder_row']['trusted_community_row_array']) == 0) {

			$trusted_community_info_array[0] = '*対象なし';
		}

		print htmlspecialchars($this->_tpl_vars['detail_folder_row']['open_level_name']);
		if (count($trusted_community_info_array) > 0) {
			$trusted_community_str = implode(", ", $trusted_community_info_array);
			print " ($trusted_community_str)";
		}
	?>
	<br></td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M010') ?></td>
	<td bgcolor="#ffffff">
		<a href="<?= $this->_tpl_vars['detail_folder_row']['entry_user_community_link_url'] ?>">
		<?= $this->_tpl_vars['detail_folder_row']['entry_user_community_name'] ?>
		</a>
		(<?= $this->_tpl_vars['detail_folder_row']['entry_date'] ?>)
	</td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M011') ?></td>
	<td bgcolor="#ffffff">
		<a href="<?= $this->_tpl_vars['detail_folder_row']['update_user_community_link_url'] ?>">
		<?= $this->_tpl_vars['detail_folder_row']['update_user_community_name'] ?>
		</a>
		(<?= $this->_tpl_vars['detail_folder_row']['update_date'] ?>)
	</td>
</tr>
</table>

</td></tr>
</table>
<!-- layout_table end //-->
</p>

<?php
// プット可能なフォルダの場合のみ表示する
if ($this->_tpl_vars['is_put_available']) {
?>
<p>
<table class="file_list_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M012') ?></td>
	<td bgcolor="#ffffff">
	<?php
	if ($this->_tpl_vars['detail_folder_row']['put_community_row_array']) {
		//print '<table class="common_table" border>';
		print '<table class="file_list_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">';
		$put_community_string_array = array();
		foreach ($this->_tpl_vars['detail_folder_row']['put_community_row_array'] as $put_community_row) {
			print '<tr>';

			// プット先コミュニティ
			print '<td bgcolor="#ffffff">';
			print '<a href="' . $put_community_row['top_page_url'] . '">';
			print $put_community_row['community_name'];
			print '</a>';
			print '</td>';

			// プット先フォルダ
			print '<td bgcolor="#ffffff">';
			print '<table class="layout_table"><tr><td>';
			// フォルダ画像
			print '<img src="' . ACS_IMAGE_DIR . 'folder.png">';
			print '</td><td>';
			print '<a href="' . $put_community_row['folder_link_url'] . '">';
			print $put_community_row['folder_name'];
			print '</a>';
			print '</td></tr></table>';
			print '</td>';

			print '</tr>';
		}
		print '</table>';
	} else {
		// プット先コミュニティなし
		print ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M013');
	}
	?>
	</td>
</tr>
</table>
</p>
<?php } ?>

<p>
<a href="<?= $this->_tpl_vars['back_url'] ?>"><?= ACSMsg::get_msg("User", "FolderDetail.tpl.php",'M014') ?></a>
</p>
