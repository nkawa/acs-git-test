<?php
/**
 * ACS File
 *
 * @author  kuwayama
 * @version $Revision: 1.35 $ $Date: 2006/12/18 07:41:48 $
 */
require_once(ACS_CLASS_DIR . 'ACSFileInfoModel.class.php');
require_once(ACS_CLASS_DIR . 'ACSFileAccessHistoryModel.class.php');
class ACSFile
{
	/* �ե�����ID */
	var $file_id;

	/* �����ʡ����ߥ�˥ƥ�ID */
	var $owner_community_id;

	/* ɽ���ѥե�����̾ */
	var $display_file_name;

	/* �����Хե�����̾ */
	var $server_file_name;

	/* ����ͥ��륵���Хե�����̾ */
	var $thumbnail_server_file_name;

	/* RSS�����Хե�����̾ */
	var $rss_server_file_name;

	/* MIME TYPE */
	var $mime_type;

	/* �ե����륵���� */
	var $file_size;

	/* ��Ͽ�桼�����ߥ�˥ƥ�ID */
	var $entry_user_community_id;

	/* ��Ͽ�桼�����ߥ�˥ƥ�̾ */
	var $entry_user_community_name;

	/* ��Ͽ�� */
	var $entry_date;

	/* �����桼�����ߥ�˥ƥ�ID */
	var $update_user_community_id;

	/* �����桼�����ߥ�˥ƥ�̾ */
	var $update_user_community_name;

	/* ������ */
	var $update_date;

	/* ���åץ��ɥƥ�ݥ��ե�����̾ */
	var $upload_temp_file_name;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param $file_info_row
	 */
	function ACSFile ($file_info_row) {
		$this->set_file_info($file_info_row);
	}

	/**
	 * �ե�������󥻥å�
	 *
	 * @param $file_info_row
	 */
	function set_file_info ($file_info_row) {
		$this->set_file_id($file_info_row['file_id']);
		$this->set_owner_community_id($file_info_row['owner_community_id']);
		$this->set_display_file_name($file_info_row['display_file_name']);
		$this->set_server_file_name($file_info_row['server_file_name']);
		$this->set_thumbnail_server_file_name($file_info_row['thumbnail_server_file_name']);
		$this->set_rss_server_file_name($file_info_row['rss_server_file_name']);
		$this->set_mime_type($file_info_row['mime_type']);
		$this->set_file_size($file_info_row['file_size']);
		$this->set_entry_user_community_id($file_info_row['entry_user_community_id']);
		$this->set_entry_user_community_name($file_info_row['entry_user_community_name']);
		$this->set_entry_date($file_info_row['entry_date']);
		$this->set_update_user_community_id($file_info_row['update_user_community_id']);
		$this->set_update_user_community_name($file_info_row['update_user_community_name']);
		$this->set_update_date($file_info_row['update_date']);

		// ���åץ��ɻ���ɬ��
		$this->set_upload_temp_file_name($file_info_row['upload_temp_file_name']);
	}

	/**
	 * ���󥹥��󥹼����ʥե�����ID�����
	 *
	 * @param $file_id
	 */
	static function get_file_info_instance ($file_id) {
		$file_info_row = ACSFileInfoModel::select_file_info_row($file_id);
		$file_obj = new ACSFile($file_info_row);

		return $file_obj;
	}


