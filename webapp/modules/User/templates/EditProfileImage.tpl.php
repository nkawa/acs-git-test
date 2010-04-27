<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4	    												  |
// | Authors: akitsu		 2006/2/8									  |
// +----------------------------------------------------------------------+
//　EditProfileImage.tpl.php
// $Id: EditProfileImage.tpl.php,v 1.7 2008/03/24 07:00:36 y-yuki Exp $
//
// @image_new_mode = 画像情報の登録がない　not　登録がある
?>

<div class="sub_title"><?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M001') ?></div>

<?php
// プロフィール表示拡張
?>
   <!-- 画像情報 -->	
<form name="upload_file" action="<?= $this->_tpl_vars['upload_image_url']['file_id_ol05'] ?>" method="POST" enctype="multipart/form-data">
<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
	<tr>
		<td><?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M005') ?></td>
	</tr>
	<tr>
		<td bgcolor="#ffffff">
			<img src="<?=$this->_tpl_vars['image_file_array'][0] ?>" style="margin-top:10px;margin-bottom:10px"><br>
		</td>
		<td bgcolor="#ffffff">
			<table border=0>
				<tr>
					<td colspan="2">
						<input type="file" name="new_file" size="30">
					</td>
				</tr>
				<tr>
					<td>
<?php
	     /* 削除パス情報出力 */
	        // 削除意思を確認する
			$path="";	 
			if(!$this->_tpl_vars['menu']['image_new_mode05']){
?>
						<input type="button" value="<?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M002') ?>" onclick="location.href='<?= $this->_tpl_vars['menu']['delete_image_url05'] ?>'">
<?php
			}
 ?>
 					</td>
 					<td>
 						<input type="submit" value="<?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M003') ?>">
 					</td>
 				</tr>
 			</table>
		</td>
	</tr>

</form>

	
<form name="upload_file" action="<?= $this->_tpl_vars['upload_image_url']['file_id_ol02'] ?>" method="POST" enctype="multipart/form-data">

	<tr>
		<td><?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M006') ?></td>
	</tr>
	<tr>
		<td bgcolor="#ffffff">
			<img src="<?=$this->_tpl_vars['image_file_array'][1] ?>" style="margin-top:10px;margin-bottom:10px"><br>
		</td>
		<td bgcolor="#ffffff">
			<table border=0>
				<tr>
					<td colspan="2">
						<input type="file" name="new_file" size="30">
					</td>
				</tr>
				<tr>
					<td>
<?php
	     /* 削除パス情報出力 */
	        // 削除意思を確認する
			$path="";	 
			if(!$this->_tpl_vars['menu']['image_new_mode02']){
?>
						<input type="button" value="<?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M002') ?>" onclick="location.href='<?= $this->_tpl_vars['menu']['delete_image_url02'] ?>'">
<?php
			}
 ?>
 					</td>
 					<td>
 						<input type="submit" value="<?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M003') ?>">
 					</td>
 				</tr>
 			</table>
		</td>
	</tr>
</form>

<?php
if($this->_tpl_vars['display_for_public'] == "1"){
?>
<form name="upload_file" action="<?= $this->_tpl_vars['upload_image_url']['file_id_ol01'] ?>" method="POST" enctype="multipart/form-data">
	<tr>
		<td><?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M007') ?></td>
	</tr>
	<tr>
		<td bgcolor="#ffffff">
			<img src="<?=$this->_tpl_vars['image_file_array'][2] ?>" style="margin-top:10px;margin-bottom:10px"><br>
		</td>
		<td bgcolor="#ffffff">
			<table border=0>
				<tr>
					<td colspan="2">
						<input type="file" name="new_file" size="30">
					</td>
				</tr>
				<tr>
					<td>
<?php
	     /* 削除パス情報出力 */
	        // 削除意思を確認する
			$path="";	 
			if(!$this->_tpl_vars['menu']['image_new_mode01']){
?>
						<input type="button" value="<?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M002') ?>" onclick="location.href='<?= $this->_tpl_vars['menu']['delete_image_url01'] ?>'">
<?php
			}
 ?>
 					</td>
 					<td>
 						<input type="submit" value="<?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M003') ?>">
 					</td>
 				</tr>
 			</table>
		</td>
	</tr>
</form>
<?php
}
?>
</table>

		<p>
		  <a href="./"><?= ACSMsg::get_msg("User", "EditProfileImage.tpl.php",'M004') ?></a>
		</p>

