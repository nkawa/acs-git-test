<?php
/**
 * ������ǽ��View���饹
 * ���Ͼ��󡡳�ǧ����Ͽ����
 * @package  acs/webapp/modules/User/views
 * @author   akitsu
 * @since    PHP 4.0
 * @version  $Revision: 1.6 $ $Date: 2006/03/02
 */
// $Id: DiaryCommentPreView_confirm.class.php,v 1.6 2006/03/29 08:08:42 kuwayama Exp $


class DiaryCommentPreSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		//get
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// �оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->getParameter('id');

		// �ܿͤΥڡ������ɤ���
		if ($community_id == $acs_user_info_row['user_community_id']) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_row($community_id);
		//�桼�����Ͼ���
		$form = $user->getAttribute('new_form_obj');
		$diary_id = $request->getParameter('diary_id');

		// form action
		$action_url  = $this->getControllerPath('User', 'DiaryCommentPre') . '&id=' . $community_id . '&diary_id=' . $diary_id . '&move_id=2';
		// action URL ��ǧ���̤Υ���󥻥�ܥ���
		$back_url  = $this->getControllerPath('User', 'DiaryComment') . '&id=' . $community_id . '&diary_id=' . $diary_id . '&move_id=3';

		// �������꡼�ȥåץڡ�����URL
		$link_page_url['diary_top_page_url'] = $this->getControllerPath('User', 'Diary') . '&id=' .$community_id;
		//¾�ͤ�������������Ƥ�����Υȥåץڡ���URL
		$link_page_url['else_user_diary_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Index') . '&id=' . $community_id;
		$diary_comment_page_url = $this->getControllerPath('User', 'DiaryComment') . '&community_id=' . $community_id .'&diary_id=' . $diary_id;
		// set
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('link_page_url', $link_page_url);
		$this->setAttribute('diary_comment_page_url', $diary_comment_page_url);
		$this->setAttribute('form', $form);
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('is_self_page', $is_self_page);
		
		// ���顼��å�����
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('DiaryCommentPre.tpl.php');

		return parent::execute();
	}
}

?>
