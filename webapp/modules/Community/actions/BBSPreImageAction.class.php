<?php
/**
 * プレビュー写真表示
 *
 * @author  akitsu
 * @version $Revision: 1.3 $ $Date: 2006/11/20 08:44:12 $
 */

class BBSPreImageAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();

//		$file_name				= $request->getParameter('file_name');
		$mime_type				= $request->getParameter('type');
		$new_file_info			= $request->getParameter('new_file_info');
		//ファイルの画像URL

		if (preg_match('/image/', $mime_type)) {
			$action = 'inline';//ブラウザ内表示
			mb_http_output('pass');		// output_bufferingを無効にする
			header("Content-disposition: $action; filename=\$file_name\"");
			header("Content-type: $content_type");
			// ファイルを読み出す
			readfile($new_file_info);
		} else {
			echo "This is not picture file format！";
			return $back_url; 
		}
	}

	function isSecure () {
			return false;
	}

	function getRequestMethods () {
		return Request::GET;
	}
}
?>
