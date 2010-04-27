<?php
/**
 * ��å����� ���
 *
 * @author  nakau
 * @version $Revision: 1.1 $ $Date: 2008/03/24 07:09:27 $
 */
class DeleteMessageSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$target_message_id = $request->getAttribute('target_message_id');

		// ��å������ν�ͭ��
		$target_user_community_id   = $target_user_info_row['user_community_id'];

		$target_user_info = '&id=' . $target_user_community_id;

		$action_url = "";
		$action_url  = $this->getControllerPath('User', 'DeleteMessage');
		$action_url .= $target_user_info;
		$action_url .= "&action_type=delete";
		
		$move_id = $request->getAttribute('move_id');
		if($move_id == 2){
			$action_url .= "&move_id=2";
		}

		// ����оݥ�å�����
		$message_id_array = array();
		foreach ($target_message_id as $message) {
			$_message_row['message_id'] = $message;
			array_push($message_id_array, $_message_row);
		}
		
		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('DeleteMessage.tpl.php');
		
		// set
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('message_id_array', $message_id_array);

		return parent::execute();
	}
}
?>
