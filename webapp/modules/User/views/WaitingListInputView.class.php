<?php
// $Id: WaitingListView::INPUT.class.php,v 1.7 2006/11/20 08:44:28 w-ota Exp $

class WaitingListInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request =  $context->getRequest();
		$user = $context->getUser();
		
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// get
		$waiting_type_name = $request->getAttribute('waiting_type_name');
		$waiting_row_array = $request->getAttribute('waiting_row_array');

		// 加工
		foreach ($waiting_row_array as $index => $waiting_row) {
			$waiting_row_array[$index]['entry_date'] = ACSLib::convert_pg_date_to_str($waiting_row['entry_date']);
			$waiting_row_array[$index]['complete_date'] = ACSLib::convert_pg_date_to_str($waiting_row['entry_date']);

			if ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D10')) {
				$waiting_row_array[$index]['image_url'] = ACSUser::get_image_url($waiting_row['waiting_community_id'], 'thumb');
				$waiting_row_array[$index]['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $waiting_row['waiting_community_id'];
			} elseif ($waiting_row['waiting_type_name'] == ACSMsg::get_mst('waiting_type_master','D30')) {
				$waiting_row_array[$index]['image_url'] = ACSCommunity::get_image_url($waiting_row['waiting_community_id'], 'thumb');
				$waiting_row_array[$index]['top_page_url'] = $this->getControllerPath('Community', DEFAULT_ACTION) . '&community_id=' . $waiting_row['waiting_community_id'];
				$waiting_row_array[$index]['entry_user_info_row']['top_page_url'] = $this->getControllerPath(DEFAULT_MODULE, DEFAULT_ACTION) . '&id=' . $waiting_row['entry_user_info_row']['user_community_id'];
				// コミュニティプロフィール
				$waiting_row_array[$index]['waiting_community_row'] = ACSCommunity::get_community_profile_row($waiting_row['waiting_community_id']);
			}

			$waiting_row_array[$index]['action_url'] = $this->getControllerPath('User', 'WaitingList') . '&id=' . $acs_user_info_row['user_community_id'] . '&waiting_id=' . $waiting_row['waiting_id'];
		}

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('WaitingList.tpl.php');

		// set
		$this->setAttribute('waiting_type_name', $waiting_type_name);
		$this->setAttribute('waiting_row_array', $waiting_row_array);

		return parent::execute();
	}
}

?>