	/**
	 * ���󥹥��󥹼����ʥ��åץ����ѡ�
	 *
	 * @param $upload_file_info_row  $_FILES['new_file']
	 * @param $owner_community_id	�оݤȤʤ륳�ߥ�˥ƥ�ID
	 * @param $acs_user_community_id ���åץ��ɤ����桼�����ߥ�˥ƥ�ID
	 * @param $file_id			   ��¸�ե�����ι������Υե�����ID
	 */
	static function get_upload_file_info_instance ($upload_file_info_row, $owner_community_id, $acs_user_community_id, $file_id = "") {
		$file_info_row = array();
		/* file_id ���� */
		if($file_id == ""){
			$file_id = ACSFileInfoModel::get_next_file_id_seq();
		}

		/* display_file_name */
		if (!ini_get('mbstring.encoding_translation')) {
			$display_file_name = mb_convert_encoding($upload_file_info_row['name'], mb_internal_encoding(), mb_http_output());
		} else {
			$display_file_name = $upload_file_info_row['name'];
		}
		/* server_file_name ���� */
		$server_file_name = ACSFile::get_upload_file_save_file_name($owner_community_id, $file_id);

		/* thumbnail_server_file_name ���� */
		$thumbnail_server_file_name = ACSFile::get_thumbnail_save_file_name($owner_community_id, $file_id);
		/* ������ */
		$filemtime  = filemtime($upload_file_info_row['tmp_name']);
		
		// insert �Ѥ˥ե����ޥåȤ���
		$entry_date = ACSLib::convert_timestamp_to_pg_date($filemtime);

		/* ������ */
		$update_date = $entry_date;

		$file_info_row['file_id'] = $file_id;
		$file_info_row['owner_community_id'] = $owner_community_id;
		$file_info_row['display_file_name'] = $display_file_name;
		$file_info_row['server_file_name'] = $server_file_name;
		$file_info_row['thumbnail_server_file_name'] = $thumbnail_server_file_name;
		$file_info_row['mime_type'] = $upload_file_info_row['type'];
		$file_info_row['file_size'] = $upload_file_info_row['size'];
		$file_info_row['entry_user_community_id'] = $acs_user_community_id;
		$file_info_row['entry_date'] = $entry_date;
		$file_info_row['update_user_community_id'] = $acs_user_community_id;
		$file_info_row['update_date'] = $update_date;

		$file_info_row['upload_temp_file_name'] = $upload_file_info_row['tmp_name'];

		$file_obj = new ACSFile($file_info_row);
		return $file_obj;
	}

	/**
	 * ���󥹥��󥹼��������򹹿����åץ����ѡ�
	 *
	 * @param $upload_file_info_row  $_FILES['new_file']
	 * @param $owner_community_id	�оݤȤʤ륳�ߥ�˥ƥ�ID
	 * @param $acs_user_community_id ���åץ��ɤ����桼�����ߥ�˥ƥ�ID
	 * @param $file_id			   ��¸�ե�����ι������Υե�����ID
	 */
	static function get_upload_file_info_instance_for_update($upload_file_info_row, $owner_community_id, $acs_user_community_id, $file_id) {
		$file_info_row = array();

		/* ��file_id���� (server_file_name��) */
		$new_file_id = ACSFileInfoModel::get_next_file_id_seq();

		/* display_file_name */
		if (!ini_get('mbstring.encoding_translation')) {
			$display_file_name = mb_convert_encoding($upload_file_info_row['name'], mb_internal_encoding(), mb_http_output());
		} else {
			$display_file_name = $upload_file_info_row['name'];
		}
		/* server_file_name ���� */
		$server_file_name = ACSFile::get_upload_file_save_file_name($owner_community_id, $new_file_id);

		/* thumbnail_server_file_name ���� */
		$thumbnail_server_file_name = ACSFile::get_thumbnail_save_file_name($owner_community_id, $new_file_id);
		/* ������ */
		$filemtime  = filemtime($upload_file_info_row['tmp_name']);
		
		// insert �Ѥ˥ե����ޥåȤ���
		$entry_date = ACSLib::convert_timestamp_to_pg_date($filemtime);

		/* ������ */
		$update_date = $entry_date;

		$file_info_row['file_id'] = $file_id;
		$file_info_row['owner_community_id'] = $owner_community_id;
		$file_info_row['display_file_name'] = $display_file_name;
		$file_info_row['server_file_name'] = $server_file_name;
		$file_info_row['thumbnail_server_file_name'] = $thumbnail_server_file_name;
		$file_info_row['mime_type'] = $upload_file_info_row['type'];
		$file_info_row['file_size'] = $upload_file_info_row['size'];
		$file_info_row['entry_user_community_id'] = $acs_user_community_id;
		$file_info_row['entry_date'] = $entry_date;
		$file_info_row['update_user_community_id'] = $acs_user_community_id;
		$file_info_row['update_date'] = $update_date;

		$file_info_row['upload_temp_file_name'] = $upload_file_info_row['tmp_name'];

		$file_obj = new ACSFile($file_info_row);
		return $file_obj;
	}

