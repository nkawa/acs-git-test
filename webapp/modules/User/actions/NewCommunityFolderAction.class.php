<?php

/**
 * �ޥ����ߥ�˥ƥ��Υե�����������
 *
 * @author  z-satosi
 * @version $Revision: 1.4 y-yuki Exp $
 */
class NewCommunityFolderAction extends BaseAction
{
	function execute() {

		$context = &$this->getContext();
		$controller = $context->getController();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$form = $request->ACSgetParameters();
	
		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		if ($user_community_id == null || $user_community_id == '') {
			$user_community_id = $request->getAttribute("id");
		}

		// ¾�桼���Υǡ����������ʤ��褦�����å�
		if (!$this->get_execute_privilege()
				&& $acs_user_info_row["user_community_id"] != $user_community_id) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// ����饤��ɽ���ξ��: 1(true)
		$inline_mode = $request->ACSgetParameter('inline_mode');
		if ($inline_mode == null || $inline_mode == '') {
			$inline_mode = $request->getAttribute("inline_mode");
		}

		// �����ϰϤλ���
		$get_days = ACSSystemConfig::get_keyword_value(
					ACSMsg::get_mst('system_config_group','D02'), 
					($inline_mode ? 'NEW_INFO_TOP_TERM' : 'NEW_INFO_LIST_TERM'));
		$request->setAttribute('get_days', $get_days);

		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		if ($inline_mode) {
			// �ޥ����ߥ�˥ƥ��ο���ե�����������������
			$new_folder_row_array = 
					ACSCommunityFolder::get_new_community_folder_row_array($user_community_id, $get_days, true);

			// �ޥ����ߥ�˥ƥ��ο���ץåȥե�����������������
			$new_put_folder_row_array = 
					ACSCommunityFolder::get_new_community_put_folder_row_array($user_community_id, $form, $get_days, true);
		} else {
			// �ޥ����ߥ�˥ƥ��ο���ե�����������������
			$new_folder_row_array = 
					ACSCommunityFolder::get_new_community_folder_row_array($user_community_id, $get_days);

			// �ޥ����ߥ�˥ƥ��ο���ץåȥե�����������������
			$new_put_folder_row_array = 
					ACSCommunityFolder::get_new_community_put_folder_row_array($user_community_id, $form, $get_days);
			
		}

		// ���ߥ�˥ƥ�̾�����Хåե�
		$this->community_name_buffer = array();

		// ������������ν����
		$sort_folder_row_array = array();

		// �������Ѥ˲ù�(���ߥ�˥ƥ��Υե�����)
		foreach ($new_folder_row_array as $index => $new_folder_row) {
			$sort_index = $new_folder_row['update_date'] . " " .
							sprintf("%06d", $new_folder_row['file_id']) . "c";
			$sort_folder_row_array[$sort_index] = $new_folder_row;
		}

		// �������Ѥ˲ù�(�ץåȥե�����)
		// (ʣ�����ߥ�˥ƥ��ؤ�put���󤬽�ʣ���ʤ��褦counter���ղ�)
		// (��ʣ�����ߥ�˥ƥ�����쳬�ؤΥե�����Τߤ��б�����Ƥ���)
		$counter = 0;
		foreach ($new_put_folder_row_array as $index => $new_folder_row) {

			// PUT�Ǥ��뤳�ȤΥե饰������򤷤Ƥ���
			$new_folder_row['is_put_icon'] = TRUE;

			$sort_index = $new_folder_row['update_date'] . " " .
						sprintf("%06d", $new_folder_row['file_id']) . "p" . $counter;
			$sort_folder_row_array[$sort_index] = $new_folder_row;

			$counter++;
		}

		// �����ȼ»�
		krsort($sort_folder_row_array);

		// ɽ��������� //
		if ($inline_mode) {
			$display_count =
					ACSSystemConfig::get_keyword_value(ACSMsg::get_mst(
						'system_config_group','D02'), 'NEW_INFO_TOP_DISPLAY_MAX_COUNT');
		} else {
			// view�ǥڡ����󥰤���Τ��������(0=�������)
			$display_count = 0;
		}

		// ɽ���Ѥ�������
		$new_folder_row_array = array();
		foreach ($sort_folder_row_array as $key => $folder_row) {

			// ɽ�������ã���Ƥ�����Ͻ������ʤ�
			if (count($new_folder_row_array) >= $display_count && $display_count != 0) {
				break;
			}

			// put�ե�����ξ��
			if ($folder_row['is_put_icon']) {

				// �ե��������μ���
				// (��ʣ���Υե��������μ����������ͭ)
				$add_folder_row_array =&
						$this->getPutFolderRows($acs_user_info_row, $folder_row);

			// ���ߥ�˥ƥ��ե�����ξ��
			} else {
				// (ɬ�פʥե��������ϼ����Ѥ�)
				$folder_row['url_community_id'] = $folder_row['owner_community_id'];
				$folder_row['url_folder_id'] = $folder_row['folder_id'];
				$add_folder_row_array = array($folder_row);
			}

			// ɽ���Ѿ��������
			foreach ($add_folder_row_array as $add_folder_row) {

				// ɽ�������ã���Ƥ�����Ͻ������ʤ�
				if (count($new_folder_row_array) >= $display_count &&
						$display_count != 0) {
					break;
				}

				// ���ɥե饰������
				$add_folder_row['is_unread'] =
						ACSLib::get_boolean($add_folder_row['is_unread']);

				// ���ߥ�˥ƥ�̾��Хåե���󥰤��Ƥ���(�����Ѥ��뤿��)
				$this->community_name_buffer[$add_folder_row['owner_community_id']] =
						$add_folder_row['community_name'];

				// �ե�����ܺپ���URL������
				$add_folder_row['file_detail_url'] =
						$this->getControllerPath('Community', 'FileDetail') .
						'&community_id=' . $add_folder_row['url_community_id'] .
						'&folder_id=' . $add_folder_row['url_folder_id'] .
						'&file_id=' . $add_folder_row['file_id'];

				array_push($new_folder_row_array, $add_folder_row);
			}
		}

		// set
		$request->setAttribute('user_community_id', $user_community_id);
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('new_folder_row_array', $new_folder_row_array);
		//$request->setAttribute('new_put_folder_row_array', $new_put_folder_row_array);

		if ($inline_mode) {
			return View::INPUT;
		} else {
			return View::SUCCESS;
		}
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('USER_PAGE_OWNER');
	}

