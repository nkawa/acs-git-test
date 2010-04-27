<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/3/02 ver1.0                                     |
// |削除意思を確認するためのダイアログ                                    |
// +----------------------------------------------------------------------+
// ダイアリー
// $Id: DeleteDiary.tpl.php,v 1.2 2006/11/20 08:44:26 w-ota Exp $
?>

<div class="sub_title">
	<?= ACSMsg::get_msg("User", "DeleteDiary.tpl.php",'M001') ?>
</div>
<br><br>

<div class="confirm_msg">
	<?= ACSMsg::get_msg("User", "DeleteDiary.tpl.php",'M002') ?><br>
	<?= ACSMsg::get_msg("User", "DeleteDiary.tpl.php",'M003') ?><br>
	<?= ACSMsg::get_msg("User", "DeleteDiary.tpl.php",'M004') ?><br><br>
	<?= ACSMsg::get_msg("User", "DeleteDiary.tpl.php",'M005') ?><br><br>
</div>

<form name="delete_file" action="<?= $this->_tpl_vars['delete_diary_url'] ?>" method="post" enctype="multipart/form-data">
	<input type="submit" value="<?= ACSMsg::get_msg("User", "DeleteDiary.tpl.php",'M006') ?>">&nbsp;
	<input type="button" value="<?= ACSMsg::get_msg("User", "DeleteDiary.tpl.php",'M007') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">
</form>
