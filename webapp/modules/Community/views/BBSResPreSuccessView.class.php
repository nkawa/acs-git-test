<?php
/**
 * 掲示板　投稿機能　Viewクラス
 * 返信投稿情報　確認・登録画面
 * @package  acs/webapp/modules/Community/views
 * @author   akitsu
 * @since    PHP 4.0
 * @version  $Revision: 1.4 $ $Date: 2006/02/29
 */
// $Id: BBSResPreConfirmView.class.php,v 1.4 2006/03/29 08:53:05 kuwayama Exp $


class BBSResPreSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
	//get

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// 対象となるコミュニティIDを取得
		$community_id = $request->getParameter('community_id');
		// コミュニティ情報
		$community_row = ACSCommunity::get_community_row($community_id);
		//ユーザ入力情報
		$form = $user->getAttribute('new_form_obj');
		$bbs_id = $request->getParameter('bbs_id');

		// コミュニティメンバかどうか
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_row['community_id']);
		// form action
		$action_url  = $this->getControllerPath('Community', 'BBSResPre') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_id . '&move_id=2';
		// action URL 確認画面のキャンセルボタン
		$back_url  = $this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $community_row['community_id'] . '&bbs_id=' . $bbs_id . '&move_id=3';

		// コミュニティトップページのURL
		$community_top_page_url = $this->getControllerPath('Community', 'Index') . '&community_id=' . $community_row['community_id'];
		//bbs_top_page_url 掲示板TOP画面
		$back_bbs_url = $this->getControllerPath('Community', 'BBS') . '&community_id=' . $community_row['community_id'];
	
		// set
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('community_top_page_url', $community_top_page_url);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('back_bbs_url', $back_bbs_url);
		$this->setAttribute('select_trusted_community_url', $select_trusted_community_url);
		$this->setAttribute('form', $form);
		$this->setAttribute('community_row', $community_row);

		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('BBSResPre.tpl.php');

		return parent::execute();
	}
}

?>
