<?php
// $Id: EditFileDetail.tpl.php,v 1.7 2007/03/28 08:39:33 w-ota Exp $
?>

<?
// ファイルコンテンツ切り替えJS出力
ACSTemplateLib::print_change_file_contents_js('edit_file_detail_form', $this->_tpl_vars['file_contents_type_master_array'], $this->_tpl_vars['file_contents_type_master_row_array_array']);
?>

<?php
if ($this->_tpl_vars['is_self_page']) {
	$title = ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'M001');
} else {
	$title  = '<a href="' . $this->_tpl_vars['target_user_info_row']['top_page_url'] . '">';
	//$title .= htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) . "さん";
	$title .= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));

	$title .= '</a>';
	$title .= ' :: <a href="' . $this->_tpl_vars['back_url'] . '">'.ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'M002').'</a>';
}
?>
<div class="sub_title"><?= $title ?> :: <?= ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'M003') ?> :: <?= ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'M004') ?></div>

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

<!-- layout_table -->

<form name="edit_file_detail_form" action="<?= $this->_tpl_vars['action_url'] ?>" method="post">
<table class="layout_table">
<tr>
	<td><div class="subsub_title"><?= ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'M005') ?></div></td>
</tr>

<tr><td>
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
	<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'M006') ?></td>
	<td bgcolor="#ffffff">
	<table class="inner_layout_table"><tr>
	<td><img src="<?= ACS_IMAGE_DIR . "file.gif" ?>"></td>
	<td><a href="<?= $this->_tpl_vars['file_info_row']['link_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['file_info_row']['display_file_name']) ?></a></td>
	</tr></table>
	</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'M007') ?></td>
<td bgcolor="#ffffff">
<select name="file_category_code" onchange="change_file_contents()">
<?php
unset($selected);
$selected[$this->_tpl_vars['file_detail_info_row']['file_category_code']] = ' selected';
foreach ($this->_tpl_vars['file_category_master_array'] as $file_category_code => $file_category_name) {
	echo "<option value=\"$file_category_code\"$selected[$file_category_code]>" . htmlspecialchars($file_category_name) . "\n";
}
?>
</select>
</td>
</tr>

<?php
foreach ($this->_tpl_vars['file_contents_type_master_array'] as $file_contents_type_code => $file_contents_type_name) {
	echo "<tr id=\"file_contents_tr[$file_contents_type_code]\"";
	if (!is_array($this->_tpl_vars['file_detail_info_row']['file_contents_row_array'][$file_contents_type_code])) {
		echo " style=\"display:none\"";
	}
	echo ">";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">";
	echo htmlspecialchars($file_contents_type_name);
	echo "</td>";
	echo "<td bgcolor=\"#ffffff\">";
	echo "<textarea name=\"file_contents_array[$file_contents_type_code]\" cols=\"50\" rows=\"3\">";
	echo htmlspecialchars($this->_tpl_vars['file_detail_info_row']['file_contents_row_array'][$file_contents_type_code]['file_contents_value']);
	echo "</textarea>";
	echo "</td>";
	echo "</tr>\n";
}
?>
</table>

</td></tr></table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'M008') ?>">
<input type="button" value="<?= ACSMsg::get_msg("User", "EditFileDetail.tpl.php",'M009') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">
</form>
