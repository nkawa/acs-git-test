<?php
/**
 * フォルダ情報詳細
 *
 * @author  kuwayama
 * @version $Revision: 1.4 $ $Date: 2006/12/08 05:06:37 $
 */
require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class FolderDetailSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// get
		$acs_user_info_row      = $user->getAttribute('acs_user_info_row');
		$target_community_info_row   = $request->getAttribute('target_community_info_row');
		$community_folder_obj        = $request->getAttribute('community_folder_obj');
		$detail_community_folder_obj = $request->getAttribute('detail_community_folder_obj');

		$target_community_id   = $target_community_info_row['community_id'];

		// URL付加情報（表示するユーザ情報）
		$target_community_info = '&community_id=' . $target_community_id;
		$folder_info           = '&folder_id=' . $community_folder_obj->folder_obj->get_folder_id();

		// フォルダの所有者
		$_target_community_info_row['community_name'] = $target_community_info_row['community_name'];
		$_target_community_info_row['top_page_url']   = $this->getControllerPath('Community', DEFAULT_ACTION);
		$_target_community_info_row['top_page_url']  .= $target_community_info;

		// メンバかどうか
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $target_community_id);

		// プットフォルダかどうか
		$is_put_folder = $detail_community_folder_obj->folder_obj->is_put_folder($target_community_info_row['community_id']);

		// フォルダパス情報
		$path_folder_obj_array = $community_folder_obj->get_path_folder_obj_array();
		$path_folder_row_array = array();
		foreach ($path_folder_obj_array as $path_folder_obj) {
			$path_folder_row = array();

			// フォルダ名
			if ($path_folder_obj->get_is_root_folder()) {
				$folder_name  = $target_community_info_row['community_name'];
				//$folder_name .= "のフォルダ";
				$folder_name = ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'FolderDetailSuccessView.class.php', 'FOLDER_NM'),
					array("{COMMUNITY_NAME}" => $target_community_info_row['community_name']));
			} else {
				$folder_name = $path_folder_obj->get_folder_name();
			}

			// フォルダURL
			$link_url  = $this->getControllerPath('Community', 'Folder');
			$link_url .= $target_community_info;
			$link_url .= "&folder_id=" . $path_folder_obj->get_folder_id();

			// set
			$path_folder_row['folder_name'] = $folder_name;
			$path_folder_row['link_url']    = $link_url;

			array_push($path_folder_row_array, $path_folder_row);
		}

		/* ---------------- */
		/* フォルダ詳細情報 */
		/* ---------------- */
		$detail_folder_obj = $detail_community_folder_obj->get_folder_obj();
		$detail_folder_row = array();
		$detail_folder_row['folder_name'] = $detail_folder_obj->get_folder_name();
		$detail_folder_row['comment']     = $detail_folder_obj->get_comment();
		$detail_folder_row['open_level_name'] = $detail_folder_obj->get_open_level_name();

		// 閲覧許可コミュニティ名作成
		$detail_folder_row['trusted_community_row_array'] = array();
		$trusted_community_row_array = $detail_folder_obj->get_trusted_community_row_array();
		foreach ($trusted_community_row_array as $trusted_community_row) {
			$_trusted_community_row = array();
			$_trusted_community_row['community_name'] = $trusted_community_row['community_name'];
			$_trusted_community_row['community_top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION)
				 . '&community_id=' . $trusted_community_row['community_id'];
			array_push($detail_folder_row['trusted_community_row_array'], $_trusted_community_row);
		}

		// 登録者
		$detail_folder_row['entry_user_community_name']      = $detail_folder_obj->get_entry_user_community_name();
		$detail_folder_row['entry_user_community_link_url']  = $this->getControllerPath('User', DEFAULT_ACTION);
		$detail_folder_row['entry_user_community_link_url'] .= '&id=' . $detail_folder_obj->get_entry_user_community_id();;
		$detail_folder_row['entry_date']                     = $detail_folder_obj->get_entry_date_yyyymmddhmi();

		// 更新者
		$detail_folder_row['update_user_community_name']      = $detail_folder_obj->get_update_user_community_name();
		$detail_folder_row['update_user_community_link_url']  = $this->getControllerPath('User', DEFAULT_ACTION);
		$detail_folder_row['update_user_community_link_url'] .= '&id=' . $detail_folder_obj->get_update_user_community_id();;
		$detail_folder_row['update_date']                     = $detail_folder_obj->get_update_date_yyyymmddhmi();

		// メニュー
		if ($is_community_member && !$is_put_folder) {
			// フォルダ情報編集メニュー
			$edit_folder_url  = $this->getControllerPath('Community', 'EditFolder');
			$edit_folder_url .= $target_community_info;
			$edit_folder_url .= $folder_info;
			$edit_folder_url .= '&edit_folder_id=' . $detail_community_folder_obj->folder_obj->get_folder_id();
			$menu['edit_folder_url'] = $edit_folder_url;

			// フォルダ削除メニュー
			$delete_folder_url  = $this->getControllerPath('Community', 'DeleteFolder');
			$delete_folder_url .= $target_community_info;
			$delete_folder_url .= $folder_info;
			$delete_folder_url .= '&action_type=confirm';
			$delete_folder_url .= '&selected_folder[]=' . $detail_community_folder_obj->folder_obj->get_folder_id();
			$menu['delete_folder_url'] = $delete_folder_url;

			// フォルダ移動メニュー
			$move_folder_url  = $this->getControllerPath('Community', 'MoveFolderList');
			$move_folder_url .= $target_community_info;
			$move_folder_url .= $folder_info;
			$move_folder_url .= '&selected_folder[]=' . $detail_community_folder_obj->folder_obj->get_folder_id();
			$menu['move_folder_url'] = $move_folder_url;
		}

		// 戻り先URL（フォルダ一覧）
		$back_url = "";
		$back_url  = $this->getControllerPath('Community', 'Folder');
		$back_url .= $target_community_info;
		$back_url .= $folder_info;

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('FolderDetail.tpl.php');

		// set
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('is_put_folder', $is_put_folder);
		$this->setAttribute('target_community_info_row', $_target_community_info_row);
		$this->setAttribute('path_folder_row_array', $path_folder_row_array);
		$this->setAttribute('detail_folder_row', $detail_folder_row);
		$this->setAttribute('menu', $menu);
		$this->setAttribute('back_url', $back_url);

		return parent::execute();
	}
}
?>
