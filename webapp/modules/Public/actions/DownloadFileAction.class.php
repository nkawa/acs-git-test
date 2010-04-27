<?php
/**
 * コミュニティ ファイルダウンロード(Public Access)
 *
 * @author  Teramoto
 * @version $ $
 */
//require_once(ACS_CLASS_DIR . 'ACSFileDetailInfo.class.php');
//require_once(ACS_CLASS_DIR . 'ACSCommunityFolder.class.php');

class DownloadFileAction extends BaseAction
{
	function execute () {

		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();

		$key = $request->getParameter('key');

		// ファイル情報取得
		$file_public_access_row = 
				ACSFileDetailInfo::get_file_public_access_row("", "access_code = '" . $key . "'");

		// ファイルアクセス数更新
		if ($file_public_access_row) {
			$form = array();
			$form['all_access_count'] = $file_public_access_row['all_access_count'] + 1;
			$form['access_count'] = $file_public_access_row['access_count'] + 1;
			ACSFileDetailInfo::update_file_public_access($file_public_access_row['file_id'], $form);
		} else {
			print "NOT FOUND";
		}

		// ファイルダウンロード処理
		$community_folder_obj = new ACSCommunityFolder($file_public_access_row['community_id'],
 				"",
				$file_public_access_row['folder_id']);
		$folder_obj = $community_folder_obj->get_folder_obj();
		$folder_obj->download_file($file_public_access_row['file_id']);
	}

	function getDefaultView() {
		return $this->execute();
	}

	function getRequestMethods () {
		return REQ_GET;
	}

	function isSecure () {
		return false;
	}

}
?>
