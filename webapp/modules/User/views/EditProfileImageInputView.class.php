<?php
/**
 * 写真アップロード機能　Viewクラス
 * プロフィール写真変更画面
 * @package  acs/webapp/modules/User/views
 * EditProfileImageView::INPUT
 * @author   akitsu
 * @since	PHP 4.0
 * @revision ver1.5 $Date: 2008/03/24 07:00:36 $
 */

class EditProfileImageInputView extends BaseView
{
	 /**
	 * execute メソッド
	 *　メッセージパッシング
	 * @param object   $user			ユーザ情報
	 * @param object   $request		 リクエスト情報
	 * @param object   $controller	  ＆アドレス　コントローラ
	 *
	 * @return parent::execute($controller, $request, $user)		  BaseViewクラス実行
	 */
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		//actionクラスから通知setされたuser_community_id を取得する
		$user_community_id = $request->getAttribute('user_community_id');
		//actionクラスから通知setされたtarget_user_info_row を取得し$profile変数のオブジェクト配列とする
		$profile = $request->getAttribute('target_user_info_row');

		$image_new_mode = $request->getAttribute('image_new_add');
		$image_file_label = $request->getAttribute('image_file_label');
		$open_level_code_row = $request->getAttribute('open_level_code_row');
		$display_for_public = ACSSystemConfig::get_keyword_value(
				ACSMsg::get_mst('system_config_group','D08'), 'DISPLAY_PHOTOS_FOR_PUBLIC');		
		if ($display_for_public == NULL) {
			$display_for_public = "0";
		}
				
		// 画像ファイルのパスを設定
		$image_file_array = ACSUser::get_image_url_with_open_level(
				$user_community_id, $open_level_code_row);
//		$profile['image_url'] = ACSUser::get_image_url($user_community_id);

		$file_id = "";			//更新処理用　追加 ver1.2 2006/2/13 (新規追加への対応)

		// メニュー設定 新規登録以外は削除メニューを表示する
		$menu = array();
		for ($i = 0; $i < count($open_level_code_row); $i++) {
			$key_name = 'file_id_ol' . $open_level_code_row[$i];
			if ($image_new_mode[$key_name]) {
				$menu['delete_image_url' . $open_level_code_row[$i]] = null;
			} else {
	            $file_id = $profile[$key_name];
				$delete_confirm_url = 
						$this->getControllerPath(
								'User','DeleteProfileImage');
				$delete_confirm_url .= '&id=' . $user_community_id;
				$delete_confirm_url .= '&file_id=' . $file_id;
				$delete_confirm_url .= '&open_level_code=' . $open_level_code_row[$i];
				$menu['delete_image_url' . $open_level_code_row[$i]]
					= $delete_confirm_url;
			}
			$menu['image_new_mode' . $open_level_code_row[$i]] 
					= $image_new_mode[$key_name];

			//画像のアップロードURL ver1.1
			$upload_image_url[$key_name] = $this->getControllerPath(
							'User','UploadProfileImage');
			$upload_image_url[$key_name] .= '&id=' . $user_community_id;
			$upload_image_url[$key_name] .= '&image_new_mode=' . $image_new_mode[$key_name];
			$upload_image_url[$key_name] .= '&file_id=' . $file_id;	
			$upload_image_url[$key_name] .= '&open_level_code=' . $open_level_code_row[$i];
		}

		// エラーメッセージ設定
		$error_msg_array = array();
		$error_row = $request->getAttribute('error_row');
		if ($error_row) {
			foreach ($error_row as $key => $msg) {
				array_push($error_msg_array, $msg);
			}
		}

		//set
		$this->setAttribute('image_file_array', $image_file_array);
		$this->setAttribute('display_for_public', $display_for_public);
		$this->setAttribute('profile', $profile);
		$this->setAttribute('menu', $menu);
		$this->setAttribute('error_msg_array', $error_msg_array);
		$this->setAttribute('upload_image_url', $upload_image_url);

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('EditProfileImage.tpl.php');
		return parent::execute();
	}
}
?>
