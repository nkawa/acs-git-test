<?
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: w-ota  v 1.5 2006/03/07 07:32:55                            |
// |         update: akitsu 2006/3/8 ver1.0                               |
// |Diary　top画面                                   　　　　　　　　　　 |
// +----------------------------------------------------------------------+
// $Id: FriendsList.tpl.php,v 1.8 2006/11/20 08:44:26 w-ota Exp $
?>

<?php
//自分のフレンズならば、リンクは無い
if ($this->_tpl_vars['is_self_page']) {
	echo "<div class=\"ttl\">";
	echo ACSMsg::get_msg("User", "FriendsList.tpl.php",'M001');
} else {
// アクセス制限内で他人のフレンズならば、「○○さんのTOP」へ戻ることができる
	echo "<div class=\"ttl\">";
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_diary_url'] ."\">";
	//echo htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) . "さん</a> :: ";
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "FriendsList.tpl.php",'NAME'),array(
		"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	echo "</a> :: ";
	echo ACSMsg::get_msg("User", "FriendsList.tpl.php",'M002');
}
	echo " (" .$this->_tpl_vars['friends_row_array_num'] .")";
	echo "</div><br>\n";
?>

<?php
if ($this->_tpl_vars['friends_group_list_url'] != '') {
	echo "<a class=\"ichiran3\" href=\"" .$this->_tpl_vars['friends_group_list_url']. "\">".ACSMsg::get_msg("User", "FriendsList.tpl.php",'M003')."</a><br><br>\n";
}
?>

<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>


<table width="650" border="0" cellpadding="8" cellspacing="1" bgcolor="#99CC33">
<tr> 
<td bgcolor="#FFFFFF">
<table width="100%" border="0" cellspacing="10" cellpadding="5">
<?php
if (count($this->_tpl_vars['friends_row_array'])) {
	$count = 0;
	foreach ($this->_tpl_vars['friends_row_array'] as $user_info_row) {
		if ($count % 4 == 0) {
			echo "<tr>";
		}
		echo "<td class=\"mytbl\" align=\"center\">";
		echo "<a href=\"".$user_info_row['top_page_url']."\">" . "<img style=\"border:none;\" src=\"".$user_info_row['image_url']."\"><br>" . htmlspecialchars($user_info_row['community_name']) . "</a>";
		echo "(" . $user_info_row['friends_row_array_num'] . ")";
		if ($user_info_row['delete_friends_url']) {
			echo " ";
			echo "[<a href=\"".$user_info_row['delete_friends_url']."\">".ACSMsg::get_msg("User", "FriendsList.tpl.php",'M004')."</a>]";
		}
		echo "</td>";
		if ($count % 4 == 3) {
			echo "</tr>\n";
		}
		$count++;
	}
} else {
	echo "<tr>";
	echo "<td>".ACSMsg::get_msg("User", "FriendsList.tpl.php",'M005')."</td>";
	echo "</tr>\n";
}
?>
</table></td></tr>

</table>
<br>
