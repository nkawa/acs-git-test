<?php
/**
 * ��å�������ǽ��View���饹
 * @package  acs/webapp/modules/User/views
 * MessageBoxView::INPUT
 * @author   nakau
 * @since	PHP 4.0
 */
// $Id: MessageBoxView::INPUT.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $


class MessageBoxInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$message_row_array = $request->getAttribute('message_row_array');
		$move_id = $request->getAttribute('move_id');

		// ��˥塼��URL
		$menu['receiv_box_url'] = $this->getControllerPath("User", 'MessageBox') . '&id=' . $target_user_info_row['user_community_id'];
		$menu['send_box_url'] = $this->getControllerPath("User", 'MessageBox') . '&id=' . $target_user_info_row['user_community_id'] .'&move_id=2';

		// �ù�
		foreach ($message_row_array as $index => $message_row) {
			// �������
			$message_row_array[$index]['post_date'] = ACSLib::convert_pg_date_to_str($message_row['post_date']);
			// ������� (��ά��: M/D)
			$message_row_array[$index]['short_post_date'] = ACSLib::convert_pg_date_to_str($message_row['post_date']);
			//$message_row_array[$index]['short_post_date'] = gmdate("n/j", strtotime($message_row['post_date']) + 9*60*60);
			if($move_id == 2){
				// �����ѥ�å������ܺ٥ڡ���URL
				$message_row_array[$index]['message_show_url'] = $this->getControllerPath("User",  'MessageShow') . '&id=' . $target_user_info_row['community_id'] . '&message_id=' . $message_row['message_id'] . '&move_id=2';
				// �������URL
				$message_delete_url = $this->getControllerPath("User",  'DeleteMessage') . '&id=' . $target_user_info_row['user_community_id'] . '&action_type=confirm&move_id=2';
			} else {
				// ������å������ܺ٥ڡ���URL
				$message_row_array[$index]['message_show_url'] = $this->getControllerPath("User",  'MessageShow') . '&id=' . $target_user_info_row['community_id'] . '&message_id=' . $message_row['message_id'];
				// �������URL
				$message_delete_url = $this->getControllerPath("User",  'DeleteMessage') . '&id=' . $target_user_info_row['user_community_id']. '&action_type=confirm' ;
			}
		}
		
		// �ڡ���������
		$display_count = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $message_row_array, $display_count);

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('menu', $menu);
		$this->setAttribute('message_row_array', $message_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('move_id', $move_id);
		$this->setAttribute('message_delete_url', $message_delete_url);
		
		// ���顼��å�����
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('MessageBox.tpl.php');
		
		return parent::execute();
	}
}

?>
