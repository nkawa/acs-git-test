<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/3/13 ver1.0                                     |
// |Diary��������̲���                                   ���� �������������� |
// +----------------------------------------------------------------------+
// $Id: SearchResultDiary.tpl.php,v 1.9 2007/03/30 05:27:23 w-ota Exp $
?>
<script language="JavaScript">
<!--
	function fmTurn(){
	}
-->
</script>

<?php
//��ʬ�������ʤ�С���󥯤�̵��
if ($this->_tpl_vars['is_self_page']) {
	echo "<div class=\"ttl\">";
	echo ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M001')."</div><br><br>\n";
} else {
// ���������������¾�ͤ������ʤ�С��֡��������TOP�פ���뤳�Ȥ��Ǥ���
	echo "<div class=\"ttl\">";
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_top_page_url'] ."\">";
	//echo htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) . "����</a> :: ";

	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	echo "</a> :: ";

	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_diary_url'] ."\">".ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M002')."</a> :: ".ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M003')."</div><br><br>\n";
}
?>

<br>
<?php
	if($this->_tpl_vars['err_str'] != ""){
		echo "<div class='err_msg'>";
		echo $this->_tpl_vars['err_str'];
		echo "</div>";
	}else{
?>

<!--  �����ΰ��� ��̾�Τ� -->
<table>
<tr><td>
<?php
	echo "<b>".ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M004')."</b><br><br>";
	echo "<span class=\"result_success\">" . count($this->_tpl_vars['diary_row_array_result']) .ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M005')."</span><br><br>";
	// ����åɰ���
	$str = '';
	foreach ($this->_tpl_vars['diary_row_array_result'] as $index => $diary_row) {
		if($diary_row['diary_delete_flag']=='f'){			//����ե饰��̵����ΤΤ߰���ɽ������
			if ($str != '') {
				$str .= "&nbsp;&nbsp;";
			}
			$str .= "<a href=\"$diary_row[diary_comment_url]\">";
			$str .= htmlspecialchars($diary_row['subject']);
			$str .= "</a>";
		}
	}
	echo $str;
?>
</td></tr>
<!--  �����ΰ��� ��Ͽ������ʸ�Τ� -->
<tr><td>
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
foreach ($this->_tpl_vars['diary_row_array_result'] as $index => $diary_row) {
	echo "<tr>";
	echo "<td bgcolor=\"#DEEEBD\" colspan=\"2\">";
	echo "<b>" . htmlspecialchars($diary_row['subject']) . "</b>";
	echo "&nbsp;&nbsp;&nbsp;";
	echo $diary_row['post_date'];
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td align=\"center\" bgcolor=\"#ffffff\">";
	echo "<a href=\"$diary_row[top_page_url]\"><img src=\"$diary_row[image_url]\" border=\"0\"></a><br>";
	echo "<a href=\"$diary_row[top_page_url]\">" . htmlspecialchars($diary_row['community_name']) . "</a>";
	echo "</td>";

	echo "<td valign=\"top\" bgcolor=\"#ffffff\">";
	echo nl2br(ACSLib::sp2nbsp(htmlspecialchars($diary_row['body'])));
	echo "</td>";
	echo "</tr>";
}
?>
</table>
</td></tr></table>
<?php
	}
?>


<hr>

<!-- �Ƹ����ѤΥե����� -->
<?php
	// �����å��ܥå����������ͤ��������뤿��ν���
	// ��̾����
	if ($this->_tpl_vars['form_pre']['search_title']) {
		$serch_title_checked_str = ' checked';
	} else {
		$serch_title_checked_str = '';
	}

	// ��ʸ����
	if ($this->_tpl_vars['form_pre']['search_all']) {
		$search_all_checked_str = ' checked';
	} else {
		$search_all_checked_str = '';
	}

	// ��������õ��
	if ($this->_tpl_vars['form_pre']['search_all_about']) {
		$search_all_about_checked_str = ' checked';
	} else {
		$search_all_about_checked_str = '';
	}
?>
<br>
<span class="sub_title"><?= ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M006') ?></span>
<form action="<?= $this->_tpl_vars['link_page_url']['search_diary_url'] ?>" name="search_form_new" method="get" enctype="multipart/form-data">
	<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
	<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
	<input type="hidden" name="id" value="<?= $this->_tpl_vars['id'] ?>">
	<input type="hidden" name="move_id" value="<?= $this->_tpl_vars['move_id'] ?>">

<table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
 <tr>
  <td bgcolor="#FFF5AA">
<!--������� -->
	<input type="text" name="q_text" value="<?=$this->_tpl_vars['form_pre']['q_text'] ?>" size="30" style="ime-mode: active;">
<!--�����о� -->
	<input type="checkbox" name="search_title" value="title_in_serch"<?= $serch_title_checked_str ?>><?= ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M007') ?>&nbsp;
	<input type="checkbox" name="search_all" value="subject_in_serch"<?= $search_all_checked_str ?>><?= ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M008') ?>&nbsp;&nbsp;
<!--�����ϰ� -->
<?= ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M009') ?>&nbsp;
<?php
	// �����ϰ�
	// ������֤򥻥åȤ���
	unset($selected);
	$selected[$this->_tpl_vars['form_pre']['open_level_code']] = ' selected';

	echo "<select name=\"open_level_code\">\n";
	// ����Υץ������
		echo "<option value=\"00\"" . $selected['00'] . ">";
		echo htmlspecialchars(ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M010')) . "\n";
	// �ץ�������˥塼ɽ��
	foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
		echo "<option value=\"$open_level_master_row[open_level_code]\"" .$selected[$open_level_master_row['open_level_code']]. ">";
		echo htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
	}
	echo "</select>\n";
?>
	&nbsp;&nbsp;
	<input type="submit" name="search" value="<?= ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M011') ?>"><br>
<!-- 	�оݤ򹭤��� -->
	<input type="checkbox" name ="search_all_about" value="all_in_serch" onChange="fmTurn()"<?= $search_all_about_checked_str ?>><?= ACSMsg::get_msg("User", "SearchResultDiary.tpl.php",'M012') ?>
  </td>
 </tr>
</table>
</form>


