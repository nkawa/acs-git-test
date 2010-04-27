<?php
/**
 * �Ǽ��ġ���Ƶ�ǽ��action���饹
 * �ֿ���ƾ��󡡳�ǧ����Ͽ����
 * @package  acs/webapp/modules/Community/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  $Revision: 1.7 $ $Date: 2006/02/28
 */
// $Id: BBSResPreAction.class.php,v 1.7 2006/12/19 10:17:26 w-ota Exp $


class BBSResPreAction extends BaseAction
{
	//field
	var $form;
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->getParameter('community_id');
		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_row($community_id);
		// �����ϰ�
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D42'));

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);

		return View::SUCCESS;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
		
		//mode�����̤����ܤ��������
		$move_id = $request->getParameter('move_id');
		// �����ϰ�
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D42'));
		// �桼��������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->getParameter('community_id');
		$bbs_id = $request->getParameter('bbs_id');

		/* ���ϲ��̤�� */
		if($move_id==1){
			// ���̾�Υե����������������
			$form['subject'] = $request->getParameter('subject');//��̾��subject
			// ��ʸ
			$form['body'] = $request->getParameter('body');		//���ơ�body
	
			$form['community_id'] = $community_id;				//�����ߥ�˥ƥ���ID
			$form['user_community_id'] = $acs_user_info_row['user_community_id']; 		// ��Ƽ�print "form_row:";
			$form['bbs_id'] = $bbs_id; 		// ��Ƽ�print "form_row:";
			
			$user->setAttribute('new_form_obj',$form);
			return View::SUCCESS;

		/* ��Ͽ����ܥ���֤Ϥ��פ�� */
		}else if($move_id==2){
			// ���̾�Υե����������������
			$form = $user->getAttribute('new_form_obj');
			// DB�ؤν񤭹�����
			$ret = ACSBBS::set_bbs_res($form);

			if($ret){
		
				// �Ǽ��ĥ�����������ص�Ͽ
				ACSBBS::set_bbs_access_history(
						$acs_user_info_row['user_community_id'], $form['bbs_id']);
		
				// bbs�������
				$bbs_row = ACSBBS::get_bbs_row($request->getParameter('bbs_id'));
		
				// ML���ץ���󤢤�ξ��
				if ($bbs_row['ml_send_flag']=='t') {

					// ML���ơ������μ���
					$ml_status_row = ACSCommunity::get_contents_row(
					$community_id, ACSMsg::get_mst('contents_type_master','D62'));
					$ml_status = $ml_status_row['contents_value'];
			
					// MLͭ��ξ��᡼�������
					if ($ml_status == 'ACTIVE') {
		
						// "Re:"�κ��
						$subject_msg = mb_ereg_replace( 
								ACS_COMMUNITY_ML_SUBJECT_PREFIX_CLEAR_REGEX,
								'', $form['subject']);
		
						// ��̾�Խ�
						$subject = "Re: ".str_replace('{BBSID}', $bbs_id,
								ACS_COMMUNITY_ML_SUBJECT_FORMAT) . $subject_msg;
			
						// ML����
						ACSCommunityMail::send_community_mailing_list(
								$community_id, $acs_user_info_row['mail_addr'],
								$subject, $form['body']);
					}
				}
		
			} else {
				echo ACSMsg::get_msg('Community', 'BBSResPreAction.class.php', 'M001');
			}
		
			// �񤭹��߸塢BBS Top ɽ���ν�����
			$action_url  = $this->getControllerPath('Community', 'BBS'). '&community_id=' . $community_id. '&move_id=4';
			header("Location: $action_url");
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
					"subject", 
					true, 
					ACSMsg::get_msg('Community', 'BBSResPreAction.class.php', 'M002'));
			parent::regValidateName($validatorManager, 
					"body", 
					true, 
					ACSMsg::get_msg('Community', 'BBSResPreAction.class.php', 'M003'));
		}
	}

	function handleError () {
		// ���ϲ���ɽ��
		return $this->execute();
	}

	function getCredential () {
		return array('EXECUTE');
	}


	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// ���ߥ�˥ƥ����Ф�OK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			return true;
		}
		return false;
	}
}

?>
