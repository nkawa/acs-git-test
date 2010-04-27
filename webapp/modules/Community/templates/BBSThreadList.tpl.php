<?php
// $Id: BBSThreadList.tpl.php,v 1.4 2007/03/01 09:01:35 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "BBSThreadList.tpl.php",'M001') ?></a> :: <a href="<?= $this->_tpl_vars['bbs_top_page_url'] ?>"><?= ACSMsg::get_msg("Community", "BBSThreadList.tpl.php",'M002') ?></a> :: <?= ACSMsg::get_msg("Community", "BBSThreadList.tpl.php",'M003') ?>
</div>
<br><br>


<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
// スレッド一覧
foreach ($this->_tpl_vars['bbs_row_array'] as $index => $bbs_row) {
	echo "<tr>";
	echo "<td bgcolor=\"#ffffff\">" . $bbs_row['post_date'] . "</td>";
	echo "<td bgcolor=\"#ffffff\"><a href=\"" .$bbs_row['bbs_res_url']. "\">" . htmlspecialchars($bbs_row['subject']) . "</a></td>";
	echo "</tr>\n";
}
?>
</table>
