<div class="ttl"><?= ACSMsg::get_msg("System", "EditSystemConfig.tpl.php",'M001') ?></div>

<?php
ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
?>

<form name="edit_system_config" method="POST" action="<?= $this->_tpl_vars['edit_system_config_url'] ?>">
<?php
foreach ($this->_tpl_vars['system_config_row_array'] as $group => $row_array) {
	print '<p>' . "\n";
	//print '<table class="common_table" border>' . "\n";
	print '<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">' . "\n";

	// グループ名出力
	print '<tr>';
	print '<th class="system_config_th" colspan=2>';
	print $group;
	print '</td>';
	print '</tr>' . "\n";

	// 項目出力
	foreach ($row_array as $row) {
		print '<tr>' . "\n";

		// 項目名
		print '<td id="myttl" bgcolor="#DEEEBD">';
		print $row['name'];
		print '</td>' . "\n";

		// 値
		$type = $row['type'];

		if ($type == 'select') {

			print '<td bgcolor="#ffffff">';
			print '<select name="' . $row['keyword'] . '" >';
			
			for ($i = 0; $i < count($row['select']); $i++) {
				if ($row['value'] == $row['select'][$i][0]) {
?>
				<option value='<?=$row['select'][$i][0]?>' selected><?=$row['select'][$i][1]?></option>
<?php
				} else {
?>
				<option value='<?=$row['select'][$i][0]?>'><?=$row['select'][$i][1]?></option>
<?php
				}
			}
			print '</select>';
			
		} else {
			$display_size = "";
			// 表示サイズ設定
			if ($type == 'string') {
				$input_type = 'text';
				$display_size = " size=80";
			} elseif ($type == 'password') {
				$input_type = 'password';
				$display_size = " size=20";
			} elseif ($type == 'number') {
				$input_type = 'text';
				$display_size = " size=10";
			} elseif ($type == 'number0') {
				$input_type = 'text';
				$display_size = " size=10";
			}
			print '<td bgcolor="#ffffff">';
			print '<input type="' . $input_type . '" name="' . $row['keyword'] . '" value="' . $row['value'] . '"' . $display_size . '>';
		}

		print $row['unit'];
		print '<br>';
		if ($row['note']) {
			// 備考がある場合は表示
			print '<span class="notice">';
			print '※' . $row['note'];
			print '</span>';
		}
		print '</td>' . "\n";

		print '</tr>' . "\n";
	}
	print '</table>' . "\n";

	print '</p>' . "\n";
}
?>

<input type="submit" value="<?= ACSMsg::get_msg("System", "EditSystemConfig.tpl.php",'M002') ?>">&nbsp;
<input type="reset" value="<?= ACSMsg::get_msg("System", "EditSystemConfig.tpl.php",'M003') ?>">
</form>
