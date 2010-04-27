<?php
/**
 * ダイアリー　Actionクラス
 * 
 * DiaryCommentAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   w-ota                     @editor akitsu
 * @since    PHP 4.0
 */
// $Id: DiaryAction.class.php,v 1.13 2007/03/27 02:12:41 w-ota Exp $

class DiaryAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// 表示対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');

		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		$year = $request->ACSgetParameter('year');   // 年
		$month = $request->ACSgetParameter('month'); // 月
		$day = $request->ACSgetParameter('day');     // 日

		if (checkdate($month, $day, $year)) {
			// 年月日指定
			$diary_row_array = ACSDiary::get_diary_row_array_by_year_month_day($user_community_id, $year, $month, $day);
		} elseif (checkdate($month, 1, $year)) {
			// 年月指定
			$diary_row_array = ACSDiary::get_diary_row_array_by_year_month($user_community_id, $year, $month);
			unset($day);
		} else {
			// 全てのダイアリー
			$diary_row_array = ACSDiary::get_diary_row_array($user_community_id);
			unset($year);
			unset($month);
			unset($day);
		}

		// 公開範囲を最終登録と同じもので表示しておく
		if($diary_row_array){
			$last_open_level_code = $diary_row_array[0]['open_level_code'];
		}
		
		// 信頼済みコミュニティ情報
		foreach ($diary_row_array as $index => $diary_row) {
			if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
				$diary_row_array[$index]['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
			}
		}

		// 公開範囲
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D10'), ACSMsg::get_mst('contents_type_master','D21'));

		// マイフレンズグループ
		$friends_group_row_array = ACSUser::get_friends_group_row_array($user_community_id);

		// set
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('diary_row_array', $diary_row_array);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$request->setAttribute('friends_group_row_array', $friends_group_row_array);
		$request->setAttribute('last_open_level_code', $last_open_level_code);

		return View::INPUT;
	}

	// POST
	function execute() {
		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();

		//キャンセルで戻ってきたときのみの処理
		$move_id = $request->getParameter('move_id');
		if($move_id == 3){
			$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		
			// 対象となるコミュニティIDを取得
			$user_id = $request->ACSGetParameter('id');

			// 元のフォーム情報を取得する
			$form = $user->getAttribute('new_form_obj');//件名：subject 内容：body 公開範囲：open_level_code 画像：new_file
			$form['user_community_id'] = $acs_user_info_row['user_community_id']; // 登録者

			$user->setAttribute('new_form_obj',$form);
		}

		// GETの処理へ
		return $this->getDefaultView();
	}

	function getRequestMethods() {
		return REQ_POST;
	}
	
	/**
	 * 認証チェックを行うか
	 * アクションを実行する前に、認証チェックが必要か設定する
	 * @access  public
	 * @return  boolean 認証チェック有無（true:必要、false:不要）
	 */
	public function isSecure()
	{
		return false;
	}
}

?>
