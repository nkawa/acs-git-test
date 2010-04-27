<?php
// +----------------------------------------------------------------------+
// | PHP version 4	    										                        		  |
// | Authors: w-ota		 v 1.3 2006/03/02      @update akitsu							  |
// +----------------------------------------------------------------------+
//　EditCommunity.tpl.php
//
// $Id: EditCommunity.tpl.php,v 1.9 2007/03/28 05:58:19 w-ota Exp $
?>

<div class="ttl">
<a href="<?= $this->_tpl_vars['community_top_page_url'] ?>">
<?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?> 
<?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M002') ?></div><br>
<br>

<?php ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']); ?>

<p>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="post" name="create_community_form">

<input type="hidden" name="join_except_community_id_array[]" value="<?= $this->_tpl_vars['community_row']['community_id'] ?>">
<input type="hidden" name="bbs_except_community_id_array[]" value="<?= $this->_tpl_vars['community_row']['community_id'] ?>">
<input type="hidden" name="community_folder_except_community_id_array[]" value="<?= $this->_tpl_vars['community_row']['community_id'] ?>">

<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<?php //コミュニティ名を編集不可にしたい場合は readonly属性を追加すること ?>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M003') ?></td>
<td bgcolor="#ffffff"><input type="text" name="community_name" value="<?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?>"></td>
</tr>

<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M004') ?></td>
<td bgcolor="#ffffff"><textarea name="community_profile" cols="50" rows="4"><?= htmlspecialchars($this->_tpl_vars['community_row']['contents_row_array']['community_profile']['contents_value']) ?></textarea></td>
</tr>

<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M005') ?></td>
<td bgcolor="#ffffff">
<select name="category_code">
<?php
foreach ($this->_tpl_vars['category_master_row_array'] as $category_master_row) {
	if($category_master_row[category_code] == $this->_tpl_vars['community_row']['category_code']){
		echo "<option value=\"$category_master_row[category_code]\" selected>";
	}else{
		echo "<option value=\"$category_master_row[category_code]\">";
	}
		echo htmlspecialchars($category_master_row['category_name']);
}
?>
</select>
</td>
</tr>

<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M006') ?></td>
<td bgcolor="#ffffff">
<?php
if($this->_tpl_vars['community_row']['admission_flag'] == 't') {
	echo "<input type=\"radio\" name=\"admission_flag\" value=\"0\" onclick=\"print_sub_menu(this, join_trusted_community_row_array, 'join_', 0)\">".ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M021')."<br>";
	echo "<input type=\"radio\" name=\"admission_flag\" value=\"1\" onclick=\"print_sub_menu(this, join_trusted_community_row_array, 'join_', 1)\" checked>".ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M022')."<br>";
}else{
	echo "<input type=\"radio\" name=\"admission_flag\" value=\"0\" onclick=\"print_sub_menu(this, join_trusted_community_row_array, 'join_', 0)\" checked>".ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M021')."<br>";
	echo "<input type=\"radio\" name=\"admission_flag\" value=\"1\" onclick=\"print_sub_menu(this, join_trusted_community_row_array, 'join_', 1)\">".ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M022')."<br>";
}
	echo "<div id=\"join_trusted_community_div\"></div>";
?>
</td>
</tr>

<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M011') ?></td>
<td bgcolor="#ffffff">
<?php
// 公開範囲: 掲示板
// 選択状態をセット
unset($selected);
foreach ($this->_tpl_vars['bbs_open_level_master_row_array'] as $open_level_master_row) {
	if ($open_level_master_row['is_default'] == 't') {
		$selected[$open_level_master_row['open_level_code']] = ' selected';
		break;
	}
}
// プルダウンメニュー表示
echo "<select name=\"bbs_open_level_code\" onchange=\"print_sub_menu(this, bbs_trusted_community_row_array, 'bbs_')\">\n";
foreach ($this->_tpl_vars['bbs_open_level_master_row_array'] as $open_level_master_row) {
	if($open_level_master_row['open_level_code'] == $this->_tpl_vars['community_row']['contents_row_array']['bbs']['open_level_code']){
		echo "<option value=\"$open_level_master_row[open_level_code]\" selected>";
	}else{
		echo "<option value=\"$open_level_master_row[open_level_code]\">";
	}
		echo htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
}
echo "</select><br>\n";
// 信頼済みコミュニティ
echo "<div id=\"bbs_trusted_community_div\"></div>";

?>
</td>
</tr>


