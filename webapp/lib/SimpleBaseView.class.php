<?php
// $Id: BaseView.class.php,v 1.15 2008/05/21 01:53:36 y-yuki Exp $

class SimpleBaseView extends SimpleViewEx
{
	protected $inlineFlg = false;
	protected $script_path = "index.php";

	/**
	 * execute
	 */
	function execute() {

		$context = &$this->getContext();
		$request = &$context->getRequest();
		$moduleName = $context->getModuleName();
		$actionName = $context->getActionName();
		$controller = $context->getController();
		$user = $context->getUser();
		$acs_user_info_row = $user->getAttribute('acs_user_info_row');

		// ログイン中のユーザ情報
		$this->setAttribute('acs_user_info_row', $acs_user_info_row);

		// ログインユーザかどうか
		$this->setAttribute('is_login_user', $acs_user_info_row['is_login_user']);
	}
	
	function getControllerPath($module="", $action="") {
		$index = "index.php?";
		$moduleNm = "module=";
		$actionNm = "&action=";
		if ($module == "" && $action == "") {
			return $index;
		}
		return $index . $moduleNm . $module . $actionNm . $action;

	}

	/**
	 * エラーメッセージ取得
	 *
	 * @param  &$controller
	 * @param  &$request
	 * @param  &$target_array
	 */
	function getErrorMessage (&$controller, &$request, &$user) {
		$error_msg_array = array();
		$error_row = $user->getAttribute('error_row');
		if (!is_array($error_row)) {
			$error_row = array();
		}

		// request のエラーとセッションにセットされているエラーを取得する
		$error_row = array_merge($error_row, $request->getErrors());
		if ($error_row) {
			foreach ($error_row as $key => $msg) {
				array_push($error_msg_array, $msg);
			}
		}
		$user->removeAttribute('error_row');
		return $error_msg_array;
	}

	/**
	 * ページング情報設定
	 * 引数の $target_array を表示する分だけに削り、ページング情報を作成する
	 *
	 * @param  &$controller
	 * @param  &$request
	 * @param  &$target_array
	 * @param  $display_max_count
	 *
	 * @return ページ出力のための row_array
	 */
	function getPagingInfo (&$controller, &$request, &$target_array, $display_max_count) {
		$params = $request->ACSGetParameters();

		// ページング情報
		$paging_info = array();

		// 表示総件数取得
		$all_count = count($target_array);
		if ($all_count <= $display_max_count) {
			// ページングの必要なし
			$paging_info['all_count'] = $all_count;
			if ($paging_info['all_count'] > 0) {
				$paging_info['start_count'] = 1;
			} else {
				$paging_info['start_count'] = 0;
			}
			$paging_info['end_count'] = $all_count;
			return $paging_info;
		}

		// 表示ページ取得
		if ($params['page'] > 0) {
			$display_page = $params['page'];
		} else {
			// 初期値
			$display_page = 1;
		}

		/*--------------------------*/
		/* 表示するデータだけに削る */
		/*--------------------------*/
		// 表示対象となるデータの開始位置
		$display_start_position = $display_max_count * ($display_page - 1);

		// 表示対象データのみにする
		$target_array = array_slice($target_array, $display_start_position, $display_max_count);

		/*----------------------*/
		/* ページのリンクを作成 */
		/*----------------------*/
		$paging_row_array = array();
		// ページ数
		$all_page_count = ceil($all_count / $display_max_count);
		for ($page_count = 1; $page_count <= $all_page_count; $page_count++) {
			// ページ数（URL エンコーディングする値）
			$params['page'] = $page_count;

			// リンク先URL
			if ($page_count != $display_page) {
				$link_url = $this->genURL($params);
			} else {
				// 表示するページにはリンクをはらない
				$link_url = "";
			}

			// set
			$paging_row = array();
			$page_row['page_number'] = $page_count;
			$page_row['link_url'] = $link_url;

			array_push($paging_row_array, $page_row);
		}

		// ページング情報セット //
		// 全件
		$paging_info['all_count'] = $all_count;
		// XX-YY
		$paging_info['start_count'] = $display_start_position + 1;
		$paging_info['end_count'] = $display_start_position + $display_max_count;
		if ($paging_info['end_count'] > $all_count) {
			$paging_info['end_count'] = $all_count;
		}
		// 前へ・次へ
		if ($display_page > 1) {
			$paging_info['prev_link'] = $paging_row_array[($display_page - 1) - 1]['link_url'];
		} else {
			$paging_info['prev_link'] = '';
		}
		if ($display_page < $all_page_count) {
			$paging_info['next_link'] = $paging_row_array[($display_page - 1) + 1]['link_url'];
		} else {
			$paging_info['next_link'] = '';
		}
		// ページングリンク
		$paging_info['paging_row_array'] = $paging_row_array;

		/*--------------------------------*/
		/* テンプレートで出力用の値を返す */
		/*--------------------------------*/
		return $paging_info;
	}

	// リンク先URLの自動生成（ページ送りに多用）
	// mojavi2の移植
	function genURL ($params)
	{
		$url = $script_path;
		$divider  = '&';
		$equals   = '=';
		$url     .= '?';

		$keys  = array_keys($params);
		$count = sizeof($keys);
		for ($i = 0; $i < $count; $i++)
		{
			if ($i > 0)
			{
				$url .= $divider;
			}
			$url .= rawurlencode(mb_convert_encoding($keys[$i], mb_http_output())) . $equals .
				rawurlencode(mb_convert_encoding($params[$keys[$i]], mb_http_output()));
		}
		return $url;
	}

}

?>
