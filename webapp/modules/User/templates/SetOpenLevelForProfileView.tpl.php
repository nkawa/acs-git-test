<?php
// $Id: SetOpenLevelForProfileView.tpl.php,v 1.5 2006/11/20 08:44:26 w-ota Exp $
?>

<font size="3"><?php

	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "SetOpenLevelForProfileView.tpl.php",'CONT'),array(
			"{CONTENT_TYPE_NAME}" => htmlspecialchars($this->_tpl_vars['contents_type_name'])));

// ��??= htmlspecialchars($this->_tpl_vars['contents_type_name']) ??�פθ����ϰ�����</font><br>
?></font><br>
<br>

<form name="set_open_level_for_profile_form">
<?php
echo "<select name=\"open_level_code\" onchange=\"print_sub_menu(this)\">\n";
// ������֤򥻥åȤ���
unset($selected);
foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
	if ($open_level_master_row['is_default'] == 't') {
		$selected[$open_level_master_row['open_level_code']] = ' selected';
		break;
	}
}
// �ץ�������˥塼ɽ��
foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
	echo "<option value=\"$open_level_master_row[open_level_code]\"{$selected[$open_level_master_row['open_level_code']]}>";
	echo htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
}
echo "</select><br>\n";

//�ޥ��ե�󥺥��롼�׻���
echo "<div id=\"trusted_community_div\"></div>";
?>

<br>
<input type="button" value=" OK " onclick="update_parent_window()">
</form>


<script language="JavaScript">
<!--
// �ƥ�����ɥ���contents_key
contents_key = '<?= $this->_tpl_vars['contents_key'] ?>';


// ���ɻ��ν���
window.onload = function () {
	if (!window.opener) {
		return;
	}

	focus();

	// �ƥ�����ɥ��ξ��� //
	parent_form = window.opener.document.forms["edit_profile_form"];
	// ��: ������٥륳����
	parent_open_level_code = parent_form.elements["open_level_code_array[" + contents_key + "]"].value;
	if (parent_form.elements["trusted_community_id_csv_array[" + contents_key + "]"]
		&& parent_form.elements["trusted_community_flag[" + contents_key + "]"]) {
		// �Ƥο���Ѥߥ��ߥ�˥ƥ�(�ޥ��ե�󥺥��롼��)ID��CSV
		parent_trusted_community_id_csv = parent_form.elements["trusted_community_id_csv_array[" + contents_key + "]"].value;
		// �Ƥο���Ѥߥ��ߥ�˥ƥ�(�ޥ��ե�󥺥��롼��)ID������
		parent_trusted_community_id_array = parent_trusted_community_id_csv.split(",");
		// �Ƥο���Ѥߥ��ߥ�˥ƥ�(�ޥ��ե�󥺥��롼��)��̵ͭ
		parent_trusted_community_flag = parseInt(parent_form.elements["trusted_community_flag[" + contents_key + "]"].value);
	}

	// ����ͥ��å�: �����ϰ�
	child_form = document.forms["set_open_level_for_profile_form"];
	child_select_obj = child_form.elements["open_level_code"];
	for (var i = 0; i < child_select_obj.length; i++) {
		if (child_select_obj[i].value == parent_open_level_code) {
			child_select_obj.selectedIndex = i;
			break;
		}
	}

	// ����ͥ��å�: �ޥ��ե�󥺥��롼�� print_sub_menu()
	child_open_level_name = child_select_obj.options[child_select_obj.selectedIndex].text;
	if (child_open_level_name == '<?= ACSMsg::get_mst('open_level_master','D05') ?>') {
		print_sub_menu(child_select_obj);

		// ����ͥ��å�: �ޥ��ե�󥺥��롼�פΥ饸���ܥ���
		if (parent_trusted_community_flag) {
			child_form.elements["trusted_community_flag"][parent_trusted_community_flag].checked = true;

			// ����ͥ��å�: �ޥ��ե�󥺥��롼�פΥ����å��ܥå���
			if (child_form.elements["trusted_community_id_array[]"]) {
				if (child_form.elements["trusted_community_id_array[]"].value) {
					// 1�ĤΥ����å��ܥå���
					check_checkbox(child_form.elements["trusted_community_id_array[]"], parent_trusted_community_id_array);
				} else {
					// ʣ���Υ����å��ܥå���
					for (var i = 0; i < child_form.elements["trusted_community_id_array[]"].length; i++) {
						check_checkbox(child_form.elements["trusted_community_id_array[]"][i], parent_trusted_community_id_array);
					}
				}
			}
		}
	}
}