	/**
	 * �ե�����ID���å�
	 *
	 * @param $file_id
	 */
	function set_file_id ($file_id) {
		$this->file_id = $file_id;
	}

	/**
	 * �ե�����ID���å�
	 *
	 * @param none
	 */
	function get_file_id () {
		return $this->file_id;
	}

	/**
	 * �����ʡ����ߥ�˥ƥ�ID���å�
	 *
	 * @param $owner_community_id
	 */
	function set_owner_community_id ($owner_community_id) {
		$this->owner_community_id = $owner_community_id;
	}

	/**
	 * �����ʡ����ߥ�˥ƥ�ID���å�
	 *
	 * @param none
	 */
	function get_owner_community_id () {
		return $this->owner_community_id;
	}

	/**
	 * ɽ���ѥե�����̾���å�
	 *
	 * @param $file_name
	 */
	function set_display_file_name ($file_name) {
		$this->display_file_name = $file_name;
	}

	/**
	 * ɽ���ѥե�����̾���å�
	 *
	 * @param none
	 */
	function get_display_file_name () {
		return $this->display_file_name;
	}

	/**
	 * �����Хե�����̾���å�
	 *
	 * @param $server_file_name
	 */
	function set_server_file_name ($server_file_name) {
		$this->server_file_name = $server_file_name;
	}

	/**
	 * �����Хե�����̾���å�
	 *
	 * @param none
	 */
	function get_server_file_name () {
		return $this->server_file_name;
	}

	/**
	 * ����ͥ��륵���Хե�����̾���å�
	 *
	 * @param $thumbnail_server_file_name
	 */
	function set_thumbnail_server_file_name ($thumbnail_server_file_name) {
		$this->thumbnail_server_file_name = $thumbnail_server_file_name;
	}

	/**
	 * ����ͥ��륵���Хե�����̾���å�
	 *
	 * @param none
	 */
	function get_thumbnail_server_file_name () {
		return $this->thumbnail_server_file_name;
	}

	/**
	 * RSS�����Хե�����̾���å�
	 *
	 * @param $rss_server_file_name
	 */
	function set_rss_server_file_name ($rss_server_file_name) {
		$this->rss_server_file_name = $rss_server_file_name;
	}

	/**
	 * RSS�����Хե�����̾���å�
	 *
	 * @param none
	 */
	function get_rss_server_file_name () {
		return $this->rss_server_file_name;
	}

	/**
	 * MIME TYPE ���å�
	 *
	 * @param $mime_type
	 */
	function set_mime_type ($mime_type) {
		$this->mime_type = $mime_type;
	}

	/**
	 * MIME TYPE ���å�
	 *
	 * @param none
	 */
	function get_mime_type () {
		return $this->mime_type;
	}

	/**
	 * �ե����륵�������å�
	 *
	 * @param $file_size
	 */
	function set_file_size ($file_size) {
		$this->file_size = $file_size;
	}

	/**
	 * �ե����륵�������å�
	 *
	 * @param none
	 */
	function get_file_size () {
		return $this->file_size;
	}

	/**
	 * �ե����륵�������å� (KB)
	 *
	 * @param none
	 */
	function get_file_size_kb () {
		$size = $this->file_size / 1024;
		return number_format(ceil($size)) . " KB";
	}

	/**
	 * ���åץ��ɥƥ�ݥ��ե�����̾���å�
	 *
	 * @param $upload_temp_file_name
	 */
	function set_upload_temp_file_name ($upload_temp_file_name) {
		$this->upload_temp_file_name = $upload_temp_file_name;
	}

	/**
	 * ���åץ��ɥƥ�ݥ��ե�����̾���å�
	 *
	 * @param none
	 */
	function get_upload_temp_file_name () {
		return $this->upload_temp_file_name;
	}

	/**
	 * ��Ͽ�ԥ桼�����ߥ�˥ƥ�ID���å�
	 *
	 * @param $entry_user_community_id
	 */
	function set_entry_user_community_id ($entry_user_community_id) {
		$this->entry_user_community_id = $entry_user_community_id;
	}

	/**
	 * ��Ͽ�ԥ桼�����ߥ�˥ƥ�ID���å�
	 *
	 * @param none
	 */
	function get_entry_user_community_id () {
		return $this->entry_user_community_id;
	}

