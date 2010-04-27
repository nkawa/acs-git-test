<?php
	//require_once(ACS_LIB_TEMPLATE_DIR . 'ACSTemplateLib.class.php');

	//
	// $Id: DecideSchedule_input.tpl.php,v 1.3 2006/12/28 07:36:15 w-ota Exp $
	//

	$target_community_row =& $this->_tpl_vars['target_community_row'];
	$acs_user_info_row =& $this->_tpl_vars['acs_user_info_row'];

	$schedule =& $this->_tpl_vars['schedule'];

	$title  = '<a href="' . $this->_tpl_vars['url_community_top'] . '">';
	$title .= htmlspecialchars($target_community_row['community_name']);
	$title .= ' '.ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M001');
	$title .= '</a>';
	$title .= " :: ";
	$title .= '<a href="' . $this->_tpl_vars['url_schedule_list'] . '">';
	$title .= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M002');
	$title .= '</a>';

	$title .= " :: ".ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M003');

?>
<div class="ttl"><?= $title ?></div>

<script language="JavaScript">
<!--
	<?php echo $this->_tpl_vars['java_message_var_string'] ?>
	<?php echo $this->_tpl_vars['java_subject_var_string'] ?>
	var before_sel_index = <?php echo $this->_tpl_vars['java_default_lang_index'] ?>;
	var dirty_flg = false;

    function onchange_msg() {
		dirty_flg = true;
	}

    function sel_lang() {
		var sel_lang_id = document.mail_form.lang_id.value;
		if(dirty_flg){
			if(confirm("<?= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M011') ?>")){
				document.mail_form.mail_message.value = msg_list[sel_lang_id];
				document.mail_form.mail_subject.value = subject_list[sel_lang_id];
				dirty_flg = false;
			}else{
				document.mail_form.lang_id.options[before_sel_index].selected = true;
			}
		}else{
			document.mail_form.mail_message.value = msg_list[sel_lang_id];
			document.mail_form.mail_subject.value = subject_list[sel_lang_id];
		}

		before_sel_index = document.mail_form.lang_id.selectedIndex;
	}
// -->
</script>

<p>
<form name="mail_form" action="<?= $this->_tpl_vars['url_commit'] ?>" method="post">
<input type="hidden" name="community_id" value="<?= $schedule->community_id ?>">
<input type="hidden" name="schedule_id" value="<?= $schedule->schedule_id ?>">
<input type="hidden" name="mailentry_adjustment_id" value="<?= $this->_tpl_vars['mailentry_adjustment_id'] ?>">
<input type="hidden" name="participate" value="">
<table class="schedulelist_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33" width="400">
<?php // 件名 ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M004') ?></th>
 <td bgcolor="#ffffff" width="300"><?= htmlspecialchars($schedule->schedule_name) ?></td>
</tr>
<?php // 場所 ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M005') ?></th>
 <td bgcolor="#ffffff" width="300"><?= htmlspecialchars($schedule->schedule_place) ?></td>
</tr>
<?php // 決定日 ?>
<tr>
 <th id="myttl" bgcolor="#DEEEBD" width="100"><?= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M006') ?></th>
 <td bgcolor="#ffffff" width="300"><?= htmlspecialchars($this->_tpl_vars['adjustment_date']) ?></td>
</tr>
</table>
</p>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<?php // 定型文言語の選択 ?>
<p>
<?= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M007') ?>:<br>
 <select name="lang_id" onChange="sel_lang()">
  <?= ACSTemplateLib::get_simple_select_options(
   $this->_tpl_vars['html_options_lang_list'], $this->_tpl_vars['current_lang']) ?>
 </select>
</p>
<?php // メール件名 ?>
<p>
<?= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M004') ?>:<br>
<input type="text" name="mail_subject" onChange="onchange_msg()" value="<?= $this->_tpl_vars['mail_subject'] ?>" size="90">
</p>
<?php // メッセージ ?>
<p>
<?= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M008') ?>:<br>
 <textarea name="mail_message" rows="15" cols="65" onChange="onchange_msg()"><?= $this->_tpl_vars['mail_message'] ?>
 </textarea>
</p>
<p>
<input type="submit" value="<?= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M009') ?>">
<input type="button" value="<?= ACSMsg::get_msg("Community", "DecideSchedule_input.tpl.php",'M010') ?>"
  onclick="location.href='<?= $this->_tpl_vars['cancel_url'] ?>'">
</form>
</p>
