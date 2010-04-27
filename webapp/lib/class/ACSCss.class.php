<?php
/**
 * ACS Css
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $ $Date: 2007/03/27 02:12:31 $
 */
class ACSCss 
{
	/* cssファイル */
	var $css_file_path;

	/* cssファイル内容 */
	var $css_file_contents;

	/**
	 * コンストラクタ
	 *
	 * @param string $css_file_path cssファイルパス
	 */
	function ACSCss ($css_file_path) {
		$this->set_css_contents ($css_file_path);
	}

	/**
	 * cssファイルの取り込み (自動漢字変換対応)
	 *
	 * @param string $css_file_path cssファイルパス
	 * @return mixed 正常時...ファイル内容/エラー時...FALSE
	 */
	function set_css_contents ($css_file_path) {
		$this->css_file_path = $css_file_path;
		$contents =& implode(NULL, file($css_file_path));
		$this->css_file_contents = mb_convert_encoding(
				$contents, mb_internal_encoding(), mb_detect_encoding($contents) ) ;
		return $this->css_file_contents;
	}

	/**
	 * acsスタイル選択情報の取得
	 *
	 * @param string $lang 言語種別
	 * @param string $encoding 文字エンコーディング
	 */
	function get_style_selection_info_array ($lang,$encoding='') {

		if ($encoding != '') {
			mb_regex_encoding($encoding);
		}

		$pattern = 'acs_style_selection[ \t]*\{([^\}]+)\}';

		$result = mb_ereg($pattern, $this->css_file_contents, $regs);

		if ($result != FALSE) {

			$styles = trim($regs[1]);
			$pattern_fmt = '.*%s[ \t\n\r\f\v]*:*([^;]+);';

			$result = array();

			// 各パラメータの取得
			mb_ereg(sprintf($pattern_fmt, 'show_list'), $styles, $regs);
			$result['show_list'] = trim($regs[1]);

			mb_ereg(sprintf($pattern_fmt, 'display_order'), $styles, $regs);
			$result['display_order'] = trim($regs[1]);

			// 言語依存パラメータの取得
			mb_ereg(sprintf($pattern_fmt, 'name\.'.$lang), $styles, $regs);
			$result['name'] = trim($regs[1]);

			mb_ereg(sprintf($pattern_fmt, 'description\.'.$lang), $styles, $regs);
			$result['description'] = trim($regs[1]);

			mb_ereg(sprintf($pattern_fmt, 'thumbnail\.'.$lang), $styles, $regs);
			$result['thumbnail'] = trim($regs[1]);

			$result['filename'] = basename($this->css_file_path);
		}

		return $result;
	}

	/* Static functions */

	/**
	 * acsスタイル選択情報の取得
	 *
	 * @param string $lang 取得する情報のエンコーディング
	 * @param string $styles_dir_path cssファイルが存在するディレクトリパス
	 * @param string $match_pattern 対象ファイルのマッチパターン
	 * @return array スタイル情報の配列
	 */
	static function get_style_selection_list_array($lang, $styles_dir_path, $match_pattern='/.*\.css/') {

		$d = dir($styles_dir_path);

		$styles_array = array();

		while (false !== ($file_entry = $d->read())) {
			//if (fnmatch($match_pattern, $file_entry)) {
			if (preg_match($match_pattern, $file_entry)) {
				$css = new ACSCss($styles_dir_path.'/'.$file_entry);
				$styles_array[$file_entry] =& $css->get_style_selection_info_array($lang);
			}
		}
		$d->close();

		// display_order
		$order_index = array();
		foreach ($styles_array as $key => $value) {
			$order_index[$key] = $value['display_order'];
		}
		asort($order_index);
		$sort_styles_array = array();
		foreach ($order_index as $key => $value) {
			$sort_styles_array[] = $styles_array[$key];
		}

		return $sort_styles_array;
	}	
	
	
	/**
	 * エンコーディング種類の取得
	 *
	 * @param string $data 文字列
	 */
	function getEncodingType($data){
		$encodingArray = array("ISO-2022-JP","UTF-8","Shift-JIS","EUC-JP","ASCII");
		$n = 0;
	
		while($focusEncoding = $encodingArray[$n]){
			if(mb_check_encoding($data,$focusEncoding)) return $focusEncoding;
			$n++;
		}
		return "nil";
	}
}

?>
