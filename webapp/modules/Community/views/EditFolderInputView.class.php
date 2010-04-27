<?php
/**
 * フォルダ 作成・変更
 *
 * @author  kuwayama
 * @version $Revision: 1.3 $ $Date: 2006/11/20 08:44:15 $
 */
require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class EditFolderInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// get
		$target_community_info_row = $request->getAttribute('target_community_info_row');
		$user_folder_obj = $request->getAttribute('user_folder_obj');
		$edit_folder_id = $request->getAttribute('edit_folder_id');
		$default_data_row = $request->getAttribute('default_data_row');  // デフォルト値として表示する値

		$parent_community_row_array = $request->getAttribute('parent_community_row_array');
		$sub_community_row_array = $request->getAttribute('sub_community_row_array');

		$target_community_id   = $target_community_info_row['community_id'];
		$view_mode = $request->getAttribute('view_mode');

		$target_community_info = '&community_id=' . $target_community_id;
		$folder_info      = '&folder_id=' . $user_folder_obj->folder_obj->get_folder_id();
		if ($view_mode == 'update') {
			$edit_folder_info = '&edit_folder_id=' . $edit_folder_id;
		} else {
			$edit_folder_info = "";
		}

		// フォルダの所有者
		$_target_community_info_row['community_id'] = $target_community_info_row['community_id'];
		$_target_community_info_row['community_name'] = $target_community_info_row['community_name'];
		$_target_community_info_row['top_page_url']   = $this->getControllerPath('User', DEFAULT_ACTION);
		$_target_community_info_row['top_page_url']  .= $target_community_info;


		// 登録・更新処理URL
		$action_url = "";
		$action_url  = $this->getControllerPath('Community', 'EditFolder');
		$action_url .= $target_community_info;
		$action_url .= $folder_info;
		$action_url .= $edit_folder_info;
		$action_url .= '&action_type=' . $view_mode;

		$cancel_url = "";
		if ($view_mode == 'create') {
			$cancel_url  = $this->getControllerPath('Community', 'Folder');
			$cancel_url .= $target_community_info;
			$cancel_url .= $folder_info;
		} elseif ($view_mode == 'update') {
			$cancel_url  = $this->getControllerPath('Community', 'FolderDetail');
			$cancel_url .= $target_community_info;
			$cancel_url .= $folder_info;
			$cancel_url .= '&detail_folder_id=' . $edit_folder_id;
		}

		// 公開範囲を設定できるかどうか
		$is_set_open_level_available = $user_folder_obj->is_set_open_level_available();

		// 公開範囲選択肢取得
		$open_level_master_row_array = ACSAccessControl::get_open_level_master_row_array(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D32'));
		// デフォルト表示データがある場合、is_default を変更する
		if ($default_data_row) {
			$selected_open_level_code = $default_data_row['open_level_code'];
			$index_count = 0;
			foreach ($open_level_master_row_array as $open_level_master_row) {
				if ($open_level_master_row['open_level_code'] == $selected_open_level_code) {
					$open_level_master_row_array[$index_count]['is_default'] = true;
				} else {
					$open_level_master_row_array[$index_count]['is_default'] = false;
				}
				$index_count++;
			}
		}

		// -------------------------
		// 閲覧許可コミュニティ
		$trusted_community_row_array = array();

		// 親コミュニティ
		$parent_community_info_array = $this->make_trusted_community_row_array($controller, $parent_community_row_array);
		//array_push($trusted_community_row_array, $parent_community_row_array);

		// サブコミュニティ
		$sub_community_info_array = $this->make_trusted_community_row_array($controller, $sub_community_row_array);
		//array_push($trusted_community_row_array, $sub_community_row_array);

		// 選択されているコミュニティ
		$selected_trusted_community_row_array = ACSCommunity::get_each_community_row_array($default_data_row['trusted_community_id_array']);
		$selected_trusted_community_info_array = $this->make_trusted_community_row_array($controller, $selected_trusted_community_row_array);

		// デフォルトで表示する閲覧許可コミュニティを作成
		//$trusted_community_row_array = array_merge($parent_community_info_array, $sub_community_info_array, $selected_trusted_community_info_array);
		//$trusted_community_row_array = array_unique($trusted_community_row_array);

		// 閲覧許可コミュニティ追加URL
		#index.php?module=Community&action=SelectTrustedCommunity&form_name=folder_open_level_form
		$add_trusted_community_url  = "";
		$add_trusted_community_url  = $this->getControllerPath('Community', 'SelectTrustedCommunity');
		$add_trusted_community_url .= '&form_name=folder_info';

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('EditFolder.tpl.php');

		// set
		$this->setAttribute('target_community_info_row', $_target_community_info_row);
		$this->setAttribute('view_mode', $view_mode);

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('cancel_url', $cancel_url);

		$this->setAttribute('is_set_open_level_available', $is_set_open_level_available);
		$this->setAttribute('open_level_master_row_array', $open_level_master_row_array);
		$this->setAttribute('parent_community_info_array', $parent_community_info_array);
		$this->setAttribute('sub_community_info_array', $sub_community_info_array);
		$this->setAttribute('selected_trusted_community_info_array', $selected_trusted_community_info_array);
		$this->setAttribute('add_trusted_community_url', $add_trusted_community_url);
//		$this->setAttribute('friends_group_row_array', $friends_group_row_array);

		$this->setAttribute('default_data_row', $default_data_row);


		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		return parent::execute();
	}

	function make_trusted_community_row_array (&$controller, $target_community_row_array) {
		$community_row_array = array();
		foreach ($target_community_row_array as $target_community_row) {
			$community_row = array();
			$community_row['community_id']   = $target_community_row['community_id'];
			$community_row['community_name'] = $target_community_row['community_name'];
			$community_row['top_page_url']   = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $target_community_row['community_id'];

			array_push($community_row_array, $community_row);
		}
		return $community_row_array;
	}
}
?>
