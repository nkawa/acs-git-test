<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: w-ota  v 1.5 2006/02/15 06:57:02                            |
// |         update: akitsu 2006/2/28 ver1.0                              |
// |Diary コメント画面                                   　　　　　　　　 |
// +----------------------------------------------------------------------+
// $Id: DiaryComment.tpl.php,v 1.19 2007/03/30 05:27:23 w-ota Exp $
?>

<?php
//自分の日記ならば、「マイダイアリー」へ戻ることができる
if ($this->_tpl_vars['is_self_page']) {
	echo "<div class=\"ttl\">";
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['top_page_url'] ."\">";
	echo ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M001') ."</a> :: ". ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M002') ."</div><br>\n";
} else {
// アクセス制限内で他人の日記ならば、「○○さんのTOP」「○○さんのダイアリー」へ戻ることができる
	echo "<div class=\"ttl\">";
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_diary_url'] ."\">";
	//echo htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) . "さん</a> :: ";
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "DiaryComment.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	echo "</a> :: ";
	echo "<a href=\"" .$this->_tpl_vars['diary_row']['else_user_diary_url'] ."\">";
	echo ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M003') ."</a> :: ". ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M002') ."</div><br>\n";
}
//  親ダイアリーが無くても見られない
if($this->_tpl_vars['diary_row']['diary_delete_flag']=='t'){
	echo ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M004');
	return;
}

?>

<!-- ダイアリー -->
<?php
//確認画面からキャンセルで戻ってきた時の処理　情報回帰
	$value = '';
	if($this->_tpl_vars['move_id'] == 3){
		$value['body'] = $this->_tpl_vars['form']['body'];
	}
?>

<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33" width="650px">
<tr>
<td bgcolor="#deeebd">
<table cellpadding="0" cellspacing="0" width="100%"><tr><td nowrap align="left"><b><?= htmlspecialchars($this->_tpl_vars['diary_row']['subject']) ?></b>&nbsp;&nbsp;&nbsp;<?= $this->_tpl_vars['diary_row']['post_date'] ?></td><td align="right"><?php
// 『足跡をつける』リンク
if($this->_tpl_vars['footprint_url'] != ""){
    print '<a href="javascript:location=\'' . $this->_tpl_vars['footprint_url'].'\'">';
	print '<img border="0" src="'.ACS_IMAGE_DIR.'footmark.gif">';
	echo ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M010');
	// 足跡ボタン押下済みの場合
	if(count($this->_tpl_vars['footprint_info']) > 0){
		print ' (' . ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M011') . ')';
	}
	echo '</a>';
}
?></td></tr></table></td>
</tr>
<tr>
<td bgcolor="#ffffff">
<?php
// 公開範囲
	echo "<table class=\"open_level_table\">\n<tr>\n<td>\n";
	echo ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M005')." : " . htmlspecialchars($this->_tpl_vars['diary_row']['open_level_name']);
	if ($this->_tpl_vars['is_self_page'] && $this->_tpl_vars['diary_row']['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')
		&& $this->_tpl_vars['diary_row']['trusted_community_flag']) {
		if (count($this->_tpl_vars['diary_row']['trusted_community_row_array'])) {
			$trusted_community_str = '';
			foreach ($this->_tpl_vars['diary_row']['trusted_community_row_array'] as $trusted_community_row) {
				if ($trusted_community_row['community_name'] != '') {
					if ($trusted_community_str != '') {
						$trusted_community_str .= ', ';
					}
					$trusted_community_str .= $trusted_community_row['community_name'];
				}
			}
			echo " (" . htmlspecialchars($trusted_community_str) . ")";
		} else {
			echo " ". ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M006');
		}
	}
	echo "</td>\n";
	echo "</tr>\n</table>\n";

	//写真
	if ($this->_tpl_vars['diary_row']['file_url']) {
		echo "<a href=\"javascript:w=window.open('" . $this->_tpl_vars['diary_row']['file_url_alink'] . "','popUp','scrollbars=yes,resizable=yes');w.focus();\">\n";
		echo "<img src=\"". $this->_tpl_vars['diary_row']['file_url'] . "\" style=\"margin-top:10px;margin-bottom:10px\" BORDER=0></a><br>";
	}
?>
<?= nl2br(ACSTemplateLib::auto_link(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['diary_row']['body'])))) ?>
</td>
</tr>
</table>
<br>
<br>

<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>

<!-- コメント一覧表示部分 -->
<?php
if ($this->_tpl_vars['diary_comment_row_array']) {
	echo "<table border=\"0\" cellpadding=\"6\" cellspacing=\"0\" bgcolor=\"#99CC33\">\n";
	$swf = 0;
	foreach ($this->_tpl_vars['diary_comment_row_array'] as $diary_comment_row) {
		echo "<tr>";
		if ($swf % 2 == 0) {
		  $cur_col = "#eeffcc";
		} else {
		  $cur_col = "#ffffff";
		}
		echo "<td bgcolor=\"" . $cur_col . "\" align=\"center\" width=\"80px\">";
		//ユーザ画像を表示 削除データで無い場合
		if($diary_comment_row['diary_comment_delete_flag']=='f'){
			echo "<a href=\"".$diary_comment_row['top_page_url']."\"><img src=\"$diary_comment_row[image_url]\" border=\"0\"></a><br>";
			echo "<a href=\"".$diary_comment_row['top_page_url']."\">" . htmlspecialchars($diary_comment_row['community_name']) . "</a>";
		}
		echo "</td>";
		echo "<td bgcolor=\"" . $cur_col . "\" valign='top'>";
		echo "<table><tr>";
		echo "<td bgcolor=\"" . $cur_col . "\" width=\"500px\" colspan=\"2\">";
		echo $diary_comment_row['post_date'];
		echo "</td>";
		// 削除ボタン　削除データで無い場合
		if($diary_comment_row['diary_comment_delete_flag']=='f'){
			echo "<td align=\"right\" valign=\"top\" BGCOLOR='#FFFFFF'>";
			echo "<form>";
			//削除ボタンの表示　＋自分
			if (($this->_tpl_vars['is_self_page']  || $diary_comment_row['self_id']== true) && $diary_comment_row['diary_comment_delete_flag']=='f') {
				echo "<input type=\"button\" value=\"".ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M007')."\" onclick=\"location.href='". $diary_comment_row['diary_delete_url'] ."'\">";
			}
			echo "</form></td>";
		}
		echo "</tr>";
		echo "<tr><td>";
		//本文を表示 削除データで無い場合
		if($diary_comment_row['diary_comment_delete_flag']=='f'){
			echo nl2br(ACSTemplateLib::auto_link(ACSLib::sp2nbsp(htmlspecialchars($diary_comment_row['body']))));
		}else{
			echo ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M008');
		}
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</td></tr>";
		$swf++;
	}
	echo "</table>\n";
	echo "<br><br>\n";
}
?>

<?php
// コメント登録部分
if ($this->_tpl_vars['acs_user_info_row']['is_acs_user']) {
	echo "<form action=\"" . $this->_tpl_vars['action_url'] ."\" method=\"post\">\n";
	echo "<table border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";
	echo "<tr>\n";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M002')."</td>\n";
	echo "<td bgcolor=\"#ffffff\"><textarea name=\"body\" cols=\"60\" rows=\"8\" style=\"width:500px\">";
	echo htmlspecialchars($value['body']);
	echo "</textarea></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
	echo "<input type=\"submit\" value=\"".ACSMsg::get_msg("User", "DiaryComment.tpl.php",'M009')."\">\n";
	echo "</form>\n";
}
?>
