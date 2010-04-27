<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/3/13 ver1.0                                     |
// |削除意思を確認するためのダイアログ                                    |
// +----------------------------------------------------------------------+
// ダイアリー
// $Id: DeleteUser.tpl.php,v 1.3 2007/03/01 09:01:41 w-ota Exp $
?>

<div class="ttl">
	<?= ACSMsg::get_msg("System", "DeleteUser.tpl.php",'M001') ?>
</div>
<br><br>

<div class="confirm_msg">
<?php
	/*
	登録されているユーザ［
	echo $this->_tpl_vars['user_info_row']['user_id'];
	echo " : ";
	echo $this->_tpl_vars['user_info_row']['user_name'];
	］を削除します。
	*/
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("System", "DeleteUser.tpl.php",'DELM'),
			array("{USER_ID}" => $this->_tpl_vars['user_info_row']['user_id'],
				 "{USER_NAME}" => $this->_tpl_vars['user_info_row']['user_name']));
?><br>
	<?= ACSMsg::get_msg("System", "DeleteUser.tpl.php",'M002') ?><br>
	<?= ACSMsg::get_msg("System", "DeleteUser.tpl.php",'M003') ?><br><br>
	<?= ACSMsg::get_msg("System", "DeleteUser.tpl.php",'M004') ?><br><br>
</div>

<form name="delete_file" action="<?= $this->_tpl_vars['delete_user_url'] ?>" method="post" enctype="multipart/form-data">
	<input type="submit" value="<?= ACSMsg::get_msg("System", "DeleteUser.tpl.php",'M005') ?>">&nbsp;
	<input type="button" value="<?= ACSMsg::get_msg("System", "DeleteUser.tpl.php",'M006') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">
</form>
