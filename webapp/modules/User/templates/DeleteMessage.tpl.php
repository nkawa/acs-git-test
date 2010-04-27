<div class="sub_title"><?= ACSMsg::get_msg("User", "DeleteMessage.tpl.php",'M001') ?></div>

<div class="confirm_msg">
<?= ACSMsg::get_msg("User", "DeleteMessage.tpl.php",'M002') ?><br>
<?= ACSMsg::get_msg("User", "DeleteMessage.tpl.php",'M003') ?><br>
<?= ACSMsg::get_msg("User", "DeleteMessage.tpl.php",'M004') ?><br>
</div>

<form name="delete_Message" method="POST" action="<?= $this->_tpl_vars['action_url'] ?>">

<p>
<table class="file_list_table">
<?php
// 削除対象のメッセージ
foreach ($this->_tpl_vars['message_id_array'] as $message) {
	print '<input type="hidden" name="selected_message[]" value="' . $message['message_id'] . '">';
}
?>
</table>
</p>

<input type="submit" value="<?= ACSMsg::get_msg("User", "DeleteMessage.tpl.php",'M005') ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("User", "DeleteMessage.tpl.php",'M006') ?>" onClick="history.back()">
</form>
