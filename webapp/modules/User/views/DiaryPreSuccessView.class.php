<?php
/**
 * ダイアリー　登録・表示機能　Viewクラス
 * 日記情報　確認・登録画面
 * @package  acs/webapp/modules/Usery/views
 * @author   akitsu
 * @since    PHP 4.0
 * @version  $Revision: 1.4 $ $Date: 2006/03/01
 */
// $Id: DiaryPreView_confirm.class.php,v 1.4 2006/03/30 12:07:10 kuwayama Exp $


class DiaryPreSuccessView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		//get
		//$target_user_info_row = $request->getAttribute('target_user_info_row');
		//$diary_row = $request->getAttribute('diary_row_array');
		//$open_level_master_row_array = $request->getAttribute('open_level_master_row_array');
		//$friends_group_row_array = $request->getAttribute('friends_group_row_array');
		
		//$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		// 対象となるUserIDを取得
		$user_id = $request->getParameter('id');
		// Diary情報
		//$diary_row_array = $request->getAttribute('diary_row_array');
		// ファイル情報
		$new_file_obj = $user->getAttribute('new_file_obj');
		$new_file_info = $user->getAttribute('new_file_info');
		//ユーザ入力情報
		$form = $user->getAttribute('new_form_obj');

		// form action
		$action_url  = $this->getControllerPath('User', 'DiaryPre') .'&id=' .$user_id .'&move_id=2';
		$back_url = $this->getControllerPath('User', 'Diary') . '&id=' .$user_id .'&move_id=3';

		// DiaryトップページのURL
		$diary_top_page_url = $this->getControllerPath('User', 'Diary') . '&id=' .$user_id;

		// 加工
			//ファイルの画像URL
			$form['file_url'] = "";
			if($form['file_name'] != ""){
				$file_name = $form['file_name'];
				$type = $new_file_obj['type'];
				$form['file_url_alink'] =  $this->getControllerPath('User', 'DiaryPreImage') . '&type=' . $type ."&new_file_info=" . $new_file_info;
			}
		// set

		// 選択されたマイフレンズグループ情報取得
		if ($form['trusted_community_id_array']) {
			$form['trusted_community_row_array'] = array();
			foreach ($form['trusted_community_id_array'] as $trusted_community_id) {
				$selected_friends_group_community_row = ACSCommunity::get_community_row($trusted_community_id);
				array_push($form['trusted_community_row_array'], $selected_friends_group_community_row);
			}
		}

		$this->setAttribute('action_url', $action_url);
		$this->setAttribute('back_url', $back_url);
		$this->setAttribute('diary_top_page_url', $diary_top_page_url);
		$this->setAttribute('form', $form);
		//$this->setAttribute('new_file_obj', $new_file_obj);
		//$this->setAttribute('new_file_info', $new_file_info);
		//$this->setAttribute('diary_row', $diary_row);

		// エラーメッセージ
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('DiaryPre.tpl.php');

		return parent::execute();
	}
}

?>
