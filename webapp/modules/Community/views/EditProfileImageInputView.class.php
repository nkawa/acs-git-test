<?php
/**
 * �̿����åץ��ɵ�ǽ��View���饹
 * �ץ�ե�����̿��ѹ�����
 * @package  acs/webapp/modules/Communication/views
 * EditProfileImageView::INPUT
 * @author   akitsu
 * @since	PHP 4.0
 * @revision ver1.0  2006/02/16
 */

class EditProfileImageInputView extends BaseView
{
	 /**
	 * execute �᥽�å�
	 *����å������ѥå���
	 * @param object   $user			�桼������
	 * @param object   $request		 �ꥯ�����Ⱦ���
	 * @param object   $controller	  �����ɥ쥹������ȥ���
	 *
	 * @return parent::execute()		  BaseView���饹�¹�
	 */
	function execute() {
	    $context = $this->getContext();
		$controller = $context->getController();
	    $request =  $context->getRequest();
		$user = $context->getUser();
		//action���饹��������set���줿user_community_id ���������
		$user_community_id = $request->getAttribute('community_id');
		//action���饹��������set���줿target_community_info_row �������$profile�ѿ��Υ��֥�����������Ȥ���
		$profile = $request->getAttribute('target_community_info_row');

		// �����ե�����Υѥ�������
		$profile['image_url'] = ACSCommunity::get_image_url($user_community_id);
		$file_id = "";			//���������� (�����ɲäؤ��б�)
				
		// ��˥塼���� ������Ͽ�ʳ��Ϻ����˥塼��ɽ������
		$image_new_mode = $request->getAttribute('image_new_add');
		$menu = array();
		if($image_new_mode){
			$menu['delete_image_url'] = null;
		}else{
			$file_id = $profile['file_id'];		//���������� �ɲ�
			//����ΰջ׳�ǧURL ver1.3
			$delete_confirm_url = $this->getControllerPath('Community','DeleteProfileImage');
			$delete_confirm_url .= '&community_id=' . $user_community_id;
			$delete_confirm_url .= '&file_id=' . $file_id;
			$menu['delete_image_url'] = $delete_confirm_url;
		}
		$menu['image_new_mode']=$image_new_mode;

		//�����Υ��åץ���URL ver1.1
		$upload_image_url = $this->getControllerPath('Community','UploadProfileImage');
		$upload_image_url .= '&community_id=' . $user_community_id;
		$upload_image_url .= '&image_new_mode=' . $image_new_mode;	//ver1.1
		$upload_image_url .= '&file_id=' . $file_id;			//���������ѡ��ɲ�

		// ���顼��å���������
		$error_msg_array = array();
		$error_row = $request->getAttribute('error_row');
		if ($error_row) {
			foreach ($error_row as $key => $msg) {
				array_push($error_msg_array, $msg);
			}
		}

		//set

		$back_url = $request->getAttribute('back_url');
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('profile', $profile);
		$this->setAttribute('menu', $menu);
		$this->setAttribute('error_msg_array', $error_msg_array);
		$this->setAttribute('upload_image_url', $upload_image_url);

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('EditProfileImage.tpl.php');
		return parent::execute();
	}
}
?>
