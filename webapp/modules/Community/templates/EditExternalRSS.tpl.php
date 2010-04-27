<?php
// $Id: EditExternalRSS.tpl.php,v 1.1 2007/03/28 05:58:19 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?><?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M002') ?>
</div>
<br>
<?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M003') ?><br>
<br>


<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>


<form name="edit_external_rss_form" action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<input type="hidden" name="external_rss_url_except_community_id_array[]" value="<?= $this->_tpl_vars['community_row']['community_id'] ?>">

<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD">RSS URL</td>
<td bgcolor="#ffffff">
<input type="text" name="external_rss_url" value="<?= htmlspecialchars($this->_tpl_vars['community_row']['contents_row_array']['external_rss_url']['contents_value']) ?>" size="80"><br>
</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M004') ?></td>
<td bgcolor="#ffffff">
<?php
foreach ($this->_tpl_vars['community_admin_user_info_row_array'] as $community_admin_user_info_row) {
	echo "<input type=\"radio\" name=\"external_rss_post_user\" value=\"$community_admin_user_info_row[user_community_id]\"";
	if ($community_admin_user_info_row['user_community_id'] == $this->_tpl_vars['community_row']['contents_row_array']['external_rss_post_user']['contents_value']) {
		echo " checked";
	}
	echo ">";
	echo htmlspecialchars($community_admin_user_info_row['community_name']) . "<br>\n";
}
?>
<span class="notice"><?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M005') ?></span>
</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M006') ?></td>
<td bgcolor="#ffffff">
<?php
// 公開範囲: 外部RSS情報
// 選択状態をセット
unset($selected);
foreach ($this->_tpl_vars['external_rss_url_open_level_master_row_array'] as $open_level_master_row) {
	if ($open_level_master_row['is_default'] == 't') {
		$selected[$open_level_master_row['open_level_code']] = ' selected';
		break;
	}
}
// プルダウンメニュー表示
echo "<select name=\"external_rss_url_open_level_code\" onchange=\"print_sub_menu(this, external_rss_url_trusted_community_row_array, 'external_rss_url_')\">\n";
foreach ($this->_tpl_vars['external_rss_url_open_level_master_row_array'] as $open_level_master_row) {
	if($open_level_master_row['open_level_code'] == $this->_tpl_vars['community_row']['contents_row_array']['external_rss_url']['open_level_code']){
		echo "<option value=\"$open_level_master_row[open_level_code]\" selected>";
	}else{
		echo "<option value=\"$open_level_master_row[open_level_code]\">";
	}
	echo htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
}
echo "</select><br>\n";
// 信頼済みコミュニティ
echo "<div id=\"external_rss_url_trusted_community_div\"></div>";

?>
</td>
</tr>

<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M007') ?></td>
<td bgcolor="#ffffff">
<?php
unset($checked);
if (ACSLib::get_boolean($this->_tpl_vars['community_row']['contents_row_array']['external_rss_ml_send_flag']['contents_value'])) {
	$checked = ' checked';
}
?>
<input type="checkbox" name="external_rss_ml_send_flag" value="t"<?= $checked ?>>
<?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M008') ?><br>
<?php
if ($this->_tpl_vars['community_row']['contents_row_array']['ml_addr']['contents_value'] == '') {
	echo "<span class=\"notice\">".ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M009')."</span><br>\n";
}
?>
</td>
</tr>

</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M010') ?>">

</form>






<script language="JavaScript">
<!--

window.onload = function () {
	// 公開範囲のデフォルト選択

	// 掲示版の公開範囲
	select_obj = document.forms["edit_external_rss_form"].elements["external_rss_url_open_level_code"];
	selected_open_level_name = select_obj.options[select_obj.selectedIndex].text;
	if (selected_open_level_name = '<?= ACSMsg::get_mst('open_level_master','D04') ?>') {
		print_sub_menu(select_obj, external_rss_url_trusted_community_row_array, "external_rss_url_");
	}
}

// 初期値:現在設定されている値
var external_rss_url_trusted_community_row_array = new Array(
<?php
$str = '';
foreach ($this->_tpl_vars['community_row']['contents_row_array']['external_rss_url']['trusted_community_row_array'] as $trusted_community_row) {
	if ($str != '') {
		$str .= ", ";
	}
	$str .= "";
	$str .= "{";
	$str .= "\"community_id\" : \"$trusted_community_row[community_id]\", ";
	$str .= "\"community_name\" : \"$trusted_community_row[community_name]\", ";
	$str .= "\"community_position\" : \"$trusted_community_row[community_position]\", ";
	$str .= "\"top_page_url\" : \"$trusted_community_row[top_page_url]\"";
	$str .= "}";
}
echo $str;
?>
);

