<?php
/**
 * テンプレートライブラリ
 *
 * @author  kuwayama
 * @version $Revision: 1.10 $ $Date: 2006/12/28 07:36:12 $
 *
 */
class ACSTemplateLib
{
	/**
	 * ページングリンク出力
	 *
	 * @param $paging_info
	 */
	function print_paging_link ($paging_info) {
		if (!$paging_info) {
			return;
		}

		// 件数表示
		//echo "全$paging_info[all_count]件: $paging_info[start_count]-$paging_info[end_count]件を表示<br>\n";
		echo ACSMsg::get_tag_replace(ACSMsg::get_msg('lib','ACSTemplateLib.class.php','PAGE_INFO'),
				array(
					"{ALL_COUNT}" 	=> $paging_info[all_count],
					"{START_COUNT}"	=> $paging_info[start_count],
					"{END_COUNT}"	=> $paging_info[end_count]
				));

		// 1ページのみの場合は表示終了
		if (!$paging_info['paging_row_array']) {
			echo "<br>\n";
			return;
		}

		// 前へ・次へ
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

		// ページ数表示
		foreach ($paging_info['paging_row_array'] as $paging_row) {
			print '<span class="page_number">';
			// リンクがある場合のみ
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
	 * エラーメッセージ出力
	 *
	 * @param $error_message_array
	 */
	function print_error_message ($error_message_array) {
		if ($error_message_array) {
			print '<div class="err_msg">' . "\n";
			// エラーメッセージ表示
			foreach ($error_message_array as $error_message) {
				print htmlspecialchars($error_message) . "<br>\n";
			}
			print "</div>\n";
		}
	}

	/**
	 * 文字列に対しURL自動リンクを行う
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
	 * 長い文字列を切り詰める (バイト指定)
	 *
	 * @param 文字列
	 * @param 最大バイト数
	 * @return 文字列
	 */
	function trim_long_str($str, $max_str_byte = 50) {
		$long_str_suffix = '...';
		if (mb_strwidth($str) > $max_str_byte) {
			$str = mb_strcut($str, 0, $max_str_byte) . $long_str_suffix;
		}
		return $str;
	}

	/**
	 * ファイルの種類によってファイルコンテンツを切り替えるJavaScript
	 *
	 * @param $form_name フォーム名
	 * @param $file_contents_type_master_array ファイルコンテンツ種別マスタ
	 * @param $file_contents_type_master_row_array_array ファイルカテゴリごとのファイルコンテンツ種別マスタの配列の配列
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
		echo "      // 表示\n";
		echo "      tr_obj.style.display = \"\";\n";
		echo "    } else {\n";
		echo "      // 非表示\n";
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
	 * 年選択リストoptions生成
	 *
	 * @param int $selected_year 初期値(未指定時は現在年)
	 * @param int $start_year   開始年(未指定時は現在年)
	 * @param int $length       選択年の長さ(未指定時は3年分)
	 * @return string 年選択option文字列
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
	 * 月選択リストoptions生成
	 *
	 * @param int $selected_month 初期値(未指定時は現在月)
	 * @return string 月選択option文字列
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
	 * 日選択リストoptions生成
	 *
	 * @param int $selected_day 初期値(未指定時は現在日)
	 * @return string 日選択option文字列
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
	 * 時選択リストoptions生成
	 *
	 * @param int $selected_hour 初期値(未指定時は現在時)
	 * @return string 時選択option文字列
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
	 * 分選択リストoptions生成
	 *
	 * @param int $selected_min 初期値(未指定時は現在分)
	 * @param int $step_min 選択最小単位(未指定時は5分)
	 * @return string 分選択option文字列
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
	 * 選択リストoptions生成
	 *
	 * @param array $vals_array 選択値の一覧配列(key => value 形式)
	 * @param string $selected_val 初期値(未指定時は先頭)
	 * @param string $is_blank_ok ブランク選択設定(未指定時は不可)
	 * @return string 日選択option文字列
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