	/**
	 * ��Ͽ�ԥ桼�����ߥ�˥ƥ�̾���å�
	 *
	 * @param $entry_user_community_name
	 */
	function set_entry_user_community_name ($entry_user_community_name) {
		$this->entry_user_community_name = $entry_user_community_name;
	}

	/**
	 * ��Ͽ�ԥ桼�����ߥ�˥ƥ�̾���å�
	 *
	 * @param none
	 */
	function get_entry_user_community_name () {
		return $this->entry_user_community_name;
	}

	/**
	 * ��Ͽ�����å�
	 *
	 * @param $entry_date
	 */
	function set_entry_date ($entry_date) {
		$this->entry_date = $entry_date;
	}

	/**
	 * ��Ͽ�����å�
	 *
	 * @param none
	 */
	function get_entry_date () {
		return $this->entry_date;
	}

	/**
	 * ��Ͽ�����å� (yyyymmddhmi)
	 *
	 * @param none
	 */
	function get_entry_date_yyyymmddhmi () {
		$date_yyyymmddhmi = ACSLib::convert_pg_date_to_str($this->entry_date);
		return $date_yyyymmddhmi;
	}

	/**
	 * �����ԥ桼�����ߥ�˥ƥ�ID���å�
	 *
	 * @param $update_user_community_id
	 */
	function set_update_user_community_id ($update_user_community_id) {
		$this->update_user_community_id = $update_user_community_id;
	}

	/**
	 * �����ԥ桼�����ߥ�˥ƥ�ID���å�
	 *
	 * @param none
	 */
	function get_update_user_community_id () {
		return $this->update_user_community_id;
	}

	/**
	 * �����ԥ桼�����ߥ�˥ƥ�̾���å�
	 *
	 * @param $update_user_community_name
	 */
	function set_update_user_community_name ($update_user_community_name) {
		$this->update_user_community_name = $update_user_community_name;
	}

	/**
	 * �����ԥ桼�����ߥ�˥ƥ�̾���å�
	 *
	 * @param none
	 */
	function get_update_user_community_name () {
		return $this->update_user_community_name;
	}

	/**
	 * ���������å�
	 *
	 * @param $update_date
	 */
	function set_update_date ($update_date) {
		$this->update_date = $update_date;
	}

	/**
	 * ���������å�
	 *
	 * @param none
	 */
	function get_update_date () {
		return $this->update_date;
	}

	/**
	 * ���������å� (yyyymmddhmi)
	 *
	 * @param none
	 */
	function get_update_date_yyyymmddhmi () {
		$date_yyyymmddhmi = ACSLib::convert_pg_date_to_str($this->update_date);
		return $date_yyyymmddhmi;
	}

	/**
	 * ���åץ��ɥե�������¸��ѥ�
	 *
	 * @param $owner_community_id
	 * @param $file_id
	 */
	function get_upload_file_save_file_name ($owner_community_id, $file_id) {
		//$upload_file_save_file_name  = ACS_FOLDER_DIR;
		//$upload_file_save_file_name .= '/';
		//$upload_file_save_file_name .= $owner_community_id;
		//$upload_file_save_file_name .= '/';

		$upload_file_save_file_name  = ACSFile::get_upload_file_save_path($owner_community_id);
		$upload_file_save_file_name .= $file_id;

		return $upload_file_save_file_name;
	}

	/**
	 * ���åץ��ɥե�������¸��ǥ��쥯�ȥ�
	 * �ե������Ǽ��롼�ȥѥ� / �桼�����ߥ�˥ƥ�ID / �ե�����̾ (file_id)
	 *
	 * @param $owner_community_id
	 */
	static function get_upload_file_save_path ($owner_community_id) {
		//$upload_file_save_path  = ACS_FOLDER_DIR;
		//$upload_file_save_path .= '/';
		$upload_file_save_path = $owner_community_id;
		$upload_file_save_path .= '/';

		return $upload_file_save_path;
	}

	/**
	 * ����ͥ�������ե�����̾
	 * �ե������Ǽ��롼�ȥѥ� / �桼�����ߥ�˥ƥ�ID / �ե�����̾ (file_id.thumb)
	 *
	 * @param $owner_community_id
	 * @param $file_id
	 */
	static function get_thumbnail_save_file_name ($owner_community_id, $file_id) {
		$thumnail_save_file_name  = ACSFile::get_upload_file_save_path($owner_community_id);
		$thumnail_save_file_name .= $file_id;
		$thumnail_save_file_name .= '.thumb';
		return $thumnail_save_file_name;
	}

