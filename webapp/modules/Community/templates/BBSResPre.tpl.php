<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/2/22 ver1.0                                     |
// |登録内容を確認するためのダイアログ                                    |
// +----------------------------------------------------------------------+
// 返信投稿情報　確認・登録画面
// $Id: BBSResPre.tpl.php,v 1.6 2007/03/30 05:27:18 w-ota Exp $
?>
<?php
if (!$this->_tpl_vars['is_community_member']) {
	$disabled_str = ' style="background-color:#dddddd" disabled';
}
?>
<!-- HTML -->
<div class="sub_title">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "BBSResPre.tpl.php",'M009') ?></a> :: <a href="<?= $this->_tpl_vars['back_bbs_url'] ?>"><?= ACSMsg::get_msg("Community", "BBSResPre.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "BBSResPre.tpl.php",'M002') ?>
</div>

<?php
	if ($this->_tpl_vars['error_message']) {
		// エラーメッセージ表示
		ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
	} else {
		echo '<div class="confirm_msg">';
		echo ACSMsg::get_msg("Community", "BBSResPre.tpl.php",'M003').'<br>';
		echo ACSMsg::get_msg("Community", "BBSResPre.tpl.php",'M004');
		echo '</div>';
	}
?>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post" name="bbs_form" enctype="multipart/form-data">

<input type="hidden" name="except_community_id_array[]" value="<?= $this->_tpl_vars['community_row']['community_id'] ?>">

<!-- <?= ACSMsg::get_msg("Community", "BBSResPre.tpl.php",'M005') ?> -->
<table class="table.confirm_table">
	<colgroup class="required">
	<colgroup class="value">
<tr>
	<td><?= ACSMsg::get_msg("Community", "BBSResPre.tpl.php",'M006') ?></td>
		<td><?= htmlspecialchars($this->_tpl_vars['form']['subject']) ?></td>
</tr>

<tr>
	<td height=150px><?= ACSMsg::get_msg("Community", "BBSResPre.tpl.php",'M007') ?></td>
		<td valign=top>
		<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['form']['body']))) ?>
		</td>
</tr>

</table>

<br><br>
<?php
	if (!$this->_tpl_vars['error_message']) {
		echo '<input type="submit" value="OK">&nbsp;';
	}
?>
    <input type="button" value="<?= ACSMsg::get_msg("Community", "BBSResPre.tpl.php",'M008') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">
</form>
<br>
