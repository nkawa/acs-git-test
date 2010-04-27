<?php
// BBS 返信投稿画面
// $Id: BBSRes.tpl.php,v 1.24 2007/03/30 05:27:18 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M001') ?></a> :: <a href="<?= $this->_tpl_vars['bbs_top_page_url'] ?>"><?= ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M002') ?></a>
<?php
if ($this->_tpl_vars['is_community_member']) {
	echo ":: ".ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M003');
}
?>
</div>
<br><br>

<?php
if ($this->_tpl_vars['is_community_member']) {
	echo ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M004')."<br><br>\n";
}
?>

<?php
if ($this->_tpl_vars['is_community_member']) {
//確認画面からキャンセルで戻ってきた時の処理　情報回帰
	$value = '';
	if($this->_tpl_vars['move_id'] == 3){
		$value['subject'] = $this->_tpl_vars['form']['subject'];
		$value['body'] = $this->_tpl_vars['form']['body'];
	}else{
		$value['subject']="Re: ". htmlspecialchars($this->_tpl_vars['bbs_row']['subject']);
	}
}
?>


<?php
// 親記事
echo "<table class=\"common_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";

// ヘッダ部	
echo "<tr><td colspan=\"2\" bgcolor=\"#DEEEBD\" class=\"head\" align=\"right\">";
if (is_array($this->_tpl_vars['bbs_row']['external_rss_row'])) {
	// 記事へのリンク
	if ($this->_tpl_vars['bbs_row']['external_rss_row']['rss_item_link'] != '') {
		echo "<a href=\"" . htmlspecialchars($this->_tpl_vars['bbs_row']['external_rss_row']['rss_item_link']) . "\" target=\"_blank\">";
		echo ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M017');
		echo "</a>";
	}
	// (YYYY/MM/DD H:MM RSS配信)
	if ($this->_tpl_vars['bbs_row']['external_rss_row']['rss_item_date'] != '') {
		echo "&nbsp;(";
		echo $this->_tpl_vars['bbs_row']['external_rss_row']['rss_item_date'];
		echo " " . ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M018') . ")";
	}
} else {
	echo "&nbsp;";
}
echo "</td></tr>\n";

// 投稿者情報
echo "<tr>";
echo "<td align=\"center\" width=\"80px\" bgcolor=\"#ffffff\">";
echo "<a href=\"" . $this->_tpl_vars['bbs_row']['top_page_url'] . "\">" . 
				"<img src=\"" . $this->_tpl_vars['bbs_row']['image_url'] . "\" border=\"0\"></a><br>";
echo "<a href=\"" . $this->_tpl_vars['bbs_row']['top_page_url'] . "\">" . htmlspecialchars($this->_tpl_vars['bbs_row']['community_name']) . "</a><br><br>";
echo "</td>";

echo "<td class=\"body\" valign=\"top\" bgcolor=\"#ffffff\">";


echo "<table class=\"layout_table\" width=\"450px\" border=\"0\">";
echo "<tr>";

echo "<td>";
echo "<b>" . htmlspecialchars($this->_tpl_vars['bbs_row']['subject']) . "</b>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;";
//echo ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M006')." : " . ACSLib::convert_pg_date_to_str($this->_tpl_vars['bbs_row']['post_date']) . "</td>";
echo ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M006')." : " . $this->_tpl_vars['bbs_row']['post_date'] . "</td>";
echo "<td align=\"right\" valign=\"top\">";
echo "</td>";
echo "</tr>";


echo "<tr><td>";
// 公開範囲
echo "<table border=\"0\" bgcolor=\"#dddddd\"><tr><td>";
echo ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M007')." : " . htmlspecialchars($this->_tpl_vars['bbs_row']['open_level_name']);
// パブリックリリースの場合のみ掲載終了日を表示させる 2/21add @akitsu
if($this->_tpl_vars['bbs_row']['expire_date']!=''){
	$out_expire_date = "[ "."掲載終了日:" . $this->_tpl_vars['bbs_row']['expire_date'] . " ]";
	echo $out_expire_date;
}
echo  "<br>";
if (count($this->_tpl_vars['bbs_row']['trusted_community_row_array'])) {
	$trusted_community_str = '';
	foreach ($this->_tpl_vars['bbs_row']['trusted_community_row_array'] as $trusted_community_row) {
		if ($trusted_community_str != '') {
			$trusted_community_str .= ", ";
		}
		$trusted_community_str .= $trusted_community_row['community_name'];
	}
	if ($trusted_community_str != '') {
		echo ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M009')." : " . htmlspecialchars($trusted_community_str) . "<br>";;
	}
}
echo "</td></tr></table>";
echo "</td></tr>";

