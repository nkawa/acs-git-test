<?php
// $Id: NewCommunityFolderView_inline.class.php,v 1.4 2007/03/01 09:01:46 w-ota Exp $

/**
 * マイコミュニティのフォルダ新着情報(inline)
 *
 * @author  z-satosi
 * @version $Revision: 1.4 $
 */
class NewCommunityFolderInputView extends BaseView
{
	var $community_name_buffer;

	function execute() {

		$context = &$this->getContext();
		$user = $context->getUser();
		$request = $context->getRequest();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		$user_community_id = $request->getAttribute('user_community_id');
		$target_user_info_row = $request->getAttribute('target_user_info_row');
		$new_folder_row_array = $request->getAttribute('new_folder_row_array');

		// マイフレンズフォルダ新着一覧
		$new_folder_url =
			$this->getControllerPath(DEFAULT_MODULE, 'NewCommunityFolder') .
			'&id=' . $user_community_id;

		// set
		$this->setAttribute('target_user_info_row', $target_user_info_row);
		$this->setAttribute('new_folder_row_array', $new_folder_row_array);
		$this->setAttribute('new_folder_url', $new_folder_url);
		$this->setAttribute('get_days', $request->getAttribute('get_days'));

		// テンプレート
		$this->setTemplate('NewCommunityFolder.tpl.php');
		$context->getController()->setRenderMode(View::RENDER_VAR);
		$request->setAttribute("NewCommunityFolder", $this->render());

		return parent::execute();
	}

}

?>
