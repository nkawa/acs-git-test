<?php
// $Id: NewPressRelease.tpl.php,v 1.6 2007/03/30 05:27:21 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("Public", "NewPressRelease.tpl.php",'M001') ?></div>
<br>

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
foreach ($this->_tpl_vars['new_bbs_for_press_release_row_array'] as $new_bbs_row) {
	echo "<tr><td id=\"myttl\" bgcolor=\"#DEEEBD\">";
	echo "<b>" . htmlspecialchars($new_bbs_row['subject']) . "</b>";
	echo "&nbsp;";
	echo "(<a href=\"$new_bbs_row[top_page_url]\">" . htmlspecialchars($new_bbs_row['community_name']) . "</a>)";
	echo "&nbsp;&nbsp;&nbsp;";
	echo $new_bbs_row['post_date'];
	echo "</td></tr>";

	echo "<tr><td bgcolor=\"#ffffff\">";
	echo "<table class=\"layout_table\"><tr>";
	if ($new_bbs_row['file_url']) {
		echo "<td valign=\"top\">";
		echo "<a href=\"javascript:w=window.open('" . $new_bbs_row["file_url_alink"] . "','popUp','scrollbars=yes,resizable=yes');w.focus();\">";
		echo "<img src=\"" . $new_bbs_row['file_url'] . "\" border=\"0\"></a>";
		echo "</td>";
	}
	echo "<td valign=\"top\">" . nl2br(ACSTemplateLib::auto_link(ACSLib::sp2nbsp(htmlspecialchars($new_bbs_row['body'])))) . "</td>";
	echo "</tr></table>";
	echo "</td></tr>";
}
?>
</table>