//写真 2/20add
if ($this->_tpl_vars['bbs_row']['file_url']) {
	echo "<tr>";
	echo "<td valign=\"top\" colspan=\"2\">";
	echo "<a href=\"javascript:w=window.open('" . $this->_tpl_vars['bbs_row']['file_url_alink'] . "','popUp','scrollbars=yes,resizable=yes');w.focus();\">";
	echo "<img src=\"". $this->_tpl_vars['bbs_row']['file_url'] . "\" style=\"margin-top:10px;margin-bottom:10px\" border=0></a>";
	echo "</td>";
	echo "</tr>\n";
}

echo "<tr>";
echo "<td valign=\"top\">";
echo nl2br(ACSTemplateLib::auto_link(ACSLib::sp2nbsp(htmlspecialchars($this->_tpl_vars['bbs_row']['body']))));
echo "</td>";
echo "</tr>";

echo "</table>";


echo "</td>";
echo "</tr>\n";


// 返信記事
foreach ($this->_tpl_vars['bbs_row']['bbs_res_row_array'] as $bbs_res_row) {
	echo "<tr>";
	echo "<td align=\"center\" width=\"80px\" bgcolor=\"#ffffff\">";
	if ( $bbs_res_row['bbs_res_delete_flag']  == 'f') {
		echo "<a href=\"".$bbs_res_row['top_page_url']."\"><img src=\"".$bbs_res_row['image_url']."\" border=\"0\"></a><br>";
		echo "<a href=\"".$bbs_res_row['top_page_url']."\">" . htmlspecialchars($bbs_res_row['community_name']) . "</a>";
	}
	echo "</td>";

	echo "<td class=\"body\" valign=\"top\" bgcolor=\"#ffffff\">";

	echo "<table class=\"layout_table\" width=\"450px\" border=\"0\">";
	echo "<tr>";
	if ( $bbs_res_row['bbs_res_delete_flag']  == 'f') {
		echo "<td><b>" . htmlspecialchars($bbs_res_row['subject']) . "</b>";
	}
	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	//echo ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M006')." : " . ACSLib::convert_pg_date_to_str($bbs_res_row['post_date']) . "</td>";
	echo ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M006')." : " . $bbs_res_row['post_date'] . "</td>";

	echo "<td class=\"nowrap\">";
	echo "<form>";
	if ($bbs_res_row['edit_bbs_res_url']) {
		echo "<input type=\"button\" value=\"".ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M011')."\" onclick=\"location.href='".$bbs_res_row['edit_bbs_res_url']."'\">";
	}
	//削除ボタンの表示　削除フラグがないこと　＋自分が投稿したもの　又は　コミュニティ管理者
	if ( $bbs_res_row['bbs_res_delete_flag']  == 'f' && $bbs_res_row['bbs_set_delete_flag'] == 1) {
		echo " <input type=\"button\" value=\"".ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M012')."\" onclick=\"location.href='".$bbs_res_row['delete_bbs_res_url']."'\">";
	}
	echo "</form>";
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td valign=\"top\">";
	if ( $bbs_res_row['bbs_res_delete_flag'] == 'f') {
		echo nl2br(ACSTemplateLib::auto_link(ACSLib::sp2nbsp(htmlspecialchars($bbs_res_row['body']))));
	}else{
		echo ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M013');
	}
	echo "</td>";
	echo "</tr>";

	echo "</table>";

	echo "</td>";
	echo "</tr>\n";
}

echo "</table>\n";
echo "<br>\n";
echo "<br>\n";
?>


<?php
if ($this->_tpl_vars['is_community_member']) {
?>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M014') ?></td>
<td bgcolor="#ffffff"><input type="text" name="subject" value="<?=$value['subject'] ?>" size="50" style="width:400px"></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M015') ?></td>
<td bgcolor="#ffffff"><textarea name="body" cols="60" rows="10" style="width:480px"><?= htmlspecialchars($value['body']) ?></textarea></td>
</tr>
<?php
if (ACSLib::get_boolean($this->_tpl_vars['bbs_row']['ml_send_flag'])) {
	echo '<tr>';
	echo '<td id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M019').'</td>';
	echo '<td bgcolor="#ffffff">'.ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M020').'</td>';
	echo '</tr>';
}
?>
</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("Community", "BBSRes.tpl.php",'M005') ?>">
</form>
<br>
<?php
}
?>
