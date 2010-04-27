<?php
/**
 * ��å���������Ͽ��ɽ����ǽ��action���饹
 * ��å��������󡡳�ǧ����Ͽ����
 * @package  acs/webapp/modules/User/action
 * @author   nakau
 * @since    PHP 4.0
 * @version  $Revision: 1.1 $ $Date: 2008/03/06
 */
// $Id: MessagePreAction.class.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $


class MessagePreAction extends BaseAction
{
	//field
	var $form;

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		//mode�����̤����ܤ��������
		$move_id = $request->getParameter('move_id');
		// �桼��������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// �оݤȤʤ�UserID�����
		$user_community_id = $request->getParameter('id');
		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);
		/* ���ϲ��̤�� */
		if($move_id==1){
		//��������������ۤ�Ʊ��
			// ���̾�Υե����������������
			$form['subject'] = $request->getParameter('subject');		//��̾��subject
			$form['body'] = $request->getParameter('body');				//���ơ�body
			$form['info_mail'] = $request->getParameter('info_mail');	//�᡼�����Ρ�info_mail
			$user->setAttribute('new_form_obj',$form);
			$request->setAttribute('target_user_info_row', $target_user_info_row);
		//�����������ޤǤۤ�Ʊ��
			return View::SUCCESS;

		/* ��Ͽ����ܥ���֤Ϥ��פ�� */
		}else if($move_id==2){
			$acs_user_info_row = $user->getAttribute('acs_user_info_row');
			$user_community_id = $request->getParameter('id');
		//��������������ۤ�Ʊ��
			// ���̾�Υե����������������
			$form = $user->getAttribute('new_form_obj');
			$new_file_obj = $form['file_obj'];
			$form['user_community_id'] = $user_community_id;
			$form['acs_user_info_id'] = $acs_user_info_row['user_community_id'];
		//�����������ޤǤۤ�Ʊ��
			// DB�ؤν񤭹�����
			ACSDB::_do_query("BEGIN");
			// Message�ơ��֥����
			$ret = ACSMessage::set_message($form);
			if($ret){
				ACSDB::_do_query("COMMIT");
			}else{
				ACSDB::_do_query("ROLLBACK");
			}
		
			// ���Υ᡼����������
			if ($form['info_mail'] == "on") {
				ACSMessage::send_info_mail($ret, $form['user_community_id'], $form['acs_user_info_id']);
			}

			// �񤭹��߸塢GET�ν�����
			$action_url =  $this->getControllerPath('User', 'MessageBox') . '&id=' . $acs_user_info_row['user_community_id'].'&move_id=2';
			header("Location: $action_url");
		}
	}

	function getRequestMethods() {
		return Request::POST;
	}

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$request = $context->getRequest();
		$move_id = $request->getParameter('move_id');

		// ���ϲ��̤���ξ��Τߡ����ϥ����å��򤹤�
		if ($move_id == 1) {
			/* ɬ�ܥ����å� */
			parent::regValidateName($validatorManager, 
					"subject", 
					true, 
					ACSMsg::get_msg('User', 'MessagePreAction.class.php', 'M001'));
		}
	}

	function handleError () {
		// ���ϲ���ɽ��
		return $this->execute();
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		return array('EXECUTE');
	}
	
	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �������桼�����ܿͰʳ���NG
		if ($user->hasCredential('PUBLIC_USER')
				 || !$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
	}
}

?>
