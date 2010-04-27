<?php
/**
 * �ץ�ӥ塼�̿�ɽ��
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
		//�ե�����β���URL

		if (preg_match('/image/', $mime_type)) {
			$action = 'inline';//�֥饦����ɽ��
			mb_http_output('pass');		// output_buffering��̵���ˤ���
			header("Content-disposition: $action; filename=\$file_name\"");
			header("Content-type: $content_type");
			// �ե�������ɤ߽Ф�
			readfile($new_file_info);
		} else {
			echo "This is not picture file format��";
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
