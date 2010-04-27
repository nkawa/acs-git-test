<?php
/**
 * 掲示板　投稿 削除処理　アクションクラス
 * DeleteBBSAction.class.php
 *
 * @author  $Author: w-ota $
 * @version ver1.0 $  2006/02/23 $
 */

class DeleteBBSAction extends BaseAction
{
	// GET
	function getDefaultView() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}

		// get parameter
		$target_community_id	   = $request->getParameter('community_id');
		$bbs_id					= $request->getParameter('bbs_id');

		$bbs_change_url = $this->getControllerPath('Community','BBS');
		$bbs_change_url .= '&community_id=' . $target_community_id .'&bbs_id=' .$bbs_id;
		
		$delete_bbs_url = $this->getControllerPath('Community','DeleteBBS') .'&community_id=' . $target_community_id .'&bbs_id=' .$bbs_id;
		$back_url = $bbs_change_url;

		$request->setAttribute('delete_bbs_url', $delete_bbs_url);
		$request->setAttribute('back_url', $back_url);
		// 表示
		return View::SUCCESS;
	}

	// POST
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
	
		if (!$this->get_execute_privilege()) {
			$controller->forward(SECURE_MODULE, SECURE_ACTION);
			return;
		}
 
		//削除処理を行う
		$target_community_id	= $request->getParameter('community_id');
		$acs_user_info_row		= $user->getAttribute('acs_user_info_row');
		$bbs_id			= $request->getParameter('bbs_id');
		//ファイル情報テーブルのデータ削除
		$bbs_obj = ACSBBS::get_bbs_row($bbs_id);
		if(!$bbs_obj){
			echo ACSMsg::get_msg('Community', 'DeleteBBSAction.class.php', 'M001');
		}
		$ret =ACSBBS::delete_bbs($bbs_obj);
		if (!$ret) {
			echo "ERROR: delete article failed";
		}

		//表示
		$bbs_change_url = $this->getControllerPath('Community','BBS');
	$bbs_change_url .= '&community_id=' . $target_community_id .'&bbs_id=' .$bbs_id;
		header("Location: $bbs_change_url");
	}

	function getRequestMethods () {
		return Request::POST;
	}

	function isSecure () {
		return false;
	}

	function getCredential () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		return array('EXECUTE');
	}

	function get_execute_privilege () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// 管理人はOK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}
		
		// 書き込んだメンバ本人はOK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			$bbs_row = ACSBBS::get_bbs_row($request->getParameter('bbs_id'));
			if ($acs_user_info_row['user_community_id'] == $bbs_row['user_community_id']) {
				return true;
			}
		}

		return false;
	}

}
?>
