<?php
/**
 * ACS Css
 *
 * @author  z-satosi
 * @version $Revision: 1.1 $ $Date: 2007/03/27 02:12:31 $
 */
class ACSCss 
{
	/* css�ե����� */
	var $css_file_path;

	/* css�ե��������� */
	var $css_file_contents;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param string $css_file_path css�ե�����ѥ�
	 */
	function ACSCss ($css_file_path) {
		$this->set_css_contents ($css_file_path);
	}

	/**
	 * css�ե�����μ����� (��ư�����Ѵ��б�)
	 *
	 * @param string $css_file_path css�ե�����ѥ�
	 * @return mixed �����...�ե���������/���顼��...FALSE
	 */
	function set_css_contents ($css_file_path) {
		$this->css_file_path = $css_file_path;
		$contents =& implode(NULL, file($css_file_path));
		$this->css_file_contents = mb_convert_encoding(
				$contents, mb_internal_encoding(), mb_detect_encoding($contents) ) ;
		return $this->css_file_contents;
	}

	/**
	 * acs���������������μ���
	 *
	 * @param string $lang �������
	 * @param string $encoding ʸ�����󥳡��ǥ���
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

			// �ƥѥ�᡼���μ���
			mb_ereg(sprintf($pattern_fmt, 'show_list'), $styles, $regs);
			$result['show_list'] = trim($regs[1]);

			mb_ereg(sprintf($pattern_fmt, 'display_order'), $styles, $regs);
			$result['display_order'] = trim($regs[1]);

			// �����¸�ѥ�᡼���μ���
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
	 * acs���������������μ���
	 *
	 * @param string $lang �����������Υ��󥳡��ǥ���
	 * @param string $styles_dir_path css�ե����뤬¸�ߤ���ǥ��쥯�ȥ�ѥ�
	 * @param string $match_pattern �оݥե�����Υޥå��ѥ�����
	 * @return array ����������������
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
	 * ���󥳡��ǥ��󥰼���μ���
	 *
	 * @param string $data ʸ����
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
