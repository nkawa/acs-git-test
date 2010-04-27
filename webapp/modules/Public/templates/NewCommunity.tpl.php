<?php
// $Id: NewCommunity.tpl.php,v 1.5 2007/03/14 04:28:19 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("Public", "NewCommunity.tpl.php",'M001') ?></div>
<br>

<table class="common_table" width="675px" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
foreach ($this->_tpl_vars['new_community_row_array'] as $new_community_row) {
	echo "<tr>";

	// ¼Ì¿¿
	echo "<td align=\"center\" bgcolor=\"#ffffff\">";
	echo "<a href=\"" . $new_community_row['top_page_url'] . "\"><img src=\"" . $new_community_row["image_url"] . "\" border=\"0\"></a><br>";
	echo "<a href=\"" . $new_community_row['top_page_url'] . "\">" . htmlspecialchars($new_community_row['community_name']) . "</a>";
	echo "(" . $new_community_row['community_member_num'] . ")";
	echo "</td>";

	// ³µÍ×
	echo "<td valign=\"top\" bgcolor=\"#ffffff\">".ACSMsg::get_msg("Public", "NewCommunity.tpl.php",'M002')."<br>";
	echo nl2br(htmlspecialchars(ACSTemplateLib::trim_long_str($new_community_row['contents_row_array']['community_profile']['contents_value'], 200)));
	echo "</td>";

	echo "</tr>\n";
}
?>
</table>
