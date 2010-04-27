<?php
/**
 * �Ǽ���RSS��Action���饹
 * 
 * $Id: BBSRSSAction.class.php,v 1.1 2006/12/13 09:51:32 w-ota Exp $
 */
class BBSRSSAction extends BaseAction
{
	// GET
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// ɽ���оݤȤʤ륳�ߥ�˥ƥ�ID�����
		$community_id = $request->getParameter('community_id');

		// ���ߥ�˥ƥ�����
		$community_row = ACSCommunity::get_community_row($community_id);
		$community_row['community_profile'] = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D07'));

		// ��������
		$term = intval($request->ACSgetParameter('term'));
		if (!$term) {
			// �����ƥ�����: ���ߥ�˥ƥ�: �Ǽ���RSS��������
			$term = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D03'), 'BBS_RSS_TERM');
		}

		// �ǿ��ηǼ���RSS
		$bbs_row_array = ACSBBS::get_new_bbs_rss_row_array($community_id, $term);

		foreach ($bbs_row_array as $index => $bbs_row) {
			// ����Ѥߥ��ߥ�˥ƥ�����
			$bbs_row_array[$index]['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);
		}

		// set
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('bbs_row_array', $bbs_row_array);
		$request->setAttribute('term', $term);

		return View::SUCCESS;
	}
	
	function isSecure () {
		return false;
	}
}

?>
