<?php
// $Id: CommunityRankingView_inline.class.php,v 1.3 2006/11/20 08:44:19 w-ota Exp $

class CommunityRankingInputView extends BaseView
{
	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();

		// get
		$ranking_community_row_array = $request->getAttribute('ranking_community_row_array');

		// 加工
		$rank = 1;
		foreach ($ranking_community_row_array as $index => $ranking_community_row) {
			// トップページURL
			$ranking_community_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $ranking_community_row['community_id'];
			// 写真
			$ranking_community_row_array[$index]['image_url'] = ACSCommunity::get_image_url($ranking_community_row['community_id'], 'thumb');
			// プロフィール
			$ranking_community_row_array[$index]['contents_row_array']['community_profile'] = ACSCommunity::get_contents_row($ranking_community_row['community_id'], ACSMsg::get_mst('contents_type_master','D07'));
			// 順位
			$ranking_community_row_array[$index]['rank'] = $rank;
			$rank++;
		}
	
		// set
		$this->setAttribute('ranking_community_row_array', $ranking_community_row_array);

		// テンプレートをセットする
		$this->setTemplate('CommunityRanking.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("CommunityRanking", $this->render());

		return parent::execute();
	}
}

?>
