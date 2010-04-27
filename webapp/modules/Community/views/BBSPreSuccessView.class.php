<?php
/**
 * �Ǽ��ġ���Ƶ�ǽ��View���饹
 * ��ƾ��󡡳�ǧ����Ͽ����
 * @package  acs/webapp/modules/Community/views
 * @author   akitsu
 * @since    PHP 4.0
 * @version  $Revision: 1.5 $ $Date: 2006/02/21
 */
// $Id: BBSPreConfirmView.class.php,v 1.5 2006/12/18 07:42:13 w-ota Exp $


class BBSPreSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		//get
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->getParameter('community_id');
		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_row($community_id);
		// �ե��������
		$new_file_obj = $user->getAttribute('new_file_obj');
		$new_file_info = $user->getAttribute('new_file_info');
		//�桼�����Ͼ���
		$form = $user->getAttribute('new_form_obj');

		// ���ߥ�˥ƥ����Ф��ɤ���
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_row['community_id']);
		// form action
		$action_url  = $this->getControllerPath('Community', 'BBSPre') . '&community_id=' . $community_row['community_id'] . '&move_id=2';
		//$action_url  = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_row['community_id'] ';
		$back_url = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_row['community_id']. '&move_id=3';

		// ���ߥ�˥ƥ��ȥåץڡ�����URL
		$community_top_page_url = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $community_row['community_id'];

		// ����Ѥߥ��ߥ�˥ƥ�����ڡ�����URL
		$select_trusted_community_url = $this->getControllerPath('Community', 'SelectTrustedCommunity') . '&form_name=bbs_form';

		// �ù�
			//�ե�����β���URL
			$form['file_url'] = "";
			if($form['file_name'] != ""){
				$file_name = $form['file_name'];
				$type = $new_file_obj['type'];
				$form['file_url_alink'] =  $this->getControllerPath('Community', 'BBSPreImage') . '&type=' . $type ."&new_file_info=" . $new_file_info;
			}

			// �ѥ֥�å���꡼�� �Ǻܽ�λ��
			if($form['expire_date'] != ""){
				$form['expire_date'] = ACSLib::convert_pg_date_to_str($form['expire_date'],true,false,false);
			}

		// ML���������å�ɽ������
		$ml_status_row =
			ACSCommunity::get_contents_row(
					$community_row['community_id'], 
					ACSMsg::get_mst('contents_type_master','D62'));
		if($ml_status_row['contents_value'] == 'ACTIVE') {
			$this->setAttribute('is_ml_active', TRUE);
			$this->setAttribute('is_ml_send', $form['is_ml_send']);
		}

		// set
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('select_trusted_community_url', $select_trusted_community_url);
		$this->setAttribute('form', $form);
		$this->setAttribute('new_file_obj', $new_file_obj);
		$this->setAttribute('new_file_info', $new_file_info);
		$this->setAttribute('community_row', $community_row);
		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('BBSPre.tpl.php');
		// ���顼��å�����
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));
		
		return parent::execute();
	}
}

?>
