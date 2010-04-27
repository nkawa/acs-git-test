<?php
	require_once(ACS_LIB_TEMPLATE_DIR . 'ACSTemplateLib.class.php');

	//
	// $Id: AnswerSchedule.tpl.php,v 1.2 2006/12/28 07:36:15 w-ota Exp $
	//

	$target_community_row =& $this->_tpl_vars['target_community_row'];
	$is_decision_screen =& $this->_tpl_vars['is_decision_screen'];
	$acs_user_info_row =& $this->_tpl_vars['acs_user_info_row'];
	$schedule =& $this->_tpl_vars['schedule'];
	$schedule_participant =& $this->_tpl_vars['schedule_participant'];
	$answer_selection =& $schedule->get_answer_selection();
	$is_closed = ($schedule->is_fixed() || $schedule->is_close() ? TRUE : FALSE);

	$title  = '<a href="' . $this->_tpl_vars['url_community_top'] . '">';
	$title .= htmlspecialchars($target_community_row['community_name']);
	$title .= ' '.ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M001');
	$title .= '</a>';
	$title .= " :: ";
	$title .= '<a href="' . $this->_tpl_vars['url_schedule_list'] . '">';
	$title .= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M002');
	$title .= '</a>';

	// �������塼�����
	if ($is_decision_screen) {
		$title .= " :: ".ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M004');
	// �������塼��Ĵ��ɽ����
	} else {
		$title .= " :: ".ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M003');
	}

?>
<div class="ttl"><?= $title ?></div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<p>
<form name="edit_form" action="<?= $this->_tpl_vars['url_commit'] ?>" method="post">
<input type="hidden" name="community_id" value="<?= $schedule->community_id ?>">
<input type="hidden" name="schedule_id" value="<?= $schedule->schedule_id ?>">
<input type="hidden" name="participate" value="">
<table class="schedulelist_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33" width="600">
<?php // ���� ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M005') ?></th>
 <td bgcolor="#ffffff" colspan="3" align="left"><b>
 <?= $this->_tpl_vars['schedule_status']  ?>
 </b></td>
</tr>
<?php // ��̾ ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M006') ?></th>
 <td bgcolor="#ffffff" width="200"><?= htmlspecialchars($schedule->schedule_name) ?></td>
<?php // ��� ?>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M007') ?></th>
 <td bgcolor="#ffffff" width="200"><?= htmlspecialchars($schedule->schedule_place) ?></td>
</tr>
<?php // ��ż� ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M008') ?></th>
 <td bgcolor="#ffffff" width="200"
  ><a href="<?= $this->_tpl_vars['user_community_name_url'] ?>"
  ><?= htmlspecialchars($this->_tpl_vars['user_community_name']) ?></a></td>
<?php // ������������ ?>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M009') ?></th>
 <td bgcolor="#ffffff" width="200"><?= htmlspecialchars(ACSLib::convert_pg_date_to_str($schedule->schedule_closing_datetime)) ?></td>
</tr>
<?php // �ܺپ��� ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M010') ?></th>
 <td bgcolor="#ffffff" width="200"><?= htmlspecialchars($schedule->schedule_detail) ?></td>
<?php // �о� ?>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M011') ?>
 </th>
 <td bgcolor="#ffffff" width="200">
  <?php 
  if ($schedule->schedule_target_kind == "ALL"){
  ?>
   <?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M022') ?>
  <?php
  }else{
  ?>
   <?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M023') ?>
  <?php
  }
  ?>
 </td>
</tr>
</table>
</p>

<?php // ������桼���λ�����Ͽ���ꥢ

