<?php
/**
 * 写真アップロード機能　Viewクラス
 * プロフィール写真変更画面
 * @package  acs/webapp/modules/Communication/views
 * EditProfileImageView::INPUT
 * @author   akitsu
 * @since	PHP 4.0
 * @revision ver1.0  2006/02/16
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
	 * @return parent::execute()		  BaseViewクラス実行
	 */
	function execute() {
	    $context = $this->getContext();
		$controller = $context->getController();
	    $request =  $context->getRequest();
		$user = $context->getUser();
		//actionクラスから通知setされたuser_community_id を取得する
		$user_community_id = $request->getAttribute('community_id');
		//actionクラスから通知setされたtarget_community_info_row を取得し$profile変数のオブジェクト配列とする
		$profile = $request->getAttribute('target_community_info_row');

		// 画像ファイルのパスを設定
		$profile['image_url'] = ACSCommunity::get_image_url($user_community_id);
		$file_id = "";			//更新処理用 (新規追加への対応)
				
		// メニュー設定 新規登録以外は削除メニューを表示する
		$image_new_mode = $request->getAttribute('image_new_add');
		$menu = array();
		if($image_new_mode){
			$menu['delete_image_url'] = null;
		}else{
			$file_id = $profile['file_id'];		//更新処理用 追加
			//削除の意思確認URL ver1.3
			$delete_confirm_url = $this->getControllerPath('Community','DeleteProfileImage');
			$delete_confirm_url .= '&community_id=' . $user_community_id;
			$delete_confirm_url .= '&file_id=' . $file_id;
			$menu['delete_image_url'] = $delete_confirm_url;
		}
		$menu['image_new_mode']=$image_new_mode;

		//画像のアップロードURL ver1.1
		$upload_image_url = $this->getControllerPath('Community','UploadProfileImage');
		$upload_image_url .= '&community_id=' . $user_community_id;
		$upload_image_url .= '&image_new_mode=' . $image_new_mode;	//ver1.1
		$upload_image_url .= '&file_id=' . $file_id;			//更新処理用　追加

		// エラーメッセージ設定
		$error_msg_array = array();
		$error_row = $request->getAttribute('error_row');
		if ($error_row) {
			foreach ($error_row as $key => $msg) {
				array_push($error_msg_array, $msg);
			}
		}

		//set

		$back_url = $request->getAttribute('back_url');
		$this->setAttribute('back_url', $back_url);
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
