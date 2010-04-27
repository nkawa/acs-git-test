<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: ota         update: akitsu 2006/2/22 ver1.0                 |
// |BBS　top画面                                   　　　　　　　　　　　 |
// +----------------------------------------------------------------------+
//
// $Id: BBS.tpl.php,v 1.41 2007/03/30 05:27:18 w-ota Exp $
?>

<?php
// 親コミュニティとサブコミュニティを結合してデフォルトの信頼済みコミュニティの配列とする
function get_trusted_community_row_array($parent_community_row_array, $sub_community_row_array) {
	$trusted_community_row_array = array();

	// 親コミュニティ
	foreach ($parent_community_row_array as $parent_community_row) {
		//$parent_community_row['community_position'] = '親コミュニティ';
		$parent_community_row['community_position'] = ACSMsg::get_msg("Community", "BBS.tpl.php",'M024');
		array_push($trusted_community_row_array, $parent_community_row);
	}
	// サブコミュニティ
	foreach ($sub_community_row_array as $sub_community_row) {
		//$sub_community_row['community_position'] = 'サブコミュニティ';
		$sub_community_row['community_position'] = ACSMsg::get_msg("Community", "BBS.tpl.php",'M025');
		array_push($trusted_community_row_array, $sub_community_row);
	}

	return $trusted_community_row_array;
}

if (!$this->_tpl_vars['is_community_member']) {
	$disabled_str = ' style="background-color:#dddddd" disabled';
}
//確認画面からキャンセルで戻ってきた時の処理　情報回帰
	$value = '';
	if($this->_tpl_vars['move_id'] == 3){
		$value['subject'] = $this->_tpl_vars['form']['subject'];
		$value['body'] = $this->_tpl_vars['form']['body'];
		if($this->_tpl_vars['form']['open_level_code'] == '06'){
			$value['xdate'] = $this->_tpl_vars['form']['xdate'];
		}
	}
//エラー処理
ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?> <?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M002') ?>
 <a href="<?= $this->_tpl_vars['bbs_rss_url'] ?>"><img src="<?php echo ACS_IMAGE_DIR . "rss.png" ?>" border=0></a>
</div>
<br>

<?php
// BBS検索画面の表示
echo "<a class=\"ichiran3\" href=\"" .$this->_tpl_vars['search_bbs_url'] ."\">";
echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M003');
echo "</a>";
if ($this->_tpl_vars['is_community_admin'] && $this->_tpl_vars['get_external_rss_url'] != '') {
	echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a class=\"ichiran3\" href=\"". htmlspecialchars($this->_tpl_vars['get_external_rss_url']) . "\"";
	echo " title=\"[RSS]\n" . htmlspecialchars($this->_tpl_vars['community_row']['contents_row_array']['external_rss_url']['contents_value']) . "\"";
	echo ">";
	echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M033');
	echo "</a>";
}
echo "<br><br>\n";
?>

<?php
if ($this->_tpl_vars['is_community_member']) {
?>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post" name="bbs_form" enctype="multipart/form-data">

<input type="hidden" name="except_community_id_array[]" value="<?= $this->_tpl_vars['community_row']['community_id'] ?>">

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M004') ?></td>
<td bgcolor="#ffffff"><input type="text" name="subject" value="<?=$value['subject'] ?>" size="50" style="width:400px"<?= $disabled_str ?>></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M005') ?></td>
<td bgcolor="#ffffff"><textarea name="body" cols="60" rows="10" style="width:480px"<?= $disabled_str ?>><?= htmlspecialchars($value['body']) ?></textarea></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M006') ?></td>
<td bgcolor="#ffffff"><input type="file" name="new_file" size="50" <?=$value ?> ></td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M007') ?></td>
<td bgcolor="#ffffff">

<?php
// 公開範囲
echo "<select name=\"open_level_code\" onchange=\"print_sub_menu(this)\"{$disabled_str}>\n";
// 選択状態をセットする
unset($selected);
foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
	if ($open_level_master_row['is_default'] == 't') {
		$selected[$open_level_master_row['open_level_code']] = ' selected';
		break;
	}
}
//キャンセル処理の場合
	if($this->_tpl_vars['move_id'] == 3){
		unset($selected);
		$selected[$this->_tpl_vars['form']['open_level_code']] = ' selected';
	}

