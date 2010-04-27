<?php
/**
 * ��å���������Ͽ��ɽ����ǽ��View���饹
 * ��å��������󡡳�ǧ����Ͽ����
 * @package  acs/webapp/modules/User/views
 * @author   nakau
 * @since    PHP 4.0
 * @version  $Revision: 1.1 $ $Date: 2008/03/06
 */
// $Id: MessagePreView_confirm.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $


class MessagePreSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		//get
		// �оݤȤʤ�UserID�����
		$user_id = $request->getParameter('id');
		//�桼�����Ͼ���
		$form = $user->getAttribute('new_form_obj');
		$target_user_info_row = $request->getAttribute('target_user_info_row');

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// form action
		$action_url  = $this->getControllerPath('User', 'MessagePre') .'&id=' .$user_id .'&move_id=2';
		$back_url = $this->getControllerPath('User', 'Message') . '&id=' .$user_id .'&move_id=3';

		// Message�ȥåץڡ�����URL
		$message_top_page_url = $this->getControllerPath('User', 'Message') . '&id=' .$user_id;

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('message_top_page_url', $message_top_page_url);
		$this->setAttribute('form', $form);

		// ���顼��å�����
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// �ƥ�ץ졼��
		$this->setTemplate('MessagePre.tpl.php');

		$this->setScreenId("0001");
		return parent::execute();
	}
}

?>