// 信頼済みコミュニティのサブメニューを表示する
function print_sub_menu(select_obj, trusted_community_row_array, prefix) {

	// 第4引数 //
	if (print_sub_menu.arguments.length == 4) {
		is_print_sub_menu = print_sub_menu.arguments[3];
		selected_open_level_name = "";
	} else {
		selected_open_level_name = select_obj.options[select_obj.selectedIndex].text;
		is_print_sub_menu = 0;
	}

	// 信頼済みコミュニティの設定箇所のdiv
	div_obj = document.getElementById(prefix + "trusted_community_div");

	// 信頼済みコミュニティのチェックボックスを一旦全て削除
	while (div_obj.hasChildNodes()) {
		div_obj.removeChild(div_obj.firstChild);
	}


	// サブメニュー表示
	if (selected_open_level_name == "<?= ACSMsg::get_mst('open_level_master','D04') ?>" || is_print_sub_menu) {
		// <table>
		table_obj = document.createElement("table");
		table_obj.className = "layout_table";

		// <tr>
		new_row = table_obj.insertRow(0);
		// <td>
		new_cell = new_row.insertCell(0);
		new_cell.id = prefix + "trusted_community_td";

		// text
		for (i = 0; i < trusted_community_row_array.length; i++) {
			// <input>
			if (document.all) {
				input_obj = document.createElement('<input name="' + prefix + 'trusted_community_id_array[]">');
			} else {
				input_obj = document.createElement("input");
				input_obj.name = prefix + "trusted_community_id_array[]";
			}
			input_obj.type = "checkbox";
			input_obj.value = trusted_community_row_array[i]["community_id"];
			//input_obj.defaultChecked = false;
			input_obj.defaultChecked = true;
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
		input_obj.value = "<?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M011') ?>";
		input_obj.onclick = function () {
			window.open("<?= $this->_tpl_vars['select_trusted_community_url'] ?>" + "&form_name=" + select_obj.form.name + '&prefix=' + prefix,
						"SelectTrustedCommunity", "width=600,height=400,top=200,left=200,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
		}
		new_cell.appendChild(input_obj);

		// <span>
		span_obj = document.createElement("span");
		span_obj.style.fontSize = "8pt";
		span_obj.appendChild(document.createTextNode("<?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M012') ?>"));
		new_cell.appendChild(span_obj);
		// </span>

		// </td>
		// </tr>


		// </table>
		div_obj.appendChild(table_obj);
	}

	// パブリックリリース
	if (selected_open_level_name == "<?= ACSMsg::get_mst('open_level_master','D06') ?>") {

		div_obj = document.getElementById("external_rss_url_trusted_community_div");

		//現在の書式をクリアする
		while (div_obj.hasChildNodes()) {
			div_obj.removeChild(div_obj.firstChild);
		}
		//新しく書式の元を成形する
		// <table>
		table_obj = document.createElement("table");
		table_obj.className = "layout_table";

		// <tr> 1行目 掲載期間の入力
		new_row = table_obj.insertRow(0);
		// <td>
		new_cell = new_row.insertCell(0);
		new_cell.id = 'trusted_community_td';

		// <span>
		span_obj = document.createElement("span");
		span_obj.style.fontSize = "10pt";
		span_obj.appendChild(document.createTextNode("<?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M013') ?> : "));
		new_cell.appendChild(span_obj);
		// </span>

		// <input>txtField
		input_obj = document.createElement("input");
		input_obj.type = "text";
		input_obj.size = "4";
		input_obj.name = "external_rss_public_release_expire_term";
		input_obj.value = "<?= $this->_tpl_vars['community_row']['contents_row_array']['external_rss_public_release_expire_term']['contents_value'] ?>";
		new_cell.appendChild(input_obj);
	
		// <span>
		span_obj = document.createElement("span");
		span_obj.style.fontSize = "8pt";
		span_obj.appendChild(document.createTextNode("<?= ACSMsg::get_msg("Community", "EditExternalRSS.tpl.php",'M014') ?>"));

		new_cell.appendChild(span_obj);
		// </span>
	
		// </td>
		// </tr>
		// </table>
		div_obj.appendChild(table_obj);
	}
}


// -->
</script>