	/**
	 * RSS����ͥ�������ե�����̾
	 * �ե������Ǽ��롼�ȥѥ� / �桼�����ߥ�˥ƥ�ID / �ե�����̾ (file_id.rss)
	 *
	 * @param $owner_community_id
	 * @param $file_id
	 */
	function get_rss_save_file_name ($owner_community_id, $file_id) {
		$rss_save_file_name  = ACSFile::get_upload_file_save_path($owner_community_id);
		$rss_save_file_name .= $file_id;
		$rss_save_file_name .= '.rss';
		return $rss_save_file_name;
	}

	/**
	 * �ե������ɲ�
	 *
	 * @param none
	 */
	function add_file () {
		$ret = ACSFileInfoModel::insert_file_info($this);

		return $ret;
	}

	/**
	 * ���åץ��ɥե�������¸
	 * �ƥ�ݥ��ե������ե�����γ�Ǽ���ذ�ư����
	 *
	 * @param $save_mode  ��¸����⡼�� ('PROFILE' or 'DIARY' or 'BBS' or 'FOLDER')
	 */
	function save_upload_file ($save_mode) {
		// 0�Х��ȥե�����ξ��ϡ����顼
		if ($this->get_file_size() <= 0) {
			return false;
		}

		/* �ǥ��쥯�ȥ�¸�ߥ����å� */
		// �ʤ����Ϻ�������
		$to_dir  = ACS_FOLDER_DIR . "/";
		$to_dir .= $this->get_upload_file_save_path($this->get_owner_community_id());
		if(!file_exists($to_dir)) {mkdir($to_dir); chmod($to_dir, 0777);}

		$from = $this->get_upload_temp_file_name();
		$to   = ACS_FOLDER_DIR . "/" . $this->get_server_file_name();
		if(is_uploaded_file($from)){
			$ret = move_uploaded_file($from, $to);
			if (!$ret) {
				return $ret;
			}
		}else{
			//ľ�ܤΥ��åץ��ɥե�����ǤϤʤ���硢��ư����
			$ret = rename($from, $to);
		}
		/* �����ξ�硢����ͥ������� */
		if ($this->is_image_file()) {
			$ret = $this->make_thumbnail($to, $save_mode);
		}
		return $ret;
	}

