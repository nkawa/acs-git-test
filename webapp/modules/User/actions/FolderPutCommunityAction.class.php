<?php
/**
 * �桼���Υե�����ץå��襳�ߥ�˥ƥ�����
 *
 * @author  kuwayama
 * @version $Revision: 1.7 $ $Date: 2006/12/18 07:42:15 $
 */
require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
require_once(ACS_CLASS_DIR . 'ACSFolderModel.class.php');
class FolderPutCommunityAction extends BaseAction
{
	/**
	 * �ץå��襳�ߥ�˥ƥ��������ɽ��
	 */
	function getDefaultView () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		// �оݤȤʤ�ե����ID�����
		$target_folder_id = $request->ACSgetParameter('folder_id');

		// ¾�桼���Υǡ����������ʤ��褦�����å�
		if (!$this->get_execute_privilege()) {
			// ���Υڡ����إ����������뤳�ȤϤǤ��ޤ���
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// �桼������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		// �ޥ����ߥ�˥ƥ�
		$community_row_array = ACSUser::get_community_row_array($user_community_id);
		// �ޥ����ߥ�˥ƥ��Υե�����ĥ꡼���ɲ�
		$community_folder_obj_array = array();
		$community_row_index = 0;
		foreach ($community_row_array as $community_row) {
			$community_folder_obj = array();
			$folder_tree = array();

			// �롼�ȥե�����Υ��󥹥�������
			$community_folder_obj = new ACSCommunityFolder($community_row['community_id'], $acs_user_info_row, '');
			$folder_tree = $community_folder_obj->get_folder_tree();
			$community_row_array[$community_row_index]['folder_tree'] = $folder_tree;
			$community_row_index++;
		}

		// �ץå��襳�ߥ�˥ƥ������ꤵ��Ƥ��륳�ߥ�˥ƥ���
		$put_community_row_array = ACSFolderModel::select_put_community($target_folder_id);


		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('target_folder_id', $target_folder_id);
		$request->setAttribute('community_row_array', $community_row_array);
		$request->setAttribute('put_community_row_array', $put_community_row_array);

		return View::INPUT;
	}

	/**
	 * �ץå��襳�ߥ�˥ƥ��������
	 */
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$user_community_id = $request->ACSgetParameter('id');
		// �оݤȤʤ�ե����ID�����
		$target_folder_id = $request->ACSgetParameter('folder_id');

		// ACSUserFolder ���󥹥�������
		$user_folder_obj = new ACSUserFolder($user_community_id, $acs_user_info_row, $target_folder_id);
		$target_folder_obj = $user_folder_obj->get_folder_obj();

		// ���򤵤줿 community_id, folder_id �����
		$selected_put_folder_row_array = $request->getParameter('selected_put_folder_id');

		// ----------------------------
		// �����Ѥ˥ǡ����ù�
		// �ץå��襳�ߥ�˥ƥ������Ѥˡ�row_array ����
		$put_community_row_array = array();
		foreach ($selected_put_folder_row_array as $community_id => $folder_id) {
			array_push($put_community_row_array, array('put_community_id' => $community_id, 'put_community_folder_id' => $folder_id));
		}

		// ----------------------------
		// ��������
		$ret = $target_folder_obj->update_put_community($target_folder_id, $put_community_row_array);
		if (!$ret) {
			print "ERROR: Update put-community failed.<br>\n";
			exit;
		}

		// ML���Υ����å��������ML�˥᡼����������
		// ���ߥ�˥ƥ�����μ���
		$send_announce_mail		= $request->getParameter('send_announce_mail');
		if($send_announce_mail == "t"){
			foreach ($selected_put_folder_row_array as $community_id => $folder_id) {
				$folder_info = ACSFolderModel::select_folder_row($folder_id);
				ACSCommunityMail::send_putfolder_mail(
							$acs_user_info_row, $folder_info, $community_id);
			}
		}

		// ����������ä��顢������ɥ����Ĥ���
		$controller->forward('Common', 'CloseChildWindow');
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {		
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// �������桼�����ܿͰʳ���NG
		if ($user->hasCredential('PUBLIC_USER')
				 || !$user->hasCredential('USER_PAGE_OWNER')) {
			return false;
		}
		return true;
	}
}
?>
