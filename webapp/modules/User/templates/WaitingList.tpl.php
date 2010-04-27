<?php
// $Id: WaitingList.tpl.php,v 1.10 2007/03/14 04:28:21 w-ota Exp $
?>

<div class="ttl"><?= $this->_tpl_vars['waiting_type_name'] ?> <?= ACSMsg::get_msg("User", "WaitingList.tpl.php",'M001') ?></div><br>
<br>

<?php
foreach ($this->_tpl_vars['waiting_row_array'] as $waiting_row) {
	echo "<form action=\"$waiting_row[action_url]\" method=\"post\">\n";

	echo "<table class=\"common_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";

	if ($this->_tpl_vars['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D30')) {
		echo "<tr>";
		echo "<td align=\"center\" rowspan=\"6\" bgcolor=\"#ffffff\">";
		if ($waiting_row['waiting_community_row']['contents_row_array']['self']['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
			echo "<img src=\"$waiting_row[image_url]\"><br>";
			echo htmlspecialchars($waiting_row['community_name']);
			echo "<br>";
			echo ACSMsg::get_msg("User", "WaitingList.tpl.php",'M002');
		} else {
			echo "<a href=\"$waiting_row[top_page_url]\"><img src=\"$waiting_row[image_url]\" border=\"0\"></a><br>";
			echo "<a href=\"$waiting_row[top_page_url]\">" . htmlspecialchars($waiting_row['community_name']) . "</a>";
		}
		echo "</td>";

		// ¼«¸Ê¾Ò²ð
		echo "<td width=\"50px\" id=\"myttl\" bgcolor=\"#DEEEBD\">" . ACSMsg::get_msg("User", "WaitingList.tpl.php",'M003') . "</td>";
		echo "<td bgcolor=\"#ffffff\">";
		echo nl2br(htmlspecialchars($waiting_row['waiting_community_row']['contents_row_array']['community_profile']['contents_value']));
		echo "</td>";
		echo "</tr>\n";

		// ¥«¥Æ¥´¥ê
		echo "<tr>";
		echo "<td width=\"50px\" id=\"myttl\" bgcolor=\"#DEEEBD\"><nobr>" . ACSMsg::get_msg("User", "WaitingList.tpl.php",'M004') . "</nobr></td>";
		echo "<td bgcolor=\"#ffffff\">" . $waiting_row['waiting_community_row']['category_name'] . "</td>";
		echo "</tr>\n";

		echo "<td colspan=\"2\" id=\"myttl\" bgcolor=\"#DEEEBD\">";

		echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "WaitingList.tpl.php",'NAME'),array(
				"{USER_NAME}" => "<a href=\"" .$waiting_row['entry_user_info_row']['top_page_url'] ."\">" .
						htmlspecialchars($waiting_row['entry_user_info_row']['community_name']) . "</a>"));

		echo "</td>";
		echo "</tr>\n";

	} else {
		echo "<tr>";
		echo "<td align=\"center\" rowspan=\"4\" bgcolor=\"#ffffff\">";
		echo "<a href=\"$waiting_row[top_page_url]\"><img src=\"$waiting_row[image_url]\" border=\"0\"></a><br>";
		echo "<a href=\"$waiting_row[top_page_url]\">" . htmlspecialchars($waiting_row['community_name']) . "</a>";
		echo "</td>";
		echo "<td id=\"myttl\" colspan=\"2\" bgcolor=\"#DEEEBD\">";
		echo ACSMsg::get_msg("User", "WaitingList.tpl.php",'M005')."</td>";
		echo "</tr>\n";
	}

	echo "<tr>";
	echo "<td colspan=\"2\" valign=\"top\" width=\"450px\" bgcolor=\"#ffffff\">";
	echo ACSMsg::get_msg("User", "WaitingList.tpl.php",'M006')." : " . $waiting_row['entry_date'] . "<br><br>";
	echo nl2br(htmlspecialchars($waiting_row['message']));
	echo "</td>";
	echo "</tr>\n";

	echo "<tr>";
	echo "<td id=\"myttl\" colspan=\"2\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "WaitingList.tpl.php",'M007')." <span class=\"notice\">".ACSMsg::get_msg("User", "WaitingList.tpl.php",'M008')."</span></td>";
	echo "</tr>\n";

	echo "<tr>";
	echo "<td colspan=\"2\" bgcolor=\"#ffffff\">";
	echo "<textarea name=\"reply_message\" cols=\"50\" rows=\"4\"></textarea>";
	echo "</td>";
	echo "</tr>\n";

	echo "<tr><td colspan=\"3\" bgcolor=\"#ffffff\">";
	echo "<input type=\"submit\" name=\"accept_button\" value=\"".ACSMsg::get_msg("User", "WaitingList.tpl.php",'M009')."\">\n";
	echo "&nbsp;";
	echo "<input type=\"submit\" name=\"reject_button\" value=\"".ACSMsg::get_msg("User", "WaitingList.tpl.php",'M010')."\">\n";
	echo "</td></tr>\n";

	echo "</table>\n";
	echo "<br>\n";

	echo "</form>\n";
	echo "<br><br>\n";
}
?>