	/*
	 * put��Υե����������������(ʣ��put�б�)
	 */
	function & getPutFolderRows(&$acs_user_info_row, &$folder_row) {

		$put_community_id = $folder_row['put_community_id'];

		$put_folder_rows = array();

		// �����ذʾ忼�����ؤ�put�ե�����ξ��
		// (put��Υ��ߥ�˥ƥ��ե�����������Ǥ��Ƥ��ʤ�)
		// (put�褬ʣ���ξ�礢��)
		if ($put_community_id=='') {

			// �桼���ե����obj����
			$user_folder_obj = new ACSUserFolder(
					$folder_row['owner_community_id'],
					$acs_user_info_row,
					$folder_row['folder_id'] );

			// �ѥ��������
			$path_folder_obj_array = $user_folder_obj->get_path_folder_obj_array();

			// ��1���إե�����ɣļ���
			$second_folder_obj =& $path_folder_obj_array[1];

			// ��1���إե��������ץå���Υ��ߥ�˥ƥ���������(ʣ���ξ��ͭ)
			$put_community_array =& $second_folder_obj->get_put_community_row_array();
			foreach ($put_community_array as $put_community) {

				$add_folder_row = $folder_row;
				$add_folder_row['url_community_id'] = $put_community['community_id'];
				$add_folder_row['url_folder_id'] = $folder_row['folder_id'];
				$add_folder_row['community_name'] = $put_community['community_name'];

				$put_folder_rows[] = $add_folder_row;
			}


		// �������ܤ�put�ե�����ξ��
		// (put��Υ��ߥ�˥ƥ��ե�����������Ǥ��Ƥ���)
		} else {

			// �Хåե��˥��ߥ�˥ƥ�̾��̵�������䤤��碌��
			if ($this->community_name_buffer[$put_community_id]=='') {
				$community_row =& ACSCommunity::get_community_row(
						$put_community_id);
				$this->community_name_buffer[$put_community_id] =
						$community_row['community_name'];
			}

			// ɽ���Ѥ˥��ߥ�˥ƥ�̾�����ꤷ�Ƥ���
			$folder_row['community_name'] =
					$this->community_name_buffer[$put_community_id];
			$folder_row['url_community_id'] = $folder_row['put_community_id'];
			$folder_row['url_folder_id'] = $folder_row['put_community_folder_id'];

			$put_folder_rows = array($folder_row);
		}
		return $put_folder_rows;
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �ܿͤξ���OK
		if (!$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
	}

}

?>
