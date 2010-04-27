<?php
/**
 * ACS SystemConfig
 * システム設定
 *
 * 使用方法：
 *  <特定のキーワードの値を取得>
 *    $system_config_keyword_value = ACSSystemConfig::get_keyword_value('グループ名', 'キーワード');
 *
 * @author  kuwayama
 * @version $Revision: 1.6 $ $Date: 2008/03/24 07:00:36 $
 */
require_once(ACS_CLASS_DIR . 'ACSSystemConfigModel.class.php');
class ACSSystemConfig
{
	/* システム設定グループ配列 */
	var $system_config_group_array = array();

	/* ACSSystemConfigKeywordData 配列 */
	var $system_config_keyword_data_obj_array = array();

	/**
	 * コンストラクタ
	 *
	 * @param $file_info_row
	 */
	function ACSSystemConfig ($system_config_group = '') {
		if (!$system_config_group) {
			// テーブルから全データ取得
			$system_config_row_array = ACSSystemConfigModel::select_system_config_row_array();

		} else {
			// 指定のシステム設定グループデータ取得
			$system_config_row_array = ACSSystemConfigModel::select_system_config_group_row($system_config_group);
		}

		// ACSSystemConfigKeywordData 配列 セット
		foreach ($system_config_row_array as $system_config_row) {
			// ACSSystemConfigKeywordData インスタンス生成（配列作成）
			$_system_config_keyword_data_obj = new ACSSystemConfigKeywordData($system_config_row);
			$this->add_system_config_keyword_data_obj($_system_config_keyword_data_obj);

			// システム設定グループ取得
			$_system_config_group = $_system_config_keyword_data_obj->get_system_config_group_name();
			if (!in_array($_system_config_group, $this->system_config_group_array)) {
				$this->add_system_config_group($_system_config_group);
			}
		}
	}

	/**
	 * ACSSystemConfigKeywordData 配列 追加
	 *
	 * @param $system_config_keyword_data_obj
	 */
	function add_system_config_keyword_data_obj ($system_config_keyword_data_obj) {
		array_push($this->system_config_keyword_data_obj_array, $system_config_keyword_data_obj);
	}

	/**
	 * システム設定グループ配列 追加
	 *
	 * @param $system_config_group
	 */
	function add_system_config_group ($system_config_group) {
		array_push($this->system_config_group_array, $system_config_group);
	}

	/**
	 * システム設定グループ配列ゲット
	 *
	 * @return システム設定グループ配列
	 */
	function get_system_config_group_array () {
		return $this->system_config_group_array;
	}

	/**
	 * 指定システム設定グループの ACSSystemConfigKeywordData 配列ゲット
	 *
	 * @param $system_config_group_name
	 */
	function get_system_config_keyword_data_obj ($system_config_group_name) {
		$ret_config_keyword_data_obj_array = array();

		foreach ($this->system_config_keyword_data_obj_array as $system_config_keyword_data_obj) {
			if ($system_config_keyword_data_obj->get_system_config_group_name() == $system_config_group_name) {
				array_push($ret_config_keyword_data_obj_array, $system_config_keyword_data_obj);
			}
		}

		return $ret_config_keyword_data_obj_array;
	}

	/**
	 * ACSSystemConfigKeywordData 配列ゲット
	 */
	function get_system_config_keyword_data_obj_array () {
		return $this->system_config_keyword_data_obj_array;
	}

	/**
	 * 指定システム設定グループ、キーワードの値ゲット
	 *
	 * @param  $system_config_group_name
	 * @param  $keyword
	 */
	function get_keyword_value ($system_config_group_name, $keyword) {
		$system_config_data_row = ACSSystemConfigModel::select_system_config_keyword_row($system_config_group_name, $keyword);
		if (!isset($system_config_data_row)) {
			// データ取得できない場合、エラー
			print "ERROR: Get system value failed. ($system_config_group_name, $keyword)";
			exit;
		}

		$keyword_data_instance = new ACSSystemConfigKeywordData($system_config_data_row);
		$value =  $keyword_data_instance->get_value();
		return $value;
	}

	/**
	 * 値更新
	 *
	 * @param  $system_config_group 更新対象のキー
	 * @param  $keyword             更新対象のキー
	 * @param  $update_value        更新する値
	 */
	function update_value ($system_config_group, $keyword, $update_value) {
		return ACSSystemConfigModel::update_system_config_value($system_config_group, $keyword, $update_value);
	}
}

