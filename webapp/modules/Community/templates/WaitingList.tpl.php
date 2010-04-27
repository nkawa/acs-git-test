<?php
// $Id: WaitingList.tpl.php,v 1.8 2007/03/14 04:28:17 w-ota Exp $
?>

<span class="sub_title">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?> <?= ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M001') ?></a> :: <span class="sub_title"><?= $this->_tpl_vars['waiting_type_name'] ?> <?= ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M002') ?></span>
</span>
<br><br>

<?php
foreach ($this->_tpl_vars['waiting_row_array'] as $waiting_row) {
	echo "<form action=\"$waiting_row[action_url]\" method=\"post\">\n";

	echo "<table border=\"1\" class=\"common_table\">\n";
	echo "<tr>";
	echo "<td align=\"center\" rowspan=\"4\">";
	echo "<a href=\"$waiting_row[top_page_url]\"><img src=\"$waiting_row[image_url]\" border=\"0\"></a><br>";
	echo "<a href=\"$waiting_row[top_page_url]\">" . htmlspecialchars($waiting_row['community_name']) . "</a>";
	echo "</td>";
	echo "<td>";
	if ($this->_tpl_vars['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D40') || $this->_tpl_vars['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D50')) {
		echo "<a href=\"{$waiting_row['entry_user_info_row']['top_page_url']}\">" . htmlspecialchars($waiting_row['entry_user_info_row']['community_name']) . "</a>";
		echo ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M003');
	}
	echo ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M004')."</td>";
	echo "</tr>\n";

	echo "<tr>";
	echo "<td valign=\"top\">";
	echo ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M005')." : " . $waiting_row['entry_date'] . "<br><br>";
	echo nl2br(htmlspecialchars($waiting_row['message']));
	echo "</td>";
	echo "</tr>\n";

	echo "<tr>";
	echo "<td>".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M006')." <span class=\"notice\">".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M007')."</span></td>";
	echo "</tr>\n";

	echo "<tr>";
	echo "<td>";
	echo "<textarea name=\"reply_message\" cols=\"50\" rows=\"4\"></textarea>";
	echo "</td>";
	echo "</tr>\n";

	echo "<tr><td colspan=\"2\">";
	if ($this->_tpl_vars['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D40')) {
		echo "<a href=\"$this->_tpl_vars[community_top_page_url]\">" . htmlspecialchars($this->_tpl_vars['community_row']['community_name']) . "</a>" . " ".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M008')."<br>\n";
		echo "　".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M009')."<br>\n";
		echo "　".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M010')." " . "<a href=\"$waiting_row[top_page_url]\">" . htmlspecialchars($waiting_row['community_name']) . "</a>" . " ".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M013')."<br>\n";
		echo "<br>\n";
		echo ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M011')."<br><br>\n";
	} else	if ($this->_tpl_vars['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D50')) {
		echo "<a href=\"$waiting_row[top_page_url]\">" . htmlspecialchars($waiting_row['community_name']) . "</a>" . " ".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M008')."<br>\n";
		echo "　".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M009')."<br>\n";
		echo "　".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M010')." " . "<a href=\"$this->_tpl_vars[community_top_page_url]\">" . htmlspecialchars($this->_tpl_vars['community_row']['community_name']) . "</a>" . " ".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M013')."<br>\n";
		echo "<br>\n";
		echo ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M011')."<br><br>\n";
	}

	echo "<input type=\"submit\" name=\"accept_button\" value=\"".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M015')."\">\n";
	echo "&nbsp;";
	echo "<input type=\"submit\" name=\"reject_button\" value=\"".ACSMsg::get_msg("Community", "WaitingList.tpl.php",'M016')."\">\n";
	echo "</td></tr>\n";

	echo "</table>\n";
	echo "<br>\n";

	echo "</form>\n";
	echo "<br><br>\n";
}
?>
