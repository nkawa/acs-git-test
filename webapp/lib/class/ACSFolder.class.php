<?php
/**
 * ACS Folder
 *
 * @author  kuwayama
 * @version $Revision: 1.25 $ $Date: 2006/12/08 05:06:29 $
 */
require_once(ACS_CLASS_DIR . 'ACSFile.class.php');
class ACSFolder
{
	/* �ե����ID */
	var $folder_id;

	/* ���ߥ�˥ƥ�ID */
	var $community_id;

	/* �ե����̾ */
	var $folder_name;

	/* ������ */
	var $comment;

	/* �ƥե����ID */
	var $parent_folder_id;

	/* �����ϰϥ����� */
	var $open_level_code;

	/* �����ϰ�̾ */
	var $open_level_name;

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

	/* �������ĥ��ߥ�˥ƥ� */
	var $trusted_community_row_array = array();

	/* �ץå��襳�ߥ�˥ƥ� */
	var $put_community_row_array = array();

	/* �롼�ȥե�����ե饰 */
	var $is_root_folder = false;

	/* �ե�����ꥹ�� */
	var $folder_obj_array = array();

	/* �ե�����ꥹ�� */
	var $file_obj_array = array();

	/* ���������桼������ */
	var $acs_user_info_row;

	/* ���֥ե���� */
	var $sub_folder_obj_array = array();

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param $folder_row
	 * @param $target_folder_id_array �ե�������ե�����Υꥹ�ȼ����оݤȤʤ�ե����ID
	 */
	function ACSFolder ($folder_row, $target_folder_id_array, $acs_user_info_row) {
		/* �ե�������󥻥å� */
		$this->set_folder_info($folder_row);

		if (!is_array($target_folder_id_array)) {
			if ($target_folder_id_array) {
				$target_folder_id_array = array($target_folder_id_array);
			} else {
				// �ʤ���硢���󥹥��󥹤Τ��֤�
				return $this;
			}
		}

		/* �ե�����ꥹ�ȡ��ե�����ꥹ�ȥ��å� */
		$this->set_folder_obj_array($target_folder_id_array);
		$this->set_file_obj_array($target_folder_id_array);

		/* ���������桼�����󥻥å� */
		if (!$acs_user_info_row == "") {
			$this->set_acs_user_info_row($acs_user_info_row);
		}
	}

	/**
	 * �ե�������󥹥��󥹺���
	 *
	 * �ե�����ꥹ�ȡ��ե�����ꥹ�Ȥʤ��Υե����������ݻ�����
	 * �ե�������󥹥��󥹤��֤�
	 *
	 * @param  $folder_row
	 * @return $folder_obj
	 */
	function get_folder_instance (&$folder_row) {
		/* ���Υ��󥹥��󥹤Τ߼��� */
		$folder_obj = new ACSFolder($folder_row, '', '');

		return $folder_obj;
	}

	/**
	 * �ե�����ꥹ�ȥ��å�
	 *
	 * @param $parent_folder_id_array
	 */
	function set_folder_obj_array (&$parent_folder_id_array) {
		$folder_obj_array = array();

		/* ���֥ե����������� */
		$folder_row_array = ACSFolderModel::select_sub_folder_row_array($parent_folder_id_array);

		/* �ե�������󥻥å� */
		foreach ($folder_row_array as $folder_row) {
			$folder_id_array = array();

			$folder_obj = ACSFolder::get_folder_instance($folder_row);
			array_push($folder_obj_array, $folder_obj);
		}
		$this->folder_obj_array = $folder_obj_array;
	}

	/**
	 * �ե�����ꥹ�ȥ��å�
	 *
	 * @param none
	 */
	function get_folder_obj_array () {
		return $this->folder_obj_array;
	}

