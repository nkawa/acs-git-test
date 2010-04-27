<?php
// $Id: NewOpenDiary.tpl.php,v 1.5 2007/03/30 05:27:21 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("Public", "NewOpenDiary.tpl.php" ,'M001') ?></div>
<br>
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
foreach ($this->_tpl_vars['new_open_diary_row_array'] as $new_open_diary_row) {
?>
	<tr>
		<td id="myttl" bgcolor="#DEEEBD" colspan="2">
			<b><?= htmlspecialchars(ACSTemplateLib::trim_long_str($new_open_diary_row['subject'], 100))?></b>
			&nbsp;&nbsp;&nbsp;
			<?= $new_open_diary_row['post_date'] ?>
		</td>
	</tr>
	<tr>
		<td align="center" bgcolor="#ffffff">
			<a href="<?=$new_open_diary_row['top_page_url']?>"><img src="<?=$new_open_diary_row['image_url']?>" border="0"></a>
			<br>
			<a href="<?=$new_open_diary_row['top_page_url']?>"><?= htmlspecialchars($new_open_diary_row['community_name'])?></a>
		</td>
		<td valign="top" bgcolor="#ffffff">
			<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars(ACSTemplateLib::trim_long_str($new_open_diary_row['body'], 500)))) ?>
		</td>
	</tr>
<?php
}
?>
</table>

