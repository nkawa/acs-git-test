<?php
// $Id: AddCommunityLink.tpl.php,v 1.9 2007/03/01 09:01:35 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?> <?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M001') ?></a> :: <a href="<?= $this->_tpl_vars['community_link_url'] ?>"><?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M002') ?></a> :: <?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M003') ?>
</div>
<br>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>


<form name="add_community_link_form" action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<input type="hidden" name="except_community_id_array[]" value="<?= $this->_tpl_vars['community_row']['community_id'] ?>">

<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M004') ?></td>
<td bgcolor="#ffffff">
<?php
// 選択するリンク種別を設定
$link_type_checked_str['parent'] = "";
$link_type_checked_str['sub'] = "";
if ($this->_tpl_vars['form']) {
	$link_type_checked_str[$this->_tpl_vars['form']['link_type']] = " checked";
} else {
	// 初期値は親コミュニティ
	$link_type_checked_str['parent'] = " checked";
}
?>
<input type="radio" name="link_type" value="parent"<?= $link_type_checked_str['parent'] ?>><?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M005') ?><br>
<input type="radio" name="link_type" value="sub"<?= $link_type_checked_str['sub'] ?>><?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M006') ?><br>
</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M007') ?></td>
<td bgcolor="#ffffff">
<div id="trusted_community_div">
	<table class="layout_table">
		<tr><td id="trusted_community_td">
		<?php
		// 選択されたコミュニティ復元
		if ($this->_tpl_vars['form']['trusted_community_row_array']) {
			foreach ($this->_tpl_vars['form']['trusted_community_row_array'] as $trusted_community_row) {
				echo '<input type="checkbox" name="trusted_community_id_array[]" value="' . $trusted_community_row['community_id'] . '" checked>' . "\n";

				echo '<a href="' . $trusted_community_row['top_page_url'] . '" target="_blank">';
				echo htmlspecialchars($trusted_community_row['community_name']);
				echo '</a><br>' . "\n";
			}
		}
		?>
		</td></tr>

		<tr><td>
		<input type="button" value="<?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M008') ?>" onClick="select_trusted_community()">
		<span style="font-size: 8pt;"><?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M009') ?></span>
		</td></tr>
	</table>
</div>
</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M010') ?></td>
<td bgcolor="#ffffff"><textarea name="message" cols="50" rows="4"><?= $this->_tpl_vars['form']['message'] ?></textarea></td>
</tr>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("Community", "AddCommunityLink.tpl.php",'M011') ?>">
</form>
<br>


<script language="JavaScript">
<!--
	prefix = '';
	function select_trusted_community () {
		window.open("<?= $this->_tpl_vars['select_trusted_community_url'] ?>" + "&form_name=" + 'add_community_link_form' + '&prefix=' + prefix,
					"SelectTrustedCommunity", "width=600,height=400,top=200,left=200,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
	}
// -->
</script>