	/**
	 * ����ե����ID�Υե�������å�
	 *
	 * @param  $search_folder_id
	 * @return $folder_obj
	 */
	function get_folder_obj ($search_folder_id) {
		$folder_obj_array = $this->get_folder_obj_array();
		foreach ($folder_obj_array as $folder_obj) {
			if ($folder_obj->get_folder_id() == $search_folder_id) {
				$ret_folder_obj = $folder_obj;
				break;
			}
		}

		return $ret_folder_obj;
	}

	/**
	 * �ե�����ꥹ�ȥ��å�
	 * ���������Ԥ�����������ǽ�ʥե�����Τ��֤�
	 *
	 * @param $acs_user_info_row
	 * @param $target_user_info_row
	 */
	function get_display_folder_obj_array ($acs_user_info_row, $target_user_info_row) {
		$ret_folder_obj_array = array();

		/* role_array ���� */
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);

		if ($this->get_is_root_folder()) {
			/* ɽ����ǽ���֥������ȼ��� */
			$all_folder_obj_array = $this->get_folder_obj_array();
			$ret_folder_obj_array = ACSAccessControl::get_valid_obj_row_array_for_user_community(
														$acs_user_info_row,
														$role_array,
														$all_folder_obj_array);
		} else {
			$ret_folder_obj_array = $this->get_folder_obj_array();
		}

