<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: nakau 2008/03/11 ver1.0                 |
// |メッセージ　確認・登録画面                                   　　　　　　　　　　　 |
// +----------------------------------------------------------------------+
// 
// $Id: MessagePre.tpl.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $
?>

<!-- HTML -->
<div class="ttl">
<?php
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_Message_url'] ."\">";
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "MessagePre.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	echo "</a> :: ";

	echo ACSMsg::get_msg("User", "MessagePre.tpl.php",'M001')."</div><br><br>\n";
?>
</div>

<?php
	if ($this->_tpl_vars['error_message']) {
		// エラーメッセージ表示
		ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
	} else {
		echo '<div class="confirm_msg">';
		echo ACSMsg::get_msg("User", "MessagePre.tpl.php",'M002').'<br>';
		echo ACSMsg::get_msg("User", "MessagePre.tpl.php",'M003');
		echo '</div>';
	}
?>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post" name="message_form" enctype="multipart/form-data">

<!-- メッセージ情報 -->
<table class="table.confirm_table">
	<colgroup class="required">
	<colgroup class="value">

<tr>
	<td height=30px><?= ACSMsg::get_msg("User", "MessagePre.tpl.php",'M004') ?></td>
		<td>
		<a href="<?= $this->_tpl_vars['link_page_url']['else_user_Message_url'] ?>">
		<?= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "MessagePre.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']))) ?>
		</a>
		</td>
</tr>
<tr>
	<td height=30px><?= ACSMsg::get_msg("User", "MessagePre.tpl.php",'M005') ?></td>
		<td>
		<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['form']['subject']))) ?>
		</td>
</tr>
<tr>
	<td height=150px><?= ACSMsg::get_msg("User", "MessagePre.tpl.php",'M006') ?></td>
		<td valign=top>
		<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['form']['body']))) ?>
		</td>
</tr>

</table>

<br><br>
<?php
	if (!$this->_tpl_vars['error_message']) {
		if ($this->_tpl_vars['form']['info_mail'] == "on") {
			echo ACSMsg::get_msg("User", "MessagePre.tpl.php",'M007');
			echo "<br><br>";
		}
		echo "<input type=\"submit\" value=\"".ACSMsg::get_msg("User", "MessagePre.tpl.php",'M008')."\">&nbsp;";
	}
	echo "<input type=\"hidden\" name=\"acs_user_id\" value=\"".$this->_tpl_vars['acs_user_info_row']['user_community_id']."\">";
?>
    <input type="button" value="<?= ACSMsg::get_msg("User", "MessagePre.tpl.php",'M009') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">&nbsp;
</form>
<br>