// --- �������塼�����ξ���ɽ�����ʤ�(��������)
if (!$is_decision_screen) {
?>
<p>
<table class="schedulelist_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"></th>
 <?php // �������������ȥ�ɽ�� 
 foreach ($this->_tpl_vars['adjustment_dates_list'] as $date_id => $adjustment_date){
 ?>
  <th bgcolor="#DEEEBD" width="60"><?= $adjustment_date['date_string'] ?></th>
 <?php
 }
 ?>
 <th bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M014') ?></th>
</tr>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100" nowrap><?= $acs_user_info_row['user_name'] ?>
 <br><?php // ���åܥ���ν���
 if ($schedule->is_target_all() == FALSE && $is_closed == FALSE) {
  // �оݤ��ּ�ͳ���áפǻ�����Ͽ�Ѥߤξ��
  if ($schedule_participant->is_participate()) {
  ?>
   <input type="button"
     onclick="document.edit_form.participate.value='f';document.edit_form.submit();" 
     value="<?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M013') ?>">
  <?php
  // �оݤ��ּ�ͳ���áפǻ���̤��Ͽ�ξ��
  } else {
  ?>
   <input type="button" 
     onclick="document.edit_form.participate.value='t';document.edit_form.submit();" 
     value="<?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M012') ?>">
  <?php
  }
 }
 ?>
 </th>
 <?php // ����ꥹ�Ȥν���
 foreach ($this->_tpl_vars['adjustment_dates_list'] as $date_id => $adjustment_date){
   $disp_answer_no =& $schedule_participant->get_answer($date_id);
   $answer_no = $disp_answer_no == '' ? 
     $schedule->get_answer_selection_default() : $disp_answer_no;
 ?>
  <td bgcolor="#ffffff" width="60" align="center">
   <?php
   // ���þ��֤ξ�������ꥹ�Ȥ����
   if ($is_closed == FALSE && 
     ($schedule_participant->is_participate() || $schedule->is_target_all())) {
   ?>
    <select name="answers[<?= $date_id ?>]">
     <?= ACSTemplateLib::get_simple_select_options(
       $this->_tpl_vars['html_options_answer_selection'], $answer_no) ?>
    </select>
   <?php
   // ���þ��֤Ǥʤ�����ɽ���Τ�
   } else {
   ?>
     <?= $answer_selection[$disp_answer_no]['answer_char'] ?>
     <input type="hidden" name="answers[<?= $date_id ?>]" 
       value="<?= $answer_no ?>">
   <?php
   }
   ?>
  </td>
 <?php
 }
 ?>
 <td bgcolor="#ffffff" nowrap>
  <?php
  // ���þ��֤ξ��ϥ��������ϰ�����
  if ($is_closed == FALSE && 
    ($schedule_participant->is_participate() || $schedule->is_target_all())) {
  ?>
  <textarea name="participant_comment" rows="3" cols="25"
    ><?= htmlspecialchars($schedule_participant->participant_comment) ?></textarea>
  <?php
  // ���þ��֤Ǥʤ�����ɽ���Τ�
  } else {
  ?>
   <?= nl2br(htmlspecialchars($schedule_participant->participant_comment)) ?>
  <?php
  }
  ?>
 </td>
</tr> 
<tr>
 <th bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M015') ?></th>
 <td colspan="<?= $this->_tpl_vars['adjustment_dates_count']+1 ?>" bgcolor="#ffffff">
  <?= $this->_tpl_vars['answer_detail_text'] ?>
 </td>
</tr>
</table>
</p>
<?php

// ��λ���֤ξ��
if ($is_closed) {
 ?>
 <input type="button" value="<?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M028') ?>"
 onclick="location.href='<?= $this->_tpl_vars['url_schedule_list'] ?>'">
 <?php
} else {
// ���þ��֤ξ�硢��Ͽ�ܥ����ɽ��
 if ($schedule_participant->is_participate() || $schedule->is_target_all()) {
?>
  <input type="submit" value="<?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M016') ?>">
 <?php
 }
 ?>
 <input type="button" value="<?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M017') ?>"
 onclick="location.href='<?= $this->_tpl_vars['url_schedule_list'] ?>'">
<?php
}
?>

<?php // --- �������塼����������ɽ����λ(�����ޤ�)
} 
?>

<?php // ���üԤΰ��� ?>
<p>
<?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M026') ?><br>
<table class="schedulelist_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"></th>
 <?php // �����ȥ��������ɽ�� 
 foreach ($this->_tpl_vars['adjustment_dates_list'] as $date_id => $adjustment_date){
 ?>
  <th bgcolor="#DEEEBD" width="60"><?= htmlspecialchars($adjustment_date['date_string']) ?></th>
 <?php
 }
 ?>
 <th bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M014') ?></th>
</tr>

