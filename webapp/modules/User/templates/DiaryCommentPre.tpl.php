<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/03/02 ver1.0                 |
// |�������꡼ �����ȡ���ǧ����Ͽ����                                   ���������������������� |
// +----------------------------------------------------------------------+
// 
// $Id: DiaryCommentPre.tpl.php,v 1.7 2007/03/30 05:27:23 w-ota Exp $
?>

<!-- HTML -->

<?php
//��ʬ�������ʤ�С��֥ޥ��������꡼�פ���뤳�Ȥ��Ǥ���
if ($this->_tpl_vars['is_self_page']) {
	echo "<div class=\"sub_title\">";
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['diary_top_page_url'] ."\">";
	echo ACSMsg::get_msg("User", "DiaryCommentPre.tpl.php",'M001')."</a> :: ";
} else {
// ���������������¾�ͤ������ʤ�С��֡��������TOP�ס֡�������Υ������꡼�פ���뤳�Ȥ��Ǥ���
	echo "<div class=\"sub_title\">";
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_diary_url'] ."\">";
	//echo htmlspecialchars($this->_tpl_vars['community_row']['community_name']) . "����</a> :: ";
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "DiaryCommentPre.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['community_row']['community_name'])));
	echo "</a> :: ";

	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['diary_top_page_url'] ."\">";
	echo ACSMsg::get_msg("User", "DiaryCommentPre.tpl.php",'M002')."</a> :: ";
}
	echo "<a href=\"" .$this->_tpl_vars['diary_comment_page_url'] ."\">".ACSMsg::get_msg("User", "DiaryCommentPre.tpl.php",'M003')."</a>\n";
	echo " :: ".ACSMsg::get_msg("User", "DiaryCommentPre.tpl.php",'M004')."</div><br><br>\n";
?>

<?php
	if ($this->_tpl_vars['error_message']) {
		// ���顼��å�����ɽ��
		ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
	} else {
		echo '<div class="confirm_msg">';
		echo ACSMsg::get_msg("User", "DiaryCommentPre.tpl.php",'M005').'<br>';
		echo ACSMsg::get_msg("User", "DiaryCommentPre.tpl.php",'M006');
		echo '</div>';
	}
?>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post" name="bbs_form" enctype="multipart/form-data">

<!-- �������� -->
<table class="table.confirm_table">
	<colgroup class="required">
	<colgroup class="value">

<tr>
	<td height=150px><?= ACSMsg::get_msg("User", "DiaryCommentPre.tpl.php",'M008') ?></td>
		<td valign=top>
		<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['form']['body']))) ?>
		</td>
</tr>

</table>

<br><br>
<?php
	if (!$this->_tpl_vars['error_message']) {
		echo '<input type="submit" value="OK">&nbsp;';
	}
?>
    <input type="button" value="<?= ACSMsg::get_msg("User", "DiaryCommentPre.tpl.php",'M007') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">&nbsp;
</form>
<br>
