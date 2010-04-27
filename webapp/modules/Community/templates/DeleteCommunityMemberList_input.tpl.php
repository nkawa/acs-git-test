<?php
	$title  = '<a href="' . $this->_tpl_vars['community_top_page_url'] . '">';
	$title .= htmlspecialchars($this->_tpl_vars['target_community_name']);
	$title .= ACSMsg::get_msg("Community", "DeleteCommunityMemberList_input.tpl.php",'M001');
	$title .= '</a>';
	$title .= " :: ".ACSMsg::get_msg("Community", "DeleteCommunityMemberList_input.tpl.php",'M002');
?>
<div class="ttl"><?= $title ?></div>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<div class="msg">
<?= ACSMsg::get_msg("Community", "DeleteCommunityMemberList_input.tpl.php",'M003') ?>
</div>

<form name="select_member" method="POST" action="<?=$this->_tpl_vars['confirm_action_url'] ?>">
<input type="hidden" name="action_type" value="confirm">

<p>
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php
	// メンバ一覧出力
	foreach ($this->_tpl_vars['community_member_info_row_array'] as $community_member_info_row) {
		$selected_info = "";
		$disabled_info = "";
		if ($community_member_info_row['is_selected']) {
			$selected_info = " checked";
		}
		if ($community_member_info_row['is_disabled']) {
			$disabled_info = " disabled";
		}
		print '<tr>' . "\n";
		print '<td  bgcolor="#ffffff">';
		print '<input type="checkbox" name="delete_user_community_id_array[]" value="' . $community_member_info_row['community_id'] . '"' . $selected_info . $disabled_info . '></td>' . "\n";
		print '<td align="center" bgcolor="#ffffff">';
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
<input type="submit" value="<?= ACSMsg::get_msg("Community", "DeleteCommunityMemberList_input.tpl.php",'M004') ?>">
</p>
</form>