	/**
	 * ����ͥ����������
	 *
	 * @param $target_file ����ͥ�������� �����ե�����ѥ�
	 * @param $save_mode  ��¸����⡼�� ('PROFILE' or 'DIARY' or 'BBS' or 'FOLDER')
	 */
	function make_thumbnail ($target_file, $save_mode) {
		// �����ƥ����ꥰ�롼��̾
		//$system_config_group = '�����ե�����';
		$system_config_group = ACSMsg::get_mst('system_config_group','D04');

		// ���������������
		$default_width_max  = "";
		$default_height_max = "";
		$thumb_width_max	= "";
		$thumb_height_max   = "";
		$rss_width_max	  = "";
		$rss_height_max	 = "";

		switch ($save_mode) {
			case 'PROFILE':
				$default_width_max  = ACSSystemConfig::get_keyword_value($system_config_group, 'PROFILE_IMAGE_WIDTH_MAX');
				$default_height_max = ACSSystemConfig::get_keyword_value($system_config_group, 'PROFILE_IMAGE_HEIGHT_MAX');
				$thumb_width_max	= ACSSystemConfig::get_keyword_value($system_config_group, 'PROFILE_IMAGE_THUMB_WIDTH_MAX');
				$thumb_height_max   = ACSSystemConfig::get_keyword_value($system_config_group, 'PROFILE_IMAGE_THUMB_HEIGHT_MAX');
				break;

			case 'DIARY':
				$default_width_max  = ACSSystemConfig::get_keyword_value($system_config_group, 'DIARY_IMAGE_WIDTH_MAX');
				$default_height_max = ACSSystemConfig::get_keyword_value($system_config_group, 'DIARY_IMAGE_HEIGHT_MAX');
				$thumb_width_max	= ACSSystemConfig::get_keyword_value($system_config_group, 'DIARY_IMAGE_THUMB_WIDTH_MAX');
				$thumb_height_max   = ACSSystemConfig::get_keyword_value($system_config_group, 'DIARY_IMAGE_THUMB_HEIGHT_MAX');
				break;

			case 'BBS':
				$default_width_max  = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_WIDTH_MAX');
				$default_height_max = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_HEIGHT_MAX');
				$thumb_width_max	= ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_THUMB_WIDTH_MAX');
				$thumb_height_max   = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_THUMB_HEIGHT_MAX');
				$rss_width_max	  = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_RSS_WIDTH_MAX');
				$rss_height_max	 = ACSSystemConfig::get_keyword_value($system_config_group, 'BBS_IMAGE_RSS_HEIGHT_MAX');
				break;

			case 'FOLDER':
				$thumb_width_max	= ACSSystemConfig::get_keyword_value($system_config_group, 'FOLDER_IMAGE_THUMB_WIDTH_MAX');
				$thumb_height_max   = ACSSystemConfig::get_keyword_value($system_config_group, 'FOLDER_IMAGE_THUMB_HEIGHT_MAX');
				break;

			default:
				return false;
		}

		// ����ͥ����������
		// ImageMagick
		$image_obj = new ACSImageMagickWrapper($target_file);
		if (!$image_obj) {
			return false;
		}

		// ���祵��������礭�����Ͻ̾�����
		//	�ե�����ξ������¤ʤ�
		if ($save_mode != 'FOLDER') {
			$new_image_name = $image_obj->reduce_image($default_width_max, $default_height_max);
		}

		// ����ͥ������
		$new_thumb_name = $image_obj->make_jpg_thumbnail(ACS_FOLDER_DIR . $this->get_thumbnail_server_file_name(),
					$thumb_width_max, $thumb_height_max);

		// RSS �ե�������
		//	BBS �ξ��ϡ�RSS�ѥե���������
		if ($save_mode == 'BBS') {
			$rss_server_file_name = $this->get_rss_save_file_name($this->get_owner_community_id(), $this->get_file_id());
			$new_thumb_name = $image_obj->make_jpg_thumbnail(ACS_FOLDER_DIR . $rss_server_file_name,
					$rss_width_max, $rss_height_max);
			$this->set_rss_server_file_name($rss_server_file_name);
		}

		return true;
	}

	/**
	 * �ե�������������
	 *
	 * @param none
	 *
	 * @return none
	 */
	function download_file ($mode = '') {
		// �ե�����ѥ�
		if ($mode == 'thumb') {
			$file_path = $this->get_thumbnail_server_file_name();
		} else if ($mode == 'rss') {
			$file_path = $this->get_rss_server_file_name();
		} else {
			$file_path = $this->get_server_file_name();
		}

		// �ե����뤬�ɤ߹��ߤǤ��ʤ����
		if (!is_readable(ACS_FOLDER_DIR . $file_path)) {
			header("Cache-Control: public, max-age=0");
			header("Pragma:");
			echo "Not Found";
			return;
		}

		// ��������ɥե�����̾
		//$download_file_name = mb_convert_encoding($this->get_display_file_name(), mb_http_output());
		$download_file_name = $this->get_display_file_name();

		// Content-type
		$content_type = $this->get_mime_type();
		if ($content_type == '') {
			$content_type = 'application/octet-stream';
		}

		// charset (text�ξ��)
		if (preg_match('/text/', $content_type)) {
			$str = implode('', file(ACS_FOLDER_DIR . $file_path));
			$encoding = mb_detect_encoding($str, 'auto');
			if ($encoding == 'ASCII' && mb_http_output() != 'pass') {
				$content_type .= "; charset=" . mb_preferred_mime_name(mb_http_output());
			} else {
				$content_type .= "; charset=" . mb_preferred_mime_name($encoding);

			}
		}

		// action: inline(�֥饦����ɽ��), attachment(��������ɥ�������)
		//if (preg_match('/text|image/', $content_type)) {
		if ($this->is_image_file() or $this->is_text_file()) {
			$action = 'inline';
		} else {
			$action = 'attachment';
		}

		// output_buffering��̵���ˤ���
		mb_http_output('pass');
		if ($mode == 'thumb') {
			header("Cache-Control: public, max-age=1800");
			header("Pragma:");
/*
header("Pragma: no-cache");
header("Cache-Control: no-store");
header("Cache-Control: no-cache");
header("Expires: -1");
//header("Expires: 0");
*/
		} else {
			header("Cache-Control: public, max-age=0");
			header("Pragma:");
/*
//header("Pragma: no-store");
header("Pragma: no-cache");
header("Cache-Control: no-store");
header("Cache-Control: no-cache");
//header("Expires: 0");
header("Expires: -1");
*/
		}

		// HTTP�إå����Ǥ� (action: inline, attachment)
		if ($this->is_image_file()) {
			header("Content-type: $content_type");
			header("Content-disposition: $action; filename=\"$download_file_name\"");
		} else {
			header("Content-type: $content_type");
			header("Content-disposition: $action; attachment; filename=\"" . ACSFile::get_download_name( $download_file_name ) . '"' );

		}		

		// �ե�������ɤ߽Ф�
		readfile(ACS_FOLDER_DIR . $file_path);
	}

