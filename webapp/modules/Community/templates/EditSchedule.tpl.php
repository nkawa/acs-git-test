<?php
	//
	// $Id: EditSchedule.tpl.php,v 1.2 2006/12/19 10:17:27 w-ota Exp $
	//

	$target_community_row =& $this->_tpl_vars['target_community_row'];
	$schedule =& $this->_tpl_vars['schedule'];

	$title  = '<a href="' . $this->_tpl_vars['url_community_top'] . '">';
	$title .= htmlspecialchars($target_community_row['community_name']);
	$title .= ' '.ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M001');
	$title .= '</a>';
	$title .= " :: ";
	$title .= '<a href="' . $this->_tpl_vars['url_schedule_list'] . '">';
	$title .= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M002');
	$title .= '</a>';

	if ($schedule->is_new()) {
		$title .= " :: ".ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M003');
	} else {
		$title .= " :: ".ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M004');
	}
?>
<div class="ttl"><?= $title ?></div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<script language="JavaScript">
<!--
	function sel_value(selectbox) {
		return selectbox.options[selectbox.selectedIndex].value;
	}

    function generate_adjustment_dates () {

		// Analyze date
		var dt_from = get_date(
				sel_value(document.edit_form.generate_year_from),
				sel_value(document.edit_form.generate_month_from),
				sel_value(document.edit_form.generate_day_from));
		var dt_to = get_date(
				sel_value(document.edit_form.generate_year_to),
				sel_value(document.edit_form.generate_month_to),
				sel_value(document.edit_form.generate_day_to));
		var entry_area = document.edit_form.edit_append_adjustment_dates;
		var showyear;

		// Error check
		if (dt_from == false || dt_to == false) {
			alert('<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M050') ?>');
			return;
		}
		if (dt_from.getTime() > dt_to.getTime()) {
			alert('<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M051') ?>');
			return;
		}
		// Generate dates string
		for (var i = 0; i < document.edit_form.generate_show_year.length; i++) {
			if (document.edit_form.generate_show_year[i].checked == true)
					showyear = document.edit_form.generate_show_year[i].value;
		}
		showyear = showyear == 't' ? true : false;
		for (var tm = dt_from.getTime(); tm <= dt_to.getTime(); tm += (1000*60*60*24)) {
			var dt = new Date();
			dt.setTime(tm);
			entry_area.value = entry_area.value + format_date(dt,showyear) + "\n";
		}
    }

	function get_date (y,m,d) {
		var dt = new Date(y,(m-1),d);
		if (isNaN(dt)) {
			return false;
		} else if (dt.getFullYear()==y && dt.getMonth()==(m-1) && dt.getDate()==d) {
			return dt;
        } else {
			return false;
		}
	}

	function format_date (dt,showyear) {
		var fmt = showyear ? 
				'<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'GEN_YMDFMT') ?>' :
				'<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'GEN_MDFMT') ?>' ;
		var wday = new Array(<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'WEEKDAY') ?>);
		var month = dt.getMonth()+1;
		var day = dt.getDate();

		if (month < 10) month = "0" + month;
		if (day < 10) day = "0" + day;

		fmt = fmt.replace("%YEAR%",dt.getFullYear());
		fmt = fmt.replace("%MONTH%",month);
		fmt = fmt.replace("%DAY%",day);
		fmt = fmt.replace("%WEEKDAY%",wday[dt.getDay()]);
		return fmt;
	}
// -->
</script>

<p>
<form name="edit_form" action="<?= $this->_tpl_vars['posturl'] ?>" method="post">
<input type="hidden" name="community_id" value="<?= $schedule->community_id ?>">
<table class="schedulelist_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33" width="600">
<?php // ·ïÌ¾ ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M005') ?></th>
 <td bgcolor="#ffffff" colspan="4">
  <input type="text" name="schedule_name" value="<?= htmlspecialchars($schedule->schedule_name) ?>" size="50" style="width:450px">
 </td>
</tr>
<?php // ¸õÊäÆü»þ ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" rowspan="2" 
   width="100"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M006') ?></th>
 <td bgcolor="#ffffff" colspan="4">
<?php
$adjust_dates =& $schedule->get_adjustment_dates(FALSE);
$del_adjust_checked =& $this->_tpl_vars['delete_ajustment_dates_checked'];
if (count($adjust_dates)>0) {
	?>
	<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M028') ?>:<br>
	<table class="schedulelist_table" border="0" cellpadding="2" cellspacing="1" bgcolor="#99CC33">
    <?php
	foreach ($adjust_dates as $adjust_id => $adjust_vals) {
		?>
		<tr>
		<td bgcolor="#DEEEBD" width="200"><?= $adjust_vals['date_string'] ?></td>
		<td bgcolor="#ffffff"><input type="checkbox" name="delete_adjustment_dates[]" 
		  value="<?= $adjust_id ?>" <?= $del_adjust_checked[$adjust_id] ?>>ºï½ü</td>
		</tr>
		<?php
	}
	?>
	</table><br>
    <?php
}
?>
  <?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M029') ?>:<br>
  <textarea name="edit_append_adjustment_dates" cols="50" rows="7" 
    style="width:450px"><?= $this->_tpl_vars['edit_append_adjustment_dates'] ?></textarea>
 </td>
