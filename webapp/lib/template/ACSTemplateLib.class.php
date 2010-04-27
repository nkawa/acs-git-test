<?php
/**
 * �ƥ�ץ졼�ȥ饤�֥��
 *
 * @author  kuwayama
 * @version $Revision: 1.10 $ $Date: 2006/12/28 07:36:12 $
 *
 */
class ACSTemplateLib
{
	/**
	 * �ڡ����󥰥�󥯽���
	 *
	 * @param $paging_info
	 */
	function print_paging_link ($paging_info) {
		if (!$paging_info) {
			return;
		}

		// ���ɽ��
		//echo "��$paging_info[all_count]��: $paging_info[start_count]-$paging_info[end_count]���ɽ��<br>\n";
		echo ACSMsg::get_tag_replace(ACSMsg::get_msg('lib','ACSTemplateLib.class.php','PAGE_INFO'),
				array(
					"{ALL_COUNT}" 	=> $paging_info[all_count],
					"{START_COUNT}"	=> $paging_info[start_count],
					"{END_COUNT}"	=> $paging_info[end_count]
				));

		// 1�ڡ����Τߤξ���ɽ����λ
		if (!$paging_info['paging_row_array']) {
			echo "<br>\n";
			return;
		}

		// ���ء�����
		if ($paging_info['prev_link']) {
			echo "<a href=\"$paging_info[prev_link]\">".ACSMsg::get_mdmsg(__FILE__,'M001')."</a>";
		} else {
			echo ACSMsg::get_mdmsg(__FILE__,'M001');
		}
		echo " ";
		if ($paging_info['next_link']) {
			echo "<a href=\"$paging_info[next_link]\">".ACSMsg::get_mdmsg(__FILE__,'M002')."</a>";
		} else {
			echo ACSMsg::get_mdmsg(__FILE__,'M002');
		}
		echo " ";

		// �ڡ�����ɽ��
		foreach ($paging_info['paging_row_array'] as $paging_row) {
			print '<span class="page_number">';
			// ��󥯤�������Τ�
			if ($paging_row['link_url']) {
				print '<a href="' . $paging_row['link_url'] . '">';
				print $paging_row['page_number'];
				print '</a>';
			} else {
				print '<b>' . $paging_row['page_number'] . '</b>';
			}
			print '</span>' . "\n";
		}
		print "<br><br>\n";
	}

	/**
	 * ���顼��å���������
	 *
	 * @param $error_message_array
	 */
	function print_error_message ($error_message_array) {
		if ($error_message_array) {
			print '<div class="err_msg">' . "\n";
			// ���顼��å�����ɽ��
			foreach ($error_message_array as $error_message) {
				print htmlspecialchars($error_message) . "<br>\n";
			}
			print "</div>\n";
		}
	}

	/**
	 * ʸ������Ф�URL��ư��󥯤�Ԥ�
	 *
	 * @param $str
	 * @return $str
	 */
	function auto_link($str) {
		$str = ereg_replace("(https?|ftp)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)",
							"<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",
							$str);
		return $str;
	}

	/*
	 * Ĺ��ʸ������ڤ�ͤ�� (�Х��Ȼ���)
	 *
	 * @param ʸ����
	 * @param ����Х��ȿ�
	 * @return ʸ����
	 */
	function trim_long_str($str, $max_str_byte = 50) {
		$long_str_suffix = '...';
		if (mb_strwidth($str) > $max_str_byte) {
			$str = mb_strcut($str, 0, $max_str_byte) . $long_str_suffix;
		}
		return $str;
	}

	/**
	 * �ե�����μ���ˤ�äƥե����륳��ƥ�Ĥ��ڤ��ؤ���JavaScript
	 *
	 * @param $form_name �ե�����̾
	 * @param $file_contents_type_master_array �ե����륳��ƥ�ļ��̥ޥ���
	 * @param $file_contents_type_master_row_array_array �ե����륫�ƥ��ꤴ�ȤΥե����륳��ƥ�ļ��̥ޥ��������������
	 * @return null
	 */
	function print_change_file_contents_js($form_name, $file_contents_type_master_array, $file_contents_type_master_row_array_array) {
		echo "<script language=\"JavaScript\">\n";
		echo "<!--\n";

		echo "window.onload = function () {\n";
		echo "  change_file_contents();\n";
		echo "}\n";

		echo "var file_contents_type_code_array = new Array(\"";
		echo implode('", "', array_keys($file_contents_type_master_array));
		echo "\");\n";
		echo "var file_contents_type_code_array_array = new Array();\n";

		foreach ($file_contents_type_master_row_array_array as $file_contents_type_master_row_array) {
			echo "file_contents_type_code_array_array[\"{$file_contents_type_master_row_array['file_category_code']}\"] = new Array(";
			$str = '';
			foreach ($file_contents_type_master_row_array['file_contents_type_master_row_array'] as $file_contents_type_master_row) {
				if ($str != '') {
					$str .= ', ';
				}
				$str .= '"' . $file_contents_type_master_row['file_contents_type_code'] . '"';
			}
			echo $str;
			echo ");\n";
		}

		echo "function change_file_contents() {\n";
		echo "  file_category_code = document.forms[\"$form_name\"].elements[\"file_category_code\"].value;\n";
		echo "  for (var i = 0; i < file_contents_type_code_array.length; i++) {\n";
		echo "    tr_obj = document.getElementById(\"file_contents_tr[\" + file_contents_type_code_array[i] + \"]\");\n";
		echo "    if (in_array(file_contents_type_code_array[i], file_contents_type_code_array_array[file_category_code])) {\n";
		echo "      // ɽ��\n";
		echo "      tr_obj.style.display = \"\";\n";
		echo "    } else {\n";
		echo "      // ��ɽ��\n";
		echo "      tr_obj.style.display = \"none\";\n";
		echo "    }\n";
		echo "  }\n";
		echo "}\n";

		echo "function in_array(user_value, user_array) {\n";
		echo "  for (var i = 0; i < user_array.length; i++) {\n";
		echo "    if (user_value == user_array[i]) {\n";
		echo "      return true;\n";
		echo "    }\n";
		echo "  }\n";
		echo "  return false;\n";
		echo "}\n";

		echo "//-->\n";
		echo "</script>\n";
	}

