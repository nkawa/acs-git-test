<?php
// $Id: EditFileDetailView::SUCCESS.class.php,v 1.2 2006/12/08 05:06:37 w-ota Exp $

class EditFileDetailSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$target_community_row = $request->getAttribute('target_community_row');
		$file_obj = $request->getAttribute('file_obj');
		$community_folder_obj = $request->getAttribute('community_folder_obj');
		$file_detail_info_row = $request->getAttribute('file_detail_info_row');

		$target_folder_obj    = $community_folder_obj->get_folder_obj();
		$target_community_id   = $target_community_row['community_id'];

		// URL付加情報（表示するユーザ情報）
		$target_community_info = '&community_id=' . $target_community_id;
		$target_folder_info = '&folder_id=' . $community_folder_obj->folder_obj->get_folder_id();

		// コミュニティメンバかどうか
		$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'],
																 $target_community_row['community_id']);

		// ルートフォルダかどうか
		$is_root_folder = $target_folder_obj->get_is_root_folder();

		// プットフォルダかどうか
		$is_put_folder = $target_folder_obj->is_put_folder($target_community_row['community_id']);

		// コミュニティ情報 //
		$target_community_row['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . $target_community_info;


		// ファイル情報
		$file_info_row = array();
		$file_info_row['file_id'] = $file_obj->get_file_id();
		$file_info_row['display_file_name'] = $file_obj->get_display_file_name();
		$file_info_row['mime_type'] = $file_obj->get_mime_type();
		$file_info_row['file_size_kb'] = $file_obj->get_file_size_kb();
		$file_info_row['file_size'] = $file_obj->get_file_size();
		$file_info_row['entry_user_community_name'] = $file_obj->get_entry_user_community_name();
		$file_info_row['entry_date'] = $file_obj->get_entry_date_yyyymmddhmi();
		$file_info_row['update_date'] = $file_obj->get_update_date_yyyymmddhmi();
		$file_info_row['update_user_community_name'] = $file_obj->get_update_user_community_name();
		$file_info_row['link_url'] = $this->getControllerPath('Community', 'DownloadFile')
			 . $target_community_info . "&file_id=" . $file_obj->get_file_id() . $target_folder_info;

		// 登録者
		$file_info_row['entry_user_community_name']      = $file_obj->get_entry_user_community_name();
		$file_info_row['entry_user_community_link_url']  = $this->getControllerPath('User', DEFAULT_ACTION);
		$file_info_row['entry_user_community_link_url'] .= '&id=' . $file_obj->get_entry_user_community_id();;
		$file_info_row['entry_date']                     = $file_obj->get_entry_date_yyyymmddhmi();

		// 更新者
		$file_info_row['update_user_community_name']      = $file_obj->get_update_user_community_name();
		$file_info_row['update_user_community_link_url']  = $this->getControllerPath('User', DEFAULT_ACTION);
		$file_info_row['update_user_community_link_url'] .= '&id=' . $file_obj->get_update_user_community_id();;
		$file_info_row['update_date']                     = $file_obj->get_update_date_yyyymmddhmi();
		

		// フォルダパス情報
		$path_folder_obj_array = $community_folder_obj->get_path_folder_obj_array();
		$path_folder_row_array = array();
		foreach ($path_folder_obj_array as $path_folder_obj) {
			$path_folder_row = array();

			// フォルダ名
			if ($path_folder_obj->get_is_root_folder()) {
				$folder_name  = $target_community_row['community_name'];
				//$folder_name .= "のフォルダ";
				$folder_name = ACSMsg::get_tag_replace(ACSMsg::get_msg('Community', 'EditFileDetailSuccessView.class.php', 'FOLDER_NM'),
					array("{COMMUNITY_NAME}" => $target_community_row['community_name']));
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
		$detail_folder_obj = $community_folder_obj->get_folder_obj();
		$detail_folder_row = array();
		$detail_folder_row['folder_name'] = $detail_folder_obj->get_folder_name();
		$detail_folder_row['comment']     = $detail_folder_obj->get_comment();
		$detail_folder_row['open_level_name'] = $detail_folder_obj->get_open_level_name();
		// 閲覧許可コミュニティ名作成
		$detail_folder_row['trusted_community_row_array'] = array();
		$trusted_community_row_array = $detail_folder_obj->get_trusted_community_row_array();
		if ($is_community_member) {
			foreach ($trusted_community_row_array as $trusted_community_row) {
				$_trusted_community_row = array();
				$_trusted_community_row['community_name'] = $trusted_community_row['community_name'];
				$_trusted_community_row['community_top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION)
					 . '&community_id=' . $trusted_community_row['community_id'];

				array_push($detail_folder_row['trusted_community_row_array'], $_trusted_community_row);
			}
		}
		// link url
		$detail_folder_row['link_url'] = $this->getControllerPath('Community', 'Folder')
			 . $target_community_info . "&folder_id=" . $detail_folder_obj->get_folder_id();

		// action URL
		$action_url  = $this->getControllerPath('Community', 'EditFileDetail');
		$action_url .= $target_community_info;
		$action_url .= $target_folder_info;
		$action_url .= '&file_id=' . $file_info_row['file_id'];

		// 戻り先URL（ファイル詳細情報）
		$back_url = "";
		$back_url  = $this->getControllerPath('Community', 'FileDetail');
		$back_url .= $target_community_info;
		$back_url .= $target_folder_info;
		$back_url .= '&file_id=' . $file_info_row['file_id'];


		// set
		$this->setAttribute('target_community_row', $target_community_row);
		$this->setAttribute('is_community_member', $is_community_member);
		$this->setAttribute('file_info_row', $file_info_row);
		$this->setAttribute('path_folder_row_array', $path_folder_row_array);
		$this->setAttribute('detail_folder_row', $detail_folder_row);
		$this->setAttribute('file_detail_info_row', $file_detail_info_row);
		$this->setAttribute('menu', $menu);
		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('file_contents_type_master_row_array_array', $request->getAttribute('file_contents_type_master_row_array_array'));

		$this->setAttribute('file_category_master_array', $request->getAttribute('file_category_master_array'));
		$this->setAttribute('file_contents_type_master_array', $request->getAttribute('file_contents_type_master_array'));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('EditFileDetail.tpl.php');

		return parent::execute();
	}
}

?>
