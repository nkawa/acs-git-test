<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/02/23 ver1.0                 |
// | 投稿情報　確認・登録画面                                   　　　　　　　　　　　 |
// +----------------------------------------------------------------------+
// $Id: BBSPre.tpl.php,v 1.16 2007/03/30 05:27:18 w-ota Exp $
?>
<!-- HTML -->
<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M002') ?>
</div>

<?php
//エラー処理
if($this->_tpl_vars['error_message']){
	ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
} else {
	echo '<div class="confirm_msg">';
	echo ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M003').'<br>';
	echo ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M004');
	echo '</div>';
}
?>

<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post" name="bbs_form" enctype="multipart/form-data">

<input type="hidden" name="except_community_id_array[]" value="<?= $this->_tpl_vars['community_row']['community_id'] ?>">

<!-- <?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M005') ?> -->
<table class="table.confirm_table">
	<colgroup class="required">
	<colgroup class="value">
	<colgroup class="partition">
	<colgroup class="required">
	<colgroup class="value">
<tr>
	<td><?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M014') ?></td>
		<td><?= htmlspecialchars($this->_tpl_vars['form']['subject']) ?></td>
	<td></td>
	<td><?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M015') ?></td>
		<td>
		<?php
			if($this->_tpl_vars['form']['file_name'] != ''){
				echo  $this->_tpl_vars['form']['file_name'];
			}
		?>
		</td>
</tr>

<tr>
	<td height=150px><?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M016') ?></td>
		<td valign=top>
		<?= nl2br(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['form']['body']))) ?>
		</td>
	<td></td>
	<td><?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M007') ?></td>
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
	<td><?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M008') ?></td>
		<td colspan=4>
			<?php
				// 公開範囲
				echo ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M008')." : " . htmlspecialchars($this->_tpl_vars['form']['open_level_name']);
				// パブリックリリースの場合のみ掲載終了日を表示させる 2/21add @akitsu
				if($this->_tpl_vars['form']['xdate']!=''){
					$out_expire_date = "[ ".ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M010').":" . $this->_tpl_vars['form']['xdate'] . " ]";
					echo $out_expire_date;
				}
				echo  "<br>";
				if (count($this->_tpl_vars['form']['trusted_community_row_array'])) {
					$trusted_community_str = '';
					foreach ($this->_tpl_vars['form']['trusted_community_row_array'] as $trusted_community_row) {
						if ($trusted_community_str != '') {
							$trusted_community_str .= ", ";
						}
						$trusted_community_str .= $trusted_community_row['community_name'];
					}
					if ($trusted_community_str != '') {
						echo ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M011')." : " . htmlspecialchars($trusted_community_str) . "<br>";;
					}
				}
			?>
	</td>
</tr>

<?php
if ($this->_tpl_vars['is_ml_active']) {
?>
<tr>
	<td><?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M017') ?></td>
	</td>
	<td colspan=4>
	<?php
	if ($this->_tpl_vars['is_ml_send']=='t') {
	?>
		<?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M018') ?>
	<?php
	} else {
	?>
		<?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M019') ?>
	<?php
	}
	?>
	</td>
</tr>
<?php
}
?>

</table>

<br><br>
<?php
    if (!$this->_tpl_vars['error_message']) {
        echo '<input type="submit" value="OK">&nbsp;';
    }
?>
    <input type="button" value="<?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M012') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">&nbsp;
		<?= ACSMsg::get_msg("Community", "BBSPre.tpl.php",'M013') ?>
</form>
<br>
