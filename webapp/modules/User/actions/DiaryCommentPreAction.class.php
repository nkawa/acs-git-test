<?php
/**
 * ��������Ͽ��ɽ����ǽ��action���饹
 * ���������Ⱦ��󡡳�ǧ����Ͽ����
 * @package  acs/webapp/modules/User/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  $Revision: 1.6 $ $Date: 2006/03/02
 */
// $Id: DiaryCommentPreAction.class.php,v 1.6 2006/11/20 08:44:25 w-ota Exp $


class DiaryCommentPreAction extends BaseAction
{
	//field
	var $form;
	
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$user_community_id = $acs_user_info_row['user_community_id'];
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);
		// �оݤȤʤ�UserID�����
		$user_id = $request->getParameter('user_id');
		// Diary����
		$diary_row_array = $request->getAttribute('diary_row_array');

		// set
		$request->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('diary_row_array', $diary_row_array);

		return View::SUCCESS;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		//mode�����̤����ܤ��������
		$move_id = $request->getParameter('move_id');
		// �桼��������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		/* ���ϲ��̤�� */
	   if($move_id==1){
			//��������������ۤ�Ʊ��
			// ���̾�Υե����������������
			$form['body'] = $request->getParameter('body');												//���ơ�body
			$form['user_community_id'] = $acs_user_info_row['user_community_id'];

			$user->setAttribute('new_form_obj',$form);
			$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($form['user_community_id']);
			$request->setAttribute('target_user_info_row', $target_user_info_row);
			//�����������ޤǤۤ�Ʊ��
			return View::SUCCESS;

		/* ��Ͽ����ܥ���֤Ϥ��פ�� */
		} else if($move_id==2) {
			$user_id = $request->getParameter('id');
			// �оݤ�diary_id�����
			$diary_id = $request->getParameter('diary_id');
			//��������������ۤ�Ʊ��
			// ���̾�Υե����������������
			$form = $user->getAttribute('new_form_obj');
			$form['diary_id'] = $diary_id;
			//�����������ޤǤۤ�Ʊ��
			// DB�ؤν񤭹�����
			ACSDB::_do_query("BEGIN");
			//DiaryComment�ơ��֥����
			$ret = ACSDiary::set_diary_comment($form);
			if(!$ret){
				ACSDB::_do_query("ROLLBACK");
				echo "ERROR: Insert dairy comment failed.";
				return;
			}
			ACSDB::_do_query("COMMIT");
			// �񤭹��߸塢GET�ν�����
			$diary_comment_top_page_url = $this->getControllerPath('User', 'DiaryComment') . '&id=' . $acs_user_info_row['user_community_id'] . '&diary_id=' . $diary_id .'&move_id=4';
			header("Location: $diary_comment_top_page_url");
		}
	}

	function isSecure () {
		return false;
	}

	function getRequestMethods() {
		return Request::POST;
	}	

	function validate () {
		return TRUE;
	}

	function registerValidators (&$validatorManager) {
		$context = $this->getContext();
		$request =  $context->getRequest();
		$move_id = $request->getParameter('move_id');

		// ���ϲ��̤���ξ�硢���ϥ����å�
		if ($move_id == 1){
			parent::regValidateName($validatorManager, 
					"body", 
					true, 
					ACSMsg::get_msg('User', 'DiaryCommentPreAction.class.php', 'M001'));
		}
	}

	function handleError () {
		// ���ϲ���ɽ��
		return $this->getDefaultView();
	}

	function getCredential() {
		return array('EXECUTE');
	}

	function get_execute_privilege (&$controller, &$request, &$user) {

		// �����ϰϾ������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($request->getParameter('id'));
		$diary_row = ACSDiary::get_diary_row($request->ACSgetParameter('diary_id'));
		if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
			$diary_row['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
		}

		// ������������Ƚ��
		$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
		$ret = ACSAccessControl::is_valid_user_for_user_community($acs_user_info_row, $role_array, $diary_row);

		return $ret;
	}
}

?>
