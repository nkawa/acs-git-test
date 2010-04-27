<?php
	$title  = '<a href="' . $this->_tpl_vars['community_top_page_url'] . '">';
	$title .= htmlspecialchars($this->_tpl_vars['target_community_name']);
	$title .= ACSMsg::get_msg("Community", "DeleteCommunityMemberList_confirm.tpl.php",'M001');
	$title .= '</a>';
	$title .= " :: ".ACSMsg::get_msg("Community", "DeleteCommunityMemberList_confirm.tpl.php",'M002');
?>
<span class="ttl"><?= $title ?></span>

<div class="confirm_msg">
<?= ACSMsg::get_msg("Community", "DeleteCommunityMemberList_confirm.tpl.php",'M003') ?><br>
<?= ACSMsg::get_msg("Community", "DeleteCommunityMemberList_confirm.tpl.php",'M004') ?><br>
<?= ACSMsg::get_msg("Community", "DeleteCommunityMemberList_confirm.tpl.php",'M005') ?><br>
</div>

<form name="delete" method="POST" action="">
<input type="hidden" name="action_type" value="back">

<p>
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
	// メンバ一覧出力
	foreach ($this->_tpl_vars['community_member_info_row_array'] as $community_member_info_row) {
		print '<tr>' . "\n";
		print '<td align="center" bgcolor="#ffffff">';
		print '<input type="hidden" name="delete_user_community_id_array[]" value="' . $community_member_info_row['community_id'] . '">';
		print '<a href="' . $community_member_info_row[top_page_url] . '"><img src="' . 
				$community_member_info_row[image_url] . '" border="0"></a><br>';
		print '<a href="' . $community_member_info_row[top_page_url] . '">';
		print htmlspecialchars($community_member_info_row['name']) . '</a><br>';
		print '</td>';
		print '</tr>' . "\n";
	}
?>
</table>
</p>

<p>
<input type="button" onClick="javascript:submitDelete(this.form)" value="<?= ACSMsg::get_msg("Community", "DeleteCommunityMemberList_confirm.tpl.php",'M006') ?>">&nbsp;
<input type="button" onClick="javascript:submitBack(this.form)" value="<?= ACSMsg::get_msg("Community", "DeleteCommunityMemberList_confirm.tpl.php",'M007') ?>">
</p>
</form>

<script type="text/javascript">
	function submitBack (form) {
		form.action = "<?=$this->_tpl_vars['back_action_url'] ?>";
		form.submit();
	}
	function submitDelete (form) {
		form.action = "<?=$this->_tpl_vars['delete_action_url'] ?>";
		form.submit();
	}
</script>
