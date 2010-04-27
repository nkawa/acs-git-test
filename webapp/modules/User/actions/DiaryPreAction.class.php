<?php
/**
 * ��������Ͽ��ɽ����ǽ��action���饹
 * �������󡡳�ǧ����Ͽ����
 * @package  acs/webapp/modules/User/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  $Revision: 1.8 $ $Date: 2006/03/01
 */
// $Id: DiaryPreAction.class.php,v 1.8 2006/12/08 05:06:41 w-ota Exp $


class DiaryPreAction extends BaseAction
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
		// �оݤȤʤ�UserID�����
		$user_community_id = $request->getParameter('id');
		// Diary����
		$diary_row_array = $request->getAttribute('diary_row_array');
		// �����ϰ�
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D21'));
		// �ޥ��ե�󥺥��롼��
		$friends_group_row_array = ACSUser::get_friends_group_row_array($user_community_id);

		// set
		$request->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('diary_row_array', $diary_row_array);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$request->setAttribute('friends_group_row_array', $friends_group_row_array);

		//return VIEW_CONFIRM;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		//mode�����̤����ܤ��������
		$move_id = $request->getParameter('move_id');
		// �����ϰ�
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D21'));
		// �桼��������
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// �оݤȤʤ�UserID�����
		$user_community_id = $request->getParameter('id');

		/* ���ϲ��̤�� */
		if($move_id==1){
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
			$form['trusted_community_flag'] =  $request->getParameter('trusted_community_flag');					//�ޥ��ե�󥺥��롼�פ���ꤷ�����
			$form['trusted_community_id_array'] =  $request->getParameter('trusted_community_id_array');	//���򤷤����롼�׷�
			
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
					echo "Read file failed.\n";
				}
				$_FILES['new_file']['tmp_name'] = $upload;
				$form['file_obj'] = $_FILES['new_file'];
				$user->setAttribute('new_file_info',$upload);
				$user->setAttribute('new_file_obj',$_FILES['new_file']);
			}

			$form['user_community_id'] = $user_community_id;
			$user->setAttribute('new_form_obj',$form);
			
			//�����������ޤǤۤ�Ʊ��
			return View::SUCCESS;

		/* ��Ͽ����ܥ���֤Ϥ��פ�� */
		} else if($move_id==2) {
			$user_community_id = $request->getParameter('id');
			//��������������ۤ�Ʊ��
			// ���̾�Υե����������������
			$form = $user->getAttribute('new_form_obj');
			$new_file_obj = $form['file_obj'];

			// �����������ޤǤۤ�Ʊ��
			// DB�ؤν񤭹�����
			ACSDB::_do_query("BEGIN");
			if($form['file_name'] != ""){	//�ե�������󤬤��ä����
				//1.�ե�����������(����)
				$file_obj = ACSFile::get_upload_file_info_instance($user->getAttribute('new_file_obj'),$user_community_id,$form['user_community_id']);
				if($file_obj){
					 //form�������Ͽ
					 $form['new_file'] = $file_obj;
				}else{
					echo "Create file failed.";
					return;
				}
			}
			//2.Diary�ơ��֥����
			$ret = ACSDiary::set_Diary($form);
			if($ret){
				ACSDB::_do_query("COMMIT");
			}else{
				ACSDB::_do_query("ROLLBACK");
			}

			// �񤭹��߸塢GET�ν�����
			$diary_top_page_url =  $this->getControllerPath('User', 'Diary') . '&id=' . $user_community_id;
			header("Location: $diary_top_page_url");
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
		$request =  $context->getRequest();
		$move_id = $request->getParameter('move_id');

		// ���ϲ��̤���ξ��Τߡ����ϥ����å��򤹤�
		if ($move_id == 1) {
			/* ɬ�ܥ����å� */
			parent::regValidateName($validatorManager, 
					"subject", 
					true, 
					ACSMsg::get_msg('User', 'DiaryPreAction.class.php', 'M001'));
			parent::regValidateName($validatorManager, 
					"body", 
					true, 
					ACSMsg::get_msg('User', 'DiaryPreAction.class.php', 'M002'));
			parent::regValidateName($validatorManager, 
					"open_level_code", 
					true, 
					ACSMsg::get_msg('User', 'DiaryPreAction.class.php', 'M003'));
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
		return array('USER_PAGE_OWNER');
	}
}

?>