	/**
	 * ����ե�������������
	 *
	 * @param none
	 *
	 * @return none
	 */
	function download_history_file($file_history_id, $mode = '') {
		$file_history_row = ACSFileHistory::get_file_history_row($file_history_id);

		// �ե�����ѥ�
		if ($mode == 'thumb') {
			$file_path = $file_history_row['thumbnail_server_file_name'];
		} else {
			$file_path = $file_history_row['server_file_name'];
		}

		// �ե����뤬�ɤ߹��ߤǤ��ʤ����
		if (!is_readable(ACS_FOLDER_DIR . $file_path)) {
			header("Cache-Control: public, max-age=0");
			header("Pragma:");
			echo "Not Found";
			return;
		}

		// ��������ɥե�����̾
//		$download_file_name = mb_convert_encoding($this->get_display_file_name(), mb_http_output());
		$download_file_name = $this->get_display_file_name();

		// Content-type
		$content_type = $file_history_row['mime_type'];
		if ($content_type == '') {
			$content_type = 'application/octet-stream';
		}

		// charset (text�ξ��)
		if (preg_match('/text/', $content_type)) {
			$str = implode('', file(ACS_FOLDER_DIR . $file_path));
			$encoding = mb_detect_encoding($str, 'auto');
			if ($encoding == 'ASCII' && mb_http_output() != 'pass') {
				$content_type .= "; charset=" . mb_preferred_mime_name(mb_http_output());
			} else {
				$content_type .= "; charset=" . mb_preferred_mime_name($encoding);
			}
		}

		// action: inline(�֥饦����ɽ��), attachment(��������ɥ�������)
		//if (preg_match('/text|image/', $content_type)) {
		if (preg_match('/image/', $file_history_row['mime_type']) || preg_match('/text/', $file_history_row['mime_type'])) {
			$action = 'inline';
		} else {
			$action = 'attachment';
		}

		// HTTP�إå����Ǥ� (action: inline, attachment)
//		header("Content-disposition: $action; filename=\"$download_file_name\"");
		header("Content-disposition: $action; attachment; filename=\"" . ACSFile::get_download_name( $download_file_name ) . '"' );
		header("Content-type: $content_type");

		// output_buffering��̵���ˤ���
		mb_http_output('pass');
		if ($mode == 'thumb') {
			header("Cache-Control: public, max-age=1800");
			header("Pragma:");
		} else {
			header("Cache-Control: public, max-age=0");
			header("Pragma:");
		}

		// �ե�������ɤ߽Ф�
		readfile(ACS_FOLDER_DIR . $file_path);
	}

	/**
	 * �����ե�����ɽ��
	 *
	 * @param $mode	   :NULL, thumb, rss
	 *
	 * @return true/false
	 */
	function view_image ($mode) {
		$mime_type = $this->get_mime_type();

		if ($this->is_image_file()) {
			// image �ե��������
			$this->download_file($mode);
			return true;

		} else {
			return false;
		}
	}

