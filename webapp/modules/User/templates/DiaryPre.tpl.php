<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/03/01 ver1.0                 |
// |ダイアリー　確認・登録画面                                   　　　　　　　　　　　 |
// +----------------------------------------------------------------------+
// 
// $Id: DiaryPre.tpl.php,v 1.8 2007/03/30 05:27:23 w-ota Exp $
?>

<!-- HTML -->
<div class="sub_title">
<a href="<?= $this->_tpl_vars['diary_top_page_url'] ?>"> <?= ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M002') ?>
</div>

<?php
	if ($this->_tpl_vars['error_message']) {
		// エラーメッセージ表示
		ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
	} else {
		echo '<div class="confirm_msg">';
		echo ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M003').'<br>';
		echo ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M004');
		echo '</div>';
	}
?>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post" name="bbs_form" enctype="multipart/form-data">

<!-- 日記情報 -->
<table class="table.confirm_table">
	<colgroup class="required">
	<colgroup class="value">
	<colgroup class="partition">
	<colgroup class="required">
	<colgroup class="value">
<tr>
	<td><?= ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M005') ?></td>
		<td><?= htmlspecialchars($this->_tpl_vars['form']['subject']) ?></td>
	<td></td>
	<td><?= ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M006') ?></td>
		<td>
		<?php
			if($this->_tpl_vars['form']['file_name'] != ''){
				echo  $this->_tpl_vars['form']['file_name'];
			}
		?>
		</td>
</tr>

<tr>
	<td height=150px><?= ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M007') ?></td>
		<td valign=top>
		<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['form']['body']))) ?>
		</td>
	<td></td>
	<td><?= ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M008') ?></td>
		<td>
			<?php
				if($this->_tpl_vars['form']['file_name'] != ''){
					echo "<img src=";
					echo $this->_tpl_vars['form']['file_url_alink'];
					echo " border=0>";
				}
			?>
		</td>
</tr>

<tr>
	<td><?= ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M009') ?></td>
		<td colspan=4>
			<?php
				// 公開範囲
				echo ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M009')." : " . htmlspecialchars($this->_tpl_vars['form']['open_level_name']);
				if (count($this->_tpl_vars['form']['trusted_community_row_array'])) {
					$trusted_community_str = '';
					foreach ($this->_tpl_vars['form']['trusted_community_row_array'] as $trusted_community_row) {
						if ($trusted_community_str != '') {
							$trusted_community_str .= ", ";
						}
						$trusted_community_str .= $trusted_community_row['community_name'];
					}
					if ($trusted_community_str != '') {
						echo " (" . htmlspecialchars($trusted_community_str) . ")<br>";;
					}
				}
			?>
	</td>
</tr>
</table>

<br><br>
<?php
	if (!$this->_tpl_vars['error_message']) {
		echo '<input type="submit" value="'.ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M011').'">&nbsp;';
	}
?>
    <input type="button" value="<?= ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M012') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">&nbsp;
		<?= ACSMsg::get_msg("User", "DiaryPre.tpl.php",'M013') ?>
</form>
<br>
