<?php
// $Id: IndexView::SUCCESS.class.php,v 1.1 2006/03/10 11:45:41 w-ota Exp $

class IndexSuccessView extends BaseView
{
	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$controller = $context->getController();
		
		// ACSユーザ情報を取得
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		/*--------------- 新着情報を取得 ---------------*/
		// 現在のレンダーモードを取得
		$renderMode = $controller->getRenderMode();

		//レンダーモードを上書き （画面出力をオフにしてる）
		$controller->setRenderMode(View::RENDER_VAR);
		$this->inlineFlg = true;

		// フォワード側で判断する
		$request->setAttribute("inline_mode", "1");

		// フォワードする
		
		// 1.新着パブリックリリース
		$controller->forward("Public", "NewPressRelease");
		$this->setAttribute("NewPressRelease", $request->getAttribute("NewPressRelease"));

		// 2.新着ダイアリー
		$controller->forward("Public", "NewOpenDiary");
		$this->setAttribute("NewOpenDiary", $request->getAttribute("NewOpenDiary"));

		// 3.新着コミュニティ
		$controller->forward("Public", "NewCommunity");
		$this->setAttribute("NewCommunity", $request->getAttribute("NewCommunity"));
		
		// 4.コミュニティランキング
		$controller->forward("Public", "CommunityRanking");
		$this->setAttribute("CommunityRanking", $request->getAttribute("CommunityRanking"));

		// 5.ユーザランキング
		$controller->forward("Public", "UserRanking");
		$this->setAttribute("UserRanking", $request->getAttribute("UserRanking"));

		// レンダーモードを元に戻す
		$controller->setRenderMode($renderMode); 
		$this->inlineFlg = false;

		/*----------------------------------------------*/
		
		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('Index.php');

		return parent::execute();
	}
}

?>
