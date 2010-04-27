<?php
/**
 * フォルダ情報詳細
 *
 * @author  kuwayama
 * @version $Revision: 1.7 $ $Date: 2006/12/08 05:06:44 $
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
		$target_user_info_row   = $request->getAttribute('target_user_info_row');
		$user_folder_obj        = $request->getAttribute('user_folder_obj');
		$detail_user_folder_obj = $request->getAttribute('detail_user_folder_obj');

		$target_user_community_id   = $target_user_info_row['user_community_id'];

		// URL付加情報（表示するユーザ情報）
		$target_community_info = '&id=' . $target_user_community_id;
		$folder_info      = '&folder_id=' . $user_folder_obj->folder_obj->get_folder_id();

		// フォルダの所有者
		$_target_user_info_row['community_name'] = $target_user_info_row['community_name'];
		$_target_user_info_row['top_page_url']   = $this->getControllerPath('User', DEFAULT_ACTION);
		$_target_user_info_row['top_page_url']  .= $target_community_info;

		// 本人かどうか
		if ($target_user_community_id == $acs_user_info_row['user_community_id']) {
			$is_self_page = true;
		} else {
			$is_self_page = false;
		}

		// プット可能なフォルダかどうか
		if ($user_folder_obj->is_put_available()) {
			$is_put_available = true;
		} else {
			$is_put_available = false;
		}

		// フォルダパス情報
		$path_folder_obj_array = $user_folder_obj->get_path_folder_obj_array();
		$path_folder_row_array = array();
		foreach ($path_folder_obj_array as $path_folder_obj) {
			$path_folder_row = array();

			// フォルダ名
			if ($path_folder_obj->get_is_root_folder()) {
				$folder_name  = $target_user_info_row['community_name'];
				//$folder_name .= "さんのフォルダ";
				$folder_name = ACSMsg::get_tag_replace(ACSMsg::get_msg('User', 'FolderDetailSuccessView.class.php' ,'FOLDER_NM'), array(
					"{COMMUNITY_NAME}" => $target_user_info_row['community_name']));

			} else {
				$folder_name = $path_folder_obj->get_folder_name();
			}

			// フォルダURL
			$link_url  = $this->getControllerPath('User', 'Folder');
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
		$detail_folder_obj = $detail_user_folder_obj->get_folder_obj();
		$detail_folder_row = array();
		$detail_folder_row['folder_name'] = $detail_folder_obj->get_folder_name();
		$detail_folder_row['comment']     = $detail_folder_obj->get_comment();
		$detail_folder_row['open_level_name'] = $detail_folder_obj->get_open_level_name();

		// 閲覧許可コミュニティ名作成
		$detail_folder_row['trusted_community_row_array'] = array();
		$trusted_community_row_array = $detail_folder_obj->get_trusted_community_row_array();
		if ($is_self_page) {
			foreach ($trusted_community_row_array as $trusted_community_row) {
				$_trusted_community_row = array();
				$_trusted_community_row['community_name'] = $trusted_community_row['community_name'];

				array_push($detail_folder_row['trusted_community_row_array'], $_trusted_community_row);
			}
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

		// プット先コミュニティ
		$detail_folder_row['put_community_row_array'] = array();
		if ($detail_folder_obj->get_put_community_row_array()) {
			foreach ($detail_folder_obj->get_put_community_row_array() as $put_community_row) {
				// メンバでない非公開コミュニティは表示対象にしない
				$_is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $put_community_row['community_id']);
				if ($put_community_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03') && !$_is_community_member) {
					continue;
				}

				$_put_community_row = array();
				// プット先コミュニティ情報
				$_put_community_row['community_name'] = $put_community_row['community_name'];
				$_put_community_row['top_page_url']   = $this->getControllerPath('Community', DEFAULT_ACTION);
				$_put_community_row['top_page_url']  .= '&community_id=' . $put_community_row['community_id'];

				// プット先フォルダ情報
				$put_folder_obj = new ACSCommunityFolder($put_community_row['community_id'], $acs_user_info_row, $put_community_row['put_community_folder_id']);

				// プット先フォルダ名（コミュニティフォルダのパス）
				$put_folder_path = $put_folder_obj->get_path_folder_obj_array();
				$folder_path_str = "";
				foreach ($put_folder_path as $_folder_obj) {
					// ルートフォルダは追加しない
					if ($_folder_obj->is_root_folder) {
						continue;
					}

					$folder_path_str .= $_folder_obj->get_folder_name();
					$folder_path_str .= "/";
				}
				$_put_community_row['folder_name']      = $folder_path_str;
				$_put_community_row['folder_link_url']  = $this->getControllerPath('Community', 'Folder');
				$_put_community_row['folder_link_url'] .= '&community_id=' . $put_community_row['community_id'];
				$_put_community_row['folder_link_url'] .= '&folder_id=' . $put_community_row['put_community_folder_id'];


				array_push($detail_folder_row['put_community_row_array'], $_put_community_row);
			}
		}

		// メニュー
		if ($is_self_page) {
			// フォルダ情報編集メニュー
			$edit_folder_url  = $this->getControllerPath('User', 'EditFolder');
			$edit_folder_url .= $target_community_info;
			$edit_folder_url .= $folder_info;
			$edit_folder_url .= '&edit_folder_id=' . $detail_user_folder_obj->folder_obj->get_folder_id();
			$menu['edit_folder_url'] = $edit_folder_url;

			// フォルダ削除メニュー
			$delete_folder_url  = $this->getControllerPath('User', 'DeleteFolder');
			$delete_folder_url .= $target_community_info;
			$delete_folder_url .= $folder_info;
			$delete_folder_url .= '&action_type=confirm';
			$delete_folder_url .= '&selected_folder[]=' . $detail_user_folder_obj->folder_obj->get_folder_id();
			$menu['delete_folder_url'] = $delete_folder_url;

			// フォルダ移動メニュー
			$move_folder_url  = $this->getControllerPath('User', 'MoveFolderList');
			$move_folder_url .= $target_community_info;
			$move_folder_url .= $folder_info;
			$move_folder_url .= '&selected_folder[]=' . $detail_user_folder_obj->folder_obj->get_folder_id();
			$menu['move_folder_url'] = $move_folder_url;
		}

		// 戻り先URL（フォルダ一覧）
		$back_url = "";
		$back_url  = $this->getControllerPath('User', 'Folder');
		$back_url .= $target_community_info;
		$back_url .= $folder_info;

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('FolderDetail.tpl.php');

		// set
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('is_put_available', $is_put_available);
		$this->setAttribute('target_user_info_row', $_target_user_info_row);
		$this->setAttribute('path_folder_row_array', $path_folder_row_array);
		$this->setAttribute('detail_folder_row', $detail_folder_row);
		$this->setAttribute('menu', $menu);
		$this->setAttribute('back_url', $back_url);

		return parent::execute();
	}
}
?>
