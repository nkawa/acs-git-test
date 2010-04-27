<?php
/**
 * 掲示板　検索結果機能　Viewクラス
 * @package  acs/webapp/modules/Community/views
 * SearchResultBBSView::SUCCESS
 * @author  akitsu
 * @since	PHP 4.0
 */
// $Id: SearchResultBBSView::SUCCESS.class.php,v 1.7 2007/03/28 02:51:44 w-ota Exp $


class SearchResultBBSSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		
		$community_id = $request->getParameter('community_id');
		// get
		$community_row = $request->getAttribute('community_row');
		$bbs_row_array = $request->getAttribute('bbs_row_array_result');
		$open_level_master_row_array = $request->getAttribute('open_level_master_row_array');
		$err_str = $request->getAttribute('err_str');
		$form_pre = $request->getAttribute('form_pre');
		// トップページURL
		$link_page_url['top_page_url'] = $this->getControllerPath('Community', 'Index') . '&id=' . '&community_id=' . $community_id;
		// BBS URL
		$link_page_url['bbs_page_url'] = $this->getControllerPath('Community', 'BBS') . '&id=' . '&community_id=' . $community_id;
		//検索画面url
		$link_page_url['search_bbs_url'] = SCRIPT_PATH;
		// get でサブミットするための、遷移先情報
		$module = 'Community';
		$action = 'SearchResultBBS';
		$community_id = $community_id;
		$move_id = '2';

		// 加工
		if($bbs_row_array){
			foreach ($bbs_row_array as $index => $bbs_row) {
				// ユーザ画像URL
				$bbs_row_array[$index]['image_url'] = ACSUser::get_image_url($bbs_row['user_community_id'],'thumb');
				//ユーザページ
				$user_row =  ACSUser::get_user_profile_row($bbs_row['user_community_id']);
				$bbs_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $bbs_row['user_community_id'];
				$bbs_row_array[$index]['community_name'] = $user_row['community_name'];
				// 投稿日時
				$bbs_row_array[$index]['post_date']  = ACSLib::convert_pg_date_to_str($bbs_row['post_date']);
				// コメントページURL
				$bbs_row_array[$index]['bbs_res_url'] = $this->getControllerPath('Community', 'BBSRes') . '&community_id=' . $bbs_row['community_id'] . '&bbs_id=' . $bbs_row['bbs_id'];

				// 信頼済みコミュニティ(マイフレンズグループ)が定義されているか
				if ($bbs_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')) {
					if (count($bbs_row['trusted_community_row_array'])
						&& $bbs_row['trusted_community_row_array'][0]['community_type_name'] == ACSMsg::get_mst('contents_type_master','D11')) {
						$bbs_row_array[$index]['trusted_community_flag'] = 0;
					} else {
						$bbs_row_array[$index]['trusted_community_flag'] = 1;
					}
				}
			}
			//---- アクセス制御 ----//
			$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $community_row);
			$bbs_row_array = ACSAccessControl::get_valid_row_array_for_community($acs_user_info_row, $role_array, $bbs_row_array);
			//特別::全件検索の場合　ターゲットが自分のままになってしまうことを回避
			$bbs_row_array_result = array();
			foreach ($bbs_row_array as $index => $bbs_row) {
				$role_array = ACSAccessControl::get_community_role_array($acs_user_info_row, $bbs_row);
				$is_valid_user = ACSAccessControl::is_valid_user_for_community($acs_user_info_row, $role_array, $bbs_row);
				if($is_valid_user){		//アクセス権がある
					// 表示する掲示板のコミュニティ名を取得
					$bbs_community_name = ACSCommunity::get_community_row($bbs_row['community_id']);
					$bbs_row['bbs_community_name'] = $bbs_community_name['community_name'];
					$bbs_row['bbs_community_page_url'] = $this->getControllerPath('Community', 'Index') . '&community_id=' . $bbs_row['community_id'];
					array_push($bbs_row_array_result, $bbs_row);
				}
			}
			//----------------------//
		}
		// 本人のページかどうか
		if ($community_id == $acs_user_info_row['user_community_id']) {
			$is_self_page = 1;
		} else {
			$is_self_page = 0;
		}
		// set
		$this->setAttribute('community_row', $community_row);
		$this->setAttribute('bbs_row_array_result', $bbs_row_array_result);
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('link_page_url', $link_page_url);
		$this->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$this->setAttribute('friends_group_row_array', $friends_group_row_array);
		$this->setAttribute('err_str',$err_str);
		$this->setAttribute('form_pre',$form_pre);

		$this->setAttribute('module',$module);
		$this->setAttribute('action',$action);
		$this->setAttribute('community_id',$community_id);
		$this->setAttribute('move_id',$move_id);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('SearchResultBBS.tpl.php');

		return parent::execute();
	}
}

?>
