<?php
// $Id: SelectTrustedCommunity.tpl.php,v 1.9 2007/03/01 09:01:35 w-ota Exp $
?>

<font size="3"><?= ACSMsg::get_msg("Community", "SelectTrustedCommunity.tpl.php",'M001') ?></font><br>

<p>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="get">
<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
<input type="hidden" name="form_name" value="<?= $this->_tpl_vars['form_name'] ?>">
<input type="hidden" name="prefix" value="<?= $this->_tpl_vars['prefix'] ?>">
<input type="hidden" name="search" value="1">

<table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
 <tr>
  <td bgcolor="#FFF5AA">
<table class="layout_table"><tr><td>
<tr>
<td><?= ACSMsg::get_msg("Community", "SelectTrustedCommunity.tpl.php",'M002') ?></td>
<td><input type="text" name="q" value="<?= htmlspecialchars($this->_tpl_vars['form']['q']) ?>" size="30"></td>
<td><input type="submit" value="<?= ACSMsg::get_msg("Community", "SelectTrustedCommunity.tpl.php",'M003') ?>"></td>
</tr>
<tr>
<td><?= ACSMsg::get_msg("Community", "SelectTrustedCommunity.tpl.php",'M004') ?></td>
<td>
<select name="category_code">
<?php
unset($selected);
$selected[$this->_tpl_vars['form']['category_code']] = ' selected';
foreach ($this->_tpl_vars['category_master_row_array'] as $category_master_row) {
	echo "<option value=\"$category_master_row[category_code]\"{$selected[$category_master_row['category_code']]}>";
	echo htmlspecialchars($category_master_row['category_name']) . "\n";
}
?>
</select>
</td>
</tr>
</table>
</td></tr></table>

</form>
</p>

<?php
if (count($this->_tpl_vars['community_row_array'])) {
	// ページング表示
	ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
}
?>

<form name="trusted_community_form" style="font-size:10pt">
<table  border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr><td bgcolor="#ffffff">
<?php
foreach ($this->_tpl_vars['community_row_array'] as $community_row) {
	echo "<input type=\"checkbox\" name=\"trusted_community_id_array[]\" value=\"$community_row[community_id]\" alt=\"";
	echo htmlspecialchars($community_row['community_name']);
	echo "\">";
	echo "<a href=\"$community_row[top_page_url]\" target=\"_blank\">";
	echo htmlspecialchars($community_row['community_name']);
	echo "</a>";
	echo "<br>\n";
}
?>
</tr></td>
</table>
<br>
<input type="button" value="<?= ACSMsg::get_msg("Community", "SelectTrustedCommunity.tpl.php",'M005') ?>" onclick="add_trusted_community(this.form)">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("Community", "SelectTrustedCommunity.tpl.php",'M006') ?>" onclick="window.close()">
</form>


<script language="JavaScript">
<!--
// 定数
var prefix = "<?= str_replace("\"", "\\\"", $this->_tpl_vars['prefix']) ?>";
var parent_form_name = "<?= str_replace("\"", "\\\"", $this->_tpl_vars['form_name']) ?>";


window.onload = function () {
	focus();
	disable_exist_community_id();
	// 検索ボックスにfocus
	document.forms[0].elements["q"].focus();
}

community_row_array = new Array(
<?php
$str = '';
foreach ($this->_tpl_vars['community_row_array'] as $community_row) {
	if ($str != '') {
		$str .= ", ";
	}
	$str .= "";
	$str .= "{";
	$str .= "\"community_id\" : \"$community_row[community_id]\", ";
	$str .= "\"community_name\" : \"$community_row[community_name]\", ";
	$str .= "\"top_page_url\" : \"$community_row[top_page_url]\"";
	$str .= "}";
}
echo $str;
?>
);


