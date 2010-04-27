<?php
/**
 * 掲示板　返信投稿 削除処理　アクションクラス
 * DeleteBBSResAction.class.php
 *
 * @author  $Author: w-ota $
 * @version ver1.0 $  2006/02/23 $
 */

class DeleteBBSResAction extends BaseAction
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
		$bbs_res_id					= $request->getParameter('bbs_res_id');
		
		$bbs_change_url = $this->getControllerPath('Community','BBSRes');
		$bbs_change_url .= '&community_id=' . $target_community_id .'&bbs_id=' .$bbs_id;
		
		$delete_bbs_url = $this->getControllerPath('Community','DeleteBBSRes') ;
		$delete_bbs_url .= '&community_id=' . $target_community_id .'&bbs_id=' .$bbs_id .'&bbs_res_id=' .$bbs_res_id;
		$back_url = $bbs_change_url;

		$request->setAttribute('delete_bbs_res_url', $delete_bbs_url);
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
		$bbs_res_id			= $request->getParameter('bbs_res_id');
		$post_date  = $request->getParameter('post_date');
		//返信情報テーブルのデータ削除
		$ret =ACSBBS::delete_bbs_res(array($bbs_res_id));
		if (!$ret) {
			echo "ERROR: delete reply-article failed";
		}

		//表示
		$bbs_change_url = $this->getControllerPath('Community','BBSRes');
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

		// 本人はOK
		if ($user->hasCredential('COMMUNITY_ADMIN')) {
			return true;
		}

		// 書き込んだメンバ本人はOK
		if ($user->hasCredential('COMMUNITY_MEMBER')) {
			$bbs_res_row = ACSBBS::get_bbs_res_row($request->getParameter('bbs_res_id'));
			if ($acs_user_info_row['user_community_id'] == $bbs_res_row['user_community_id']) {
				return true;
			}
		}

		return false;
	}
}
?>
