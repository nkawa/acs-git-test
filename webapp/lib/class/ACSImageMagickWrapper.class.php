<?php
// $Id: ACSImageMagickWrapper.class.php,v 1.5 2006/03/30 04:38:01 w-ota Exp $

// define
define('ACS_IMAGE_MAGICK_CONVERT', '/usr/bin/convert');
define('ACS_THUMBNAIL_WIDTH', 76);  // ����ͥ���κ��粣��
define('ACS_THUMBNAIL_HEIGHT', 76); // ����ͥ���κ������

class ACSImageMagickWrapper
{
	// �����ե�����̾
	var $file_name;
	// �����β���
	var $width;
	// �����ν���
	var $height;

	// ���󥹥ȥ饯��
	function ACSImageMagickWrapper($file_name) {
		$image_info_array = @getimagesize($file_name);
		if ($image_info_array) {
			$this->file_name = $file_name;
			$this->width = $image_info_array[0];
			$this->height = $image_info_array[1];
		}
	}

	/*
	 * �������������
	 *
	 * @param $new_file_name ��������ե�����̾
	 * @param $image_type    �����ե����륿����
	 * @param $max_width     ���粣��
	 * @param $max_height    �������
	 */
	function convert ($new_file_name, $image_type, $max_width = ACS_THUMBNAIL_WIDTH, $max_height = ACS_THUMBNAIL_HEIGHT) {
		$tmp_file_name = $this->file_name . '.tmp';
		if ($image_type) {
			$tmp_file_name .= '.' . $image_type;
		}

		$cmd  = ACS_IMAGE_MAGICK_CONVERT;
		// ���������Ĳ����κ����ͤ����礭����Хꥵ��������
		if ($this->width > $max_width || $this->height > $max_height) {
			$cmd .= " -resize {$max_width}x{$max_height}";
		}
		$cmd .= " -quality 100";
		$cmd .= " +profile '*'";
		$cmd .= " " . $this->file_name;
		$cmd .= " " . $tmp_file_name;

		// convert���ޥ�ɼ¹�
		exec($cmd);
		// �ե�����̾�ѹ�
		rename($tmp_file_name, $new_file_name);

		return basename($new_file_name);
	}

	/**
	 * JPEG �Υ���ͥ���������������
	 *
	 * @param $new_file_name ��������ե�����̾
	 * @param $max_width     ���粣��
	 * @param $max_height    �������
	 */
	function make_jpg_thumbnail ($new_file_name, $max_width = ACS_THUMBNAIL_WIDTH, $max_height = ACS_THUMBNAIL_HEIGHT) {
		ACSImageMagickWrapper::convert($new_file_name, 'jpg', $max_width, $max_height);
	}

	/**
	 * ���ꥵ�����ʲ��Υ������˽̾�����
	 *
	 * @param $max_width  ���粣��
	 * @param $max_height �������
	 */
	function reduce_image ($max_width = ACS_THUMBNAIL_WIDTH, $max_height = ACS_THUMBNAIL_HEIGHT) {
		if ($this->width > $max_width || $this->height > $max_height) {
			ACSImageMagickWrapper::convert($this->file_name, '', $max_width, $max_height);
		}
	}
}

?>
