<?php
/**
 * パブリックリリース機能　Actionクラス
 * 
 * PressReleaseAllRSSAction.class.php
 * @package  acs/webapp/modules/Community/action
 * @author   acs
 * @since    PHP 5.0
 * @version  $Revision: 1.20 $ $Date: 2009/06/19 10:00:00 $
 */
class PressReleaseAllRSSAction extends BaseAction
{
	// GET　標準出力
	function getDefaultView() {
	}

	// POST 標準出力のみ
	function execute() {

		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// コミュニティ情報
		// RSSに表示する全コミュニティ　のコミュニティID指定定数
		// 対象とならないコミュニティIDを取得
		$except_community_id = $request->getParameter('except_community_id');

		// BBS記事一覧
		$bbs_row_array = ACSBBS::get_bbs_rss_row_array($except_community_id,0);

		// システム情報の設定
		$system_config_keyword_value['SYSTEM_NAME'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_NAME');
		$system_config_keyword_value['SYSTEM_OUTLINE'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_OUTLINE');
		$system_config_keyword_value['SYSTEM_BASE_URL'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_URL');
		$system_config_keyword_value['SYSTEM_MAIL_ADDR'] = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_MAIL_ADDR');
	//	$system_config_keyword_value['SYSTEM_IMAGE'] = ACSSystemConfig::get_keyword_value('システム', 'SYSTEM_IMAGE');		// 3/13現在画像は無い　次行　暫定処理
		$system_config_keyword_value['SYSTEM_IMAGE']['title'] = ACSMsg::get_msg('Community', 'PressReleaseAllRSSAction.class.php', 'M001');
		$system_config_keyword_value['SYSTEM_IMAGE']['url'] = ACSMsg::get_msg('Community', 'PressReleaseAllRSSAction.class.php', 'M002');
		$system_config_keyword_value['SYSTEM_IMAGE']['link'] = ACSMsg::get_msg('Community', 'PressReleaseAllRSSAction.class.php', 'M003');
		$system_config_keyword_value['SYSTEM_IMAGE']['description'] = ACSMsg::get_msg('Community', 'PressReleaseAllRSSAction.class.php', 'M004');

		$rss_syndicationURL .= $system_config_keyword_value['SYSTEM_BASE_URL'] . $this->getControllerPath('Community', 'PressReleaseAllRSS');

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
			//相対するCommunity名を作る
			$community_row = ACSCommunity::get_community_row($bbs_row['community_id']);
				$bbs_rss_array[$index]['community_id_name'] = $community_row['community_name'];
			//記事のリンク先を作る
				$bbs_rss_array[$index]['bbs_url']=$this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $bbs_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];
			//画像のリンク先を作る
				$bbs_rss_array[$index]['file_link']="";
				if($bbs_row['file_id'] != ""){
					$bbs_rss_array[$index]['file_url'] = $system_config_keyword_value['SYSTEM_BASE_URL'] . ACSBBSFile::get_image_url($bbs_row['bbs_id'],'rss');		//RSS表示用
				}
			}
		}

		$user->setAttribute('bbs_rss_array',$bbs_rss_array);

		// RSS対象のBBS記事一覧
		$bbs_rss_array = $user->getAttribute('bbs_rss_array');
		// RSS対象のアドレス開始値
		$system_top_address = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D01'), 'SYSTEM_BASE_URL');

		// set
		$request->setAttribute('system_config_keyword_value', $system_config_keyword_value);
		$request->setAttribute('rss_syndicationURL', $rss_syndicationURL);
		$request->setAttribute('bbs_rss_array', $bbs_rss_array);
		$request->setAttribute('system_top_address', $system_top_address);
		
		require "PressReleaseAllRSS.php";

	}

	function isSecure () {
		return false;
	}

}
?>
