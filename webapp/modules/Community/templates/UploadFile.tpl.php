<?php
// $Id: UploadFile.tpl.php,v 1.5 2007/03/01 09:01:35 w-ota Exp $
?>

<?
// ファイルコンテンツ切り替えJS出力
ACSTemplateLib::print_change_file_contents_js('upload_file', $this->_tpl_vars['file_contents_type_master_array'], $this->_tpl_vars['file_contents_type_master_row_array_array']);
?>

<?php
	$title  = '<a href="' . $this->_tpl_vars['target_community_row']['top_page_url'] . '">';
	$title .= htmlspecialchars($this->_tpl_vars['target_community_row']['community_name']) . " ".ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M001');
	$title .= '</a>';
?>
<div class="ttl"><?= $title ?> :: <?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M002') ?> :: <?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M003') ?></div>
<br>

<form name="upload_file" action="<?= $this->_tpl_vars['action_url'] ?>" method="POST" enctype="multipart/form-data">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M004') ?></td>
<td bgcolor="#ffffff"><input type="file" name="new_file" size="50"> <input type="submit" value="<?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M005') ?>"></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M006') ?></td>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M007') ?><br><textarea name="comment" cols="50" rows="5"></textarea></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M008') ?></td>
<td bgcolor="#ffffff">
<select name="file_category_code" onchange="change_file_contents()">
<?php
foreach ($this->_tpl_vars['file_category_master_array'] as $file_category_code => $file_category_name) {
	echo "<option value=\"$file_category_code\">" . htmlspecialchars($file_category_name) . "\n";
}
?>
</select>
</td>
</tr>

<?php
$default_file_category_code = array_search(ACSMsg::get_mst('file_category_master','D0000'), $this->_tpl_vars['file_category_master_array']);

foreach ($this->_tpl_vars['file_contents_type_master_array'] as $file_contents_type_code => $file_contents_type_name) {
	echo "<tr id=\"file_contents_tr[$file_contents_type_code]\"";
	if (!array_key_exists($file_contents_type_code, $this->_tpl_vars['file_contents_type_master_row_array_array'][$default_file_category_code]['file_contents_type_master_row_array'])) {
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
<input type="checkbox" value="t" name="send_announce_mail" <?= $this->_tpl_vars['send_annouce_mail_checked'] ?>>
    <?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M011') ?>
</form>
<br>

<a href="<?= $this->_tpl_vars['folder_url'] ?>"><?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M009') ?></a>&nbsp;&nbsp;
<a href="<?= $this->_tpl_vars['folder_group_mode_url'] ?>"><?= ACSMsg::get_msg("Community", "UploadFile.tpl.php", 'M010') ?></a><br>
