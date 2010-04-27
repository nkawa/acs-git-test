<?php
// $Id: UserRankingView_inline.class.php,v 1.3 2006/11/20 08:44:19 w-ota Exp $

class UserRankingInputView extends BaseView
{
	public function execute ()
	{
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();

		// get
		$ranking_user_info_row_array = $request->getAttribute('ranking_user_info_row_array');

		// 加工
		$rank = 1;
		foreach ($ranking_user_info_row_array as $index => $ranking_user_info_row) {
			// トップページURL
			$ranking_user_info_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $ranking_user_info_row['user_community_id'];
			// 写真
			$ranking_user_info_row_array[$index]['image_url'] = ACSUser::get_image_url($ranking_user_info_row['user_community_id'], 'thumb');
			// プロフィール
			$ranking_user_info_row_array[$index]['contents_row_array']['profile'] = ACSCommunity::get_contents_row($ranking_user_info_row['user_community_id'], ACSMsg::get_mst('contents_type_master','D07'));
			// 順位
			$ranking_user_info_row_array[$index]['rank'] = $rank;
			$rank++;
		}

		// set
		$this->setAttribute('ranking_user_info_row_array', $ranking_user_info_row_array);

		// テンプレートをセットする
		$this->setTemplate('UserRanking.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("UserRanking", $this->render());

		return parent::execute();
		
	}
}

?>
