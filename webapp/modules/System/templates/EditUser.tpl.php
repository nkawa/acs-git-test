<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/3/13 ver1.0                                     |
// | システム　ユーザ情報を変更する画面                                   |
// +----------------------------------------------------------------------+
// $Id: EditSystemConfig.tpl.php,v 1.6 2008/04/24 16:00:00 y-yuki Exp $
?>

<SCRIPT language="JavaScript">
<!--
// パスワード変更の意思があったら入力を有効とする
function fmTurn(){
	if(document.edit_user_form.passwd_change.checked){
		document.edit_user_form.passwd.disabled = false;
		document.edit_user_form.passwd2.disabled = false;
		document.edit_user_form.passwd.focus();
	}else{
		document.edit_user_form.passwd.disabled = true;
		document.edit_user_form.passwd2.disabled = true;
	}
}
//-->
</SCRIPT>

<div class="ttl"><?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M001') ?></div>
<br><br>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post" name="edit_user_form">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M002') ?></td>
<td bgcolor="#ffffff">
	<?php
		echo "<input type=\"text\" name=\"user_id\" size=\"30\" value=\"";
		echo $this->_tpl_vars['user_info_row']['user_id'];
		echo "\">";
		//echo "\" readonly>";

		echo "<input type=\"hidden\" name=\"old_user_id\" value=\"";
		echo $this->_tpl_vars['user_info_row']['user_id'];
		echo "\">";
		?>
</td>
</tr>

<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M003') ?></td>
<td bgcolor="#ffffff">
<input type="checkbox" name ="passwd_change" value="change_on" onClick="fmTurn()"><?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M004') ?><br>
<input type="password" name="passwd" size="20" style = "" disabled><br>
<input type="password" name="passwd2" size="20" disabled> <?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M005') ?><br>
	<span class="comment">
	<?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M006') ?>
	</span>
</td>
</tr>

<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M007') ?></td>
<td bgcolor="#ffffff">
	<?php
		echo "<input type=\"text\" name=\"user_name\" size=\"30\" value=\"";
		echo $this->_tpl_vars['user_info_row']['user_name'];
		echo "\" style=\"ime-mode: active;\">";
	?>
</td>
</tr>

<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M008') ?></td>
<td bgcolor="#ffffff">
	<?php
		echo "<input type=\"text\" name=\"mail_addr\" size=\"40\" value=\"";
		echo $this->_tpl_vars['user_info_row']['mail_addr'];
		echo "\" style=\"ime-mode: inactive;\">";
	?>
	</td>
</tr>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M009') ?>">&nbsp;
	<input type="button" value="<?= ACSMsg::get_msg("System", "EditUser.tpl.php",'M010') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'"><br>
</form>
<br>
