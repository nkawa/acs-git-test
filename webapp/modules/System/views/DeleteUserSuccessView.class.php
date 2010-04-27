<?php
/**
 * �����ƥࡡ�桼���������桼����������ǧ���� view���饹
 * @package  acs/webapp/modules/System/views
 * DeleteUserView_confirm
 * @author   akitsu  
 * @since	PHP 4.0
 */
// $Id: DeleteUserView_confirm.class.php,v 1.1 2006/03/13 07:04:58 z-akitsu Exp $


class DeleteUserSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$user_info_row = $request->getAttribute('user_info_row');

		// URL
		$delete_user_url = $this->getControllerPath('System', 'DeleteUser'). '&id=' . $user_info_row['user_community_id'];
		$back_url = $this->getControllerPath('System', 'UserList');

		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('DeleteUser.tpl.php');

		// set
		$this->setAttribute('delete_user_url', $delete_user_url);
		$this->setAttribute('back_url', $back_url);
		
		$this->setAttribute('user_info_row', $user_info_row);

		return parent::execute();
	}
}

?>
