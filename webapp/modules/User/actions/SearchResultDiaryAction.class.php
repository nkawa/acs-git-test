<?php
/**
 * ダイアリー検索結果　Actionクラス
 * 
 * SearchResultDiaryAction.class.php
 * @package  acs/webapp/module/User/Action
 * @author   akitsu
 * @since    PHP 4.0
 */
// $Id: SearchResultDiaryAction.class.php,v 1.5 2006/12/08 05:06:42 w-ota Exp $

class SearchResultDiaryAction extends BaseAction
{
	// POST 	検索ボタンの処理
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 表示対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		// ユーザ情報
		$target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($user_community_id);

		//mode　画面の遷移を取得する
		$move_id = $request->getParameter('move_id');

		// 画面上のフォーム情報を取得する
		if ($move_id == 1) {
			$form = $request->getParameters('search_form_default');
		} else if($move_id == 2) {
			$form = $request->getParameters('search_form_new');
		}

		// formの情報に従い検索を行う
		if ($move_id > 0) {
			$err_str = "";
			/*	Array
			    [id] => 1000
			    [move_id] => 2
			    [q_text] => 検索条件				//条件（※必須）
			    [search_title] => title_in_serch	//件名を選択
			    [search_all] => subject_in_serch	//本文を選択
			    [open_level_code] => 00				//公開範囲（00は選択なし）（※必須）
			    [search_all_about] => all_in_serch	//すべての日記を選択
			*/
		// ------------ 検索情報の取得（注意！バイト単位で処理）
		for ($i = 1; $i < 3; $i++) {
			$str_where_create[$i] = ACSDiary::set_diary_where_list($form,$i);
			if ($str_where_create[$i]['err_str']) {
				$err_str = $str_where_create[$i]['err_str'];	//where句の一部が作成できないオペレータミス
				if ($err_str != '') {
					break;
				}
			} else {
				$str_where[$i] = $str_where_create[$i]['like_sql'];
			}
		}
		//公開範囲は別指定
		$str_open_level_code = '00';
		if ($form['open_level_code'] != '00') {		//公開範囲を選択している場合
			$str_open_level_code =  $form['open_level_code'] ;
		}

		// ------------ 
		// db検索処理 and or ２回行う
		if (!$err_str) {
			$diary_row_array_result = ACSDiary::get_diary_where_array($str_where[1],$str_open_level_code,array());
			//複合条件のサマリ
			//and完全一致を省いた日記からor部分一致を検索する
			if ($str_where_create[1]['str_count'] == 2 || $str_where_create[2]['str_count'] == 2) {
				$str_where_create['not_id'] = array();
				foreach ($diary_row_array_result as $index => $diary_row) {
					array_push($str_where_create['not_id'], $diary_row['diary_id']);
				}
				$diary_row_array_not = ACSDiary::get_diary_where_array(
					$str_where[2],$str_open_level_code,$str_where_create['not_id']);
				foreach ($diary_row_array_not as $index => $diary_row) {
					array_push($diary_row_array_result, $diary_row);
				}
			}
			if (!$diary_row_array_result) {
				//$err_str = "該当する情報がありません";
				$err_str = ACSMsg::get_msg('User', 'SearchResultDiaryAction.class.php' ,'M001');
			} else {
				// 信頼済みコミュニティ情報
				foreach ($diary_row_array_result as $index => $diary_row) {
					if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
						$diary_row_array_result[$index]['trusted_community_row_array'] = ACSDiary::get_diary_trusted_community_row_array($diary_row['diary_id']);
					}
				}
			}
		}

		$request->setAttribute('diary_row_array_result',$diary_row_array_result);
		$request->setAttribute('err_str',$err_str);
		$request->setAttribute('form_pre',$form);
	}

	// 公開範囲
	$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(
			ACSMsg::get_mst('community_type_master','D10'), 
			ACSMsg::get_mst('contents_type_master','D21'));

		// マイフレンズグループ
		$friends_group_row_array = ACSUser::get_friends_group_row_array($user_community_id);

		// set
		$user->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		$request->setAttribute('diary_row_array', $diary_row_array);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$request->setAttribute('friends_group_row_array', $friends_group_row_array);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
	
}
?>
