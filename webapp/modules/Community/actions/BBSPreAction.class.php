<?php
/**
 * �Ǽ��ġ���Ƶ�ǽ��action���饹
 * ��ƾ��󡡳�ǧ����Ͽ����
 * @package  acs/webapp/modules/Community/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  $Revision: 1.12 $ $Date: 2006/12/19 10:17:26 $
 */
// $Id: BBSPreAction.class.php,v 1.12 2006/12/19 10:17:26 w-ota Exp $
class BBSPreAction extends BaseAction
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
		//���顼�ν����
		

/* ���ϲ��̤�� */
	if($move_id==1){
	$err = 'OK';		//���顼�ͤν����
//��������������ۤ�Ʊ��
		// ���̾�Υե����������������
		$form['subject'] = $request->getParameter('subject');//��̾��subject
		$form['body'] = $request->getParameter('body');												//���ơ�body
		$form['open_level_code'] = $request->getParameter('open_level_code');	//�����ϰϥ����ɡ�open_level_code
			foreach ($open_level_master_row_array as $open_level_master_row) {
				if($open_level_master_row['open_level_code'] == $form['open_level_code']){
					$form['open_level_name'] = htmlspecialchars($open_level_master_row['open_level_name']) ;//�����ϰ�ɽ��̾��open_level_name
				}
			}
		$form['new_file'] = $request->getParameter('new_file');								//������new_file
		//�ե��������Τ���ʤ�������å����Ƥ���
		if (!ini_get('mbstring.encoding_translation')) {
			$form['file_name'] = mb_convert_encoding($_FILES['new_file']['name'], mb_internal_encoding(), mb_http_output());
		} else {
			$form['file_name'] = $_FILES['new_file']['name'];
		}

		if($form['file_name'] != ''){
			/* �ǥ��쥯�ȥ�¸�ߥ����å� */
			// �ʤ����Ϻ�������
			$to_dir  = ACS_TEMPORARY_FILE_DIR;
			if(!file_exists($to_dir)) {mkdir($to_dir); chmod($to_dir, 0777);}
		//�ե����뤬�����硢���֤��������ꤹ��
			$_FILES['new_file']['upload_tmp_dir'] = ACS_TEMPORARY_FILE_DIR;
			//���֤��Υե�����̾�����ꤹ��
			$type_name = session_id();
			$upload = $_FILES['new_file']['upload_tmp_dir'];
			$upload .= $type_name;
			if ( !move_uploaded_file( $_FILES['new_file']['tmp_name'], $upload ) ) {
				echo "�ե�������ɤ߹��ߤ˼��Ԥ��ޤ���\n";
			}
			$_FILES['new_file']['tmp_name'] = $upload;
			$form['file_obj'] = $_FILES['new_file'];
			$user->setAttribute('new_file_info',$upload);
			$user->setAttribute('new_file_obj',$_FILES['new_file']);
		}
		$form['xdate'] = $request->getParameter('xdate');											//�Ǻܺǽ�����xdate
		//�Ǻܺǽ����ǥե������
		if($form['xdate'] == ''){
			$form[xdate] ='';
		}
		//�������ĥ��ߥ�˥ƥ�
		$form['trusted_community_id_array'] = $request->getParameter('trusted_community_id_array');
		$form['trusted_community_row_array'] = ACSCommunity::get_each_community_row_array($form['trusted_community_id_array']);

		$form['community_id'] = $community_id;																		//�����ߥ�˥ƥ���ID
		$form['user_community_id'] = $acs_user_info_row['user_community_id']; 		// ��Ƽ�print "form_row:";

		// ML�������ץ����
		$form['is_ml_send'] = $request->getParameter('is_ml_send');								//������new_file
//�����������ޤǤۤ�Ʊ��
		$user->setAttribute('new_form_obj',$form);

		return View::SUCCESS;





/* ��Ͽ����ܥ���֤Ϥ��פ�� */
	}else if($move_id==2){
//��������������ۤ�Ʊ
		// ���̾�Υե����������������
		$form = $user->getAttribute('new_form_obj');
		$new_file_obj = $form['file_obj'];
//�����������ޤǤۤ�Ʊ��
		// DB�ؤν񤭹�����
		ACSDB::_do_query("BEGIN");
		if($form['file_name'] != ""){	//�ե�������󤬤��ä����
		//1.�ե�����������(����)
			$file_obj = ACSFile::get_upload_file_info_instance($user->getAttribute('new_file_obj'),$community_id,$form['user_community_id']);
			//form�������Ͽ
			$form['new_file'] = $file_obj;
		}
		//2.bbs�ơ��֥����
		$ret = ACSBBS::set_bbs($form);
		if($ret){
			ACSDB::_do_query("COMMIT");
			// �Ǽ��ĥ�����������
			ACSBBS::set_bbs_access_history($acs_user_info_row['user_community_id'], $ret);
		}else{
			ACSDB::_do_query("ROLLBACK");
		}
		$bbs_id_seq = $ret;

		// ML���ץ���󤢤�ξ��
		if ($form['is_ml_send']=='t') {

			// ML���ơ������μ���
			$ml_status_row = ACSCommunity::get_contents_row(
					$community_id, ACSMsg::get_mst('contents_type_master','D62'));
			$ml_status = $ml_status_row['contents_value'];

			// MLͭ��ξ��᡼�������
			if ($bbs_id_seq && $ml_status == 'ACTIVE') {

				// ��̾�Խ�
				$subject = str_replace('{BBSID}',
						$bbs_id_seq,ACS_COMMUNITY_ML_SUBJECT_FORMAT) . $form['subject'];

				// ML����
				ACSCommunityMail::send_community_mailing_list(
						$community_id, 
						$acs_user_info_row['mail_addr'],
						$subject, 
						$form['body']);
			}
		}

		$action_url  = $this->getControllerPath('Community', 'BBS'). '&community_id=' . $community_id. '&move_id=4';

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
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$move_id = $request->getParameter('move_id');

		// ���ϲ��̤���ξ��Τߡ����ϥ����å��򤹤�
		if ($move_id == 1) {
			/* ɬ�ܥ����å� */
			parent::regValidateName($validatorManager, 
					"subject", 
					true, 
					ACSMsg::get_msg('Community', 'BBSPreAction.class.php', 'M001'));
			parent::regValidateName($validatorManager, 
					"body", 
					true, 
					ACSMsg::get_msg('Community', 'BBSPreAction.class.php', 'M002'));
			parent::regValidateName($validatorManager, 
					"open_level_code", 
					true, 
					ACSMsg::get_msg('Community', 'BBSPreAction.class.php', 'M003'));

			/* ���ե����å� */
			// �Ǻܽ�λ��
			$xdate = $request->getParameter('xdate');
			if ($xdate) {
				$validator =& new DateValidator($controller);
				$criteria = array('date_error' => ACSMsg::get_msg('Community', 'BBSPreAction.class.php', 'M004'));
				$validator->initialize($criteria);
				//$validatorManager->register('xdate', $validator);
				$validatorManager->registerValidator('xdate', $validator);
			}
		}
	}

	function handleError () {
		return $this->execute();
	}
	
	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_MEMBER');
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
