<?php
// $Id: FileDetail.tpl.php,v 1.12 2007/03/28 09:24:54 w-ota Exp $

if ($this->_tpl_vars['is_self_page']) {
	$title = ACSMsg::get_msg("User", "FileDetail.tpl.php",'M001');
} else {
	$title  = '<a href="' . $this->_tpl_vars['target_user_info_row']['top_page_url'] . '">';
	// $title .= htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) . "さん";
	$title .= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "FileDetail.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	$title .= '</a>';
	$title .= ' :: '.ACSMsg::get_msg("User", "FileDetail.tpl.php",'M002');
}
?>
<div class="ttl"><?= $title ?> :: <?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M003') ?></div>

<?php
ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
?>

<?php
/**********************
if($this->_tpl_vars['footprint_url'] != ""){
    print '<p><input type="button" onclick="javascript:location=\'' . $this->_tpl_vars['footprint_url'].
                '\'" value="' . ACSMsg::get_msg("User", "FileDetail.tpl.php",'M030') . '">';

	if(count($this->_tpl_vars['footprint_info']) > 0){
        print '(' . ACSMsg::get_msg("User", "FileDetail.tpl.php",'M031') . ')';
    }
	print '</p></form>';
}
**********************/
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
	<td><div class="subsub_title"><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M004') ?></div></td>
	<td align="right">&nbsp;
	<?php
	if ($this->_tpl_vars['menu']['update_file_url']) {
		print '[<a href="' . $this->_tpl_vars['menu']['update_file_url'] . '">'.ACSMsg::get_msg("User", "FileDetail.tpl.php",'M005').'</a>]&nbsp;';
	}
	if ($this->_tpl_vars['menu']['rename_folder_list_url']) {
		print '[<a href="' . $this->_tpl_vars['menu']['rename_folder_list_url'] . '">'.ACSMsg::get_msg("User", "FileDetail.tpl.php",'M006').'</a>]&nbsp;';
	}
	if ($this->_tpl_vars['menu']['move_folder_list_url']) {
		print '[<a href="' . $this->_tpl_vars['menu']['move_folder_list_url'] . '">'.ACSMsg::get_msg("User", "FileDetail.tpl.php",'M007').'</a>]&nbsp;';
	}
	if ($this->_tpl_vars['menu']['delete_folder_url']) {
		print '[<a href="' . $this->_tpl_vars['menu']['delete_folder_url'] . '">'.ACSMsg::get_msg("User", "FileDetail.tpl.php",'M008').'</a>]';
	}
	?>
	</td>
</tr>

<tr><td colspan="2">
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
	<td id="myttl" bgcolor="#DEEEBD" nowrap><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M009') ?></td>
	<td bgcolor="#ffffff">
	<table class="inner_layout_table" width="100%"><tr>
	<td><img src="<?= ACS_IMAGE_DIR . "file.gif" ?>"></td>
	<td>
	<a href="<?= $this->_tpl_vars['file_info_row']['link_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['file_info_row']['display_file_name']) ?></a>&nbsp;&nbsp;(<?= htmlspecialchars($this->_tpl_vars['file_info_row']['mime_type']) ?>)
	</td>
	<td><?php
	if($this->_tpl_vars['footprint_url'] != ""){
    	print '&nbsp&nbsp&nbsp<a href="javascript:location=\'' . $this->_tpl_vars['footprint_url']. '\'">'.
				'<img border="0" src="'.ACS_IMAGE_DIR.'footmark.gif">'.
				ACSMsg::get_msg("User", "FileDetail.tpl.php",'M030');

		if(count($this->_tpl_vars['footprint_info']) > 0){
        	print ' (' . ACSMsg::get_msg("User", "FileDetail.tpl.php",'M031') . ')';
    	}
		print '</a>';
	}
	?></td>
	</tr></table>
	</td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD" nowrap><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M010') ?></td>
	<td bgcolor="#ffffff"><?= $this->_tpl_vars['file_info_row']['file_size_kb'] ?> (<?= $this->_tpl_vars['file_info_row']['file_size'] ?> <?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M011') ?>)</td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD" nowrap><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M012') ?></td>
	<td bgcolor="#ffffff"><a href="<?= $this->_tpl_vars['detail_folder_row']['link_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['detail_folder_row']['folder_name']) ?></a></td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD" nowrap><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M013') ?></td>
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

			//$trusted_community_info_array[0] = '*対象なし';
			$trusted_community_info_array[0] = ACSMsg::get_msg("User", "FileDetail.tpl.php",'M029');
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
	<td id="myttl" bgcolor="#DEEEBD" nowrap><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M014') ?></td>
	<td bgcolor="#ffffff">
	<a href="<?= $this->_tpl_vars['file_info_row']['entry_user_community_link_url'] ?>"><?= $this->_tpl_vars['file_info_row']['entry_user_community_name'] ?></a>
	(<?= $this->_tpl_vars['file_info_row']['entry_date'] ?>)
	</td>
</tr>
<tr>
	<td id="myttl" bgcolor="#DEEEBD" nowrap><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M015') ?></td>
	<td bgcolor="#ffffff">
	<a href="<?= $this->_tpl_vars['file_info_row']['update_user_community_link_url'] ?>"><?= $this->_tpl_vars['file_info_row']['update_user_community_name'] ?></a>
	(<?= $this->_tpl_vars['file_info_row']['update_date'] ?>)
	</td>