// �����å��ܥå����˥����å��������
function check_checkbox(checkbox_obj, trusted_community_id_array) {
	for (var i = 0; i < trusted_community_id_array.length; i++) {
		if (checkbox_obj.value == trusted_community_id_array[i]) {
			checkbox_obj.checked = true;
			break;
		}
	}
}


// �ƥ�����ɥ��򹹿�����
function update_parent_window() {
	if (!window.opener) {
		return;
	}

	parent_form = window.opener.document.forms["edit_profile_form"];
	child_form = document.forms["set_open_level_for_profile_form"];
	child_select_obj = child_form.elements["open_level_code"];

	// �ҥ�����ɥ�����
	child_open_level_code = child_form.elements["open_level_code"].value;
	child_trusted_community_flag = 0;
	child_trusted_community_id_csv = '';

	if (child_select_obj.options[child_select_obj.selectedIndex].text == '<?= ACSMsg::get_mst('open_level_master','D05') ?>') {
		// trusted_community_flag
		if (child_form.elements["trusted_community_flag"][1].checked) {
			child_trusted_community_flag = 1;
		}

		// trusted_community_id_csv
		child_trusted_community_id_csv = '';
		if (child_form.elements["trusted_community_id_array[]"]) {
			if (child_form.elements["trusted_community_id_array[]"].value) {
				// 1�ĤΥ����å��ܥå���
				if (child_form.elements["trusted_community_id_array[]"].checked) {
					child_trusted_community_id_csv = child_form.elements["trusted_community_id_array[]"].value;
				}
			} else {
				// ʣ���Υ����å��ܥå���
				for (var i = 0; i < child_form.elements["trusted_community_id_array[]"].length; i++) {
					if (child_form.elements["trusted_community_id_array[]"][i].checked) {
						if (child_trusted_community_id_csv != '') {
							child_trusted_community_id_csv += ',';
						}
						child_trusted_community_id_csv += child_form.elements["trusted_community_id_array[]"][i].value;
					}
				}
			}
		}
	}

	// �ƥ�����ɥ��˥��å�
	parent_form.elements["open_level_code_array[" + contents_key + "]"].value = child_open_level_code;
	if (parent_form.elements["trusted_community_flag[" + contents_key + "]"]) {
		parent_form.elements["trusted_community_flag[" + contents_key + "]"].value = child_trusted_community_flag;
		parent_form.elements["trusted_community_id_csv_array[" + contents_key + "]"].value = child_trusted_community_id_csv;
	}

	// �ƥ�����ɥ��񤭴���

	// �񤭴����оݤο�td
	parent_td_obj = window.opener.document.getElementById(contents_key + "_td");
	// ��td�λҥΡ��ɾõ�
	while (parent_td_obj.hasChildNodes()) {
		parent_td_obj.removeChild(parent_td_obj.firstChild);
	}

	// �ҤǸ������򤵤�Ƥ��������٥�̾
	child_open_level_name = child_select_obj.options[child_select_obj.selectedIndex].text;

	// text
	parent_td_obj.appendChild(window.opener.document.createTextNode(child_open_level_name));
	// �ѹ�������Τ��������դ���
	parent_td_obj.style.color = "#ff0000";
	parent_form.elements["update_button"].disabled = false;
	if (child_open_level_name == '<?= ACSMsg::get_mst('open_level_master','D05') ?>') {
		if (child_form.elements["trusted_community_flag"][1].checked) {
			trusted_community_str = '';

			if (child_form.elements["trusted_community_id_array[]"]) {
				if (child_form.elements["trusted_community_id_array[]"].value) {
					// 1�ĤΥ����å��ܥå���
					if (child_form.elements["trusted_community_id_array[]"].checked) {
						trusted_community_str = trusted_community_row_array[0]["community_name"];
					}
				} else {
					// ʣ���Υ����å��ܥå���
					for (var i = 0; i < child_form.elements["trusted_community_id_array[]"].length; i++) {
						if (child_form.elements["trusted_community_id_array[]"][i].checked) {
							if (trusted_community_str != '') {
								trusted_community_str += ', ';
							}
							trusted_community_str += trusted_community_row_array[i]["community_name"];
						}
					}
				}
			}

			if (trusted_community_str == '') {
				//trusted_community_str = '*�оݤʤ�';
				trusted_community_str = '<?= ACSMsg::get_msg("User", "SetOpenLevelForProfileView.tpl.php",'M001') ?>';
			}

			parent_td_obj.appendChild(window.opener.document.createTextNode(" (" + trusted_community_str + ")"));
		}
	}

	// ������ɥ����Ĥ���
	window.close();
}


