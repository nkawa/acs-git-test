<?php
/**
 * ユーザのフォルダプット先コミュニティ設定
 *
 * @author  kuwayama
 * @version $Revision: 1.5 $ $Date: 2006/06/16 07:52:35 $
 */
//class FolderPutCommunityInputView extends SimpleBaseView
class FolderPutCommunityInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row    = $user->getAttribute('acs_user_info_row');
		// 表示対象のユーザ情報
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		// 表示対象のフォルダID
		$target_folder_id = $request->getAttribute('target_folder_id');
		// 選択対象となるコミュニティ
		$community_row_array  = $request->getAttribute('community_row_array');
		// 設定されているプット先コミュニティ
		$put_community_row_array  = $request->getAttribute('put_community_row_array');


		// ----------------------------
		// 加工
		// プット先コミュニティ設定URL
		$put_community_url  = $this->getControllerPath('User', 'FolderPutCommunity');
		$put_community_url .= '&id=' . $target_user_info_row['user_community_id'];
		$put_community_url .= '&folder_id=' . $target_folder_id;

		// 選択可能なコミュニティを加工
		$select_community_row_array = array();
		foreach ($community_row_array as $community_row) {
			$select_community_row = array();

			$select_community_row['community_id'] = $community_row['community_id'];
			$select_community_row['community_name'] = $community_row['community_name'];
			$select_community_row['top_page_url']  = $this->getControllerPath('Community', DEFAULT_ACTION);
			$select_community_row['top_page_url'] .= '&community_id=' . $community_row['community_id'];

			// フォルダツリー取得
			$select_community_row['folder_tree'] = "";
			//$folder_tree = $community_row['folder_tree'];
			$folder_tree = array();
			$this->make_folder_tree($community_row['folder_tree'], $folder_tree);

			// 設定されているかどうか
			foreach ($put_community_row_array as $put_community_row) {
				// フォルダ検索
				$folder_tree_index = 0;
				foreach ($folder_tree as $folder_row) {
					if ($put_community_row['community_id'] == $community_row['community_id'] &&
						$put_community_row['put_community_folder_id'] == $folder_row['folder_id']) {

						$folder_tree[$folder_tree_index]['is_selected'] = true;
						$is_selected = true;
						break;
					}
					$folder_tree_index++;
				}
			}

			// フォルダツリーをセット
			$select_community_row['folder_tree'] = $folder_tree;

			array_push($select_community_row_array, $select_community_row);
		}


		// ----------------------------
		// set
		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('FolderPutCommunity.tpl.php');

		$this->setAttribute('put_community_url', $put_community_url);
		$this->setAttribute('select_community_row_array', $select_community_row_array);

		return parent::execute();
	}

	function make_folder_tree ($root_folder_obj, &$_folder_tree, $tree_level = 0) {

		$sub_folder_obj_array = $root_folder_obj->get_sub_folder_obj_array();

		foreach ($sub_folder_obj_array as $sub_folder_obj) {
			$tree_level++;
			$folder_row = array();

			// row 設定
			$folder_row['folder_id']   = $sub_folder_obj->get_folder_id();
			$folder_row['folder_name'] = $sub_folder_obj->get_folder_name();
			$folder_row['is_selected'] = false;    // 初期化
			$folder_row['tree_level']  = $tree_level;

			array_push($_folder_tree, $folder_row);

			// さらにサブフォルダを検索（再帰）
			$this->make_folder_tree($sub_folder_obj, $_folder_tree, $tree_level);

			// 1階層上の検索に戻る
			$tree_level--;
		}
	}
}
?>
