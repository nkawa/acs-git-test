<?php
/**
 * ダイアリー検索結果機能　Viewクラス
 * @package  acs/webapp/modules/User/views
 * SearchResultDiaryView::SUCCESS
 * @author  akitsu
 * @since	PHP 4.0
 */
// $Id: SearchResultDiaryView::SUCCESS.class.php,v 1.9 2007/03/28 02:51:48 w-ota Exp $


class SearchResultDiarySuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$diary_row_array = $request->getAttribute('diary_row_array_result');
		$open_level_master_row_array = $request->getAttribute('open_level_master_row_array');
		$friends_group_row_array = $request->getAttribute('friends_group_row_array');
		$err_str = $request->getAttribute('err_str');
		$form_pre = $request->getAttribute('form_pre');

		// トップページURL
		$link_page_url['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Diary') . '&id=' . $acs_user_info_row['user_community_id'];
		//他人の日記を閲覧している場合のトップページURL
		$link_page_url['else_user_top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Index') . '&id=' . $target_user_info_row['community_id'];
		//他人の日記を閲覧している場合の日記ページURL
		$link_page_url['else_user_diary_url'] = $this->getControllerPath(DEFAULT_MODULE, 'Diary') . '&id=' . $target_user_info_row['community_id'];

		//検索画面url
		$link_page_url['search_diary_url'] = SCRIPT_PATH;
		// get でサブミットするための、遷移先情報
		$module = 'User';
		$action = 'SearchResultDiary';
		$id = $target_user_info_row['community_id'];
		$move_id = '2';

		// 加工
		if($diary_row_array){
			foreach ($diary_row_array as $index => $diary_row) {
				// ユーザ画像URL
				$diary_row_array[$index]['image_url'] = ACSUser::get_image_url($diary_row['community_id'],'thumb');
				//ユーザページ
				$user_row =  ACSUser::get_user_profile_row($diary_row['community_id']);
				$diary_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $diary_row['community_id'];
				$diary_row_array[$index]['community_name'] = $user_row['community_name'];
				// 投稿日時
				$diary_row_array[$index]['post_date']  = ACSLib::convert_pg_date_to_str($diary_row['post_date']);
				// コメントページURL
				$diary_row_array[$index]['diary_comment_url'] = $this->getControllerPath('User', 'DiaryComment') . '&id=' . $diary_row['community_id'] . '&diary_id=' . $diary_row['diary_id'];

				// 信頼済みコミュニティ(マイフレンズグループ)が定義されているか
				if ($diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
					if (count($diary_row['trusted_community_row_array'])
						&& $diary_row['trusted_community_row_array'][0]['community_type_name'] == ACSMsg::get_mst('community_type_master','D20')) {
						$diary_row_array[$index]['trusted_community_flag'] = 0;
					} else {
						$diary_row_array[$index]['trusted_community_flag'] = 1;
					}
				}
			}
			//---- アクセス制御 ----//
			$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $target_user_info_row);
			$diary_row_array = ACSAccessControl::get_valid_row_array_for_user_community($acs_user_info_row, $role_array, $diary_row_array);
			//特別::全件検索の場合　ターゲットが自分のままになってしまうことを回避
			$diary_row_array_result = array();
			foreach ($diary_row_array as $index => $diary_row) {
				$diary_target_user_info_row = ACSUser::get_user_info_row_by_user_community_id($diary_row['community_id']);
				$role_array = ACSAccessControl::get_user_community_role_array($acs_user_info_row, $diary_target_user_info_row);
				$is_valid_user = ACSAccessControl::is_valid_user_for_community($acs_user_info_row, $role_array, $diary_row);
				if($is_valid_user){		//アクセス権がある
					array_push($diary_row_array_result, $diary_row);
				}
			}
			//----------------------//
		}
		// 本人のページかどうか
		if ($target_user_info_row['user_community_id'] == $acs_user_info_row['user_community_id']) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('diary_row_array_result', $diary_row_array_result);
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('link_page_url', $link_page_url);
		$this->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$this->setAttribute('friends_group_row_array', $friends_group_row_array);
		$this->setAttribute('err_str',$err_str);
		$this->setAttribute('form_pre',$form_pre);

		$this->setAttribute('module',$module);
		$this->setAttribute('action',$action);
		$this->setAttribute('id',$id);
		$this->setAttribute('move_id',$move_id);
		
		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SearchResultDiary.tpl.php');

		return parent::execute();
	}
}

?>
