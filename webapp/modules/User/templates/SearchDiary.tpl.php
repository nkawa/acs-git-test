<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/3/13 ver1.0                                     |
// |Diary　検索画面                                   　　 　　　　　　　 |
// +----------------------------------------------------------------------+
// $Id: SearchDiary.tpl.php,v 1.7 2007/03/01 09:01:43 w-ota Exp $
?>
<script language="JavaScript">
<!--
	function fmTurn(){
	}
-->
</script>

<?php
//自分の日記ならば、リンクは無い
if ($this->_tpl_vars['is_self_page']) {
	echo "<div class=\"ttl\">";
	echo ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M001')."</div><br><br>\n";
} else {
// アクセス制限内で他人の日記ならば、「○○さんのTOP」へ戻ることができる
	echo "<div class=\"ttl\">";
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_top_page_url'] ."\">";
	//echo htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) . "さん</a> :: ";
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "SearchDiary.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	echo "</a> :: ";

	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_diary_url'] ."\">".ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M002')."</a> :: ".ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M003')."</div><br><br>\n";
}
?>

<br>
<form name="search_form_default" action="<?= $this->_tpl_vars['link_page_url']['search_diary_url'] ?>" method="get" enctype="multipart/form-data">
	<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
	<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
	<input type="hidden" name="id" value="<?= $this->_tpl_vars['id'] ?>">
	<input type="hidden" name="move_id" value="<?= $this->_tpl_vars['move_id'] ?>">
 <table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
  <tr>
   <td bgcolor="#FFF5AA">
<!--キーワード -->
	<input type="text" name="q_text" value="" size="30" style="ime-mode: active;">
<!--検索対象 -->
	<input type="checkbox" name="search_title" value="title_in_serch" checked><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M004') ?>&nbsp;
	<input type="checkbox" name="search_all" value="subject_in_serch" checked><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M005') ?>&nbsp;&nbsp;
<!--公開範囲 -->
<?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M006') ?>&nbsp;
<?php
	// 公開範囲
	echo "<select name=\"open_level_code\">\n";
	// 選択状態をセットする
	unset($selected);
	// 規定のプルダウン
		echo "<option value=\"00\"" . $selected['00'] ." selected>";
		echo htmlspecialchars(ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M007')) . "\n";
	// プルダウンメニュー表示
	foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
		echo "<option value=\"$open_level_master_row[open_level_code]\"" .$selected[$open_level_master_row['open_level_code']] . ">";
		echo htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
	}
	echo "</select>\n";
?>
	&nbsp;&nbsp;
	<input type="submit" name="search" value="<?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M003') ?>"><br>
<!-- 	対象を広げる -->
	<input type="checkbox" name ="search_all_about" value="all_in_serch" onChange="fmTurn()"><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M009') ?>
   </td>
  </tr>
 </table>
</form>

<br>
<dl>
	<b><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M010') ?></b><br><br>
	<?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M011') ?><br>
	<?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M012') ?><br>
	<?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M013') ?><br>
	<br>
	<dt><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M014') ?></dt>
	<dd><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M015') ?><br>
	<?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M016') ?><br></dd>
	<br>

	<dt><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M017') ?></dt>
	<dd><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M018') ?><br>
	<?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M019') ?><br>
	<?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M020') ?><br>
	<?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M021') ?><br>
	</dd>
	<br>
	<dt><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M022') ?></dt>
	<dd><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M023') ?><br></dd>
	<br>
	<dt><?= ACSMsg::get_msg("User", "SearchDiary.tpl.php",'M024') ?><br></dt>
	
</dl>
<br>

