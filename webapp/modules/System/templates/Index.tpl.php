<?php
// $Id: Index.tpl.php,v 1.6 2007/03/01 09:01:41 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("System", "Index.tpl.php",'M001') ?></div>


<ul>
<li><a href="<?= $this->_tpl_vars['user_list_url'] ?>"><?= ACSMsg::get_msg("System", "Index.tpl.php",'M002') ?></a><br><br>
<li><a href="<?= $this->_tpl_vars['log_url'] ?>"><?= ACSMsg::get_msg("System", "Index.tpl.php",'M003') ?></a><br><br>
<li><a href="<?= $this->_tpl_vars['system_announce_list_url'] ?>"><?= ACSMsg::get_msg("System", "Index.tpl.php",'M004') ?></a><br><br>
<li><a href="<?= $this->_tpl_vars['edit_system_config_url'] ?>"><?= ACSMsg::get_msg("System", "Index.tpl.php",'M005') ?></a><br><br>
</ul>
