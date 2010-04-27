<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/2/16 ver1.0                                     |
// |削除意思を確認するためのダイアログ                                    |
// +----------------------------------------------------------------------+
//
// $Id: DeleteProfileImage.tpl.php,v 1.0
?>

<div class="sub_title">
	<?= ACSMsg::get_msg("Community", "DeleteProfileImage.tpl.php",'M001') ?>
</div>
<br><br>

<div class="confirm_msg">
	<?= ACSMsg::get_msg("Community", "DeleteProfileImage.tpl.php",'M002') ?><br>
	<?= ACSMsg::get_msg("Community", "DeleteProfileImage.tpl.php",'M003') ?><br>
	<?= ACSMsg::get_msg("Community", "DeleteProfileImage.tpl.php",'M004') ?><br><br>
</div>

<form name="delete_file" action="<?= $this->_tpl_vars['delete_image_url'] ?>" method="post" enctype="multipart/form-data">
	<input type="submit" value="<?= ACSMsg::get_msg("Community", "DeleteProfileImage.tpl.php",'M005') ?>">&nbsp;
	<input type="button" value="<?= ACSMsg::get_msg("Community", "DeleteProfileImage.tpl.php",'M006') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">
</form>
