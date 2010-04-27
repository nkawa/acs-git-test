<?php
/**
 * ダイアリーコメント機能　Viewクラス
 * @package  acs/webapp/modules/User/views
 * DiaryCommentView::INPUT
 * @author   ota  update akitsu
 * @since	PHP 4.0
 */
// $Id: DiaryCommentView::INPUT.class.php,v 1.16 2007/03/28 02:26:55 w-ota Exp $


class DiaryCommentInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$diary_row = $request->getAttribute('diary_row');
		$diary_comment_row_array = $request->getAttribute('diary_comment_row_array');

		$footprint_info = $request->getAttribute('footprint_info');

		// 信頼済みコミュニティ(マイフレンズグループ)が定義されているか
		if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
			if (count($diary_row['trusted_community_row_array'])
				&& $diary_row['trusted_community_row_array'][0]['community_type_name'] == ACSMsg::get_mst('community_type_master','D20')) {
				$diary_row['trusted_community_flag'] = 0;
			} else {
				$diary_row['trusted_community_flag'] = 1;
			}
		}

		// 加工
		// トップページURL
		$link_page_url['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Diary') . '&id=' . $diary_row['user_community_id'];
		//他人の日記を閲覧している場合のトップページURL
		$link_page_url['else_user_diary_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Index') . '&id=' . $target_user_info_row['community_id'];

		// トップページURL
		$diary_row['top_page_url'] = $link_page_url['top_page_url'];
		//他人の日記を閲覧している場合のダイアリートップページURL
		$diary_row['else_user_diary_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Diary') . '&id=' . $target_user_info_row['community_id'];
		// 画像URL
		$diary_row['image_url'] = ACSUser::get_image_url($diary_row['community_id'],'thumb');
		// 投稿日時
		$diary_row['post_date']  = ACSLib::convert_pg_date_to_str($diary_row['post_date']);

		//ファイルの画像URL
		$diary_row[$index]['file_url'] = "";
		if($diary_row['file_id'] != ""){
			$diary_row['file_url'] = ACSDiaryFile::get_image_url($diary_row['file_id'],'thumb');		//投稿内表示用
			$diary_row['file_url_alink'] = ACSDiaryFile::get_image_url($diary_row['file_id'],'');	//ポップアップ用
		}

		//コメント
		foreach ($diary_comment_row_array as $comment_index => $diary_comment_row) {
			// トップページURL
			$diary_comment_row_array[$comment_index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $diary_comment_row['user_community_id'];
			// 画像URL
			$diary_comment_row_array[$comment_index]['image_url'] = ACSUser::get_image_url($diary_comment_row['community_id'],'thumb');
			// 投稿日時
			$diary_comment_row_array[$comment_index]['post_date']  = ACSLib::convert_pg_date_to_str($diary_comment_row['post_date']);
			// 削除画面URL
			$diary_comment_row_array[$comment_index]['diary_delete_url'] = $this->getControllerPath('User', 'DeleteDiaryComment') . '&id=' . $target_user_info_row['user_community_id'] . '&diary_id=' . $diary_row['diary_id'] . '&diary_comment_id=' . $diary_comment_row['diary_comment_id'];
			$diary_comment_row_array[$comment_index]['self_id'] = false;		//自分が投稿したcommentかを判定する
			if($diary_comment_row['user_community_id'] == $acs_user_info_row['user_community_id'] ){
				$diary_comment_row_array[$comment_index]['self_id'] = true;
			}
		}

		// 本人のページかどうか
		if ($target_user_info_row['user_community_id'] == $acs_user_info_row['user_community_id']) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}

		// 足跡登録URL(自画面) //
		$footprint_url = "";
		if($is_self_page == 0 && $acs_user_info_row['is_login_user']) {
			$footprint_url = $this->getControllerPath('User', 'FootprintDiaryComment')
						. '&diary_id=' . $diary_row['diary_id'];
		}

		// 確認画面の表示用
		$action_url = $this->getControllerPath('User', 'DiaryCommentPre') . '&id=' . $target_user_info_row['community_id'] .'&diary_id=' . $diary_row['diary_id']  .'&move_id=1';

		// ページング設定
		$display_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D02'), 'NEW_INFO_LIST_DISPLAY_MAX_COUNT');
		$paging_info = $this->getPagingInfo($controller, $request, $diary_comment_row_array, $display_count);

		// set
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('diary_row', $diary_row);
		$this->setAttribute('diary_comment_row_array', $diary_comment_row_array);
		$this->setAttribute('paging_info', $paging_info);
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('link_page_url', $link_page_url);
		$this->setAttribute('footprint_url', $footprint_url);
		$this->setAttribute('footprint_info', $footprint_info);
		//$this->setAttribute('footprint_community_id', $footprint_community_id);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DiaryComment.tpl.php');

		// 確認画面からキャンセルボタンで戻ってきたときのみの処理
		if($request->getParameter('move_id') == 3){
			// 元のフォーム情報を取得する
			$form = $user->getAttribute('new_form_obj');//件名：subject 内容：body
			// 対象となるダイアリーIDを取得する
			$diary_id = $request->ACSgetParameter('diary_id');
			$this->setAttribute('form', $form);
			$this->setAttribute('move_id', $request->getParameter('move_id'));
		}
		return parent::execute();
	}
}

?>
