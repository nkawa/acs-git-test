<?php
/**
 * ACS Done
 *
 * Common モジュールの Done アクションに渡すオブジェクト
 * 表示させたい内容を保持する
 *
 * ＜使用方法＞
 * -------------------------------------------------------------------
 * require_once(ACS_CLASS_DIR . 'ACSDone.class.php');
 *
 * $done_obj = new ACSDone();
 *
 * $done_obj->set_title('タイトル');
 * $done_obj->set_message('メッセージ');
 *
 * // リンクは必要な分だけ、add_link する
 * $done_obj->add_link('リンク先名１', 'link1_url');
 * $done_obj->add_link('リンク先名２', 'link2_url');
 *
 * $request->setAttribute('done_obj', $done_obj);
 * -------------------------------------------------------------------
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/01/06 07:55:08 $
 */
class ACSDone
{
	/* タイトル */
	var $title;

	/* メッセージ */
	var $message;

	/* リンク */
	var $donelink_obj_array = array();


	/**
	 * タイトルセット
	 *
	 * @param $title
	 */
	function set_title ($title) {
		$this->title = $title;
	}

	/**
	 * タイトルゲット
	 *
	 * @return タイトル
	 */
	function get_title () {
		return $this->title;
	}

	/**
	 * メッセージセット
	 *
	 * @param $message
	 */
	function set_message ($message) {
		$this->message = $message;
	}

	/**
	 * メッセージゲット
	 *
	 * @return メッセージ
	 */
	function get_message () {
		return $this->message;
	}

	/**
	 * リンクセット
	 *
	 * @param $link_name
	 * @param $link_url
	 */
	function add_link ($link_name, $link_url) {
		$donelink_obj = new ACSDoneLink();

		$donelink_obj->set_link_name($link_name);
		$donelink_obj->set_url($link_url);

		array_push($this->donelink_obj_array, $donelink_obj);
	}

	/**
	 * リンクゲット
	 *
	 * @return リンクの配列
	 */
	function get_link_row_array () {
		$link_row_array = array();
		foreach ($this->donelink_obj_array as $donelink_obj) {
			$link_row = array();

			$link_row['link_name'] = $donelink_obj->get_link_name();
			$link_row['url']  = $donelink_obj->get_url();

			array_push($link_row_array, $link_row);
		}
		return $link_row_array;
	}
}

/**
 * ACS DoneLink
 *
 * Doneクラスで保持するリンクのオブジェクト
 * リンク先名とURLを保持する
 *
 * @author  kuwayama
 * @version $Revision: 1.1 $ $Date: 2006/01/06 07:55:08 $
 */
class ACSDoneLink
{
	/* リンク先名 */
	var $link_name;

	/* リンク先 URL */
	var $url;

	/**
	 * リンク先名セット
	 *
	 * @param リンク先
	 */
	function set_link_name ($link_name) {
		$this->link_name = $link_name;
	}

	/**
	 * リンク先名ゲット
	 */
	function get_link_name () {
		return $this->link_name;
	}

	/**
	 * リンク先 URL セット
	 *
	 * @param リンク先
	 */
	function set_url ($url) {
		$this->url = $url;
	}

	/**
	 * リンク先 URL ゲット
	 */
	function get_url () {
		return $this->url;
	}
}
?>
