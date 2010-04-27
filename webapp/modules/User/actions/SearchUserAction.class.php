<?php
// $Id: SearchUserAction.class.php,v 1.5 2006/11/20 08:44:25 w-ota Exp $

class SearchUserAction extends BaseAction
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$form = $request->ACSgetParameters();

		// ������
		if ($form['search']) {
			// �桼������������������

			if ($acs_user_info_row['is_acs_user']) {
				// ������桼���Υ�������
				$user_info_row_array = ACSUser::search_user_info_row_array($form, array(ACSMsg::get_mst('open_level_master','D01'), ACSMsg::get_mst('open_level_master','D02')));
				// ���ʾҲ�
				foreach ($user_info_row_array as $index => $user_info_row) {
					$user_info_row_array[$index]['contents_row_array']['community_profile'] = 
							ACSCommunity::get_contents_row($user_info_row['user_community_id'], ACSMsg::get_mst('contents_type_master','D08'));
				}
			} else {
				// ���̥桼���Υ�������
				$user_info_row_array = ACSUser::search_user_info_row_array($form, array(ACSMsg::get_mst('open_level_master','D01')));
				// ���ʾҲ�
				foreach ($user_info_row_array as $index => $user_info_row) {
					$user_info_row_array[$index]['contents_row_array']['community_profile'] = 
							ACSCommunity::get_contents_row($user_info_row['user_community_id'], ACSMsg::get_mst('contents_type_master','D07'));
				}
			}

			// set
			$request->setAttribute('user_info_row_array', $user_info_row_array);
		}

		// set
		$request->setAttribute('form', $form);
		return View::INPUT;
	}

	function isSecure () {
		return false;
	}

}

?>
