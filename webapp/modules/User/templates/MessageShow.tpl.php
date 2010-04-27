<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: nakau  v 1.0 2008/03/11 00:40:31	                          |
// |User　message詳細画面                           　　　　　 　　　　　 |
// +----------------------------------------------------------------------+
// $Id: MessageShow.tpl.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $
?>



<table class="layout_table" width="665px">
<tr>
<td valign="top">
<!-------------------------------->
<table class="layout_table">
<tr>
<td class="nowrap">
<br>
<span class="sub_title"><?= ACSMsg::get_msg("User", "MessageShow.tpl.php",'M001') ?></span><br>
<br>

<table border="0" cellpadding="8" cellspacing="1" bgcolor="#99CC33">
<tr> 
<td bgcolor="#FFFFFF">
<table border="0" cellspacing="10" cellpadding="5">
<tr><td width="60px" height="60px" align="center">
<a href="<?=$this->_tpl_vars['menu']['receiv_box_url'] ?>">
<?= ACSMsg::get_msg("User", "MessageShow.tpl.php",'M002') ?>
</a><br>
<a href="<?=$this->_tpl_vars['menu']['send_box_url'] ?>">
<?= ACSMsg::get_msg("User", "MessageShow.tpl.php",'M003') ?>
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
		echo ACSMsg::get_msg("User", "MessageShow.tpl.php",'M004');
	} else {
		echo ACSMsg::get_msg("User", "MessageShow.tpl.php",'M005');
	}
	echo "</div><br><br>\n";
?>

<form method="post">

<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<?php
	if ($this->_tpl_vars['move_id'] == 2) {
		echo "<td height=30px id=\"myttl\" bgcolor=\"#DEEEBD\">". ACSMsg::get_msg("User", "MessageShow.tpl.php",'M006')."</td>";
	} else {
		echo "<td height=30px id=\"myttl\" bgcolor=\"#DEEEBD\">". ACSMsg::get_msg("User", "MessageShow.tpl.php",'M007')."</td>";
	}
?>
		<td bgcolor="#ffffff">
		<a href="<?= $this->_tpl_vars['link_page_url']['else_user_message_url'] ?>">
		<?= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "MessageShow.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['message_row']['user_name']))) ?>
		</a>
		</td>
</tr>
<tr>
	<td height=30px id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "MessageShow.tpl.php",'M008') ?></td>
		<td bgcolor="#ffffff">
		<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['message_row']['post_date']))) ?>
		</td>
</tr>
<tr>
	<td height=30px id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "MessageShow.tpl.php",'M009') ?></td>
		<td bgcolor="#ffffff">
		<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['message_row']['subject']))) ?>
		</td>
</tr>
<tr>
	<td height=150px id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "MessageShow.tpl.php",'M010') ?></td>
		<td valign=top bgcolor="#ffffff" width="500px">
		<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['message_row']['body']))) ?>
		</td>
</tr>

</table>


<?php
	if ($this->_tpl_vars['move_id'] != 2) {
		echo "<br>";
		echo "<input type=\"button\" value=\"".ACSMsg::get_msg("User", "MessageShow.tpl.php",'M011')."\" onclick=\"location.href='".$this->_tpl_vars['message_return_url']."'\">";
	}
?>
</form>

<!---------------------------------------------------------------->

<br><br>

<!---------------------------------------------------------------->


</td>
</table>