<?php // �ƻ��üԤν��ʾ�����ɽ�� ?>
<?php
foreach ($this->_tpl_vars['schedule_participant_list'] as 
		$list_user_community_id => $schedule_participant) {
?>
 <tr>
  <th id="myttl" bgcolor="#DEEEBD" width="100" nowrap
   ><?= $schedule_participant->user_community_name ?></th>
  <?php
   foreach($this->_tpl_vars['adjustment_dates_list'] as $date_id => $adjustment_date){
   $answer_no =& $schedule_participant->get_answer($date_id);
  ?>
   <td bgcolor="#ffffff" width="60" align="center">
    <?= $answer_selection[$answer_no]['answer_char'] ?>
   </td>
  <?php
  }
  ?>
  <td bgcolor="#ffffff" width="200" nowrap
    ><?= nl2br(htmlspecialchars($schedule_participant->participant_comment)) ?></td>
 </tr>
<?php
}
?>
</table>
</form>
</p>

<?php // ���ð���(����) ?>
<p>
<form name="decide_form" action="<?= $this->_tpl_vars['url_decide'] ?>" method="get">
<input type="hidden" name="<?= MODULE_ACCESSOR ?>" value="<?= $this->_tpl_vars['current_module'] ?>">
<input type="hidden" name="<?= ACTION_ACCESSOR ?>" value="<?= $this->_tpl_vars['current_action'] ?>">
<input type="hidden" name="community_id" value="<?= $schedule->community_id ?>">
<input type="hidden" name="schedule_id" value="<?= $schedule->schedule_id ?>">
<?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M027') ?><br>
<table class="schedulelist_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"></th>
<?php // �������������ȥ�ɽ�� 
foreach($this->_tpl_vars['adjustment_dates_list'] as $date_id => $adjustment_date){
?>
 <th bgcolor="#DEEEBD" width="60"><?= $adjustment_date['date_string'] ?></th>
<?php
}
?>
 <th bgcolor="#DEEEBD" width="200"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M015') ?></th>
</tr>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100" nowrap
   ><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M018') ?></th>
<?php // ������ɽ�� 
$total_score =& $this->_tpl_vars['total_score'];
foreach ($this->_tpl_vars['adjustment_dates_list'] as $date_id => $adjustment_date){
?>
 <td bgcolor="#ffffff" width="60" align="center"
   ><?= $total_score[$date_id] ?></td>
<?php
}
?>
 <td bgcolor="#ffffff" rowspan="2" nowrap><?= $this->_tpl_vars['answer_detail_text'] ?></td>
</tr>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100" nowrap
   ><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M019') ?></th>
<?php // ���潸��
$total_count =& $this->_tpl_vars['total_count'];
foreach ($this->_tpl_vars['adjustment_dates_list'] as $date_id => $adjustment_date){
?>
 <td bgcolor="#ffffff" width="60" align="left">
 <?php
 foreach ($answer_selection as $answer_no => $selection) {
  if ($selection['answer_char'] != '') {
   $count = $total_count[$date_id][$answer_no];
   $count = $count=='' ? 0 : $count;
   ?><?= $selection['answer_char'] ?>:<?= $count ?><br><?php
  }
 }
 ?>
 </td>
<?php
}
?>
</tr>
<?php
//�������塼�����ξ�� 
if ($is_decision_screen) {
?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M021') ?></th>
 <?php
 $checked = "CHECKED";
 $mailentry_adjustment_id = $this->_tpl_vars['mailentry_adjustment_id'];
 foreach ($this->_tpl_vars['adjustment_dates_list'] as $date_id => $adjustment_date){
	if($mailentry_adjustment_id == ""){
		$mailentry_adjustment_id = $date_id;
	}
	if($date_id == $this->_tpl_vars['mailentry_adjustment_id']){
 		$checked = "CHECKED";
	}
 ?>
  <td bgcolor="#ffffff" width="60" align="center"
    ><input type="radio" name="mailentry_adjustment_id" value="<?= $date_id ?>" 
    <?= $checked ?>></td>
 <?php
  $checked = "";
 }
 ?>
 <td bgcolor="#ffffff" width="60"></td>
</tr>
<?php
}
?>
</table>
<?php
//�������塼�����ξ�� 
if ($is_decision_screen) {
?>
<br>
<input type="hidden" name="post_from_answer" value="t">
<input type="submit" value="<?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M020') ?>">
<input type="button" value="<?= ACSMsg::get_msg("Community", "AnswerSchedule.tpl.php",'M017') ?>"
  onclick="location.href='<?= $this->_tpl_vars['url_schedule_list'] ?>'">
<?php
} 
?>
</form>
</p>
