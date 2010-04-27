<?php
/**
 * フォルダ 移動先選択
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/03/19 08:01:39 $
 */
require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class MoveFolderListInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// get
		$target_community_info_row = $request->getAttribute('target_community_info_row');
		$community_folder_obj = $request->getAttribute('community_folder_obj');

		// フォルダの所有者
		$target_community_id   = $target_community_info_row['community_id'];

		$target_community_info = '&community_id=' . $target_community_id;
		$folder_info      = '&folder_id=' . $community_folder_obj->folder_obj->get_folder_id();

		$action_url = "";
		$action_url  = $this->getControllerPath('Community', 'MoveFolder');
		$action_url .= $target_community_info;
		$action_url .= $folder_info;

		$cancel_url = "";
		$cancel_url  = $this->getControllerPath('Community', 'Folder');
		$cancel_url .= $target_community_info;
		$cancel_url .= $folder_info;

		// 移動対象のフォルダ
		$folder_row_array = array();
		$selected_folder_obj_array = $request->getAttribute('selected_folder_obj_array');
		if ($selected_folder_obj_array) {
			foreach ($selected_folder_obj_array as $selected_folder_obj) {
				$_folder_row = array();

				$_folder_row['folder_name'] = $selected_folder_obj->get_folder_name();
				$_folder_row['folder_id'] = $selected_folder_obj->get_folder_id();
				array_push($folder_row_array, $_folder_row);
			}
		}

		// 移動対象のファイル
		$file_row_array = array();
		$selected_file_obj_array = $request->getAttribute('selected_file_obj_array');
		if ($selected_file_obj_array) {
			foreach ($selected_file_obj_array as $selected_file_obj) {
				$_file_row = array();

				$_file_row['file_name'] = $selected_file_obj->get_display_file_name();
				$_file_row['file_id'] = $selected_file_obj->get_file_id();
				array_push($file_row_array, $_file_row);
			}
		}

		// フォルダ構成取得
		$community_folder_tree_obj = $request->getAttribute('community_folder_tree');
		$folder_tree = array();
		$selected_folder_id_array = $request->getAttribute('selected_folder_id_array');
		if (!$selected_folder_id_array) {
			$selected_folder_id_array = array();
		}
		$this->make_folder_tree($community_folder_tree_obj, $folder_tree, $selected_folder_id_array);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('MoveFolderList.tpl.php');

		// set
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('cancel_url', $cancel_url);

		// 移動対象
		$this->setAttribute('folder_row_array', $folder_row_array);
		$this->setAttribute('file_row_array', $file_row_array);

		// 移動先選択肢
		$this->setAttribute('folder_tree', $folder_tree);

		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		return parent::execute();
	}

	function make_folder_tree ($root_folder_obj, &$_folder_tree, &$selected_folder_id_array, $tree_level = 0) {
		// ルートフォルダ追加
		if ($tree_level == 0) {
			$folder_row = array();
			$folder_row['folder_id']   = $root_folder_obj->get_folder_id();
			$folder_row['folder_name'] = $root_folder_obj->get_folder_name();
			$folder_row['tree_level']  = $tree_level;
			array_push($_folder_tree, $folder_row);
		}

		$sub_folder_obj_array = $root_folder_obj->get_sub_folder_obj_array();

		foreach ($sub_folder_obj_array as $sub_folder_obj) {
			$tree_level++;
			$folder_row = array();

			// 選択できない移動先フォルダは追加しない
			if (!in_array($sub_folder_obj->get_folder_id(), $selected_folder_id_array)) {
				// row 設定
				$folder_row['folder_id']   = $sub_folder_obj->get_folder_id();
				$folder_row['folder_name'] = $sub_folder_obj->get_folder_name();
				$folder_row['tree_level']  = $tree_level;

				array_push($_folder_tree, $folder_row);

				// さらにサブフォルダを検索（再帰）
				$this->make_folder_tree($sub_folder_obj, $_folder_tree, $selected_folder_id_array, $tree_level);
			}

			// 1階層上の検索に戻る
			$tree_level--;
		}
	}
}
?>
