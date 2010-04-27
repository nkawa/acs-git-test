<?php

define('_ACSZIP_ZIP_CMD', 'cd %s;/usr/bin/zip -r %s %s;cd -');

/**
 * ACS Zip
 *
 * @author  z-satosi
 * @version $Revision: 1.2 $ $Date: 2007/03/28 02:26:48 $
 */
class ACSZip
{
	/* zip�ե����� */
	var $zip_file;

	/* zip�ե�����������Υ���ǥ��쥯�ȥ� */
	var $zip_work_dir;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param string $zip_work_dir zip�����ѥ���ǥ��쥯�ȥ�
	 * @param string $zip_file zip�ե�����̾
	 */
	function ACSZip ($zip_work_dir, $zip_file = "") {
		$this->zip_work_dir = mb_ereg_replace('/$','',$zip_work_dir);
		$this->zip_file = $zip_file == "" ? 
				$this->zip_work_dir.'.zip' : dirname($this->zip_work_dir).'/'.$zip_file;
		$this->initialize();
	}

	/**
	 * ����ǥ��쥯�ȥ�ν����
	 */
	function initialize () {
		$this->clear_zip_file ();
		$this->clear_work_dir_and_files ();
		ACSLib::make_dir($this->zip_work_dir);
	}

	/**
	 * ����ǥ��쥯�ȥꡦ�ե�����ξõ�
	 */
	function clear_work_dir_and_files () {
		ACSLib::remove_dir($this->zip_work_dir);
	}

	/**
	 * zip�ե�����ξõ�
	 */
	function clear_zip_file () {
		@unlink($this->zip_file);
	}

	/**
	 * �ǥ��쥯�ȥ�κ���(����Υǥ��쥯�ȥ�⼫ư����)
	 *
	 * @param string $dir �ǥ��쥯�ȥ�
	 * @param string $name_encoding ���ܸ�ե����롦�ե����̾�Υ��󥳡��ǥ���
	 * @return mixed �����...�ե���������/���顼��...FALSE
	 */
	function make_dir ($dir, $name_encoding = '') {
		// �ե������¸�ߤ��ʤ���硢�ǥ��쥯�ȥ�κ���
		$dir_array = explode("/",  $dir);
		$dest_dir = $this->zip_work_dir;
		foreach ($dir_array as $dir) {
			if ($dir != '') {
				$dir = $name_encoding != '' ?
						mb_convert_encoding($dir, $name_encoding) : $dir;
				$dest_dir .= '/' . $dir;
				ACSLib::make_dir($dest_dir);
			}
		}
	}

	/**
	 * �ե����������(�ǥ��쥯�ȥ�μ�ư����)
	 *
	 * @param string $from_file ���ԡ����ե�����Υѥ�
	 * @param string $dest_file ���ԡ���ե�����Υѥ�(zip_work_dir ����Υѥ�)
	 * @param string $name_encoding ���ܸ�ե����롦�ե����̾�Υ��󥳡��ǥ���
	 * @return mixed �����...�ե���������/���顼��...FALSE
	 */
	function entry ($from_file, $dest_file, $name_encoding = '') {

		$dest_file = mb_ereg_replace( '^/', '', $dest_file);

		// ���ԡ���Υե������¸�ߤ��ʤ���硢�ǥ��쥯�ȥ�κ���
		$this->make_dir(dirname($dest_file), $name_encoding);

		if ($name_encoding != '') {
			$dest_file = mb_convert_encoding($dest_file, $name_encoding);
		}
		return @copy($from_file, $this->zip_work_dir . '/' . $dest_file);
	}

	/**
	 * ����ǥ��쥯�ȥ�ΰ��̤�¹Ԥ���zip�ե���������
	 */
	function commpress () {
		$commpress = $this->zip_work_dir;
		$commpress_cmd = sprintf(_ACSZIP_ZIP_CMD, 
				dirname($this->zip_work_dir),
				basename($this->zip_file),
				basename($this->zip_work_dir) . '/');

		return exec($commpress_cmd);
	}

	/**
	 * zip�ե�����μ���
	 * 
	 * @param string $attachement_file_name ��������ɻ��Υե�����̾����ꤹ����
	 */
	function download ($attachement_file_name = '') {

		$file_name = $attachement_file_name=='' ? 
				basename($this->zip_file) : $attachement_file_name;

		// HTTP�إå�����
		mb_http_output('pass');
		header("Cache-Control: public, max-age=0");
		header("Pragma:");
		header('Content-disposition: attachment; filename="'.$file_name.'"');
		header("Content-type: application/zip");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 

		// �ե�������ɤ߽Ф�
		readfile($this->zip_file);
	}
}

?>