	/*
	 * ǯ����ꥹ��options����
	 *
	 * @param int $selected_year �����(̤������ϸ���ǯ)
	 * @param int $start_year   ����ǯ(̤������ϸ���ǯ)
	 * @param int $length       ����ǯ��Ĺ��(̤�������3ǯʬ)
	 * @return string ǯ����optionʸ����
	 */
	function get_year_select_options ($selected_year = '', $start_year = '', $length = 3) {

		$dt = getdate();
		if ($selected_year === '') {
			$selected_year = $dt['year'];
		}
		if ($start_year === '') {
			$start_year = $dt['year'];
		}

		$vals = array();
		for ($year = $start_year; $year < ($start_year + $length); $year++) {
			$vals[$year] = $year;
		}
		return ACSTemplateLib::get_simple_select_options($vals,$selected_year);
	}

	/*
	 * ������ꥹ��options����
	 *
	 * @param int $selected_month �����(̤������ϸ��߷�)
	 * @return string ������optionʸ����
	 */
	function get_month_select_options ($selected_month = '') {

		if ($selected_month === '') {
			$dt = getdate();
			$selected_month = $dt['mon'];
		}

		$vals = array();
		for ($month = 1; $month <= 12; $month++) {
			$vals[$month] = sprintf("%02d",$month);
		}
		return ACSTemplateLib::get_simple_select_options($vals,$selected_month);
	}

	/*
	 * ������ꥹ��options����
	 *
	 * @param int $selected_day �����(̤������ϸ�����)
	 * @return string ������optionʸ����
	 */
	function get_day_select_options ($selected_day = '') {

		if ($selected_day === '') {
			$dt = getdate();
			$selected_day = $dt['mday'];
		}

		$vals = array();
		for ($day = 1; $day <= 31; $day++) {
			$vals[$day] = sprintf("%02d",$day);
		}
		return ACSTemplateLib::get_simple_select_options($vals,$selected_day);
	}

	/*
	 * ������ꥹ��options����
	 *
	 * @param int $selected_hour �����(̤������ϸ��߻�)
	 * @return string ������optionʸ����
	 */
	function get_hour_select_options ($selected_hour = '') {

		if ($selected_hour === '') {
			$dt = getdate();
			$selected_hour = $dt['hours'];
		}

		$vals = array();
		for ($hour = 0; $hour <= 23; $hour++) {
			$vals[$hour] = sprintf("%02d",$hour);
		}
		return ACSTemplateLib::get_simple_select_options($vals,$selected_hour);
	}

	/*
	 * ʬ����ꥹ��options����
	 *
	 * @param int $selected_min �����(̤������ϸ���ʬ)
	 * @param int $step_min ����Ǿ�ñ��(̤�������5ʬ)
	 * @return string ʬ����optionʸ����
	 */
	function get_min_select_options ($selected_min = '', $step_min = 5) {

		if ($selected_min === '') {
			$dt = getdate();
			$selected_min = $dt['minutes'];
		}

		$vals = array();
		for ($min = 0; $min <= 59; $min += $step_min) {
			$vals[$min] = sprintf("%02d",$min);
		}
		return ACSTemplateLib::get_simple_select_options(
				$vals, floor($selected_min/$step_min)*$step_min);
	}

	/*
	 * ����ꥹ��options����
	 *
	 * @param array $vals_array �����ͤΰ�������(key => value ����)
	 * @param string $selected_val �����(̤���������Ƭ)
	 * @param string $is_blank_ok �֥����������(̤��������Բ�)
	 * @return string ������optionʸ����
	 */
	function get_simple_select_options (
			$vals_array, $selected_val = '', $is_blank_ok = FALSE) {
		$html = '';
		if ($is_blank_ok == TRUE) {
			$html .= '<option value="">'."\n";
		}
		foreach ($vals_array as $value => $display) {
			$html .= '<option value="' . $value . '" ' .
					($value == $selected_val ? ' SELECTED' : ''). '>' . $display . "\n";
		}
		return $html;
	}
}
?>
