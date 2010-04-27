<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: nakau  v 1.0 2008/03/11 00:40:31	                          |
// |User　MessageBox画面                            　　　　　 　　　　　 |
// +----------------------------------------------------------------------+
// $Id: MessageBox.tpl.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $
?>



<table class="layout_table" width="665px">
<tr>
<td valign="top">
<!-------------------------------->
<table class="layout_table">
<tr>
<td class="nowrap">
<br>
<span class="sub_title"><?= ACSMsg::get_msg("User", "MessageBox.tpl.php",'M001') ?></span><br>
<br>

<table border="0" cellpadding="8" cellspacing="1" bgcolor="#99CC33">
<tr> 
<td bgcolor="#FFFFFF">
<table border="0" cellspacing="10" cellpadding="5">
<tr><td width="60px" height="60px" align="center">
<a href="<?=$this->_tpl_vars['menu']['receiv_box_url'] ?>">
<?= ACSMsg::get_msg("User", "MessageBox.tpl.php",'M002') ?>
</a><br>
<a href="<?=$this->_tpl_vars['menu']['send_box_url'] ?>">
<?= ACSMsg::get_msg("User", "MessageBox.tpl.php",'M003') ?>
</a><br>
</td></tr>
</table>
</td></tr>
</table>

</td>
</tr>
</table>
<!-------------------------------->
</td>
<td width="15px">&nbsp;</td>
<td valign="top">

<?php
echo "<div class=\"ttl\">";
if ($this->_tpl_vars['move_id'] == 2) {
	echo ACSMsg::get_msg("User", "MessageBox.tpl.php",'M003');
} else {
	echo ACSMsg::get_msg("User", "MessageBox.tpl.php",'M002');
}
echo "</div>";

if ($this->_tpl_vars['error_message']) {
	echo ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
} else {
	echo "<br><br>\n";
}

if (count($this->_tpl_vars['message_row_array'])) {

	echo "<form name=\"message_box_form\" method=\"POST\" action=\"" . $this->_tpl_vars["message_delete_url"] . "\">";
	
	// ページング表示
	ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);

	// メッセージ一覧　テーブル
    echo "<table class=\"common_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";
    echo "<tr>";
    echo "<td WIDTH=30px id=\"myttl\" bgcolor=\"#DEEEBD\" nowrap>".ACSMsg::get_msg("User", "MessageBox.tpl.php",'M004')."</td>";
    echo "<td WIDTH=30px id=\"myttl\" bgcolor=\"#DEEEBD\" nowrap>".ACSMsg::get_msg("User", "MessageBox.tpl.php",'M005')."</td>";
    if ($this->_tpl_vars['move_id'] == 2) {
    	echo "<td WIDTH=130px id=\"myttl\" bgcolor=\"#DEEEBD\" nowrap>".ACSMsg::get_msg("User", "MessageBox.tpl.php",'M006')."</td>";
    } else {
	    echo "<td WIDTH=130px id=\"myttl\" bgcolor=\"#DEEEBD\" nowrap>".ACSMsg::get_msg("User", "MessageBox.tpl.php",'M007')."</td>";
    }
    echo "<td WIDTH=230px id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "MessageBox.tpl.php",'M008')."</td>";
    echo "<td WIDTH=140px id=\"myttl\" bgcolor=\"#DEEEBD\" nowrap>".ACSMsg::get_msg("User", "MessageBox.tpl.php",'M009')."</td>";
    echo "</tr>\n";
    foreach ($this->_tpl_vars['message_row_array'] as $message_row) {
		echo "<tr>";
		echo "<td bgcolor=\"#ffffff\" align=\"center\">";
		if ($message_row['read_flag'] == "f") {
			echo ACSMsg::get_msg("User", "MessageBox.tpl.php",'M010')."</td>";
		} else {
			echo ACSMsg::get_msg("User", "MessageBox.tpl.php",'M011')."</td>";
		}
		echo "<td bgcolor=\"#ffffff\" align=\"center\">";
		echo "<input type=\"checkbox\" name=\"selected_message[]\" value=\"".$message_row['message_id']."\"></td>";
		echo "<td bgcolor=\"#ffffff\">" ;
		echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "MessageBox.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($message_row['user_name'])));
		echo "</td>";
		echo "<td bgcolor=\"#ffffff\"><a href=\"".$message_row['message_show_url']."\">".htmlspecialchars($message_row['subject'])."</a></td>";
		echo "<td bgcolor=\"#ffffff\">" . $message_row['short_post_date'] . "</td>";
		echo "</tr>\n";
	}
	echo "</table>";
	echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
    echo "<input type=\"submit\" value=\"".ACSMsg::get_msg("User", "MessageBox.tpl.php",'M005')."\">";
    echo "</form>";
} else {
	echo "<table width=510px><tr><td>";
	echo ACSMsg::get_msg("User", "MessageBox.tpl.php",'M012');
	echo "</td></tr></table>";
}

?>

<!---------------------------------------------------------------->

<br><br>

<!---------------------------------------------------------------->


</td>
</table>
