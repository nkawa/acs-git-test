<?php
if ($this->_tpl_vars['is_self_page']) {
	$title = ACSMsg::get_msg("User", "Folder_group.tpl.php",'M001');
} else {
	$title  = '<a href="' . $this->_tpl_vars['target_user_info_row']['top_page_url'] . '">';
	//$title .= htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) . "����";
	$title .= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "Folder_group.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	$title .= '</a>';
	$title .= ' :: '.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M002');
}
?>
<div class="sub_title"><?= $title ?>&nbsp;&nbsp;[<a href="<?= $this->_tpl_vars['search_folder_url'] ?>"><?= ACSMsg::get_msg("User", "Folder_group.tpl.php",'M003') ?></a>]</div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<?php
	// �ѥ��������
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
	// �����ϰ�ɽ��
	$target_folder_open_level_row = $this->_tpl_vars['target_folder_open_level_row'];

	// �������ĥ��ߥ�˥ƥ�����
	$trusted_community_info_array = array();
	// �ܿͤΤ�ɽ��
	if ($this->_tpl_vars['is_self_page']) {
		foreach ($target_folder_open_level_row['trusted_community_row_array'] as $trusted_community_row) {
			$trusted_community_name = htmlspecialchars($trusted_community_row['community_name']);
			if (!$trusted_community_name) {
				continue;
			}

			array_push($trusted_community_info_array, $trusted_community_name);
		}
		// �������ߥ�˥ƥ������ꤵ��Ƥ��ʤ���硢���оݤʤ���
		if ($target_folder_open_level_row['name'] == ACSMsg::get_msg("User", "Folder_group.tpl.php",'M004')
			&& count($target_folder_open_level_row['trusted_community_row_array']) == 0) {

			$trusted_community_info_array[0] = ACSMsg::get_msg("User", "Folder_group.tpl.php",'M005');
		}
	}
	print '<table class="open_level_table"><tr><td>';
	print ACSMsg::get_msg("User", "Folder_group.tpl.php",'M006')." : ";
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
	<input type="submit" value="<?= ACSMsg::get_msg("User", "Folder_group.tpl.php",'M007') ?>">
	<a href="<?= $this->_tpl_vars['upload_file_url'] ?>"><?= ACSMsg::get_msg("User", "Folder_group.tpl.php",'M008') ?></a>
	</span>
	</p>
	</form>

	<p>
	<form name="operation" action="" method="POST">
	<input type="button" value="<?= ACSMsg::get_msg("User", "Folder_group.tpl.php",'M009') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['move_folder_url'] ?>')">
	<input type="button" value="<?= ACSMsg::get_msg("User", "Folder_group.tpl.php",'M010') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['rename_folder_url'] ?>')">
	<input type="button" value="<?= ACSMsg::get_msg("User", "Folder_group.tpl.php",'M011') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['delete_folder_url'] ?>')">
	<input type="button" value="<?= ACSMsg::get_msg("User", "Folder_group.tpl.php",'M012') ?>" onClick="submit_operation(this.form, '<?= $this->_tpl_vars['edit_folder_url'] ?>')">
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
	/* �ե��������ɽ�� */
	echo "[ <a href=\"" . $this->_tpl_vars['folder_url'] . "\">".ACSMsg::get_msg("User", "Folder_group.tpl.php",'M013')."</a> | ".ACSMsg::get_msg("User", "Folder_group.tpl.php",'M014')." ]<br><br>\n";

	if (count($this->_tpl_vars['folder_row_array']) > 0) {
		print '<table class="file_list_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">' . "\n";
		print '<tr>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M015').'<br></th>' . "\n";
		if ($this->_tpl_vars['is_self_page']) {
			// ����ѥ����å��ܥå���ʬ
			print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M016').'</th>' . "\n";
		}
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M017').'</th>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M018').'</th>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M019').'</th>' . "\n";
		print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M020').'</th>' . "\n";
		if ($this->_tpl_vars['is_root_folder']) {
			print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M006').'</th>' . "\n";
			print '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M022').'</th>' . "\n";
		}
		print '</tr>' . "\n";
	}

	foreach ($this->_tpl_vars['folder_row_array'] as $folder_row) {
		print "<tr>\n";

		print "<td bgcolor=\"#ffffff\" align=\"center\" class=\"nowrap\">";
		/* �����ϰ����� */
		if ($this->_tpl_vars['is_self_page'] && $this->_tpl_vars['is_root_folder']) {
			print '[<a href="' . $folder_row['folder_put_community_url'] . '" target="_blank">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M023').'</a>]<br>';
		}
		/* �ե�����ܺ� */
		print '[<a href="' . $folder_row['detail_url'] . '">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M024').'</a>]';
		print "</td>\n";

		/* ����ѤΥ����å��ܥå��� */
		if ($this->_tpl_vars['is_self_page']) {
			print '<td bgcolor="#ffffff" align="center">';
			print '<input type="checkbox" name="selected_folder[]" value="' . $folder_row['folder_id'] . '">';
			print "</td>\n";
		}

		/* �ե�������� */
		print "<td bgcolor=\"#ffffff\">";
		print '<table class="layout_table"><tr>';
		print "<td>";
		print '<img src="' . ACS_IMAGE_DIR . 'folder.png">';
		print "</td>";

		/* �ե����̾ (���) */
		print "<td>";
		print '<a href="' . $folder_row['link_url'] . '">';
		print htmlspecialchars($folder_row['name']);
		print '</a>';
		print "</td>";
		print "</tr></table>";
		print "</td>\n";

		/* ������ */
		print "<td bgcolor=\"#ffffff\">";
		print '<a href="' . $folder_row['update_user_community_link_url'] . '">';
		print htmlspecialchars($folder_row['update_user_community_name']);
		print '</a>';
		print "</td>\n";

		/* ������ */
		print "<td bgcolor=\"#ffffff\">";
		print $folder_row['update_date'];
		print "</td>\n";

		/* ������ */
		print "<td bgcolor=\"#ffffff\"><br></td>\n";

		/* �����ϰ� */
		if ($this->_tpl_vars['is_root_folder']) {
			// �������ĥ��ߥ�˥ƥ�����
			$trusted_community_info_array = array();
			// �ܿͤΤ�ɽ��
			if ($this->_tpl_vars['is_self_page']) {
				foreach ($folder_row['trusted_community_row_array'] as $trusted_community_row) {
					$trusted_community_name = htmlspecialchars($trusted_community_row['community_name']);
					if (!$trusted_community_name) {
						continue;
					}

					array_push($trusted_community_info_array, $trusted_community_name);
				}
				// �������ߥ�˥ƥ������ꤵ��Ƥ��ʤ���硢���оݤʤ���
				if ($folder_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')
					&& count($folder_row['trusted_community_row_array']) == 0) {

					//$trusted_community_info_array[0] = '*�оݤʤ�';
					$trusted_community_info_array[0] = ACSMsg::get_msg("User", "Folder_group.tpl.php",'M005');
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

		/* �ץå��襳�ߥ�˥ƥ� */
		if ($this->_tpl_vars['is_root_folder']) {
			print "<td bgcolor=\"#ffffff\">\n";
			if ($folder_row['put_community_url']) {
				print '<a href="' . $folder_row['put_community_url'] . '" target="_blank">';
				print ACSMsg::get_msg("User", "Folder_group.tpl.php",'M025');
				print '</a>';
			} else {
				print ACSMsg::get_msg("User", "Folder_group.tpl.php",'M026');
			}
			print "<br>";
			print "</td>\n";
		}
	}
	if (count($this->_tpl_vars['folder_row_array']) > 0) {
		print "</table><br>\n";
	}

?>
<?php
// ���롼��ɽ��

foreach ($this->_tpl_vars['file_contents_type_master_row_array_array'] as $file_contents_type_master_row_array) {
	$file_category_code = $file_contents_type_master_row_array['file_category_code'];
	$file_category_name = $file_contents_type_master_row_array['file_category_name'];

	// �ե����륫�ƥ��ꥳ���ɤ��ȤΥե�����ܺپ����Ϣ������1��ʾ夢����
	if (count($this->_tpl_vars['file_detail_info_row_array_array'][$file_category_code]['file_detail_info_row_array']) > 0) {
		echo "<div class=\"subsub_title\">";
		echo $file_contents_type_master_row_array['file_category_name'];
		echo "</div>\n";

		echo '<table class="file_list_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">';
		echo "<tr>";
		if ($this->_tpl_vars['is_self_page']) {
			echo '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M015')."</th>";
		}
		echo '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M016')."</th>";
		echo '<th id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("User", "Folder_group.tpl.php",'M017')."</th>";

		// �ե����륳��ƥ�Ĥ��ȤΥ����ȥ�ɽ��
		foreach ($this->_tpl_vars['file_contents_type_master_row_array_array'][$file_category_code]['file_contents_type_master_row_array']
				 as $file_contents_type_master_row) {
			echo '<th id="myttl" bgcolor="#DEEEBD">';
			echo htmlspecialchars($file_contents_type_master_row['file_contents_type_name']);
			echo "</th>";
		}
		echo "</tr>\n";

		// �ե�����ܺپ���
		foreach ($this->_tpl_vars['file_detail_info_row_array_array'][$file_category_code]['file_detail_info_row_array'] as $file_detail_info_row) {
			echo "<tr>\n";

			// ���ܾ���
			echo "<td align=\"center\" bgcolor=\"#ffffff\" class=\"nowrap\">[<a href=\"$file_detail_info_row[file_detail_url]\">".ACSMsg::get_msg("User", "Folder_group.tpl.php",'M024')."</a>]</td>\n";
			if ($this->_tpl_vars['is_self_page']) {
				echo "<td bgcolor=\"#ffffff\" align=\"center\"><input type=\"checkbox\" name=\"selected_file[]\" value=\"$file_detail_info_row[file_id]\"></td>\n";
			}
			if ($file_category_name == ACSMsg::get_mst('file_category_master','D0003')) {
				echo "<td bgcolor=\"#ffffff\" align=\"center\">";
				echo "<img src=\"$file_detail_info_row[image_url]\"><br>";
				echo "<a href=\"$file_detail_info_row[link_url]\">$file_detail_info_row[display_file_name]</a>";
				echo "</td>\n";
			} else {
				echo "<td bgcolor=\"#ffffff\"><a href=\"$file_detail_info_row[link_url]\">$file_detail_info_row[display_file_name]</a></td>\n";
			}

			// �ե����륳��ƥ�Ĥ��ȤΥ���ƥ��ɽ��
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
<?php if ($this->_tpl_vars['is_self_page']) { ?>
</form>
<?php } ?>