</tr>
</table>

</td></tr>
</table>
<!-- layout_table end //-->
</p>


<!-- layout_table -->
<table class="layout_table">
<tr>
	<td><div class="subsub_title"><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M016') ?></div></td>
	<td align="right">
	<?php
	if ($this->_tpl_vars['menu']['edit_file_detail_url']) {
		print '[<a href="' . $this->_tpl_vars['menu']['edit_file_detail_url'] . '">'.ACSMsg::get_msg("User", "FileDetail.tpl.php",'M017').'</a>]';
	}
	?>
	</td>
</tr>
<td colspan="2">
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
	if (count($this->_tpl_vars['file_detail_info_row']['file_contents_row_array'])) {
		echo "<tr>";
		echo "<td id=\"myttl\" class=\"nowrap\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "FileDetail.tpl.php",'M018')."</td>";
		echo "<td class=\"nowrap\" bgcolor=\"#ffffff\">" . htmlspecialchars($this->_tpl_vars['file_detail_info_row']['file_category_name']) . "</td>";
		echo "</tr>\n";
		foreach ($this->_tpl_vars['file_detail_info_row']['file_contents_row_array'] as $file_contents_row) {
			echo "<tr>";
			echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">";
			echo htmlspecialchars($file_contents_row['file_contents_type_name']);
			echo "</td>";
			echo "<td  bgcolor=\"#ffffff\">";
			echo nl2br(htmlspecialchars($file_contents_row['file_contents_value']));
			echo "</td>";
			echo "</tr>\n";
		}
	} else {
		echo "<tr><td bgcolor=\"#ffffff\" width=\"100px\">".ACSMsg::get_msg("User", "FileDetail.tpl.php",'M019')."</td></tr>\n";
	}
?>
</table>
</td></tr></table>
<br>


<table class="layout_table">
<tr>
<td>
<div class="subsub_title"><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M020') ?></div>
<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>
</td>
</tr>
<td>
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td class="nowrap" id="myttl" bgcolor="#DEEEBD">&nbsp;</td>
<td class="nowrap" id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M021') ?></td>
<td class="nowrap" id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M022') ?></td>
<td class="nowrap" id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M023') ?></td>
<td class="nowrap" id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M024') ?></td>
<td class="nowrap" id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M025') ?></td>
</tr>
<?php
foreach ($this->_tpl_vars['file_history_row_array'] as $file_history_row) {
	echo "<tr>\n";

	echo "<td class=\"nowrap\" bgcolor=\"#ffffff\">" . htmlspecialchars($file_history_row['file_history_operation_name']) . "</td>\n";
	echo "<td class=\"nowrap\" bgcolor=\"#ffffff\">" . htmlspecialchars($file_history_row['update_date']) . "</td>\n";
	echo "<td class=\"nowrap\" bgcolor=\"#ffffff\"><a href=\"$file_history_row[link_url]\">" . htmlspecialchars($file_history_row['community_name']) . "</a></td>\n";
	echo "<td bgcolor=\"#ffffff\"><a href=\"$file_history_row[download_history_file_url]\">" . htmlspecialchars($file_history_row['display_file_name']) . "</a></td>\n";
	echo "<td class=\"nowrap\" bgcolor=\"#ffffff\">";
	if ($file_history_row['restore_history_file_url'] != '') {
		echo "[<a href=\"$file_history_row[restore_history_file_url]\">".ACSMsg::get_msg("User", "FileDetail.tpl.php",'M026')."</a>]";
	} else {
		echo "&nbsp;";
	}
	echo "</td>\n";
	// コメント
	$comment_str = "";
	foreach ($file_history_row['file_history_comment_row_array'] as $file_history_comment_row) {
		if ($file_history_comment_row['comment'] != '') {
			if ($comment_str != '') {
				$comment_str .= "<br>";
			}
			$comment_str .= "<li>";
			$comment_str .= nl2br(htmlspecialchars($file_history_comment_row['comment']));
			$comment_str .= "<span class=\"nowrap\"> (";
			$comment_str .= "<a href=\"$file_history_comment_row[link_url]\">";
			$comment_str .= htmlspecialchars($file_history_comment_row['community_name']);
			$comment_str .= "</a> ";
			$comment_str .= $file_history_comment_row['post_date'];
			$comment_str .= ")</span>";
		}
	}
	if ($comment_str == '') {
		$comment_str = '&nbsp;';
	}
	echo "<td bgcolor=\"#ffffff\">" . $comment_str . "</td>\n"; // エスケープ済
	echo "</tr>\n";
}
?>
</table>

</td>
</tr>
<?php
if ($this->_tpl_vars['acs_user_info_row']['is_acs_user']) {
?>
<tr>
<td align="right">
<form action="<?= $this->_tpl_vars['file_history_comment_url'] ?>" method="post">
<input type="text" name="comment" size="50"> <input type="submit" value="<?= ACSMsg::get_msg("User", "FileDetail.tpl.php",'M027') ?>">
</form>
</td>
</tr>
<?php
}
?>
</table>
<br>



<a href="<?= $this->_tpl_vars['back_url'] ?>"><?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M028') ?></a>
<br>