// プルダウンメニュー表示
foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
	echo "<option value=\"".$open_level_master_row['open_level_code']. "\"{$selected[$open_level_master_row['open_level_code']]}>";
	echo htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
}
echo "</select><br>\n";

// 信頼済みコミュニティ　部分の書式設定　選択されたプルダウンメニューにより変化する
echo "<div id=\"trusted_community_div\"></div>";
?>

</td>
</tr>
<?php
if ($this->_tpl_vars['is_ml_active'] || $this->_tpl_vars['is_community_admin']) {
	echo '<tr>';
	echo '<td id="myttl" bgcolor="#DEEEBD">'.ACSMsg::get_msg("Community", "BBS.tpl.php",'M028').'</td>';
	echo '<td bgcolor="#ffffff">';

	if ($this->_tpl_vars['is_ml_active']) {
		echo '<input type="checkbox" name="is_ml_send" value="t"' . ($this->_tpl_vars['form']['is_ml_send']=='t' ? ' CHECKED' : '') . '>';
		echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M029');
	} else {
		echo '<span class="notice">' . ACSMsg::get_msg("Community", "BBS.tpl.php",'M030') . '</span';
	}
}
?>
</td>
</tr>
</table>
<br>
<?php
// submit
if ($this->_tpl_vars['is_community_member']) {
	echo "<input type=\"submit\" value=\"".ACSMsg::get_msg("Community", "BBS.tpl.php",'M008')."\">\n";
} else {
	echo "<input type=\"button\" value=\"".ACSMsg::get_msg("Community", "BBS.tpl.php",'M008')."\" disabled>\n";
}
?>
</form>
<br>


<script language="JavaScript">
<!--
window.onload = function () {
	// 公開範囲のデフォルト選択
	select_obj = document.forms["bbs_form"].elements["open_level_code"];
	selected_open_level_name = select_obj.options[select_obj.selectedIndex].text
	if (selected_open_level_name == '<?= ACSMsg::get_mst('open_level_master','D04') ?>' || selected_open_level_name == '<?= ACSMsg::get_mst('open_level_master','D06') ?>') {
		print_sub_menu(select_obj);
	}
}

<?php
// 信頼済みコミュニティのデフォルト
$trusted_community_row_array = get_trusted_community_row_array($this->_tpl_vars['parent_community_row_array'], $this->_tpl_vars['sub_community_row_array']);
?>
trusted_community_row_array = new Array(
<?php
$str = '';
foreach ($trusted_community_row_array as $trusted_community_row) {
	if ($str != '') {
		$str .= ", ";
	}
	$str .= "";
	$str .= "{";
	$str .= "\"community_id\" : \"".$trusted_community_row['community_id']."\", ";
	$str .= "\"community_name\" : \"".$trusted_community_row['community_name']."\", ";
	$str .= "\"community_position\" : \"".$trusted_community_row['community_position']."\", ";
	$str .= "\"top_page_url\" : \"".$trusted_community_row['top_page_url']."\"";
	$str .= "}";
}
echo $str;
?>
);

post_date  = new Array(2);
<?php
//パブリックリリース選択時
if($value['xdate']){
	echo "post_date[0]=\"yes\";";
	$str_post = '';
	$str_post .= "post_date[1]=\"".$value['xdate']."\"";
	echo $str_post .";";
}
?>

