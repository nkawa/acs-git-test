<?php
// $Id: CommunityLink.tpl.php,v 1.3 2007/03/01 09:01:35 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "CommunityLink.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "CommunityLink.tpl.php",'M002') ?>
</div>


<br><br>


<a href="<?= $this->_tpl_vars['add_community_link_url'] ?>"><?= ACSMsg::get_msg("Community", "CommunityLink.tpl.php",'M003') ?></a><br>
<br>

<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
if ($this->_tpl_vars['parent_community_row_array']) {
	echo "<tr>\n";
	echo "<td bgcolor=\"#ffffff\">".ACSMsg::get_msg("Community", "CommunityLink.tpl.php",'M004')."</td>\n";
	echo "<td bgcolor=\"#ffffff\">";
	foreach ($this->_tpl_vars['parent_community_row_array'] as $parent_community_row) {
		echo "<a href=\"$parent_community_row[top_page_url]\">" . htmlspecialchars($parent_community_row['community_name']) . "</a>";
		echo " [<a href=\"$parent_community_row[delete_community_link_url]\">".ACSMsg::get_msg("Community", "CommunityLink.tpl.php",'M005')."</a>]";
		echo "<br>\n";
	}
	echo "</td>";
	echo "</tr>\n";
}

if ($this->_tpl_vars['sub_community_row_array']) {
	echo "<tr>\n";
	echo "<td bgcolor=\"#ffffff\">".ACSMsg::get_msg("Community", "CommunityLink.tpl.php",'M006')."</td>\n";
	echo "<td bgcolor=\"#ffffff\">";
	foreach ($this->_tpl_vars['sub_community_row_array'] as $sub_community_row) {
		echo "<a href=\"$sub_community_row[top_page_url]\">" . htmlspecialchars($sub_community_row['community_name']) . "</a>";
		echo " [<a href=\"$sub_community_row[delete_community_link_url]\">".ACSMsg::get_msg("Community", "CommunityLink.tpl.php",'M005')."</a>]";
		echo "<br>\n";
	}
	echo "</td>";
	echo "</tr>\n";
}
?>
</table>
