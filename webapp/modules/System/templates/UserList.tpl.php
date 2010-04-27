<?php
// $Id: UserList.tpl.php,v 1.6 2008/03/24 07:00:36 y-yuki Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("System", "UserList.tpl.php",'M001') ?></div>
<br><br>

<a href="<?= $this->_tpl_vars['add_user_url'] ?>"><?= ACSMsg::get_msg("System", "UserList.tpl.php",'M002') ?></a><br><br>

<p>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="get">
<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
<input type="hidden" name="search" value="1">

<table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
 <tr>
  <td bgcolor="#FFF5AA">
<?= ACSMsg::get_msg("System", "UserList.tpl.php",'M003') ?> <input type="text" name="q" value="<?= htmlspecialchars($this->_tpl_vars['form']['q']) ?>" size="30">
<input type="submit" value="<?= ACSMsg::get_msg("System", "UserList.tpl.php",'M004') ?>">
</tr></td>
</table>
</form>
</p>


<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>


<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD">&nbsp;</td>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "UserList.tpl.php",'M005') ?></td>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "UserList.tpl.php",'M006') ?></td>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "UserList.tpl.php",'M007') ?></td>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "UserList.tpl.php",'M008') ?></td>
</tr>

<?php
foreach ($this->_tpl_vars['user_info_row_array'] as $user_info_row) {
	echo "<tr>\n";
	echo "<td bgcolor=\"#ffffff\">[<a href=";
	echo $user_info_row['edit_page_url'];
	echo ">".ACSMsg::get_msg("System", "UserList.tpl.php",'M009')."</a>][<a href=";
	echo $user_info_row['delete_page_url'];
	//echo ">".ACSMsg::get_msg("System", "UserList.tpl.php",'M010')."</a>]</td>";
	echo ">".ACSMsg::get_msg("System", "UserList.tpl.php",'M010')."</a>][<a href=";
	echo $user_info_row['login_info_url'];
	echo ">".ACSMsg::get_msg("System", "UserList.tpl.php",'M011')."</a>]</td>";
	echo "<td bgcolor=\"#ffffff\">" . htmlspecialchars($user_info_row['user_id']) . "</td>";
	echo "<td bgcolor=\"#ffffff\">" . htmlspecialchars($user_info_row['user_name']) . "</td>";
	echo "<td bgcolor=\"#ffffff\"><a href=\"" .$user_info_row['top_page_url']. "\">" . htmlspecialchars($user_info_row['community_name']) . "</a></td>";
	echo "<td bgcolor=\"#ffffff\">";
	if ($user_info_row['mail_addr'] != '') {
		echo htmlspecialchars($user_info_row['mail_addr']);
	} else {
		echo "&nbsp;";
	}
	echo "</td>";
	echo "</tr>\n";
}
?>
</table>

<br>
