<?php
// $Id: BaseView.class.php,v 1.15 2008/05/21 01:53:36 y-yuki Exp $

class SimpleBaseView extends SimpleViewEx
{
	protected $inlineFlg = false;
	protected $script_path = "index.php";

	/**
	 * execute
	 */
	function execute() {

		$context = &$this->getContext();
		$request = &$context->getRequest();
		$moduleName = $context->getModuleName();
		$actionName = $context->getActionName();
		$controller = $context->getController();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ��������Υ桼������
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);

		// ������桼�����ɤ���
		$this->setAttribute('is_login_user', $acs_user_info_row['is_login_user']);
	}
	
	function getControllerPath($module="", $action="") {
		$index = "index.php?";
		$moduleNm = "module=";
		$actionNm = "&action=";
		if ($module == "" && $action == "") {
			return $index;
		}
		return $index . $moduleNm . $module . $actionNm . $action;

	}

	/**
	 * ���顼��å���������
	 *
	 * @param  &$controller
	 * @param  &$request
	 * @param  &$target_array
	 */
	function getErrorMessage (&$controller, &$request, &$user) {
		$error_msg_array = array();
		$error_row = $user->getAttribute('error_row');
		if (!is_array($error_row)) {
			$error_row = array();
		}

		// request �Υ��顼�ȥ��å����˥��åȤ���Ƥ��륨�顼���������
		$error_row = array_merge($error_row, $request->getErrors());
		if ($error_row) {
			foreach ($error_row as $key => $msg) {
				array_push($error_msg_array, $msg);
			}
		}
		$user->removeAttribute('error_row');
		return $error_msg_array;
	}

	/**
	 * �ڡ����󥰾�������
	 * ������ $target_array ��ɽ������ʬ�����˺�ꡢ�ڡ����󥰾�����������
	 *
	 * @param  &$controller
	 * @param  &$request
	 * @param  &$target_array
	 * @param  $display_max_count
	 *
	 * @return �ڡ������ϤΤ���� row_array
	 */
	function getPagingInfo (&$controller, &$request, &$target_array, $display_max_count) {
		$params = $request->ACSGetParameters();

		// �ڡ����󥰾���
		$paging_info = array();

		// ɽ����������
		$all_count = count($target_array);
		if ($all_count <= $display_max_count) {
			// �ڡ����󥰤�ɬ�פʤ�
			$paging_info['all_count'] = $all_count;
			if ($paging_info['all_count'] > 0) {
				$paging_info['start_count'] = 1;
			} else {
				$paging_info['start_count'] = 0;
			}
			$paging_info['end_count'] = $all_count;
			return $paging_info;
		}

		// ɽ���ڡ�������
		if ($params['page'] > 0) {
			$display_page = $params['page'];
		} else {
			// �����
			$display_page = 1;
		}

		/*--------------------------*/
		/* ɽ������ǡ��������˺�� */
		/*--------------------------*/
		// ɽ���оݤȤʤ�ǡ����γ��ϰ���
		$display_start_position = $display_max_count * ($display_page - 1);

		// ɽ���оݥǡ����Τߤˤ���
		$target_array = array_slice($target_array, $display_start_position, $display_max_count);

		/*----------------------*/
		/* �ڡ����Υ�󥯤���� */
		/*----------------------*/
		$paging_row_array = array();
		// �ڡ�����
		$all_page_count = ceil($all_count / $display_max_count);
		for ($page_count = 1; $page_count <= $all_page_count; $page_count++) {
			// �ڡ�������URL ���󥳡��ǥ��󥰤����͡�
			$params['page'] = $page_count;

			// �����URL
			if ($page_count != $display_page) {
				$link_url = $this->genURL($params);
			} else {
				// ɽ������ڡ����ˤϥ�󥯤�Ϥ�ʤ�
				$link_url = "";
			}

			// set
			$paging_row = array();
			$page_row['page_number'] = $page_count;
			$page_row['link_url'] = $link_url;

			array_push($paging_row_array, $page_row);
		}

		// �ڡ����󥰾��󥻥å� //
		// ����
		$paging_info['all_count'] = $all_count;
		// XX-YY
		$paging_info['start_count'] = $display_start_position + 1;
		$paging_info['end_count'] = $display_start_position + $display_max_count;
		if ($paging_info['end_count'] > $all_count) {
			$paging_info['end_count'] = $all_count;
		}
		// ���ء�����
		if ($display_page > 1) {
			$paging_info['prev_link'] = $paging_row_array[($display_page - 1) - 1]['link_url'];
		} else {
			$paging_info['prev_link'] = '';
		}
		if ($display_page < $all_page_count) {
			$paging_info['next_link'] = $paging_row_array[($display_page - 1) + 1]['link_url'];
		} else {
			$paging_info['next_link'] = '';
		}
		// �ڡ����󥰥��
		$paging_info['paging_row_array'] = $paging_row_array;

		/*--------------------------------*/
		/* �ƥ�ץ졼�Ȥǽ����Ѥ��ͤ��֤� */
		/*--------------------------------*/
		return $paging_info;
	}

	// �����URL�μ�ư�����ʥڡ��������¿�ѡ�
	// mojavi2�ΰܿ�
	function genURL ($params)
	{
		$url = $script_path;
		$divider  = '&';
		$equals   = '=';
		$url     .= '?';

		$keys  = array_keys($params);
		$count = sizeof($keys);
		for ($i = 0; $i < $count; $i++)
		{
			if ($i > 0)
			{
				$url .= $divider;
			}
			$url .= rawurlencode(mb_convert_encoding($keys[$i], mb_http_output())) . $equals .
				rawurlencode(mb_convert_encoding($params[$keys[$i]], mb_http_output()));
		}
		return $url;
	}

}

?>
