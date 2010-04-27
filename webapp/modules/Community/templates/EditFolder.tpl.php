<?php
if ($this->_tpl_vars['view_mode'] == 'create') {
	$title = ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M001');
	$submit_button_name = ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M002');
} elseif ($this->_tpl_vars['view_mode'] == 'update') {
	$title = ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M003');
	$submit_button_name = ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M004');
}
?>
<div class="ttl"><?= $title ?></div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<form name="folder_info" method="POST" action="<?= $this->_tpl_vars['action_url'] ?>">

<input type="hidden" name="except_community_id_array[]" value="<?= $this->_tpl_vars['target_community_info_row']['community_id'] ?>">

<p>
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M005') ?></td>
	<td bgcolor="#ffffff"><input type="text" name="folder_name" value="<?= htmlspecialchars($this->_tpl_vars['default_data_row']['folder_name']) ?>"></td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M006') ?></td>
	<td bgcolor="#ffffff"><input type="text" name="comment" value="<?= htmlspecialchars($this->_tpl_vars['default_data_row']['comment']) ?>"></td>
</tr>

<?php
// 公開範囲が設定できる場合のみ、出力
if ($this->_tpl_vars['is_set_open_level_available']) {
?>
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M009') ?></td>
	<td bgcolor="#ffffff">
		<select name="open_level_code" onChange="show_open_level_option(this.options[this.selectedIndex].text)">
		<?php
		// 選択肢作成
		$trusted_community_display_mode = 'none';
		foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
			if ($open_level_master_row['is_default']) {
				$selected_str = " selected";

				// 選択されている公開範囲が「友人に公開」の場合は、trusted_community を表示する
				if ($open_level_master_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D04')) {
					$trusted_community_display_mode = 'block';
				}
			} else {
				$selected_str = "";
			}
			print '<option value="' . $open_level_master_row['open_level_code'] . '"' . $selected_str . '>' . htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
		}
		?>
		</select>

		<div id="trusted_community_div" style="display:<?= $trusted_community_display_mode ?>;">
			<table class="layout_table">
			<tr><td id="trusted_community_td">
			<?php
			// 出力済みコミュニティ
			$output_community_id_array = array();

			// 親コミュニティ出力
			if ($this->_tpl_vars['parent_community_info_array']) {
				foreach ($this->_tpl_vars['parent_community_info_array'] as $parent_community_info) {
					if ($this->_tpl_vars['default_data_row']['trusted_community_id_array']) {
						if (in_array($sub_community_info['community_id'], $this->_tpl_vars['default_data_row']['trusted_community_id_array'])) {
							$checked_str = " checked";
							array_push($output_community_id_array, $sub_community_info['community_id']);
						} else {
							$checked_str = "";
						}
					}
					print '<input type="checkbox" name="trusted_community_id_array[]" value="' . $parent_community_info['community_id'] . '"' . $checked_str . '>';
					print '<a href="' . $parent_community_info['top_page_url'] . '" target="_blank">';
					print htmlspecialchars($parent_community_info['community_name']) . "<br>\n";
					print '</a>';
				}
			}

			// サブコミュニティ出力
			if ($this->_tpl_vars['sub_community_info_array']) {
				foreach ($this->_tpl_vars['sub_community_info_array'] as $sub_community_info) {
					if ($this->_tpl_vars['default_data_row']['trusted_community_id_array']) {
						if (in_array($sub_community_info['community_id'], $this->_tpl_vars['default_data_row']['trusted_community_id_array'])) {
							$checked_str = " checked";
							array_push($output_community_id_array, $sub_community_info['community_id']);
						} else {
							$checked_str = "";
						}
					}
					print '<input type="checkbox" name="trusted_community_id_array[]" value="' . $sub_community_info['community_id'] . '"' . $checked_str . '>';
					print '<a href="' . $sub_community_info['top_page_url'] . '" target="_blank">';
					print htmlspecialchars($sub_community_info['community_name']) . "<br>\n";
					print '</a>';
				}
			}

			// 親・サブコミュニティ以外で選択されているコミュニティ出力
			if ($this->_tpl_vars['selected_trusted_community_info_array']) {
				foreach ($this->_tpl_vars['selected_trusted_community_info_array'] as $selected_trusted_community_info) {
					if (in_array($selected_trusted_community_info['community_id'], $output_community_id_array)) {
						// 親・サブコミュニティとして出力済みのものは、パス
						continue;
					}
					print '<input type="checkbox" name="trusted_community_id_array[]" value="' . $selected_trusted_community_info['community_id'] . '" checked>';
					print '<a href="' . $selected_trusted_community_info['top_page_url'] . '" target="_blank">';
					print htmlspecialchars($selected_trusted_community_info['community_name']) . "<br>\n";
					print '</a>';
				}
			}
			?>
			</td></tr>
			</table>
			<input type="button" value="<?= ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M010') ?>" onClick="window.open('<?= $this->_tpl_vars['add_trusted_community_url'] ?>', 'SelectTrustedCommunity', 'width=600,height=400,top=200,left=200,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes');">
			<span style="font-size: 8pt"><?= ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M007') ?></span>
		</div>
	</td>
</tr>
<?php
// 公開範囲が設定できる場合のみ、出力
}
?>

</table>
</p>

<p>
<input type="submit" value="<?= $submit_button_name ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("Community", "EditFolder.tpl.php",'M008') ?>" onclick="location.href='<?= $this->_tpl_vars['cancel_url'] ?>'">
</p>
</form>

<script type="text/javascript">
<!--
	// 友人に公開の選択肢表示を操作
	function show_open_level_option (selected_open_level_name) {
		trusted_community_div_obj = document.getElementById('trusted_community_div');
		if (selected_open_level_name == '<?= ACSMsg::get_mst('open_level_master','D04') ?>') {
			trusted_community_div_obj.style.display = "block";
		} else {
			trusted_community_div_obj.style.display = "none";
		}
	}
//-->
</script>
