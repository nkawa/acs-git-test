<?php
	$title  = '<a href="' . $this->_tpl_vars['community_top_page_url'] . '">';
	$title .= htmlspecialchars($this->_tpl_vars['target_community_name']);
	$title .= ACSMsg::get_msg("Community", "DeleteCommunity_confirm.tpl.php",'M001');
	$title .= '</a>';
	$title .= " :: ".ACSMsg::get_msg("Community", "DeleteCommunity_confirm.tpl.php",'M002');
?>
<div class="ttl"><?= $title ?></div>

<div class="confirm_msg">
<?= ACSMsg::get_msg("Community", "DeleteCommunity_confirm.tpl.php",'M003') ?><br>
<?= ACSMsg::get_msg("Community", "DeleteCommunity_confirm.tpl.php",'M004') ?><br>
<?= ACSMsg::get_msg("Community", "DeleteCommunity_confirm.tpl.php",'M005') ?><br>
</div>

<form name="delete" method="POST" action="<?=$this->_tpl_vars['delete_action_url'] ?>">

<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
	<td align="center" bgcolor="#ffffff">
		<a href="<?=$this->_tpl_vars['delete_community_row']['top_page_url']?>">
         <img src="<?=$this->_tpl_vars['delete_community_row']['image_url'] ?>" border="0"></a><br>
		<a href="<?=$this->_tpl_vars['delete_community_row']['top_page_url']?>"><?=htmlspecialchars($this->_tpl_vars['delete_community_row']['community_name']) ?></a>
	</td>
	<td valign="top" bgcolor="#ffffff">
		<?= ACSMsg::get_msg("Community", "DeleteCommunity_confirm.tpl.php",'M006') ?><br>
		<?=nl2br(htmlspecialchars($this->_tpl_vars['delete_community_row']['community_profile'])) ?>
	</td>
</tr>
</table>

<p>
<input type="submit" value="<?= ACSMsg::get_msg("Community", "DeleteCommunity_confirm.tpl.php",'M007') ?>">&nbsp;
<input type="button" onClick="location.href='<?=$this->_tpl_vars['cancel_action_url'] ?>'" value="<?= ACSMsg::get_msg("Community", "DeleteCommunity_confirm.tpl.php",'M008') ?>">
</p>
</form>
