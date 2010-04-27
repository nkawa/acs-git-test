<?php
// $Id: JoinCommunity_admission.tpl.php,v 1.2 2006/11/20 08:44:14 w-ota Exp $
?>

<span class="sub_title">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?> <?= ACSMsg::get_msg("Community", "JoinCommunity_admission.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "JoinCommunity_admission.tpl.php",'M002') ?>
</span>
<br><br>

<?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "JoinCommunity_admission.tpl.php",'M003') ?><br>
<br>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<?= ACSMsg::get_msg("Community", "JoinCommunity_admission.tpl.php",'M004') ?> :<br>
<textarea name="message" cols="50" rows="4"></textarea><br>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("Community", "JoinCommunity_admission.tpl.php",'M005') ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("Community", "JoinCommunity_admission.tpl.php",'M006') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">

</form>