class ACSSystemConfigKeywordData
{
	/* システム設定グループコード */
	var $system_config_group_code;

	/* システム設定グループ */
	var $system_config_group_name;

	/* キーワード */
	var $keyword;

	/* 項目名 */
	var $name;

	/* 値 */
	var $value;

	/* 型 */
	var $type;

	/* 単位 */
	var $unit;

	/* 備考 */
	var $note;
	
	/* 選択肢 */
	var $select;

	/**
	 * コンストラクタ
	 *
	 * @param $system_config_group
	 * @param $keyword
	 */
	function ACSSystemConfigKeywordData ($system_config_data_row = array()) {
		$this->set_system_config_group_code($system_config_data_row['system_config_group_code']);
		$this->set_system_config_group_name($system_config_data_row['system_config_group_name']);
		$this->set_keyword($system_config_data_row['keyword']);
		//$this->set_name($system_config_data_row['name']);
		$this->set_value($system_config_data_row['value']);
		$this->set_type($system_config_data_row['type']);
		//$this->set_unit($system_config_data_row['unit']);
		//$this->set_note($system_config_data_row['note']);

		// メッセージファイルより取得
		$this->set_name(ACSMsg::get_mdmsg(__FILE__,$system_config_data_row['keyword']));
		$this->set_unit(ACSMsg::get_mdmsg(__FILE__,$system_config_data_row['keyword'].".unit"));
		$this->set_note(ACSMsg::get_mdmsg(__FILE__,$system_config_data_row['keyword'].".note"));

		$this->set_select(ACSMsg::get_mdmsg(__FILE__,$system_config_data_row['keyword'].".select"));
	}

	/**
	 * システム設定グループコードセット
	 *
	 * @parma $system_config_group_code
	 */
	function set_system_config_group_code ($system_config_group_code) {
		$this->system_config_group_code = $system_config_group_code;
	}

	/**
	 * システム設定グループコードゲット
	 *
	 * @return システム設定グループ
	 */
	function get_system_config_group_code () {
		return $this->system_config_group_code;
	}

	/**
	 * システム設定グループ名セット
	 *
	 * @parma $system_config_group_name
	 */
	function set_system_config_group_name ($system_config_group_name) {
		$this->system_config_group_name = $system_config_group_name;
	}

	/**
	 * システム設定グループ名ゲット
	 *
	 * @return システム設定グループ
	 */
	function get_system_config_group_name () {
		return $this->system_config_group_name;
	}

	/**
	 * キーワードセット
	 *
	 * @parma $keyword
	 */
	function set_keyword ($keyword) {
		$this->keyword = $keyword;
	}

	/**
	 * キーワードゲット
	 *
	 * @return キーワード
	 */
	function get_keyword () {
		return $this->keyword;
	}

	/**
	 * 項目名セット
	 *
	 * @parma $name
	 */
	function set_name ($name) {
		$this->name = $name;
	}

	/**
	 * 項目名ゲット
	 *
	 * @return 項目名
	 */
	function get_name () {
		return $this->name;
	}

	/**
	 * 値セット
	 *
	 * @parma $value
	 */
	function set_value ($value) {
		$this->value = $value;
	}

	/**
	 * 値ゲット
	 *
	 * @return 値
	 */
	function get_value () {
		return $this->value;
	}

	/**
	 * 型セット
	 *
	 * @parma $type
	 */
	function set_type ($type) {
		$this->type = $type;
	}

	/**
	 * 型ゲット
	 *
	 * @return 型
	 */
	function get_type () {
		return $this->type;
	}

	/**
	 * 単位セット
	 *
	 * @parma $unit
	 */
	function set_unit ($unit) {
		$this->unit = $unit;
	}

	/**
	 * 単位ゲット
	 *
	 * @return 単位
	 */
	function get_unit () {
		return $this->unit;
	}

	/**
	 * 備考セット
	 *
	 * @parma $note
	 */
	function set_note ($note) {
		$this->note = $note;
	}

	/**
	 * 備考ゲット
	 *
	 * @return 備考
	 */
	function get_note () {
		return $this->note;
	}
	
	/**
	 * 選択肢セット
	 *
	 * @parma $note
	 */
	function set_select ($select) {
		$this->select = $select;
	}

	/**
	 * 選択肢ゲット
	 *
	 * @return 選択肢
	 */
	function get_select () {
		return $this->select;
	}

}
?>
