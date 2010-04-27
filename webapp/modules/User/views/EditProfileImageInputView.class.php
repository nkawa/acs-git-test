<?php
/**
 * �̿����åץ��ɵ�ǽ��View���饹
 * �ץ�ե�����̿��ѹ�����
 * @package  acs/webapp/modules/User/views
 * EditProfileImageView::INPUT
 * @author   akitsu
 * @since	PHP 4.0
 * @revision ver1.5 $Date: 2008/03/24 07:00:36 $
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
	 * @return parent::execute($controller, $request, $user)		  BaseView���饹�¹�
	 */
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		//action���饹��������set���줿user_community_id ���������
		$user_community_id = $request->getAttribute('user_community_id');
		//action���饹��������set���줿target_user_info_row �������$profile�ѿ��Υ��֥�����������Ȥ���
		$profile = $request->getAttribute('target_user_info_row');

		$image_new_mode = $request->getAttribute('image_new_add');
		$image_file_label = $request->getAttribute('image_file_label');
		$open_level_code_row = $request->getAttribute('open_level_code_row');
		$display_for_public = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D08'), 'DISPLAY_PHOTOS_FOR_PUBLIC');		
		if ($display_for_public == NULL) {
			$display_for_public = "0";
		}
				
		// �����ե�����Υѥ�������
		$image_file_array = ACSUser::get_image_url_with_open_level(
				$user_community_id, $open_level_code_row);
//		$profile['image_url'] = ACSUser::get_image_url($user_community_id);

		$file_id = "";			//���������ѡ��ɲ� ver1.2 2006/2/13 (�����ɲäؤ��б�)

		// ��˥塼���� ������Ͽ�ʳ��Ϻ����˥塼��ɽ������
		$menu = array();
		for ($i = 0; $i < count($open_level_code_row); $i++) {
			$key_name = 'file_id_ol' . $open_level_code_row[$i];
			if ($image_new_mode[$key_name]) {
				$menu['delete_image_url' . $open_level_code_row[$i]] = null;
			} else {
	            $file_id = $profile[$key_name];
				$delete_confirm_url = 
						$this->getControllerPath(
								'User','DeleteProfileImage');
				$delete_confirm_url .= '&id=' . $user_community_id;
				$delete_confirm_url .= '&file_id=' . $file_id;
				$delete_confirm_url .= '&open_level_code=' . $open_level_code_row[$i];
				$menu['delete_image_url' . $open_level_code_row[$i]]
					= $delete_confirm_url;
			}
			$menu['image_new_mode' . $open_level_code_row[$i]] 
					= $image_new_mode[$key_name];

			//�����Υ��åץ���URL ver1.1
			$upload_image_url[$key_name] = $this->getControllerPath(
							'User','UploadProfileImage');
			$upload_image_url[$key_name] .= '&id=' . $user_community_id;
			$upload_image_url[$key_name] .= '&image_new_mode=' . $image_new_mode[$key_name];
			$upload_image_url[$key_name] .= '&file_id=' . $file_id;	
			$upload_image_url[$key_name] .= '&open_level_code=' . $open_level_code_row[$i];
		}

		// ���顼��å���������
		$error_msg_array = array();
		$error_row = $request->getAttribute('error_row');
		if ($error_row) {
			foreach ($error_row as $key => $msg) {
				array_push($error_msg_array, $msg);
			}
		}

		//set
		$this->setAttribute('image_file_array', $image_file_array);
		$this->setAttribute('display_for_public', $display_for_public);
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
