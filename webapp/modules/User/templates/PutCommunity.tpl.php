<?php
if ($this->_tpl_vars['is_self_page']) {
	$title = ACSMsg::get_msg("User", "PutCommunity.tpl.php",'M001');
} else {
	//$title  = '<a href="' . $this->_tpl_vars['target_user_community_info_row']['top_page_url'] . '">';
	//$title .= htmlspecialchars($this->_tpl_vars['target_user_community_info_row']['community_name']);
	//$title .= '</a>';
	//$title .= 'さんのフォルダ';
	$title = ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "PutCommunity.tpl.php",'NAME'),array(
			"{USER_NAME}" => '<a href="' . $this->_tpl_vars['target_user_community_info_row']['top_page_url'] . '">'.
					htmlspecialchars($this->_tpl_vars['target_user_community_info_row']['community_name']).
					'</a>'));
}
?>
<div class="sub_title"><?= $title; ?> :: <?php

	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "PutCommunity.tpl.php",'PUTCOM'),array(
			"{FOLDER_NAME}" => htmlspecialchars($this->_tpl_vars['folder_info_row']['folder_name'])));

// htmlspecialchars($this->_tpl_vars['folder_info_row']['folder_name']) ??」のプット先コミュニティ

?></div>


<p>
<table class="file_list_table" border>
<?php
if (count($this->_tpl_vars['put_community_row_array'])) {

	// プット先コミュニティ表示
	foreach ($this->_tpl_vars['put_community_row_array'] as $put_community_row) {
		print "<tr>";

		print '<td>';
		// プット先コミュニティ
		print '<a href="' . $put_community_row['top_page_url'] . '">';
		print htmlspecialchars($put_community_row['community_name']);
		print '</a><br>';
		print '</td>';

		// プット先フォルダ
		print '<td>';

		print '<table class="layout_table"><tr><td>';
		// フォルダ画像
		print '<img src="' . ACS_IMAGE_DIR . 'folder.png">';

		print "</td><td>";

		// フォルダパス
		print '<a href="' . $put_community_row['put_folder_url'] . '">';
		print htmlspecialchars($put_community_row['put_folder_name']);
		print '</a><br>';
		print "</td></tr></table>";

		print '</td>';


		print "</tr>\n";
	}
} else {
	print "<tr>";
	print "<td>".ACSMsg::get_msg("User", "PutCommunity.tpl.php",'M002')."</td>";
	print "</tr>";
}
?>
</table>
</p>
