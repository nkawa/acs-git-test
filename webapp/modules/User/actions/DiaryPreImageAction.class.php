<?php
/**
 * �ץ�ӥ塼�̿�ɽ��
 *
 * @author  akitsu
 * @version $Revision: 1.2 $ $Date: 2006/11/20 08:44:25 $
 */

class DiaryPreImageAction extends BaseAction
{
	function execute () {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$mime_type     = $request->getParameter('type');
		$new_file_info = $request->getParameter('new_file_info');

		//�ե�����β���URL
		if (preg_match('/image/', $mime_type)) {
			$action = 'inline';//�֥饦����ɽ��
			mb_http_output('pass');		// output_buffering��̵���ˤ���
			header("Content-disposition: $action; filename=\$file_name\"");
			header("Content-type: $content_type");
			// �ե�������ɤ߽Ф�
			readfile($new_file_info);
		} else {
			echo ACSMsg::get_msg('User', 'DiaryPreImageAction.class.php' ,'M001');
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
