<?php
	$title  = '<a href="' . $this->_tpl_vars['community_top_page_url'] . '">';
	$title .= htmlspecialchars($this->_tpl_vars['target_community_name']);
	$title .= ' '.ACSMsg::get_msg("Community", "LeaveCommunity_confirm.tpl.php",'M001');
	$title .= '</a>';
	$title .= " :: ".ACSMsg::get_msg("Community", "LeaveCommunity_confirm.tpl.php",'M002');
?>
<span class="sub_title"><?= $title ?></span>

<div class="confirm_msg">
<?= ACSMsg::get_msg("Community", "LeaveCommunity_confirm.tpl.php",'M003') ?><br>
<?= ACSMsg::get_msg("Community", "LeaveCommunity_confirm.tpl.php",'M004') ?><br>
<?= ACSMsg::get_msg("Community", "LeaveCommunity_confirm.tpl.php",'M005') ?><br>
</div>

<p>
<form name="delete" method="POST" action="<?=$this->_tpl_vars['leave_action_url'] ?>">
<input type="submit" value="<?= ACSMsg::get_msg("Community", "LeaveCommunity_confirm.tpl.php",'M006') ?>">&nbsp;
<input type="button" onClick="location.href='<?=$this->_tpl_vars['cancel_action_url'] ?>'" value="<?= ACSMsg::get_msg("Community", "LeaveCommunity_confirm.tpl.php",'M007') ?>">
</form>
</p>
