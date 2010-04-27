<?php
/**
 * プロフィール写真変更画面 Actionクラス
 * @package  acs/webapp/modules/Community/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  ver1.0  2006/02/14 $
 */

class EditProfileImageAction extends BaseAction
{
	/**
	 * 初期画面
	 * GETメソッドの場合、呼ばれる
	 */
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		/* エラーを取得 */
		//$error_row = $user->getAttribute('error_row');
		//$user->removeAttribute('error_row');

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('community_id');
		// プロフィール情報を取得
		$target_community_info_row = ACSCommunity::get_community_row($user_community_id);
		// 戻るリンク先情報を取得
		$back_url =  $this->getControllerPath('Community','Index');
		$back_url .= '&community_id=' . $user_community_id;

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// 画像が新規登録か更新かを判定する true:新規 false:更新
		// ファイル情報の存在確認

		$image_file_id = $target_community_info_row['file_id'];// ファイル情報取得
		if ($image_file_id) {
			$image_new_add = false;
		} else {
			$image_new_add = true;
		}
			
		// set
		//user_community_id をviewクラスへ通知する
		$request->setAttribute('community_id', $user_community_id);
		//target_user_info_rowをviewクラスへ通知する
		$request->setAttribute('target_community_info_row', $target_community_info_row);
		//画像が新規登録か更新かのimage_new_addをviewクラスへ通知する
		$request->setAttribute('image_new_add',$image_new_add);
		//戻るリンク先をviewクラスへ通知する
		$request->setAttribute('back_url', $back_url);	

		return View::INPUT;
	}

	function getRequestMethods() {
			return Request::GET;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		return array('COMMUNITY_ADMIN');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$user = $context->getUser();

		// コミュニティ管理者はOK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		return false;
	}
}
?>
