<?php
// $Id: CommunityRanking.tpl.php,v 1.5 2007/03/14 04:28:19 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("Public", "CommunityRanking.tpl.php",'M001') ?></div>
<br>

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
foreach ($this->_tpl_vars['ranking_community_row_array'] as $ranking_community_row) {
	echo "<tr>";
	echo "<td align=\"center\" bgcolor=\"#ffffff\">" . $ranking_community_row['rank'] . "</td>";
	echo "<td align=\"center\" bgcolor=\"#ffffff\">";
	echo "<a href=\"" . $ranking_community_row['top_page_url'] . "\"><img src=\"" . 
				$ranking_community_row['image_url'] . "\" border=\"0\"></a><br>";
	echo "<a href=\"" . $ranking_community_row['top_page_url'] . "\">" . htmlspecialchars($ranking_community_row['community_name']) . "</a>";
	echo " (" . $ranking_community_row['ranking_score'] . "pt)";
	echo "</td>";

	echo "<td valign=\"top\" bgcolor=\"#ffffff\">";
	echo nl2br(htmlspecialchars(ACSTemplateLib::trim_long_str($ranking_community_row['contents_row_array']['community_profile']['contents_value'], 200)));
	echo "</td>";
	echo "</tr>";
}
?>
</table>