// 信頼済みコミュニティのサブメニューを表示する
function print_sub_menu(select_obj) {
	selected_open_level_name = select_obj.options[select_obj.selectedIndex].text
	div_obj = document.getElementById("trusted_community_div");

	while (div_obj.hasChildNodes()) {
		div_obj.removeChild(div_obj.firstChild);
	}

	if (selected_open_level_name == "<?= ACSMsg::get_mst('open_level_master','D04') ?>") {
		// <table>
		table_obj = document.createElement("table");
		table_obj.className = "layout_table";

		// <tr>
		new_row = table_obj.insertRow(0);
		// <td>
		new_cell = new_row.insertCell(0);
		new_cell.id = 'trusted_community_td';

		// text
		for (i = 0; i < trusted_community_row_array.length; i++) {
			// <input>
			if (document.all) {
				input_obj = document.createElement('<input name="trusted_community_id_array[]">');
			} else {
				input_obj = document.createElement("input");
				input_obj.name = "trusted_community_id_array[]";
			}
			input_obj.type = "checkbox";
			input_obj.value = trusted_community_row_array[i]["community_id"];
			input_obj.defaultChecked = false;
			new_cell.appendChild(input_obj);

			// <a>
			a_obj = document.createElement("a");
			a_obj.href = trusted_community_row_array[i]["top_page_url"];
			a_obj.target = "_blank";
			a_obj.appendChild(document.createTextNode(trusted_community_row_array[i]["community_name"]));
			new_cell.appendChild(a_obj);

			// text
			if (trusted_community_row_array[i]["community_position"] != '') {
				new_cell.appendChild(document.createTextNode(" ("));
				new_cell.appendChild(document.createTextNode(trusted_community_row_array[i]["community_position"]));
				new_cell.appendChild(document.createTextNode(")"));
			}
			// <br>
			new_cell.appendChild(document.createElement("br"));
		}
		// </td>
		// </tr>


		// <tr>
		new_row = table_obj.insertRow(1);
		// <td>
		new_cell = new_row.insertCell(0);

		// button
		input_obj = document.createElement("input");
		input_obj.type = "button";
		input_obj.value = "<?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M026') ?>";
		input_obj.onclick = function () {
			window.open("<?= $this->_tpl_vars['select_trusted_community_url'] ?>", "SelectTrustedCommunity", "width=600,height=400,top=200,left=200,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
		}
		new_cell.appendChild(input_obj);

		// <span>
		span_obj = document.createElement("span");
		span_obj.style.fontSize = "8pt";
		span_obj.appendChild(document.createTextNode("<?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M009') ?>"));
		new_cell.appendChild(span_obj);
		// </span>

		// </td>
		// </tr>


		// </table>
		div_obj.appendChild(table_obj);
	}

	/* 信頼済みコミュニティのサブメニューを表示する パブリックリリースの場合 */
	if (selected_open_level_name == "<?= ACSMsg::get_mst('open_level_master','D06') ?>") {

	// 信頼済みコミュニティ　部分の書式設定　選択されたプルダウンメニューにより変化する
		div_obj = document.getElementById("trusted_community_div");

		//現在の書式をクリアする
		while (div_obj.hasChildNodes()) {
			div_obj.removeChild(div_obj.firstChild);
		}
		//新しく書式の元を成形する
		// <table>
		table_obj = document.createElement("table");
		table_obj.className = "layout_table";

		// <tr> 1行目 掲載終了日の入力
		new_row = table_obj.insertRow(0);
		// <td>
		new_cell = new_row.insertCell(0);
		new_cell.id = 'trusted_community_td';

			// <span>
			span_obj = document.createElement("span");
			span_obj.style.fontSize = "10pt";
			span_obj.appendChild(document.createTextNode("<?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M010') ?>"));
			new_cell.appendChild(span_obj);
			// </span>

			// <input>txtField
			input_obj = document.createElement("input");
			input_obj.type = "text";
			input_obj.name = "xdate";
			input_obj.id = "expire_date";
			input_obj.value="";
			if(post_date[0] == "yes"){
				input_obj.value = post_date[1];
			}
			new_cell.appendChild(input_obj);

			// </td>
			// </tr>

			// <tr> 2行目 notes
			new_row = table_obj.insertRow(1);
			// <td>
			new_cell = new_row.insertCell(0);
	
			// <span>
			span_obj = document.createElement("span");
			span_obj.style.fontSize = "8pt";
			span_obj.appendChild(document.createTextNode("<?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M011') ?>"));

			new_cell.appendChild(span_obj);
			// </span>
	
			// </td>
			// </tr>
		// </table>
		// 信頼済みコミュニティ　部分の新しい書式設定
		div_obj.appendChild(table_obj);
		
	}
}
// -->
</script>

<?php
}
?>


