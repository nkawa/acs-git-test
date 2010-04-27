<?php
// $Id: ACSImageMagickWrapper.class.php,v 1.5 2006/03/30 04:38:01 w-ota Exp $

// define
define('ACS_IMAGE_MAGICK_CONVERT', '/usr/bin/convert');
define('ACS_THUMBNAIL_WIDTH', 76);  // サムネイルの最大横幅
define('ACS_THUMBNAIL_HEIGHT', 76); // サムネイルの最大縦幅

class ACSImageMagickWrapper
{
	// 画像ファイル名
	var $file_name;
	// 画像の横幅
	var $width;
	// 画像の縦幅
	var $height;

	// コンストラクタ
	function ACSImageMagickWrapper($file_name) {
		$image_info_array = @getimagesize($file_name);
		if ($image_info_array) {
			$this->file_name = $file_name;
			$this->width = $image_info_array[0];
			$this->height = $image_info_array[1];
		}
	}

	/*
	 * 画像を作成する
	 *
	 * @param $new_file_name 作成するファイル名
	 * @param $image_type    画像ファイルタイプ
	 * @param $max_width     最大横幅
	 * @param $max_height    最大縦幅
	 */
	function convert ($new_file_name, $image_type, $max_width = ACS_THUMBNAIL_WIDTH, $max_height = ACS_THUMBNAIL_HEIGHT) {
		$tmp_file_name = $this->file_name . '.tmp';
		if ($image_type) {
			$tmp_file_name .= '.' . $image_type;
		}

		$cmd  = ACS_IMAGE_MAGICK_CONVERT;
		// 元画像が縦横幅の最大値よりも大きければリサイズする
		if ($this->width > $max_width || $this->height > $max_height) {
			$cmd .= " -resize {$max_width}x{$max_height}";
		}
		$cmd .= " -quality 100";
		$cmd .= " +profile '*'";
		$cmd .= " " . $this->file_name;
		$cmd .= " " . $tmp_file_name;

		// convertコマンド実行
		exec($cmd);
		// ファイル名変更
		rename($tmp_file_name, $new_file_name);

		return basename($new_file_name);
	}

	/**
	 * JPEG のサムネイル画像を作成する
	 *
	 * @param $new_file_name 作成するファイル名
	 * @param $max_width     最大横幅
	 * @param $max_height    最大縦幅
	 */
	function make_jpg_thumbnail ($new_file_name, $max_width = ACS_THUMBNAIL_WIDTH, $max_height = ACS_THUMBNAIL_HEIGHT) {
		ACSImageMagickWrapper::convert($new_file_name, 'jpg', $max_width, $max_height);
	}

	/**
	 * 指定サイズ以下のサイズに縮小する
	 *
	 * @param $max_width  最大横幅
	 * @param $max_height 最大縦幅
	 */
	function reduce_image ($max_width = ACS_THUMBNAIL_WIDTH, $max_height = ACS_THUMBNAIL_HEIGHT) {
		if ($this->width > $max_width || $this->height > $max_height) {
			ACSImageMagickWrapper::convert($this->file_name, '', $max_width, $max_height);
		}
	}
}

?>
