<?php
	//
	// $Id: Schedule.tpl.php,v 1.1 2006/12/18 07:42:12 w-ota Exp $
	//

	$target_community_row =& $this->_tpl_vars['target_community_row'];
	$schedule_list =& $this->_tpl_vars['schedule_list'];

	$title  = '<a href="' . $this->_tpl_vars['url_community_top'] . '">';
	$title .= htmlspecialchars($target_community_row['community_name']);
	$title .= ' '.ACSMsg::get_msg("Community", "Schedule.tpl.php",'M001');
	$title .= '</a>';
	$title .= " :: ".ACSMsg::get_msg("Community", "Schedule.tpl.php",'M002');
?>
<div class="ttl"><?= $title ?></div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<p>[ <a href="<?= $this->_tpl_vars['url_schedule_new'] ?>"><?= ACSMsg::get_msg("Community", "Schedule.tpl.php",'M003') ?></a> ]</p>

<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>

<p>
<?php if (count($schedule_list) > 0) { ?>

	<table class="schedulelist_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
	<tr>
		<th id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "Schedule.tpl.php",'M004') ?></th>
		<th id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "Schedule.tpl.php",'M005') ?></th>
		<th id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "Schedule.tpl.php",'M012') ?></th>
		<th id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "Schedule.tpl.php",'M006') ?></th>
		<th id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "Schedule.tpl.php",'M007') ?></th>
		<th id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "Schedule.tpl.php",'M008') ?></th>
		<th id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "Schedule.tpl.php",'M009') ?></th>
	</tr>

	<?php
	reset($schedule_list);
	foreach ($schedule_list as $schedule_array) {

		$manage_menu = "";
		$schedule =& $schedule_array['instance'];

		if ($schedule->is_organizer($this->_tpl_vars['acs_user_info_row']) 
				&& $schedule->is_fixed() === FALSE) {
			$manage_menu = sprintf('[ <a href="%s">%s</a> ] [ <a href="%s">%s</a> ]',
					$schedule_array['url_edit'],
					ACSMsg::get_msg("Community", "Schedule.tpl.php",'M010'),
					$schedule_array['url_decide'],
					ACSMsg::get_msg("Community", "Schedule.tpl.php",'M011'));
		}

		?><tr>
			<td bgcolor="#ffffff" align="center" nowrap><?= $manage_menu ?></td>
			<td bgcolor="#ffffff" align="left"
			    ><a href="<?= $schedule_array['url_answer'] ?>"
				><?= htmlspecialchars($schedule->schedule_name) ?></a></td>
			<td bgcolor="#ffffff" align="left" nowrap
				><?= htmlspecialchars($schedule->user_community_name) ?></td>
			<td bgcolor="#ffffff" align="left"><?= htmlspecialchars($schedule_array['disp_detail']) ?></td>
			<td bgcolor="#ffffff" align="left"><?= $schedule_array['disp_closing'] ?></td>
			<td bgcolor="#ffffff" align="center"><?= $schedule_array['disp_person_count'] ?></td>
			<td bgcolor="#ffffff" align="center"><?= $schedule_array['disp_status'] ?></td>
		</tr><?php
	} 

?>
	</table>

<?php } else { ?>
<?php } ?>
</p>
<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>

