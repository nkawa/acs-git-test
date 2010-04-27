<?php
/**
 * �ե�����ܺپ���
 *
 * @author  kuwayama
 * @version $Revision: 1.2 $ $Date: 2006/05/01 09:58:06 $
 */
//require_once(ACS_CLASS_DIR . 'ACSUserFolder.class.php');
class FolderDetailAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// �оݤȤʤ�桼�����ߥ�˥ƥ�ID�����
		$target_user_community_id = $request->getParameter('id');
		// �оݤȤʤ�ե����ID�����
		$target_user_community_folder_id = $request->getParameter('folder_id');
		// �ܺپ����ɽ������ե����ID�����
		$detail_user_community_folder_id = $request->getParameter('detail_folder_id');

		// ɽ������ڡ����ν�ͭ�Ծ������
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($target_user_community_id);
		// �ե�����������
		$user_folder_obj = new ACSUserFolder($target_user_community_id,
											$acs_user_info_row,
											$target_user_community_folder_id);

		$detail_user_folder_obj = new ACSUserFolder($target_user_community_id,
											$acs_user_info_row,
											$detail_user_community_folder_id);

		// �ե�����θ����ϰϤǥ�����������
		if (!$detail_user_folder_obj->has_privilege($target_user_info_row)) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('user_folder_obj', $user_folder_obj);
		$request->setAttribute('detail_user_folder_obj', $detail_user_folder_obj);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
}
?>