function add_trusted_community(child_form_obj) {
	if (window.opener != null) {
		parent_td_obj = window.opener.document.getElementById(prefix + "trusted_community_td");

		if (child_form_obj.elements["trusted_community_id_array[]"]) {
			if (child_form_obj.elements["trusted_community_id_array[]"].value) {
				// 1個のチェックボックス
				if (child_form_obj.elements["trusted_community_id_array[]"].checked && !child_form_obj.elements["trusted_community_id_array[]"].disabled) {
					_add_trusted_community(parent_td_obj, 0);
				}
			} else {
				// 複数のチェックボックス
				for (var i = 0; i < child_form_obj.elements["trusted_community_id_array[]"].length; i++) {
					if (child_form_obj.elements["trusted_community_id_array[]"][i].checked && !child_form_obj.elements["trusted_community_id_array[]"][i].disabled) {
						_add_trusted_community(parent_td_obj, i);
					}
				}
			}
		}

		window.close();
	}
}

function _add_trusted_community(parent_td_obj, i) {
	// <input>
	if (document.all) {
		parent_input_obj = window.opener.document.createElement('<input name="' + prefix + 'trusted_community_id_array[]">');
	} else {
		parent_input_obj = window.opener.document.createElement("input");
		parent_input_obj.name = prefix + "trusted_community_id_array[]";					
	}
	parent_input_obj.type = "checkbox";
	parent_input_obj.value = community_row_array[i]["community_id"];
	parent_input_obj.defaultChecked = true;
	parent_td_obj.appendChild(parent_input_obj);

	// <a>
	parent_a_obj = window.opener.document.createElement("a");
	parent_a_obj.href = community_row_array[i]["top_page_url"];
	parent_a_obj.target = "_blank";
	parent_a_obj.appendChild(window.opener.document.createTextNode(community_row_array[i]["community_name"]));
	parent_td_obj.appendChild(parent_a_obj);

	// <br>
	parent_td_obj.appendChild(window.opener.document.createElement("br"));
}

function disable_exist_community_id() {
	child_form_obj = document.forms["trusted_community_form"];

	// 親フォーム
	parent_form = window.opener.document.forms[parent_form_name];

	// 親ウィンドウで既に選択されている信頼済みコミュニティ
	exist_trusted_community_array = new Array();

	if (parent_form.elements[prefix + "trusted_community_id_array[]"]) {
		// 既に選択されている信頼済みコミュニティを収集する
		if (parent_form.elements[prefix + "trusted_community_id_array[]"].value) {
			// 1個のチェックボックス
			exist_trusted_community_array.push(parent_form.elements[prefix + "trusted_community_id_array[]"].value);
		} else {
			// 複数のチェックボックス
			for (var i = 0; i < parent_form.elements[prefix + "trusted_community_id_array[]"].length; i++) {
				exist_trusted_community_array.push(parent_form.elements[prefix + "trusted_community_id_array[]"][i].value);
			}
		}
	}
		
	// 親ウィンドウで除外指定されているコミュニティを収集する
	if (parent_form.elements[prefix + "except_community_id_array[]"]) {
		if (parent_form.elements[prefix + "except_community_id_array[]"].value) {
			// 1個のhidden要素
			exist_trusted_community_array.push(parent_form.elements[prefix + "except_community_id_array[]"].value);
		} else {
			// 複数のhidden要素
			for (var i = 0; i < parent_form.elements[prefix + "except_community_id_array[]"].length; i++) {
				exist_trusted_community_array.push(parent_form.elements[prefix + "except_community_id_array[]"][i].value);
			}
		}
	}

	// 既に選択されている信頼済みコミュニティのチェックボックスはdisabledとする
	if (child_form_obj.elements["trusted_community_id_array[]"]) {
		if (child_form_obj.elements["trusted_community_id_array[]"].value) {
			// 1個のチェックボックス
			if (find_array(child_form_obj.elements["trusted_community_id_array[]"].value, exist_trusted_community_array)) {
				child_form_obj.elements["trusted_community_id_array[]"].disabled = true;
			}
		} else {
			// 複数のチェックボックス
			for (var i = 0; i < child_form_obj.elements["trusted_community_id_array[]"].length; i++) {
				if (find_array(child_form_obj.elements["trusted_community_id_array[]"][i].value, exist_trusted_community_array)) {
					child_form_obj.elements["trusted_community_id_array[]"][i].disabled = true;
				}
			}
		}
	}
}


function find_array(value, arr) {
	var i;
	for (i = 0; i < arr.length; i++) {
		if (arr[i] == value) {
			return true;
		}
	}
	return false;
}
// -->
</script>

</body>
</html>