// �ޥ��ե�󥺥��롼�פ�Ϣ������
trusted_community_row_array = new Array(
<?php
$str = '';
foreach ($this->_tpl_vars['friends_group_row_array'] as $friends_group_row) {
	if ($str != '') {
		$str .= ", ";
	}
	$str .= "";
	$str .= "{";
	$str .= "\"community_id\" : \"$friends_group_row[community_id]\", ";
	$str .= "\"community_name\" : \"$friends_group_row[community_name]\", ";
	$str .= "\"top_page_url\" : \"$friends_group_row[top_page_url]\"";
	$str .= "}";
}
echo $str;
?>
);


// ͧ�ͤ˸����Υ��֥�˥塼��ɽ������
function print_sub_menu(select_obj) {
	selected_open_level_name = select_obj.options[select_obj.selectedIndex].text;

	div_obj = document.getElementById("trusted_community_div");

	while (div_obj.hasChildNodes()) {
		div_obj.removeChild(div_obj.firstChild);
	}

	if (selected_open_level_name == "<?= ACSMsg::get_mst('open_level_master','D05') ?>") {
		// <table>
		table_obj = document.createElement("table");
		table_obj.className = "layout_table";

		// <tr>
		new_row = table_obj.insertRow(0);
		// <td>
		new_cell = new_row.insertCell(0);
		// <input>
		if (document.all) {
			input_obj = document.createElement('<input name="trusted_community_flag">');
		} else {
			input_obj = document.createElement("input");
			input_obj.name = "trusted_community_flag";
		}
		input_obj.type = "radio";
		input_obj.value = "0";
		input_obj.defaultChecked = true;
		input_obj.onclick = function () {
			if (this.form.elements["trusted_community_id_array[]"]) {
				if (this.form.elements["trusted_community_id_array[]"].value) {
					// 1�ĤΥ����å��ܥå���
					this.form.elements["trusted_community_id_array[]"].checked = false;
				} else {
					// ʣ���Υ����å��ܥå���
					for (i = 0; i < this.form.elements["trusted_community_id_array[]"].length; i++) {
						this.form.elements["trusted_community_id_array[]"][i].checked = false;
					}
				}
			}
		}
		new_cell.appendChild(input_obj);
		// </td>

		// <td>
		new_cell = new_row.insertCell(1);
		// text
		new_cell.appendChild(document.createTextNode("<?= ACSMsg::get_msg("User", "SetOpenLevelForProfileView.tpl.php",'M002') ?>"));
		new_cell.appendChild(document.createElement("br"));
		// </td>
		// </tr>


		// <tr>
		new_row = table_obj.insertRow(1);
		// <td>
		new_cell = new_row.insertCell(0);
		new_cell.setAttribute("vAlign", "top");
		if (document.all) {
			// <input>
			input_obj = document.createElement('<input name="trusted_community_flag">');
		} else {
			// <input>
			input_obj = document.createElement("input");
			input_obj.name = "trusted_community_flag";
		}
		input_obj.type = "radio";
		input_obj.value = "1";
		input_obj.defaultChecked = false;
		new_cell.appendChild(input_obj);
		// </td>
		
		// <td>
		new_cell = new_row.insertCell(1);
		// text
		new_cell.appendChild(document.createTextNode("<?= ACSMsg::get_msg("User", "SetOpenLevelForProfileView.tpl.php",'M003') ?>"));
		new_cell.appendChild(document.createElement("br"));
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
			input_obj.onclick = function () {
				// �ƥޥ��ե�󥺥��롼�פΥ����å��ܥå���������å����줿�Ȥ���
				// �֥ޥ��ե�󥺥��롼�ספΥ饸���ܥ����������֤ˤ���
				this.form.elements["trusted_community_flag"][1].checked = true;
			}
			new_cell.appendChild(input_obj);
			// text
			new_cell.appendChild(document.createTextNode(trusted_community_row_array[i]["community_name"]));
			new_cell.appendChild(document.createElement("br"));
		}
		// </td>
		// </tr>

		// </table>
		div_obj.appendChild(table_obj);
	}
}
// -->
</script>
