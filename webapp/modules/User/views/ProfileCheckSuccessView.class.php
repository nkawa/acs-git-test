<?php
/**
 * �ޥ��ڡ�����ǽ��View���饹
 * �ץ�ե������ǧ����
 * @package  acs/webapp/modules/User/views
 * ViewProfile_confirm
 * @author   akitsu
 * @since	PHP 4.0
 */
// $Id: ProfileCheckView_confirm.class.php,v 1.5 2006/11/20 08:44:28 w-ota Exp $

class ProfileCheckSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$user_community_id = $request->getAttribute('user_community_id');

		$profile = $request->getAttribute('target_user_info_row');

		//�桼�������򤷤�������
		$view_mode = $request->getAttribute('view_mode');

		// ������٥�ޥ���
		$open_level_master_array = ACSDB::get_master_array('open_level');

		//ɽ���оݤȤʤ���������ȴ����
		$view_at = array();
		switch($view_mode){
			case 1:
				array_push($view_at,array_search(ACSMsg::get_mst('open_level_master','D01'), $open_level_master_array));
				array_push($view_at,array_search(ACSMsg::get_mst('open_level_master','D02'), $open_level_master_array));
				break;
			case 2:
				array_push($view_at,array_search(ACSMsg::get_mst('open_level_master','D01'), $open_level_master_array));
				array_push($view_at,array_search(ACSMsg::get_mst('open_level_master','D02'), $open_level_master_array));
				array_push($view_at,array_search(ACSMsg::get_mst('open_level_master','D05'), $open_level_master_array));
				break;
			default:
				array_push($view_at,array_search(ACSMsg::get_mst('open_level_master','D01'), $open_level_master_array));
		}
		//���̸��� 01 ������桼���˸��� 02 ���٤Ƥ�ͧ�ͤ˸��� 05
		$profile['contents_row_array']['birthplace'] = ACSAccessControl::set_not_open($profile['contents_row_array']['birthplace'],$view_at);
		$profile['contents_row_array']['user_name'] = ACSAccessControl::set_not_open($profile['contents_row_array']['user_name'],$view_at);
		$profile['contents_row_array']['birthday'] = ACSAccessControl::set_not_open($profile['contents_row_array']['birthday'],$view_at);

		//top
		$top_page_url = $this->getControllerPath('User', 'Index') . '&id=' . $user_community_id;

		// ��˥塼����
		$menu = array();
		//���̸��� 01
		$menu['all_url'] = $this->getControllerPath('User', 'ProfileCheck') . '&id=' . $user_community_id . '&view_mode=0';
		//������桼���˸��� 02
		$menu['login_url'] = $this->getControllerPath('User', 'ProfileCheck') . '&id=' . $user_community_id . '&view_mode=1';
		//���٤Ƥ�ͧ�ͤ˸��� 05
		$menu['friend_url'] = $this->getControllerPath('User', 'ProfileCheck') . '&id=' . $user_community_id . '&view_mode=2';

		// set
		$this->setAttribute('profile', $profile);

		// ��˥塼
		$this->setAttribute('menu', $menu);
		$this->setAttribute('top_page_url', $top_page_url);
		$this->setAttribute('view_mode',$view_mode);
		
		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('ProfileCheck.tpl.php');

		return parent::execute();
	}
}

?>