	/**
	 * �����ե����뤫�ɤ���
	 */
	function is_image_file () {
		if (preg_match('/image/', $this->get_mime_type())) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * �ƥ����ȥե����뤫�ɤ���
	 */
	function is_text_file () {
		if (preg_match('/text/', $this->get_mime_type())) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * ɽ���ѥե�����̾�ѹ�
	 *
	 * @param  $new_display_file_name
	 * @return $ret
	 */
	function rename_display_file_name ($new_display_file_name) {
		$ret = ACSFileInfoModel::update_display_file_name($this->get_file_id(), $new_display_file_name);
		return $ret;
	}

	/**
	 * �ե�������
	 *
	 * @return $ret
	 */
	function delete_file () {
		$ret = ACSFileInfoModel::delete_file_info($this);
		return $ret;
	}

	/**
	 * �ե�����Υ����������������������
	 *
	 * @param $user_community_id �桼�����ߥ�˥ƥ�ID
	 * @param $file_id �ե�����ID
	 * @return �ե�����Υ�������������� (Ϣ������)
	 */
	static function get_file_access_history_row($user_community_id, $file_id) {
		$user_community_id = pg_escape_string($user_community_id);
		$file_id = pg_escape_string($file_id);

		$sql  = "SELECT *" . 
				" FROM file_access_history" .
				" WHERE user_community_id = '" . $user_community_id . "'" . 
				"  AND file_id = '" . $file_id . "'";

		$row = ACSDB::_get_row($sql);
		return $row;
	}

	/**
	 * �ե�����Υ��������������Ͽ����
	 *
	 * @param $file_id
	 * @return ����(true) / ����(false)
	 */
	static function set_file_access_history($user_community_id, $file_id) {
		// �ե����륢����������
		$file_access_history_row = ACSFile::get_file_access_history_row($user_community_id, $file_id);

		$file_access_history_form = array(
										'user_community_id' => $user_community_id,
										'file_id' 			=> $file_id,
										'access_date' 		=> 'now'
		);

		// �쥳���ɤ�¸�ߤ������UPDATE
		if ($file_access_history_row) {
			ACSFileAccessHistoryModel::update_file_access_history($file_access_history_form);
		// �쥳���ɤ�¸�ߤ��ʤ�����INSERT
		} else {
			ACSFileAccessHistoryModel::insert_file_access_history($file_access_history_form);
		}
	}

	/**
	 * �ե������������ɵ�ǽ�ѡ����󥳡��ǥ��󥰤����ꤹ��
	 *
	 * @return ����(true) / ����(false)
	 */
	function get_current_mb_encoding() {
		// ���󥳡��ǥ��󥰤���ꤹ����
		return ACSFile::_get_mb_encoding( 'EUC-JP' ) ;
		//return ACSFile::_get_mb_encoding( lang_get( 'charset' ) );
	}

	/**
	 * �ե������������ɵ�ǽ�ѡ����󥳡��ɤ�Ԥ�
	 *
	 * @return ����(���󥳡��ɸ�) / ����(null)
	 */
	function _get_mb_encoding( $p_charset ) {

		$mb_encoding_array = array(
			'Shift_JIS' => 'SJIS-win',
			'EUC-JP' => 'EUC-JP',
			'UTF-8'  => 'UTF-8'
		);

		if ( isset( $mb_encoding_array[$p_charset] ) ) {
			return $mb_encoding_array[$p_charset];
		}

		return null;
	}

	/**
	 * �ե������������ɵ�ǽ�ѡ���������ɥե�����̾���������
	 *
	 * @return �ե�����̾
	 */
	function get_download_name( $p_filename ) {

		$encoding = ACSFile::get_current_mb_encoding();
		if ( $encoding === null ) {
			return $p_filename;
		}

		$ua = $_SERVER['HTTP_USER_AGENT'];

		// �桼��������������Ȥˤ�äƥե�����̾���Ѵ�
		if ( strstr( $ua, 'MSIE' ) && !strstr( $ua, 'Opera' ) ) {
			$t_filename = mb_convert_encoding( $p_filename, 'SJIS-win', $encoding );

		} elseif (strstr( $ua, 'Safari') ) {
			// Safari�б�
			$t_filename = "";

		} else {
			$t_filename = mb_convert_encoding( $p_filename, 'UTF-8', $encoding );
		}

		return $t_filename;
	}

}
?>
