<?php
/**
 * ���ߥ�˥ƥ�����ʳ�ǧ��
 *
 * @author  kuwayama
 * @version $Revision: 1.3 $ $Date: 2006/02/20 07:06:10 $
 */
class DeleteCommunitySuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$target_community_row = $request->getAttribute('target_community_row');
		$delete_user_info_row_array = $request->getAttribute('delete_user_info_row_array');

		// URL ���ղä��� target_community
		$target_community_info = '&community_id=' . $target_community_row['community_id'];


		// ���ߥ�˥ƥ���URL
		$community_top_page_url  = $this->getControllerPath('Community', 'Index');
		$community_top_page_url .= $target_community_info;

		// ����󥻥�URL
		$cancel_action_url = $community_top_page_url;

		// ������������URL
		$delete_action_url  = $this->getControllerPath('Community',
														     'DeleteCommunity');
		$delete_action_url .= $target_community_info;

		// ���ߥ�˥ƥ��γ���
		$delete_community_row = array();

		$delete_community_row['community_name']    = $target_community_row['community_name'];
		$delete_community_row['top_page_url']      = $this->getControllerPath('Community', DEFAULT_ACTION) .  $target_community_info;
		$delete_community_row['image_url']         = ACSCommunity::get_image_url($target_community_row['community_id']);
		$delete_community_row['community_profile'] = $target_community_row['community_profile']['contents_value'];

		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('target_community_name', $target_community_row['community_name']);
		$this->setAttribute('delete_community_row', $delete_community_row);

		// form �Υ���������� URL
		$this->setAttribute('cancel_action_url', $cancel_action_url);
		$this->setAttribute('delete_action_url', $delete_action_url);


		// �ƥ�ץ졼��
		$this->setScreenId("0001");
		$this->setTemplate('DeleteCommunity_confirm.tpl.php');

		return parent::execute();
	}
}
?>