</tr>
<tr>
 <th id="myttl" bgcolor="#DEEEBD"
    width="56"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M020') ?></th>
 <td bgcolor="#DEEEBD" colspan="3">
  <table>
  <tr><td>
   <?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M021') ?>
   <select name="generate_year_from"><?= $this->_tpl_vars["html_options_generate_year"] ?>
   </select> /
   <select name="generate_month_from"><?= $this->_tpl_vars["html_options_generate_month"] ?>
   </select> /
   <select name="generate_day_from"><?= $this->_tpl_vars["html_options_generate_day"] ?>
   </select><br>
   <?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M022') ?>
   <select name="generate_year_to"><?= $this->_tpl_vars["html_options_generate_year"] ?>
   </select> /
   <select name="generate_month_to"><?= $this->_tpl_vars["html_options_generate_month"] ?>
   </select> /
   <select name="generate_day_to"><?= $this->_tpl_vars["html_options_generate_day"] ?>
   </select>
  </td>
  <td>
   <input type="radio" name="generate_show_year" value="f" CHECKED
     ><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M031') ?>
   <input type="radio" name="generate_show_year" value="t"
     ><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M030') ?><br>
   <input type="button" onclick="generate_adjustment_dates();" 
     value="<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M023') ?>">
  </td></tr>
  </table>
 </td>
</tr>

<?php // ¾ì½ê ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M007') ?></th>
 <td bgcolor="#ffffff" colspan="4">
  <input type="text" name="schedule_place" value="<?= htmlspecialchars($schedule->schedule_place) ?>" size="50" style="width:450px">
 </td>
</tr>
<?php // ¾ÜºÙ ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M008') ?></th>
 <td bgcolor="#ffffff" colspan="4">
  <input type="text" name="schedule_detail" value="<?= htmlspecialchars($schedule->schedule_detail) ?>" size="50" style="width:450px">
 </td>
</tr>

<?php // ²óÅúÄùÀÚÆü»þ ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M009') ?></th>
 <th id="myttl" bgcolor="#DEEEBD" width="56"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M010') ?></th>
 <td bgcolor="#ffffff">
  <select name="edit_closing_year">
   <?= $this->_tpl_vars["html_options_closing_year"] ?>
  </select> /
  <select name="edit_closing_month">
   <?= $this->_tpl_vars["html_options_closing_month"] ?>
  </select> /
  <select name="edit_closing_day">
   <?= $this->_tpl_vars["html_options_closing_day"] ?>
  </select>
 </td>
 <th id="myttl" bgcolor="#DEEEBD" width="46"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M011') ?></th>
 <td bgcolor="#ffffff">
  <select name="edit_closing_hour">
   <?= $this->_tpl_vars["html_options_closing_hour"] ?>
  </select> :
  <select name="edit_closing_min">
   <?= $this->_tpl_vars["html_options_closing_min"] ?>
  </select>
 </td>
</tr>

<?php // ÂÐ¾Ý ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M012') ?></th>
 <td bgcolor="#ffffff" colspan="4">
  <?php
  // ¿·µ¬ÅÐÏ¿¤Î¾ì¹ç
  if ($schedule->is_new()) {
  ?>
   <input type="radio" name="schedule_target_kind" value="ALL"
     <?= $this->_tpl_vars['html_checked_target_all'] ?>><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M013') ?>
   <input type="radio" name="schedule_target_kind" value="FREE"
     <?= $this->_tpl_vars['html_checked_target_free'] ?>><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M014') ?>
  <?php
  } else {
   echo ($schedule->is_target_all() ? 
     ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M013') : ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M014'));
  }
  ?>
 </td>
</tr>
</table>
</p>

<p>
<?php // ÁªÂò»è ?>
<table class="schedulelist_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33" width="600">
<tr>
 <th id="myttl" bgcolor="#DEEEBD"  width="100" rowspan="<?= $schedule->get_answer_selection_count()+1 ?>"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M015') ?></th>
 <th id="myttl" bgcolor="#DEEEBD" width="70"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M016') ?></th>
 <th id="myttl" bgcolor="#DEEEBD" width="50"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M018') ?></th>
 <th id="myttl" bgcolor="#DEEEBD" width="50"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M017') ?></th>
 <th id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M019') ?></th>
</tr>
<?php
$answer_selection =& $schedule->get_answer_selection();
foreach ($answer_selection as $answer_no => $answer_def) {
?>
 <tr>
  <td bgcolor="#ffffff" align="center" width="70">
   <input type="text" name="answer_char[<?= $answer_no ?>]" 
     value="<?= $answer_def['answer_char'] ?>" size="2" maxlength="1">
  </td>
  <td bgcolor="#ffffff" align="center" width="50">
   <input type="text" name="answer_score[<?= $answer_no ?>]" 
     value="<?= $answer_def['answer_score'] ?>" size="2" maxlength="3">
  </td>
  <td bgcolor="#ffffff" align="center" width="50">
   <input type="radio" name="answer_default" value="<?= $answer_no ?>"
   <?= ($answer_def['answer_default']=='t' ? 'CHECKED' : '') ?>>
  </td>
  <td bgcolor="#ffffff">
   <input type="text" name="answer_detail[<?= $answer_no ?>]" 
     value="<?= htmlspecialchars($answer_def['answer_detail']) ?>" size="25" style="width:250px">
  </td>
 </tr>
<?php
}
?>
</table>
<br>
<input type="checkbox" value="t" 
  name="send_annouce_mail" <?= $this->_tpl_vars['send_annouce_mail_checked'] ?>
  ><?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M024') ?><br>
<?php
if ($schedule->is_new()) {
?><input type="submit" 
  value="<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M025') ?>">
<?php
} else {
?><input type="submit" 
  value="<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M026') ?>">
<?php
}
?>
<input type="button" 
  value="<?= ACSMsg::get_msg("Community", "EditSchedule.tpl.php",'M027') ?>"
  onclick="location.href='<?= $this->_tpl_vars['url_schedule_list'] ?>'">

</form>
</p>
