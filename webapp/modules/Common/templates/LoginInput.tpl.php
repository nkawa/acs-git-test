<?php
// $Id: LoginInput.tpl.php,v 1.00 2008/03/03 19:00:00 y-yuki Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("Common", "Index.tpl.php",'M001') ?></div>
<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>
<p>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">
<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
<input type="hidden" name="search" value="1">

<table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
 <tr>
  <td bgcolor="#FFF5AA">
<table class="layout_table">
<tr>
<td><?= ACSMsg::get_msg("Common", "Index.tpl.php",'M002') ?></td>
<td><input type="text" name="userid" value="<?= htmlspecialchars($_POST['userid']) ?>" size="30" style="width:200px" tabindex="1"></td>
</tr>
<tr>
<td><?= ACSMsg::get_msg("Common", "Index.tpl.php",'M003') ?></td>
<td><input type="password" name="passwd" value="<?= htmlspecialchars($_POST['passwd']) ?>" size="30" style="width:200px" tabindex="2"></td>
</tr>
<tr>
<td colspan="2" align="center">
<input type="submit" value="<?= ACSMsg::get_msg("Common", "Index.tpl.php",'M004') ?>" tabindex="3">
</td>
</tr>
</table>
</td></tr></table>
</form>
</p>
