<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/2/23 ver1.0                                     |
// |削除意思を確認するためのダイアログ                                    |
// +----------------------------------------------------------------------+
//
// $Id: DeleteBBSRes.tpl.php,v 1.0
?>

<div class="ttl">
	<?= ACSMsg::get_msg("Community", "DeleteBBSRes.tpl.php",'M001') ?>
</div>
<br><br>

<div class="confirm_msg">
	<?= ACSMsg::get_msg("Community", "DeleteBBSRes.tpl.php",'M002') ?><br>
	<?= ACSMsg::get_msg("Community", "DeleteBBSRes.tpl.php",'M003') ?><br><br>
	<?= ACSMsg::get_msg("Community", "DeleteBBSRes.tpl.php",'M004') ?><br><br>
</div>

<form name="delete_file" action="<?= $this->_tpl_vars['delete_bbs_res_url'] ?>" method="post" enctype="multipart/form-data">
	<input type="submit" value="<?= ACSMsg::get_msg("Community", "DeleteBBSRes.tpl.php",'M005') ?>">&nbsp;
	<input type="button" value="<?= ACSMsg::get_msg("Community", "DeleteBBSRes.tpl.php",'M006') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">
</form>
