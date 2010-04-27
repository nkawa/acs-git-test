<?php
/**
 * �Ǽ��ġ���Ƶ�ǽ��View���饹
 * �ֿ���ƾ��󡡳�ǧ����Ͽ����
 * @package  acs/webapp/modules/Community/views
 * @author   akitsu
 * @since    PHP 4.0
 * @version  $Revision: 1.4 $ $Date: 2006/02/29
 */
// $Id: BBSResPreConfirmView.class.php,v 1.4 2006/03/29 08:53:05 kuwayama Exp $


class BBSResPreSuccessView extends BaseView
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
		//�桼�����Ͼ���
		$form = $user->getAttribute('new_form_obj');
		$bbs_id = $request->getParameter('bbs_id');

		// ���ߥ�˥ƥ����Ф��ɤ���
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_row['community_id']);
		// form action
		$action_url  = $this->getControllerPath('Community', 'BBSResPre') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_id . '&move_id=2';
		// action URL ��ǧ���̤Υ���󥻥�ܥ���
		$back_url  = $this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_id . '&move_id=3';

		// ���ߥ�˥ƥ��ȥåץڡ�����URL
		$community_top_page_url = $this->getControllerPath('Community', 'Index') . '&community_id=' . $community_row['community_id'];
		//bbs_top_page_url �Ǽ���TOP����
		$back_bbs_url = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_row['community_id'];
	
		// set
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('back_bbs_url', $back_bbs_url);
		$this->setAttribute('select_trusted_community_url', $select_trusted_community_url);
		$this->setAttribute('form', $form);
		$this->setAttribute('community_row', $community_row);

		// ���顼��å�����
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('BBSResPre.tpl.php');

		return parent::execute();
	}
}

?>
