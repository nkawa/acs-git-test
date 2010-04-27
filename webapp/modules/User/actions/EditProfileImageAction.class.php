<?php
/**
 * プロフィール写真変更画面 Actionクラス
 * @package  acs/webapp/modules/User/action
 * @author   akitsu
 * @since	PHP 4.0
 * @version  ver1.1 $Date: 2008/03/24 07:00:36 $
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

		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		// プロフィール情報を取得
		$target_user_info_row = ACSUser::get_user_profile_row($user_community_id);

		// 公開レベルコード
		$open_level_code_row = array('05', '02', '01');

		//画像が新規登録か更新かを判定する true:新規 false:更新
		// ファイル情報の存在確認
		
		// ラベル		
		for ($i = 0; $i < count($open_level_code_row); $i++) {
			//
			$image_file_id = $target_user_info_row['file_id_ol' . $open_level_code_row[$i]];
			if ($image_file_id) {
				$image_new_add['file_id_ol' . $open_level_code_row[$i]] = false;
			} else {
				$image_new_add['file_id_ol' . $open_level_code_row[$i]] = true;
			}
		}

		// set
		//user_community_id をviewクラスへ通知する
		$request->setAttribute('user_community_id', $user_community_id);
		//target_user_info_rowをviewクラスへ通知する
		$request->setAttribute('target_user_info_row', $target_user_info_row);
		//画像が新規登録か更新かのimage_new_addをviewクラスへ通知する
		$request->setAttribute('image_new_add',$image_new_add);
		$request->setAttribute('open_level_code_row', $open_level_code_row);
//		$request->setAttribute('image_file_label', $image_file_label);

		return View::INPUT;
	}

	function getRequestMethods() {
			return Request::GET;
	}

	function isSecure () {
		return false;
	}

	function getCredential() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		return array('USER_PAGE_OWNER');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$request =  $context->getRequest();
		$user = $context->getUser();

		// 対象となるユーザコミュニティIDを取得
		$user_community_id = $request->ACSgetParameter('id');
		// 自分のユーザ情報を取得
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		if ($user_community_id == $acs_user_info_row['user_community_id']) {
			// 自ユーザのみ変更OK
			return true;
		}
		return false;
	}
}
?>