<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M020') ?></td>
<td bgcolor="#ffffff">
<?php
// 公開範囲: コミュニティフォルダ
// 選択状態をセット
unset($selected);
foreach ($this->_tpl_vars['community_folder_open_level_master_row_array'] as $open_level_master_row) {
	if ($open_level_master_row['is_default'] == 't') {
		$selected[$open_level_master_row['open_level_code']] = ' selected';
		break;
	}
}
// プルダウンメニュー表示
echo "<select name=\"community_folder_open_level_code\" onchange=\"print_sub_menu(this, community_folder_trusted_community_row_array, 'community_folder_')\">\n";
foreach ($this->_tpl_vars['community_folder_open_level_master_row_array'] as $open_level_master_row) {
	if($open_level_master_row['open_level_code'] == $this->_tpl_vars['community_row']['contents_row_array']['community_folder']['open_level_code']){
		echo "<option value=\"$open_level_master_row[open_level_code]\" selected>";
	}else{
		echo "<option value=\"$open_level_master_row[open_level_code]\">";
	}
	echo htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
}
echo "</select><br>\n";
// 信頼済みコミュニティ
echo "<div id=\"community_folder_trusted_community_div\"></div>";
?>
</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M012') ?></td>
<td bgcolor="#ffffff">
<?php
// 公開範囲: 掲示板
// 選択状態をセット
unset($selected);
foreach ($this->_tpl_vars['self_open_level_master_row_array'] as $open_level_master_row) {
	if ($open_level_master_row['is_default'] == 't') {
		$selected[$open_level_master_row['open_level_code']] = ' checked';
		break;
	}
}
// プルダウンメニュー表示
foreach ($this->_tpl_vars['self_open_level_master_row_array'] as $open_level_master_row) {
	if($open_level_master_row['open_level_code'] == $this->_tpl_vars['community_row']['contents_row_array']['self']['open_level_code']) {
			echo "<input type=\"radio\" name=\"self_open_level_code\" value=\"$open_level_master_row[open_level_code]\" checked>";
	}else{
			echo "<input type=\"radio\" name=\"self_open_level_code\" value=\"$open_level_master_row[open_level_code]\">";
	}
	echo htmlspecialchars($open_level_master_row['open_level_name']);
	if ($open_level_master_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03')) {
		echo "<span class=\"notice\">";
		echo ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M013');
		echo "</span>";
	}
	echo "<br>\n";
}
?>
</td>
</tr>

<?php 
// コミュニティＭＬアドレス
?>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M023') ?></td>
<?php
$community_ml_address =& $this->_tpl_vars['community_row']['contents_row_array']['ml_addr']['contents_value'];
if ($community_ml_address == '') {
?>
 <td bgcolor="#ffffff">
   <?= ACS_COMMUNITY_ML_ADDR_PREFIX ?><input type="text" 
     name="community_ml_address" value="<?= htmlspecialchars($this->_tpl_vars['edit_community_ml_address']) ?>" 
     size="30"><?= ACS_COMMUNITY_ML_ADDR_SUFFIX ?><br>
   <span class="notice"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M024') ?><br><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M025') ?></span>
 </td>
<?php
} else {
?>
 <td bgcolor="#ffffff">
  <?= htmlspecialchars($community_ml_address) ?>
<?php
	  if ($this->_tpl_vars['community_row']['contents_row_array']['ml_status']['contents_value'] == 'QUEUE') {
		  echo '<br><span class="notice">';
		  echo ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M025');
		  echo '</span>';
	  }
?>
 </td>
<?php
}
?>
</tr>

<?php 
// 非更新情報　登録日
?>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M014') ?></td>
<td bgcolor="#ffffff"><?= $this->_tpl_vars['community_row']['register_date'] ?></td>
</tr>

</table>
<br>

<input type="submit" value="<?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M015') ?>">&nbsp;
    <input type="button" value="<?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M016') ?>" onclick="location.href='<?= $this->_tpl_vars['community_top_page_url'] ?>'"><br>
</form>
</p>


<script language="JavaScript">
<!--

window.onload = function () {
	// 公開範囲のデフォルト選択

	// 参加資格
	radio_obj = document.forms["create_community_form"].elements["admission_flag"];
	if (radio_obj[1].checked) {
		print_sub_menu(radio_obj[1], join_trusted_community_row_array, "join_", 1);
	}

	// 掲示版の公開範囲
	select_obj = document.forms["create_community_form"].elements["bbs_open_level_code"];
	selected_open_level_name = select_obj.options[select_obj.selectedIndex].text;
	if (selected_open_level_name = '<?= ACSMsg::get_mst('open_level_master','D04') ?>') {
		print_sub_menu(select_obj, bbs_trusted_community_row_array, "bbs_");
	}

	// コミュニティフォルダの公開範囲
	select_obj = document.forms["create_community_form"].elements["community_folder_open_level_code"];
	selected_open_level_name = select_obj.options[select_obj.selectedIndex].text;
	if (selected_open_level_name = '<?= ACSMsg::get_mst('open_level_master','D04') ?>') {
		print_sub_menu(select_obj, community_folder_trusted_community_row_array, "community_folder_");
	}
}


// 初期値:現在設定されている値
var bbs_trusted_community_row_array = new Array(
<?php
$str = '';
foreach ($this->_tpl_vars['community_row']['contents_row_array']['bbs']['trusted_community_row_array'] as $trusted_community_row) {
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
var community_folder_trusted_community_row_array = new Array(
<?php
$str = '';
foreach ($this->_tpl_vars['community_row']['contents_row_array']['community_folder']['trusted_community_row_array'] as $trusted_community_row) {
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
var join_trusted_community_row_array = new Array(
<?php
$str = '';
foreach ($this->_tpl_vars['community_row']['join_trusted_community_row_array'] as $trusted_community_row) {
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
		input_obj.value = "<?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M017') ?>";
		input_obj.onclick = function () {
			window.open("<?= $this->_tpl_vars['select_trusted_community_url'] ?>" + "&form_name=" + select_obj.form.name + '&prefix=' + prefix,
						"SelectTrustedCommunity", "width=600,height=400,top=200,left=200,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes");
		}
		new_cell.appendChild(input_obj);

		// <span>
		span_obj = document.createElement("span");
		span_obj.style.fontSize = "8pt";
		if (prefix == "join_") {
			span_obj.appendChild(document.createTextNode("<?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M018') ?>"));
		} else {
			span_obj.appendChild(document.createTextNode("<?= ACSMsg::get_msg("Community", "EditCommunity.tpl.php",'M019') ?>"));
		}
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
