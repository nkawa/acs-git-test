<?php
/**
 * パブリックリリース機能　Actionクラス
 * 
 * PressReleaseRSSAction.class.php
 * @package  acs/webapp/modules/Community/action
 * @author   acs
 * @since    PHP 5.0
 * @version  $Revision: 1.20 $ $Date: 2009/06/19 10:00:00 $
 */
class PressReleaseRSSAction extends BaseAction
{
	// GET　標準出力
	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser(); 

		// RSS対象のアドレス開始値
		$system_top_address = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_URL');

		$rss_syndicationURL  = $system_top_address;
		$rss_syndicationURL .= $this->getControllerPath('Community', 'PressReleaseRSS');
		$rss_syndicationURL .= '&community_id=' . $request->getParameter('community_id');

		// コミュニティ情報
		$community_id = $request->getParameter('community_id');
		$community_row = ACSCommunity::get_community_row($community_id);
		$community_row['community_profile'] = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D07'));

		
		//コミュニティトップページ
		$community_top_page_url = $this->getControllerPath('Community', 'Index') . '&community_id=' . $community_id;
		$community_row['community_profile']['top_page_url'] = $community_top_page_url;
		
		//コミュニティ写真表示
		if($community_row['file_id']){
			$community_file_info_row = ACSFileInfoModel::select_file_info_row($community_row['file_id']);
			$community_row['image_title'] = $community_file_info_row['display_file_name'];
		}else{
			$community_row['image_title'] = ACSMsg::get_msg('Community', 'PressReleaseRSSAction.class.php', 'M001');
		}
		$community_row['image_url'] = ACSCommunity::get_image_url($community_id,'rss');

		// BBS記事一覧 一つだけ出力するものを選択できる
		$bbs_row_array = ACSBBS::get_bbs_rss_row_array($community_id,1);
		if(count($bbs_row_array) > 0){
		// 掲載終了日が本日を越えていないもののみにする
			// 本日を取得
			$today = date("Y/m/d");
				$bbs_rss_array = array();
			foreach ($bbs_row_array as $index => $bbs_row) {
				//対象となる記事のみを抽出
				$bbs_date = ACSLib::convert_pg_date_to_str($bbs_row['expire_date'],false,false,false);
				if($bbs_date >= $today || $bbs_date == null){
					//記事配列を作り変え
					$bbs_rss_array[$index] = $bbs_row;
					//記事のリンク先を作る
					$bbs_rss_array[$index]['bbs_url']=$this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
					//画像のリンク先を作る
					$bbs_rss_array[$index]['file_link']="";
					if($bbs_row['file_id'] != ""){
						$bbs_rss_array[$index]['file_url'] = $system_top_address . ACSBBSFile::get_image_url($bbs_row['bbs_id'],'rss') ;		//RSS表示用
					}
				}
			}
			$detail = true;		//存在する
		}else{
			$detail = false;//存在しない
		}
		// RSS対象のBBS記事一覧
		$user->setAttribute('bbs_rss_array',$bbs_rss_array);

		// RSS対象のCommunity情報		
		$user->setAttribute('community_row',$community_row);
		$community_name = htmlspecialchars($community_row['community_name']);

		// set
		$request->setAttribute('rss_syndicationURL', $rss_syndicationURL);
		$request->setAttribute('bbs_rss_array', $bbs_rss_array);
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('community_name', $community_name);
		$request->setAttribute('system_top_address', $system_top_address);

		//記事の存在フラグ
		$request->setAttribute('detail',$detail);
		require "PressReleaseRSS.php";

	}

	function isSecure () {
		return false;
	}

}
?>
