<?php
if ($this->_tpl_vars['is_self_page']) {
	$title = ACSMsg::get_msg("User", "PutCommunity.tpl.php",'M001');
} else {
	//$title  = '<a href="' . $this->_tpl_vars['target_user_community_info_row']['top_page_url'] . '">';
	//$title .= htmlspecialchars($this->_tpl_vars['target_user_community_info_row']['community_name']);
	//$title .= '</a>';
	//$title .= '����Υե����';
	$title = ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "PutCommunity.tpl.php",'NAME'),array(
			"{USER_NAME}" => '<a href="' . $this->_tpl_vars['target_user_community_info_row']['top_page_url'] . '">'.
					htmlspecialchars($this->_tpl_vars['target_user_community_info_row']['community_name']).
					'</a>'));
}
?>
<div class="sub_title"><?= $title; ?> :: <?php

	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "PutCommunity.tpl.php",'PUTCOM'),array(
			"{FOLDER_NAME}" => htmlspecialchars($this->_tpl_vars['folder_info_row']['folder_name'])));

// htmlspecialchars($this->_tpl_vars['folder_info_row']['folder_name']) ??�פΥץå��襳�ߥ�˥ƥ�

?></div>


<p>
<table class="file_list_table" border>
<?php
if (count($this->_tpl_vars['put_community_row_array'])) {

	// �ץå��襳�ߥ�˥ƥ�ɽ��
	foreach ($this->_tpl_vars['put_community_row_array'] as $put_community_row) {
		print "<tr>";

		print '<td>';
		// �ץå��襳�ߥ�˥ƥ�
		print '<a href="' . $put_community_row['top_page_url'] . '">';
		print htmlspecialchars($put_community_row['community_name']);
		print '</a><br>';
		print '</td>';

		// �ץå���ե����
		print '<td>';

		print '<table class="layout_table"><tr><td>';
		// �ե��������
		print '<img src="' . ACS_IMAGE_DIR . 'folder.png">';

		print "</td><td>";

		// �ե�����ѥ�
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
