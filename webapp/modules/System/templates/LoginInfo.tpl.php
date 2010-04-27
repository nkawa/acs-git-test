<?php
// $Id: LoginInfo.tpl.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M001') ?></div>
<br><br>

<p>
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M002') ?></td>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M003') ?></td>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M004') ?></td>
</tr>
<tr>
<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['user_id']) ?></td>
<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['user_name']) ?></td>
<td bgcolor="#ffffff">
<a href="<?=$this->_tpl_vars['top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) ?></a></td>
</table>
</p>

<?php
if ($this->_tpl_vars['login_info_row_array']) {
	// ページング表示
	ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
	
	echo "<table class=\"common_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">";
	echo "<tr>";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">";
	echo ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M005')."</td>";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">";
	echo ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M006')."</td>";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">";
	echo ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M007')."</td>";
	echo "</tr>";

	foreach ($this->_tpl_vars['login_info_row_array'] as $login_info_row) {
		echo "<tr>\n";
		echo "<td bgcolor=\"#ffffff\">";
		echo $login_info_row['login_date'];
		echo "</td>";
		echo "<td bgcolor=\"#ffffff\">";
		echo $login_info_row['logout_date'];
		echo "</td>";
		echo "<td bgcolor=\"#ffffff\">";
		if ($login_info_row['use_button_flg'] == "f") {
			echo ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M008');
		} else {
			echo ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M009');
		}
		echo "</td>";
		echo "</tr>\n";
	}
	echo "</table>";
} else {
	echo ACSMsg::get_msg("System", "LoginInfo.tpl.php",'M010');
}
?>

<br>
