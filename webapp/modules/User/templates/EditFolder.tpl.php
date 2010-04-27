<?php
if ($this->_tpl_vars['view_mode'] == 'create') {
	$title = ACSMsg::get_msg("User", "EditFolder.tpl.php",'M001');
	$submit_button_name = ACSMsg::get_msg("User", "EditFolder.tpl.php",'M002');
} elseif ($this->_tpl_vars['view_mode'] == 'update') {
	$title = ACSMsg::get_msg("User", "EditFolder.tpl.php",'M003');
	$submit_button_name = ACSMsg::get_msg("User", "EditFolder.tpl.php",'M004');
}
?>
<div class="ttl"><?= $title ?></div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<form name="folder_info" method="POST" action="<?= $this->_tpl_vars['action_url'] ?>">
<p>
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditFolder.tpl.php",'M005') ?></td>
	<td bgcolor="#ffffff"><input type="text" name="folder_name" value="<?= htmlspecialchars($this->_tpl_vars['default_data_row']['folder_name']) ?>"></td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditFolder.tpl.php",'M006') ?></td>
	<td bgcolor="#ffffff"><input type="text" name="comment" value="<?= htmlspecialchars($this->_tpl_vars['default_data_row']['comment']) ?>"></td>
</tr>

<?php
// �����ϰϤ�����Ǥ�����Τߡ�����
if ($this->_tpl_vars['is_set_open_level_available']) {
?>
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditFolder.tpl.php",'M009') ?></td>
	<td bgcolor="#ffffff">
		<select name="open_level_code" onChange="show_open_level_option(this.options[this.selectedIndex].text)">
		<?php
		// ��������
		$trusted_community_display_mode = 'none';
		foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
			if ($open_level_master_row['is_default']) {
				$selected_str = " selected";

				// ���򤵤�Ƥ�������ϰϤ���ͧ�ͤ˸����פξ��ϡ�trusted_community ��ɽ������
				if ($open_level_master_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
					$trusted_community_display_mode = 'block';
				}
			} else {
				$selected_str = "";
			}
			print '<option value="' . $open_level_master_row['open_level_code'] . '"' . $selected_str . '>' . htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
		}
		?>
		</select>

		<div id="trusted_community" style="display:<?= $trusted_community_display_mode ?>;">
			<table class="layout_table">
			<tr>
				<?php
				// ���Ƥ�ͧ�ͤ����򤹤뤫�ɤ���
				$checked_str = " checked";
				$trusted_community_flag_all = "";
				$trusted_community_flag_friens_group = "";
				if ($this->_tpl_vars['default_data_row']['trusted_community_flag'] == '0') {
					$trusted_community_flag_all = $checked_str;
				} elseif ($this->_tpl_vars['default_data_row']['trusted_community_flag'] == '1') {
					$trusted_community_flag_friens_group = $checked_str;
				}
				?>
				<td colspan=2><input type="radio" name="trusted_community_flag" value="0" onClick="select_trusted_community_flag_all_friends(this.form)" <?= $trusted_community_flag_all ?>><?= ACSMsg::get_msg("User", "EditFolder.tpl.php",'M007') ?></td>
			</tr>
			<tr>
				<td colspan=2><input type="radio" name="trusted_community_flag" value="1" <?= $trusted_community_flag_friens_group ?>><?= ACSMsg::get_msg("User", "EditFolder.tpl.php",'M010') ?></td>
			</tr>

			<?php
			// �ޥ��ե�󥺥��롼�פ�������Τ�
			if ($this->_tpl_vars['friends_group_row_array']) {
				print '<tr>';
				// ����ǥ��ʬ
				print '<td><br></td>';
				print '<td>';
				foreach ($this->_tpl_vars['friends_group_row_array'] as $friends_group_row) {
					$checked_str = "";

					// �ǥե�����ͤ������硢�����å����뤫��Ƚ�̤���
					if ($this->_tpl_vars['default_data_row']['trusted_community_id_array']) {
						if (in_array($friends_group_row['community_id'], $this->_tpl_vars['default_data_row']['trusted_community_id_array'])) {
							$checked_str = " checked";
						}
					}

					print '<input type="checkbox" name="trusted_community[]" value="' . $friends_group_row['community_id'] . '" onClick="select_trusted_community_flag_friends_group(this.form)"' . $checked_str . '>';
					print htmlspecialchars($friends_group_row['community_name']) . "<br>\n";
				}
				print '</td>';
				print '</tr>' . "\n";
			}
			?>
			</table>
		</div>
	</td>
</tr>
<?php
// �����ϰϤ�����Ǥ�����Τߡ�����
}
?>

</table>
</p>

<p>
<input type="submit" value="<?= $submit_button_name ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("User", "EditFolder.tpl.php",'M008') ?>" onclick="location.href='<?= $this->_tpl_vars['cancel_url'] ?>'">
</p>
</form>

<script type="text/javascript">
<!--
	// ͧ�ͤ˸����������ɽ�������
	function show_open_level_option (selected_open_level_name) {
		trusted_community_div_obj = document.getElementById('trusted_community');
		if (selected_open_level_name == '<?= ACSMsg::get_mst('open_level_master','D05') ?>') {
			trusted_community_div_obj.style.display = "block";
		} else {
			trusted_community_div_obj.style.display = "none";
		}
	}

	// �ޥ��ե�󥺥��롼�פ�������ν���
	function select_trusted_community_flag_friends_group (form_obj) {
		form_obj.elements['trusted_community_flag'][1].checked = true;
	}

	// ���ƤΥե�󥺤����򤷤����ν���
	function select_trusted_community_flag_all_friends (form_obj) {
		if (!form_obj.elements['trusted_community[]']) {
			// �ޥ��ե�󥺥��롼�פ��ʤ����Ͻ�λ
			return;
		}
		for (count = 0; count < form_obj.elements['trusted_community[]'].length; count++) {
			form_obj.elements['trusted_community[]'][count].checked = false;
		}
	}
//-->
</script>
