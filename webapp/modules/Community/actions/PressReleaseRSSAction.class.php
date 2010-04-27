<?php
/**
 * �ѥ֥�å���꡼����ǽ��Action���饹
 * 
 * PressReleaseRSSAction.class.php
 * @package  acs/webapp/modules/Community/action
 * @author   acs
 * @since    PHP 5.0
 * @version  $Revision: 1.20 $ $Date: 2009/06/19 10:00:00 $
 */
class PressReleaseRSSAction extends BaseAction
{
	// GET��ɸ�����
	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser(); 

		// RSS�оݤΥ��ɥ쥹������
		$system_top_address = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_URL');

		$rss_syndicationURL  = $system_top_address;
		$rss_syndicationURL .= $this->getControllerPath('Community', 'PressReleaseRSS');
		$rss_syndicationURL .= '&community_id=' . $request->getParameter('community_id');

		// ���ߥ�˥ƥ�����
		$community_id = $request->getParameter('community_id');
		$community_row = ACSCommunity::get_community_row($community_id);
		$community_row['community_profile'] = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D07'));

		
		//���ߥ�˥ƥ��ȥåץڡ���
		$community_top_page_url = $this->getControllerPath('Community', 'Index') . '&community_id=' . $community_id;
		$community_row['community_profile']['top_page_url'] = $community_top_page_url;
		
		//���ߥ�˥ƥ��̿�ɽ��
		if($community_row['file_id']){
			$community_file_info_row = ACSFileInfoModel::select_file_info_row($community_row['file_id']);
			$community_row['image_title'] = $community_file_info_row['display_file_name'];
		}else{
			$community_row['image_title'] = ACSMsg::get_msg('Community', 'PressReleaseRSSAction.class.php', 'M001');
		}
		$community_row['image_url'] = ACSCommunity::get_image_url($community_id,'rss');

		// BBS�������� ��Ĥ������Ϥ����Τ�����Ǥ���
		$bbs_row_array = ACSBBS::get_bbs_rss_row_array($community_id,1);
		if(count($bbs_row_array) > 0){
		// �Ǻܽ�λ����������ۤ��Ƥ��ʤ���ΤΤߤˤ���
			// ���������
			$today = date("Y/m/d");
				$bbs_rss_array = array();
			foreach ($bbs_row_array as $index => $bbs_row) {
				//�оݤȤʤ뵭���Τߤ����
				$bbs_date = ACSLib::convert_pg_date_to_str($bbs_row['expire_date'],false,false,false);
				if($bbs_date >= $today || $bbs_date == null){
					//������������Ѥ�
					$bbs_rss_array[$index] = $bbs_row;
					//�����Υ�������
					$bbs_rss_array[$index]['bbs_url']=$this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
					//�����Υ�������
					$bbs_rss_array[$index]['file_link']="";
					if($bbs_row['file_id'] != ""){
						$bbs_rss_array[$index]['file_url'] = $system_top_address . ACSBBSFile::get_image_url($bbs_row['bbs_id'],'rss') ;		//RSSɽ����
					}
				}
			}
			$detail = true;		//¸�ߤ���
		}else{
			$detail = false;//¸�ߤ��ʤ�
		}
		// RSS�оݤ�BBS��������
		$user->setAttribute('bbs_rss_array',$bbs_rss_array);

		// RSS�оݤ�Community����		
		$user->setAttribute('community_row',$community_row);
		$community_name = htmlspecialchars($community_row['community_name']);

		// set
		$request->setAttribute('rss_syndicationURL', $rss_syndicationURL);
		$request->setAttribute('bbs_rss_array', $bbs_rss_array);
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('community_name', $community_name);
		$request->setAttribute('system_top_address', $system_top_address);

		//������¸�ߥե饰
		$request->setAttribute('detail',$detail);
		require "PressReleaseRSS.php";

	}

	function isSecure () {
		return false;
	}

}
?>
