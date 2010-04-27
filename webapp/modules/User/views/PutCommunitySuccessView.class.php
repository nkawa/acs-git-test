<?php
/**
 * ユーザフォルダのプット先コミュニティ表示
 *
 * @author  kuwayama
 * @version $Revision: 1.4 $ $Date: 2006/11/20 08:44:28 $
 */
require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');
class PutCommunitySuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		// get
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$user_folder_obj = $request->getAttribute('user_folder_obj');

		// フォルダの所有者
		$target_user_community_id   = $target_user_info_row['user_community_id'];
		$target_user_community_info_row['community_name'] = $target_user_info_row['community_name'];
		$target_user_community_info_row['top_page_url']  = $this->getControllerPath('User', DEFAULT_ACTION);
		$target_user_community_info_row['top_page_url'] .= '&id=' . $target_user_community_id;

		// 対象のフォルダ
		$folder_obj = $user_folder_obj->get_folder_obj();

		// 本人のページかどうか
		$is_self_page = false;
		if ($target_user_community_id == $acs_user_info_row['user_community_id']) {
			$is_self_page = true;
		}

		// 対象のフォルダ情報
		$folder_info_row = array();
		$folder_info_row['folder_name'] = $folder_obj->get_folder_name();
		$folder_info_row['folder_url']  = $this->getControllerPath('User', 'Folder');
		$folder_info_row['folder_url'] .= '&id=' . $target_user_community_id;

		// フォルダへ戻るURL
		$back_url = $folder_info_row['folder_url'];

		// プット先コミュニティ取得
		$org_put_community_row_array = $folder_obj->get_put_community_row_array();

		// プット先コミュニティを表示用に加工
		$put_community_row_array = array();    // テンプレートに渡すプット先コミュニティ情報
		foreach ($org_put_community_row_array as $put_community_row) {
			// メンバでない非公開コミュニティは表示対象にしない
			$_is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $put_community_row['community_id']);
			if ($put_community_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03') && !$_is_community_member) {
				continue;
			}

			$_put_community_row = array();

			// ----------------------
			// プット先コミュニティ加工
			// コミュニティ名
			$_put_community_row['community_name'] = $put_community_row['community_name'];

			// コミュニティトップページURL
			$_put_community_row['top_page_url']  = $this->getControllerPath('Community', DEFAULT_ACTION);
			$_put_community_row['top_page_url'] .= '&community_id=' . $put_community_row['community_id'];


			// ----------------------
			// プット先フォルダ加工

			// ACSFolder インスタンス生成
			//    プット先フォルダのパス情報を取得するため
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

			$_put_community_row['put_folder_name'] = $folder_path_str;

			// プット先フォルダURL
			$put_folder_url  = $this->getControllerPath('Community', 'Folder');
			$put_folder_url .= '&community_id=' . $put_community_row['community_id'];
			$put_folder_url .= '&folder_id=' . $put_community_row['put_community_folder_id'];
			$_put_community_row['put_folder_url'] = $put_folder_url;

			array_push($put_community_row_array, $_put_community_row);
		}


		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('PutCommunity.tpl.php');

		// set
		$this->setAttribute('target_user_community_info_row', $target_user_community_info_row);
		$this->setAttribute('is_self_page', $is_self_page);
		$this->setAttribute('folder_info_row', $folder_info_row);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('put_community_row_array', $put_community_row_array);

		return parent::execute();
	}
}
?>