		// ����������ǽ�ʥե�����Τ��֤�
		//return $this->folder_obj_array;
		return $ret_folder_obj_array;
	}

	/**
	 * �ե�����ꥹ�ȥ��å� (���ߥ�˥ƥ���)
	 * ���������Ԥ�����������ǽ�ʥե�����Τ��֤�
	 *
	 * @param $acs_user_info_row
	 * @param $target_community_row
	 */
	function get_display_folder_obj_array_for_community ($acs_user_info_row, $target_community_row) {
		$ret_folder_obj_array = array();

		/* role_array ���� */
		$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $target_community_row);

		if ($this->get_is_root_folder()) {
			/* ɽ����ǽ���֥������ȼ��� */
			$all_folder_obj_array = $this->get_folder_obj_array();
			$ret_folder_obj_array = ACSAccessControl::get_valid_obj_row_array_for_community(
														$acs_user_info_row,
														$role_array,
														$all_folder_obj_array);
		} else {
			$ret_folder_obj_array = $this->get_folder_obj_array();
		}

		// ����������ǽ�ʥե�����Τ��֤�
		//return $this->folder_obj_array;
		return $ret_folder_obj_array;
	}

	/**
	 * �ե�����ꥹ�ȥ��å�
	 *
	 * @param $folder_id_array
	 */
	function set_file_obj_array (&$folder_id_array) {
		$file_obj_array = array();

		/* �ե����������� */
		$file_info_row_array = ACSFolderModel::select_folder_file_info_row_array($folder_id_array);
		if (count($file_info_row_array) <= 0) {
			return;
		}

		/* �ե�������󥻥å� */
		foreach ($file_info_row_array as $file_info_row) {
		//_debug($file_info_row);
			$file_obj = new ACSFile($file_info_row);

			array_push($file_obj_array, $file_obj);
			//array_push($this->file_obj_array, $file_obj);
		}
		$this->file_obj_array = $file_obj_array;
	}

	/**
	 * �ե�����ꥹ�ȥ��å�
	 *
	 * @param none
	 */
	function get_file_obj_array () {
		return $this->file_obj_array;
	}

	/**
	 * ����ID�Υե����륲�å�
	 *
	 * @param  $search_file_id
	 * @return $file_obj
	 */
	function get_file_obj ($search_file_id) {
		$file_obj_array = $this->get_file_obj_array();
		foreach ($file_obj_array as $file_obj) {
			if ($file_obj->get_file_id() == $search_file_id) {
				$ret_file_obj = $file_obj;
				break;
			}
		}

		return $ret_file_obj;
	}

	/**
	 * �ե�������󥻥å�
	 *
	 * @param $folder_row
	 */
	function set_folder_info (&$folder_row) {
		$this->set_folder_id($folder_row['folder_id']);
		$this->set_community_id($folder_row['community_id']);

		if ($folder_row['parent_folder_id']) {
			$this->set_folder_name($folder_row['folder_name']);
		} else {
			// �롼�ȥե�����ξ��
			$this->set_folder_name(ACSMsg::get_mdmsg(__FILE__,'M001'));
		}

		$this->set_comment($folder_row['comment']);
		$this->set_parent_folder_id($folder_row['parent_folder_id']);
		$this->set_open_level_code($folder_row['open_level_code']);
		$this->set_open_level_name($folder_row['open_level_name']);
		$this->set_trusted_community_row_array($folder_row['trusted_community_row_array']);
		$this->set_put_community_row_array($folder_row['put_community_row_array']);

		$this->set_entry_user_community_id($folder_row['entry_user_community_id']);
		$this->set_entry_user_community_name($folder_row['entry_user_community_name']);
		$this->set_entry_date($folder_row['entry_date']);
		$this->set_update_user_community_id($folder_row['update_user_community_id']);
		$this->set_update_user_community_name($folder_row['update_user_community_name']);
		$this->set_update_date($folder_row['update_date']);
	}

	/**
	 * �ե����ID���å�
	 *
	 * @param $folder_id
	 */
	function set_folder_id ($folder_id) {
		$this->folder_id = $folder_id;
	}

	/**
	 * �ե����ID���å�
	 *
	 * @param none
	 */
	function get_folder_id () {
		return $this->folder_id;
	}

	/**
	 * ���ߥ�˥ƥ�ID���å�
	 *
	 * @param $community_id
	 */
	function set_community_id ($community_id) {
		$this->community_id = $community_id;
	}

	/**
	 * ���ߥ�˥ƥ�ID���å�
	 *
	 * @param none
	 */
	function get_community_id () {
		return $this->community_id;
	}

	/**
	 * �ե����̾���å�
	 *
	 * @param $folder_name
	 */
	function set_folder_name ($folder_name) {
		$this->folder_name = $folder_name;
	}

	/**
	 * �ե����̾���å�
	 *
	 * @param none
	 */
	function get_folder_name () {
		return $this->folder_name;
	}

	/**
	 * �����ȥ��å�
	 *
	 * @param $comment
	 */
	function set_comment ($comment) {
		$this->comment = $comment;
	}

	/**
	 * �����ȥ��å�
	 *
	 * @param none
	 */
	function get_comment () {
		return $this->comment;
	}

	/**
	 * �ƥե����ID���å�
	 *
	 * @param $parent_folder_id
	 */
	function set_parent_folder_id ($parent_folder_id) {
		$this->parent_folder_id = $parent_folder_id;

		// �롼�ȥե�����ե饰���å�
		$this->set_is_root_folder($parent_folder_id);
	}

	/**
	 * �ƥե����ID���å�
	 *
	 * @param none
	 */
	function get_parent_folder_id () {
		return $this->parent_folder_id;
	}

	/**
	 * �����ϰϥ����ɥ��å�
	 *
	 * @param $open_level_code
	 */
	function set_open_level_code ($open_level_code) {
		$this->open_level_code = $open_level_code;
	}

	/**
	 * �����ϰϥ����ɥ��å�
	 *
	 * @param none
	 */
	function get_open_level_code () {
		return $this->open_level_code;
	}

	/**
	 * �����ϰ�̾���å�
	 *
	 * @param $open_level_name
	 */
	function set_open_level_name ($open_level_name) {
		$this->open_level_name = $open_level_name;
	}

	/**
	 * �����ϰ�̾���å�
	 *
	 * @param none
	 */
	function get_open_level_name () {
		return $this->open_level_name;
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
	 * �롼�ȥե�����ե饰���å�
	 *
	 * @param $is_root_folder
	 */
	function set_is_root_folder ($parent_folder_id) {
		if ($parent_folder_id == "") {
			$this->is_root_folder = true;
		} else {
			$this->is_root_folder = false;
		}
	}

	/**
	 * �롼�ȥե�����ե饰���å�
	 *
	 * @param none
	 */
	function get_is_root_folder () {
		return $this->is_root_folder;
	}

	/**
	 * ���������桼�����󥻥å�
	 *
	 * @param $acs_user_info_row
	 */
	function set_acs_user_info_row ($acs_user_info_row) {
		$this->acs_user_info_row = $acs_user_info_row;
	}

	/**
	 * ���������桼�����󥲥å�
	 *
	 * @param none
	 */
	function get_acs_user_info_row () {
		return $this->acs_user_info_row;
	}

	/**
	 * �������ĥ��ߥ�˥ƥ����å�
	 *
	 * @param $trusted_community_row_array
	 */
	function set_trusted_community_row_array ($trusted_community_row_array) {
		if ($trusted_community_row_array == "") {
			$trusted_community_row_array = array();
		}
		$this->trusted_community_row_array = $trusted_community_row_array;
	}

	/**
	 * �������ĥ��ߥ�˥ƥ����å�
	 *
	 * @param none
	 */
	function get_trusted_community_row_array () {
		return $this->trusted_community_row_array;
	}

	/**
	 * �ץå��襳�ߥ�˥ƥ����å�
	 *
	 * @param $put_community_row_array
	 */
	function set_put_community_row_array ($put_community_row_array) {
		if ($put_community_row_array == "") {
			$put_community_row_array = array();
		}
		$this->put_community_row_array = $put_community_row_array;
	}

	/**
	 * �ץå��襳�ߥ�˥ƥ����å�
	 *
	 * @param none
	 */
	function get_put_community_row_array () {
		return $this->put_community_row_array;
	}

	/**
	 * �ե������ɲ�
	 *
	 * @param $file_obj
	 *
	 * @return $ret
	 */
	function add_file ($file_obj) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		ACSDB::_do_query("BEGIN");

		/* �ơ��֥���ɲ� */
		// file_info
		$ret = $file_obj->add_file();
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// folder_file
		$ret = ACSFolderModel::insert_folder_file($this->get_folder_id(), $file_obj->get_file_id());
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* �ե�����ι������򹹿� */
		$ret = ACSFolderModel::update_folder_update_date($this->get_folder_id(),
														 $acs_user_community_id,
														 $file_obj->get_update_date());
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* �ե�������ư */
		$ret = $file_obj->save_upload_file('FOLDER');
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* �ե�����ꥹ�Ȥ��ɲ� */
		array_push($this->file_obj_array, $file_obj);

		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * �ե����빹�� (�������)
	 *
	 * @param $file_obj
	 *
	 * @return $ret
	 */
	function update_file($file_obj) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		ACSDB::_do_query("BEGIN");

		/* �ơ��֥빹�� */
		// file_info
		$row = array();
		$row['server_file_name'] = $file_obj->get_server_file_name();
		$row['thumbnail_server_file_name'] = $file_obj->get_thumbnail_server_file_name();
		$row['rss_server_file_name'] = $file_obj->get_rss_server_file_name();
		$row['mime_type'] = $file_obj->get_mime_type();
		$row['file_size'] = $file_obj->get_file_size();
		$row['update_user_community_id'] = $file_obj->get_update_user_community_id();
		$row['update_date'] = $file_obj->get_update_date();
		$ret = ACSFileInfoModel::update_file_info($file_obj->get_file_id(), $row); 
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* �ե�����ι������򹹿� */
		$ret = ACSFolderModel::update_folder_update_date($this->get_folder_id(),
														 $acs_user_community_id,
														 $file_obj->get_update_date());
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* �ե�������ư(��¸) */
		$ret = $file_obj->save_upload_file('FOLDER');
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * �ե��������� (�������)
	 *
	 * @param $file_obj
	 *
	 * @return $ret
	 */
	function restore_history_file($file_info_row, $file_history_row) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		ACSDB::_do_query("BEGIN");

		// file_info_row����
		$row = array();
		$row['display_file_name'] = $file_history_row['display_file_name'];
		$row['server_file_name'] = $file_history_row['server_file_name'];
		$row['thumbnail_server_file_name'] = $file_history_row['thumbnail_server_file_name'];
		$row['mime_type'] = $file_history_row['mime_type'];
		$row['file_size'] = $file_history_row['file_size'];
		$row['update_user_community_id'] = $acs_user_community_id;
		$row['update_date'] = 'now';
		$ret = ACSFileInfoModel::update_file_info($file_info_row['file_id'], $row); 
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		/* �ե�����ι������򹹿� */
		$file_obj = ACSFile::get_file_info_instance($file_info_row['file_id']);
		$ret = ACSFolderModel::update_folder_update_date($this->get_folder_id(),
														 $acs_user_community_id,
														 $file_obj->get_update_date());
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * �ե������ư
	 *
	 * @param  $file_obj ��ư�оݤΥե����륪�֥�������
	 * @param  $new_folder_id ��ư��Υե����ID
	 * @return $ret
	 */
	function move_file ($file_obj, $new_folder_id) {
		$ret = ACSFolderModel::update_folder_file_folder_id($this->get_folder_id(), $file_obj->get_file_id(), $new_folder_id);
		return $ret;
	}

	/**
	 * �ե��������
	 *
	 * @param $input_folder_row
	 *
	 * @return $ret
	 */
	function create_folder ($input_folder_row) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		// �ե�������ͥ��å�
		$folder_row  = array();
		$timestamp_pg_date = ACSLib::convert_timestamp_to_pg_date();
		$folder_id   = ACSDB::get_next_seq('folder_id_seq');
		if ($input_folder_row['entry_date']) {
			$entry_date  = $input_folder_row['entry_date'];
		} else {
			$entry_date  = $timestamp_pg_date;
		}
		$update_date = $entry_date;

		$folder_row['folder_id']                   = $folder_id;
		$folder_row['community_id']                = $this->get_community_id();
		$folder_row['folder_name']                 = $input_folder_row['folder_name'];
		$folder_row['comment']                     = $input_folder_row['comment'];
		$folder_row['parent_folder_id']            = $this->get_folder_id();
		$folder_row['entry_user_community_id']     = $acs_user_community_id;
		$folder_row['entry_date']                  = $entry_date;
		$folder_row['update_user_community_id']    = $acs_user_community_id;
		$folder_row['update_date']                 = $update_date;
		$folder_row['open_level_code']             = $input_folder_row['open_level_code'];

		$trusted_community_id_array = $input_folder_row['trusted_community_id_array'];


		/* �ե�������� */
		$ret = ACSFolderModel::insert_folder($folder_row, $trusted_community_id_array);
		if (!$ret) {
			return $ret;
		}

		/* �ƥե�����ι������򹹿� */
		$ret = ACSFolderModel::update_folder_update_date($this->get_folder_id(),
														 $acs_user_community_id,
														 $update_date);
		if (!$ret) {
			return $ret;
		}

		/* �ե�����ꥹ�Ȥ��ɲ� */
		$folder_obj = ACSFolder::get_folder_instance($folder_row);
		array_push($this->folder_obj_array, $folder_obj);

		return $ret;
	}

	/**
	 * �ե���������ѹ�
	 *
	 * @param $input_folder_row
	 *
	 * @return $ret
	 */
	function update_folder ($input_folder_row) {
		$acs_user_info_row = $this->get_acs_user_info_row();
		$acs_user_community_id = $acs_user_info_row['user_community_id'];

		$folder_row['folder_name']                 = $input_folder_row['folder_name'];
		$folder_row['comment']                     = $input_folder_row['comment'];
		$folder_row['update_user_community_id']    = $acs_user_community_id;
		$folder_row['update_date']                 = ACSLib::convert_timestamp_to_pg_date();
		$folder_row['open_level_code']             = $input_folder_row['open_level_code'];

		$trusted_community_id_array  = $input_folder_row['trusted_community_id_array'];

		$ret = ACSFolderModel::update_folder_info($this->get_folder_id(), $folder_row, $trusted_community_id_array);
		return $ret;
	}

	/**
	 * �ե�������������
	 *
	 * @param $file_id
	 *
	 * @return none
	 */
	function download_file ($file_id) {
		$file_obj = $this->get_file_obj($file_id);
		$file_obj->download_file();
	}

	/**
	 * �ץåȥե�������ɤ���
	 *
	 * @param  $community_id ɽ�����Ƥ��륳�ߥ�˥ƥ�
	 * @return true/false
	 */
	function is_put_folder ($community_id) {
		if ($this->get_community_id() == $community_id) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * �ץå��襳�ߥ�˥ƥ������뤫�ɤ���
	 */
	function has_put_community () {
		if (count($this->get_put_community_row_array()) > 0) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * �ץå��襳�ߥ�˥ƥ��Υե����ID����
	 *
	 * @param $community_id
	 */
	function get_put_community_folder_id ($community_id) {
		$ret_put_community_folder_id = "";
		$put_community_row_array = $this->get_put_community_row_array();

		foreach ($put_community_row_array as $put_community_row) {
			if ($put_community_row['community_id'] == $community_id) {
				$ret_put_community_folder_id = $put_community_row['put_community_folder_id'];
				break;
			}
		}

		return $ret_put_community_folder_id;
	}

	/**
	 * �ץå��襳�ߥ�˥ƥ�����
	 */
	function update_put_community ($folder_id, $put_community_array) {
		ACSDB::_do_query("BEGIN");

		foreach ($put_community_array as $put_community) {
			// delete
			$ret = ACSFolderModel::delete_put_community($folder_id, $put_community['put_community_id']);
			if (!$ret) {
				ACSDB::_do_query("ROLLBACK");
				return false;
			}

			// �ץå���ե����ID�λ��꤬�����硢insert
			if ($put_community['put_community_folder_id']) {
				// insert
				$ret = ACSFolderModel::insert_put_community($folder_id, $put_community['put_community_id'], $put_community['put_community_folder_id']);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return false;
				}
			}
		}

		ACSDB::_do_query("COMMIT");

		return true;
	}

	/**
	 * ���֥ե�������å�
	 *
	 * @param $sub_folder_obj_array
	 */
	function set_sub_folder_obj_array ($sub_folder_obj_array) {
		$this->sub_folder_obj_array = $sub_folder_obj_array;
	}

	/**
	 * ���֥ե�������å�
	 */
	function get_sub_folder_obj_array () {
		return $this->sub_folder_obj_array;
	}

	/**
	 * �ե����̾����
	 *
	 * @param  $new_folder_name
	 * @return $ret
	 */
	function rename_folder_name ($new_folder_name) {
		$ret = ACSFolderModel::update_folder_name($this->get_folder_id(), $new_folder_name);
		return $ret;
	}

	/**
	 * �ե������ư
	 *
	 * @param  $new_parent_folder_id
	 * @return $ret
	 */
	function move_folder ($new_parent_folder_id) {
		$ret = ACSFolderModel::update_parent_folder_id($this->get_folder_id(), $new_parent_folder_id);
		return $ret;
	}

	/**
	 * �����ϰϹ���
	 *
	 * @param  $new_open_level_code
	 * @param  $new_trusted_community_row_array
	 * @return $ret
	 */
	function update_open_level_code ($new_open_level_code, $new_trusted_community_row_array) {
		$ret = ACSFolderModel::update_folder_open_level_code($this->get_folder_id(), $new_open_level_code, $new_trusted_community_row_array);
		return $ret;
	}
}
?>
