<?php
/**
 * システム設定編集
 *
 * @author  kuwayama
 * @version $Revision: 1.3 $ $Date: 2008/03/24 07:00:36 $
 */
class EditSystemConfigInputView extends BaseView
{
	function execute() {
		$context = $this->getContext();
		$controller = $context->getController();
		$request = $context->getRequest();
		$user = $context->getUser();
		
		// get
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');
		$system_config_obj = $request->getAttribute('system_config_obj');

		$form = $request->getAttribute('form');

		// 加工
		// Actoin URL
		$edit_system_config_url = $this->getControllerPath('System', 'EditSystemConfig');

		// template 用表示データ作成
		// key: グループ
		// value: template 用データの配列
		$system_config_row_array = array();
		$system_config_group_array = $system_config_obj->get_system_config_group_array();
		foreach ($system_config_group_array as $system_config_group) {
			$_system_config_keyword_data_row_array = array();

			// 取得したグループのキーワードを取得
			$system_config_keyword_data_obj_array = $system_config_obj->get_system_config_keyword_data_obj($system_config_group);

			foreach ($system_config_keyword_data_obj_array as $system_config_keyword_data_obj) {
				// template 用データの配列作成
				$_system_config_keyword_data_row = array();

				$_system_config_keyword_data_row['keyword'] = $system_config_keyword_data_obj->get_keyword();
				$_system_config_keyword_data_row['name'] = $system_config_keyword_data_obj->get_name();
				if (is_array($form)) {
					$_system_config_keyword_data_row['value'] = $form[$_system_config_keyword_data_row['keyword']];
				} else {
					$_system_config_keyword_data_row['value'] = $system_config_keyword_data_obj->get_value();
				}
				$_system_config_keyword_data_row['type'] = $system_config_keyword_data_obj->get_type();
				$_system_config_keyword_data_row['unit'] = $system_config_keyword_data_obj->get_unit();
				$_system_config_keyword_data_row['note'] = $system_config_keyword_data_obj->get_note();

				$wk_select = $system_config_keyword_data_obj->get_select();
				if ($wk_select) {
					$wk_select_array = split(",", $wk_select);
					for ($i = 0; $i < count($wk_select_array); $i++) {
						$_system_config_keyword_data_row['select'][$i] = split(":", $wk_select_array[$i]);
					}
				}
				
				array_push($_system_config_keyword_data_row_array, $_system_config_keyword_data_row);
			}

			// key: グループ
			// value: template 用データの配列
			$system_config_row_array[$system_config_group] = $_system_config_keyword_data_row_array;
		}

		// テンプレート
		$this->setScreenId("0001");
		$this->setTemplate('EditSystemConfig.tpl.php');

		// set
		$this->setAttribute('error_message', $this->getErrorMessage($controller, $request, $user));

		$this->setAttribute('edit_system_config_url', $edit_system_config_url);
		$this->setAttribute('system_config_row_array', $system_config_row_array);

		return parent::execute();
	}
}

?>
