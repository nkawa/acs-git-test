<?php
// $Id: DeleteCommunityLink.tpl.php,v 1.2 2006/11/20 08:44:14 w-ota Exp $
?>

<div class="sub_title">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "DeleteCommunityLink.tpl.php",'M001') ?></a> :: <a href="<?= $this->_tpl_vars['community_link_url'] ?>"><?= ACSMsg::get_msg("Community", "DeleteCommunityLink.tpl.php",'M002') ?></a> :: <?= ACSMsg::get_msg("Community", "DeleteCommunityLink.tpl.php",'M003') ?>
</div>

<div class="confirm_msg">
<?= ACSMsg::get_msg("Community", "DeleteCommunityLink.tpl.php",'M004') ?><br>
<?= ACSMsg::get_msg("Community", "DeleteCommunityLink.tpl.php",'M005') ?><br>
</div>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<table class="common_table" border>
<?php
echo "<tr>\n";
if ($this->_tpl_vars['mode'] == 'parent') {
	echo "<td>".ACSMsg::get_msg("Community", "DeleteCommunityLink.tpl.php",'M006')."</td>\n";
} elseif ($this->_tpl_vars['mode'] == 'sub') {
	echo "<td>".ACSMsg::get_msg("Community", "DeleteCommunityLink.tpl.php",'M007')."</td>\n";
}
echo "<td>";
echo "<a href=\"{$this->_tpl_vars['delete_community_row']['top_page_url']}\">" . htmlspecialchars($this->_tpl_vars['delete_community_row']['community_name']) . "</a>";
echo "</td>";
echo "</tr>\n";
?>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("Community", "DeleteCommunityLink.tpl.php",'M008') ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("Community", "DeleteCommunityLink.tpl.php",'M009') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">
