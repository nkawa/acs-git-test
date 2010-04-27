<?php
/**
 * 掲示板　検索結果　Actionクラス
 * 
 * SearchResultBBSAction.class.php
 * @package  acs/webapp/module/Community/Action
 * @author   akitsu
 * @since    PHP 4.0
 */
// $Id: SearchResultBBSAction.class.php,v 1.4 2006/11/20 08:44:12 w-ota Exp $

class SearchResultBBSAction extends BaseAction
{
	// POST 	検索ボタンの処理
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 表示対象となるコミュニティIDを取得
		$community_id = $request->ACSgetParameter('community_id');
		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

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
			    [q_text] => 検索条件					//条件（※必須）
			    [search_title] => title_in_serch		//件名を選択
			    [search_all] => subject_in_serch		//本文を選択
			    [open_level_code] => 00					//公開範囲（00は選択なし）（※必須）
			    [search_all_about] => all_in_serch		//すべてのBBSを選択
			*/

			// ------------ 検索情報の取得（注意！バイト単位で処理）
			for ($i = 1; $i < 3; $i++) {
				$str_where_create[$i] = ACSBBS::set_bbs_where_list($form,$i);
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
			if ($form['open_level_code'] != '00') {	//公開範囲を選択している場合
					$str_open_level_code =  $form['open_level_code'] ;
			}

			// ------------ 
			//db検索処理 and or ２回行う
			if (!$err_str) {
				$bbs_row_array_result = ACSBBS::get_bbs_where_array($str_where[1],$str_open_level_code,array());
				//複合条件のサマリ
				//and完全一致を省いた日記からor部分一致を検索する
				if ($str_where_create[1]['str_count'] == 2 || $str_where_create[2]['str_count'] == 2) {
					$str_where_create['not_id'] = array();
					foreach ($bbs_row_array_result as $index => $bbs_row) {
						array_push($str_where_create['not_id'], $bbs_row['bbs_id']);
					}
					$bbs_row_array_not = ACSBBS::get_bbs_where_array($str_where[2],$str_open_level_code,$str_where_create['not_id']);
					foreach ($bbs_row_array_not as $index => $bbs_row) {
						array_push($bbs_row_array_result, $bbs_row);
					}
				}
				if (!$bbs_row_array_result) {
					$err_str = ACSMsg::get_msg('Community', 'SearchResultBBSAction.class.php', 'M001');
				} else {
					// 信頼済みコミュニティ情報
					foreach ($bbs_row_array_result as $index => $bbs_row) {
						// 信頼済みコミュニティ一覧
						$bbs_row_array[$index]['trusted_community_row_array'] = ACSBBS::get_bbs_trusted_community_row_array($bbs_row['bbs_id']);
					}
				}
			}
		}

		$request->setAttribute('bbs_row_array_result',$bbs_row_array_result);
		$request->setAttribute('err_str',$err_str);
		$request->setAttribute('form_pre',$form);

		// 公開範囲
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(
				ACSMsg::get_mst('community_type_master','D40'), 
				ACSMsg::get_mst('contents_type_master','D42'));

		// set
		$user->setAttribute('acs_user_info_row', $acs_user_info_row);
		$request->setAttribute('community_row', $community_row);
		$request->setAttribute('bbs_row_array', $bbs_row_array);
		$request->setAttribute('open_level_master_row_array', $open_level_master_row_array);
//		$request->setAttribute('friends_group_row_array', $friends_group_row_array);

		return View::SUCCESS;
	}

	function isSecure () {
		return false;
	}
	
	// アクセス制御情報
	function get_access_control_info(&$controller, &$request, &$user) {
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 対象となるコミュニティIDを取得
		$community_id = $request->getParameter('community_id');

		// コミュニティ情報
		$community_row = ACSCommunity::get_community_profile_row($community_id);

		// アクセス制御情報 //
		$bbs_contents_row = ACSCommunity::get_contents_row($community_id, ACSMsg::get_mst('contents_type_master','D41'));
		$bbs_contents_row['trusted_community_row_array'] = ACSCommunity::get_contents_trusted_community_row_array($community_id, $bbs_contents_row['contents_type_code'], $bbs_contents_row['open_level_code']);
		$access_control_info = array(
				 'role_array' => ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row),
				 'contents_row_array' => array($bbs_contents_row)
		);

		return $access_control_info;
	}
}
?>