<!--　表示  -->
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33" width="660">
<th id="myttl" bgcolor="#DEEEBD"><span class="nowrap"><?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M012') ?></span></th>
<td bgcolor="#ffffff">
<?php
// スレッド一覧
$str = '';
foreach ($this->_tpl_vars['bbs_row_array'] as $index => $bbs_row) {
	if ($str != '') {
		$str .= "&nbsp;&nbsp;";
	}
	$str .= "<a href=\"$bbs_row[bbs_res_url]\">";
	$str .= htmlspecialchars(ACSTemplateLib::trim_long_str($bbs_row['subject']));
	$str .= "</a>";
	$str .= "($bbs_row[bbs_res_row_array_num])";
}
echo $str;
?>
<div align="right">[<a href="<?= $this->_tpl_vars['bbs_thread_list_url'] ?>"><?= ACSMsg::get_msg("Community", "BBS.tpl.php",'M013') ?></a>]</div>
</td></tr></table>
<br>


<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']); 
?>
<br>


<?php
// table
foreach ($this->_tpl_vars['bbs_row_array'] as $index => $bbs_row) {
	if($bbs_row['bbs_delete_flag']=='f'){
		// 親記事
		//echo "<table border=\"1\" class=\"bbs_table\">\n";
		echo "<table class=\"common_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";

		// ヘッダ部	
		echo "<tr><td colspan=\"2\" bgcolor=\"#DEEEBD\" class=\"head\" align=\"right\">";
		if (is_array($bbs_row['external_rss_row'])) {
			// 記事へのリンク
			if ($bbs_row['external_rss_row']['rss_item_link'] != '') {
				echo "<a href=\"" . htmlspecialchars($bbs_row['external_rss_row']['rss_item_link']) . "\" target=\"_blank\">";
				echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M031');
				echo "</a>";
			}
			// (YYYY/MM/DD H:MM RSS配信)
			if ($bbs_row['external_rss_row']['rss_item_date'] != '') {
				echo "&nbsp;(";
				echo $bbs_row['external_rss_row']['rss_item_date'];
				echo " " . ACSMsg::get_msg("Community", "BBS.tpl.php",'M032') . ")";
			}
		} else {
			echo "&nbsp;";
		}
		echo "</td></tr>\n";
	
		// 投稿者情報
		echo "<tr>";
		echo "<td align=\"center\" width=\"80px\" bgcolor=\"#ffffff\">";
		if($bbs_row['bbs_delete_flag']=='f'){
			echo "<a href=\"".$bbs_row['top_page_url']."\"><img src=\"".$bbs_row['image_url']."\" border=\"0\"></a><br>";
			echo "<a href=\"".$bbs_row['top_page_url']."\">" . htmlspecialchars($bbs_row['community_name']) . "</a>";
		}
		echo "</td>";
	
		echo "<td class=\"body\" valign=\"top\" bgcolor=\"#ffffff\">";
	
	
		echo "<table class=\"layout_table\" width=\"480px\">";
		echo "<tr>";
	
		echo "<td>";
		if($bbs_row['bbs_delete_flag']=='f'){
			echo "<b>" . htmlspecialchars($bbs_row['subject']) . "</b>";
		}
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		//echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M014')." : " . ACSLib::convert_pg_date_to_str($bbs_row['post_date']) . "</td>";
		echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M014')." : " . $bbs_row['post_date'] . "</td>";
		echo "<td align=\"right\" valign=\"top\">";
		echo "<form>";
		//返信ボタンの表示　Communityのメンバであること、削除フラグがないこと
		if ($this->_tpl_vars['is_community_member'] && $bbs_row['bbs_delete_flag']=='f') {
			echo "<input type=\"button\" value=\"".ACSMsg::get_msg("Community", "BBS.tpl.php",'M015')."\" onclick=\"location.href='$bbs_row[bbs_res_url]'\">";
			echo "&nbsp;";
		} else {
			//echo "<input type=\"button\" value=\"返信\" disabled>";
		}
		// 編集ボタンの表示
		if ($bbs_row['edit_bbs_url']) {
			echo "<input type=\"button\" value=\"".ACSMsg::get_msg("Community", "BBS.tpl.php",'M016')."\" onclick=\"location.href='$bbs_row[edit_bbs_url]'\">";
			echo "&nbsp;";
		}
		//削除ボタンの表示　＋自分が投稿したもの　又は　コミュニティ管理者
		if ($this->_tpl_vars['is_community_member'] && $bbs_row['bbs_delete_flag']=='f' && $bbs_row['bbs_set_delete_flag'] == true ) {
			echo "<input type=\"button\" value=\"".ACSMsg::get_msg("Community", "BBS.tpl.php",'M017')."\" onclick=\"location.href='$bbs_row[bbs_delete_url]'\">";
		} else {
			//echo "<input type=\"button\" value=\"削除\" disabled>";
		}
	
		echo "</form>";
		echo "</td>";
		echo "</tr>\n";
	
	
		echo "<tr><td colspan=\"2\">";
		// 公開範囲
		if($bbs_row['bbs_delete_flag']=='f'){
			echo "<table border=\"0\" bgcolor=\"#dddddd\"><tr><td>";
			echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M007')." : " . htmlspecialchars($bbs_row['open_level_name']);
			// パブリックリリースの場合のみ掲載終了日を表示させる 2/21add @akitsu
			if($bbs_row['expire_date']!=''){
				$out_expire_date = "[ ".ACSMsg::get_msg("Community", "BBS.tpl.php",'M019').":" . $bbs_row['expire_date'] . " ]";
				echo $out_expire_date;
			}
			echo  "<br>";
			if (count($bbs_row['trusted_community_row_array'])) {
				$trusted_community_str = '';
				foreach ($bbs_row['trusted_community_row_array'] as $trusted_community_row) {
					if ($trusted_community_str != '') {
						$trusted_community_str .= ", ";
					}
					$trusted_community_str .= $trusted_community_row['community_name'];
				}
				if ($trusted_community_str != '') {
					echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M020')." : " . htmlspecialchars($trusted_community_str) . "<br>";;
				}
			}
			echo "</td></tr></table>";
		}
		echo "</td></tr>\n";
	
		//写真
		if ($bbs_row['file_url']) {
			echo "<tr>";
			echo "<td valign=\"top\" colspan=\"2\">";
			echo "<a href=\"javascript:w=window.open('" . $bbs_row['file_url_alink'] . "','popUp','scrollbars=yes,resizable=yes');w.focus();\">";
			echo "<img src=\"". $bbs_row['file_url'] . "\" style=\"margin-top:10px;margin-bottom:10px\" BORDER=0></a>";
			echo "</td>";
			echo "</tr>\n";
		}
	
		echo "<tr>";
		echo "<td valign=\"top\" colspan=\"2\">";
		if($bbs_row['bbs_delete_flag']=='f'){
			echo nl2br(ACSTemplateLib::auto_link(ACSLib::sp2nbsp(htmlspecialchars($bbs_row['body']))));
		}else{
			echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M021');
		}
		echo "</td>";
		echo "</tr>\n";

		echo "</table>";
	
	
		echo "</td>";
		echo "</tr>\n";
	

		// 表示件数が省略されている場合
		if ($bbs_row['omission_num']) {
			echo "<tr><td colspan=\"2\" align=\"right\" bgcolor=\"#ffffff\">";
			//echo "<span class=\"note\">(1 - $bbs_row[omission_num] 件目は省略されました)</span>";
			echo "<span class=\"note\">".
					ACSMsg::get_tag_replace(ACSMsg::get_msg("Community", "BBS.tpl.php",'TPL'),
						array("{OMISSION_NUM}" => $bbs_row['omission_num'])) . "</span>";
			echo "&nbsp;&nbsp;";
			//echo "<a href=\"$bbs_row[bbs_res_url]\">全て読む($bbs_row[bbs_res_row_array_num])</a>";
			echo "<a href=\"".$bbs_row['bbs_res_url']."\">".
					ACSMsg::get_msg("Community", "BBS.tpl.php",'M027')."(".$bbs_row['bbs_res_row_array_num'].")</a>";
			echo "</td></tr>\n";
		}

	
		// 返信記事
		if($bbs_row['bbs_delete_flag']=='f'){
			foreach ($bbs_row['bbs_res_row_array'] as $bbs_res_row) {		//親が削除されている時　ここから無し
				echo "<tr>";
				echo "<td align=\"center\" width=\"80px\" bgcolor=\"#ffffff\">";
				if($bbs_res_row['bbs_res_delete_flag']=='f'){
					echo "<a href=\"".$bbs_res_row['top_page_url']."\"><img src=\"" . 
										$bbs_res_row['image_url'] . "\" border=\"0\"></a><br>";
					echo "<a href=\"".$bbs_res_row['top_page_url']."\">" . htmlspecialchars($bbs_res_row['community_name']) . "</a>";
				}
				echo "</td>";
	
				echo "<td class=\"body\" valign=\"top\" bgcolor=\"#ffffff\">";
	
				echo "<table class=\"layout_table\" width=\"450px\" border=\"0\">";
				echo "<tr>";
				echo "<td><b>";
				if($bbs_res_row['bbs_res_delete_flag']=='f'){
					echo htmlspecialchars($bbs_res_row['subject']) . "</b>";
				}
				echo "&nbsp;&nbsp;&nbsp;&nbsp;";
				//echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M014')." : " . ACSLib::convert_pg_date_to_str($bbs_res_row['post_date']) . "</td>";
				echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M014')." : " . $bbs_res_row['post_date'] . "</td>";
	
				echo "<td></td>";
				echo "</tr>";
	
				echo "<tr>";
				echo "<td valign=\"top\">";
				if($bbs_res_row['bbs_res_delete_flag']=='f'){
					echo nl2br(ACSTemplateLib::auto_link(ACSLib::sp2nbsp(htmlspecialchars($bbs_res_row['body']))));
				}else{
					echo ACSMsg::get_msg("Community", "BBS.tpl.php",'M021');
				}
				echo "</td>";
				echo "</tr>";
	
				echo "</table>";
	
				echo "</td>";
				echo "</tr>\n";
			}

			// 続きがある場合
			if ($bbs_row['omission_num']) {
				echo "<tr><td colspan=\"2\" align=\"right\">";
				//echo "<a href=\"$bbs_row[bbs_res_url]\">全て読む($bbs_row[bbs_res_row_array_num])</a>";
				echo "<a href=\"".$bbs_row['bbs_res_url']."\">".ACSMsg::get_msg("Community", "BBS.tpl.php",'M027')."(".$bbs_row['bbs_res_row_array_num'].")</a>";
				echo "</td></tr>\n";
			}
	
			echo "</table>\n";
			echo "<br>\n";
		}	//親が削除されている時　ここまで無し
	}
	echo "<br>\n";
}
?>

<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']); 
?>
