<?php
// $Id: ACSExternalRSS.class.php,v 1.1 2007/03/28 05:58:13 w-ota Exp $

class ACSExternalRSS {

	/**
	 * 外部RSS自動取り込みを実行する
	 *
	 * @param $community_row コミュニティ情報
	 * @return $msg
	 */
	static function do_process($community_row) {
		$msg = "";
		// 公開レベルマスタ
		$open_level_master_array = ACSDB::get_master_array('open_level');

		// 外部RSSコンテンツ情報を付加
		$community_row = ACSExternalRSS::add_contents_row_array($community_row);
		// RSS情報を取得
		$rss_row_array = ACSExternalRSS::get_external_rss_row_array($community_row['contents_row_array']['external_rss_url']['contents_value']);

		if ($rss_row_array == false) {
			return;
		}

		// 外部RSS.投稿者が該当コミュニティのコミュニティ管理者であるかどうかチェックする
		$community_admin_user_info_row_array = ACSCommunity::get_community_admin_user_info_row_array($community_row['community_id']);
		$err_flg = 1;
		foreach ($community_admin_user_info_row_array as $community_admin_user_info_row) {
			if ($community_admin_user_info_row['user_community_id'] == $community_row['contents_row_array']['external_rss_post_user']['contents_value']) {
				$err_flg = 0;
				break;
			}
		}
		if ($err_flg) {
			return;
		}

		// 投稿者情報 (ml_addrを含む情報)
		$post_user_info_row = ACSUser::get_user_info_row_by_user_community_id($community_row['contents_row_array']['external_rss_post_user']['contents_value']);
		$post_user_info_row = ACSUser::get_user_info_row_by_user_id($post_user_info_row['user_id']);

		// 投稿情報($form) //

		// 初期化
		$form = array();
		$form['community_id'] = $community_row['community_id'];
		$form['user_community_id'] = $community_row['contents_row_array']['external_rss_post_user']['contents_value'];
		$form['open_level_code'] = $community_row['contents_row_array']['external_rss_url']['open_level_code'];
		// パブリックリリース の場合
		if ($open_level_master_array[$form['open_level_code']] == ACSMsg::get_mst('open_level_master','D06')
			&& intval($community_row['contents_row_array']['external_rss_public_release_expire_term']['contents_value']) >= 1) {
			$form['expire_date'] = "CURRENT_DATE + '@ " . ($community_row['contents_row_array']['external_rss_public_release_expire_term']['contents_value'] - 1) . " days'::INTERVAL";
		} else {
			$form['expire_date'] = '';
		}
		// 非公開 (メンバのみ) の場合
		if ($open_level_master_array[$form['open_level_code']] == ACSMsg::get_mst('open_level_master','D04')) {
			$form['trusted_community_row_array'] = $community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'];
		} else {
			$form['trusted_community_row_array'] = array();
		}
		$form['ml_send_flag'] = ACSLib::get_pg_boolean(ACSLib::get_boolean($community_row['contents_row_array']['external_rss_ml_send_flag']['contents_value']));


		// RSS記事ごとの処理 //

		foreach ($rss_row_array['items'] as $rss_item_row) {
			// 掲載されているかチェック
			if (!ACSExternalRSS::is_posted_value($community_row['community_id'], $rss_item_row['rss_item_title'], $rss_item_row['rss_item_date'])) {
				// 掲載されていない場合
				$form['subject'] = $rss_item_row['rss_item_title'];
				$form['body'] = $rss_item_row['rss_item_content'];
				$form['post_date'] = $rss_item_row['rss_item_date'];

				$bbs_id = ACSExternalRSS::set_bbs_and_external_rss($form, $rss_row_array['channel_info'], $rss_item_row);
				if (!$bbs_id) {
					echo "ERROR\n";
					exit;
				} else {
					$msg .= "掲載: $community_row[community_name] (community_id=$community_row[community_id])\n";
					$msg .= "\t=> $form[subject] (bbs_id=$bbs_id)\n";

					if (ACSLib::get_boolean($form['ml_send_flag'])) {
						// MLステータスの取得
						$ml_status_row = ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D62'));
						$ml_status = $ml_status_row['contents_value'];

						// ML有りの場合メールを送信
						if ($ml_status == 'ACTIVE') {
							// 件名編集
							$subject = str_replace('{BBSID}', $bbs_id, ACS_COMMUNITY_ML_SUBJECT_FORMAT) . $form['subject'];

							// ML送信
							ACSCommunityMail::send_community_mailing_list(
																		  $community_row['community_id'],
																		  $post_user_info_row['mail_addr'],
																		  $form['subject'],
																		  $form['body']);
							$msg .= "\t=> ML送信\n";
						}
					}
				}
			}
		}

		return $msg;
	}

	/**
	 * $community_rowに外部RSSに関するコンテンツ情報を付加する
	 *
	 * @param $community_row コミュニティ情報
	 * @return なし
	 */
	static function add_contents_row_array($community_row) {
		// contents
		if (!is_array($community_row['contents_row_array'])) {
			$community_row['contents_row_array'] = array();
		}
		// 外部RSS.URL
		$community_row['contents_row_array']['external_rss_url'] =
			 ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D63'));
		// 外部RSS.投稿者
		$community_row['contents_row_array']['external_rss_post_user'] =
			 ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D64'));
		// 外部RSS.ML通知
		$community_row['contents_row_array']['external_rss_ml_send_flag'] =
			 ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D65'));
		// 外部RSS.パブリックリリース期間
		$community_row['contents_row_array']['external_rss_public_release_expire_term'] =
			 ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D66'));
		// 外部RSS.URL contents_trusted_community
		$community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'] =
			 ACSCommunity::get_contents_trusted_community_row_array($community_row['community_id'], $community_row['contents_row_array']['external_rss_url']['contents_type_code'], $community_row['contents_row_array']['external_rss_url']['open_level_code']);

		return $community_row;
	}

	/**
	 * RSSフィードURLをセットしているコミュニティ情報一覧を取得する
	 *
	 * @param $community_id コミュニティID
	 * @return コミュニティ情報一覧 (連想配列の配列)
	 */
	static function get_external_rss_community_row_array() {
		$sql  = "SELECT community.community_id, community.community_name";
		$sql .= " FROM community, contents, contents_type_master";
		$sql .= " WHERE community.delete_flag = 'f'";
		$sql .= "   AND community.community_id = contents.community_id";
		$sql .= "   AND contents.contents_type_code = contents_type_master.contents_type_code";
		$sql .= "   AND contents.contents_value IS NOT NULL";
		$sql .= "   AND contents.contents_value != ''";
		$sql .= "   AND contents_type_master.contents_type_name = '".ACSMsg::get_mst('contents_type_master','D63')."'";
		$sql .= " ORDER BY community.community_id";

		$row_array = ACSDB::_get_row_array($sql);
		return $row_array;
	}

	/**
	 * 外部RSS情報を取得する
	 *
	 * @param $bbs_id bbs_id
	 * @return 外部RSS情報(連想配列)
	 */
	static function get_external_rss_row($bbs_id) {
		$bbs_id = pg_escape_string($bbs_id);

		$sql  = "SELECT *";
		$sql .= " FROM bbs, external_rss";
		$sql .= " WHERE bbs.bbs_id = '$bbs_id'";
		$sql .= "   AND bbs.bbs_id = external_rss.bbs_id";
		$row = ACSDB::_get_row($sql);

		return $row;
	}

	/**
	 * 既に掲載されているかどうか
	 *
	 * @param $community_id コミュニティID
	 * @param $rss_title RSS<title>
	 * @param $rss_date RSS<dc:date>
	 * @param true / false
	*/
	static function is_posted_value($community_id, $rss_item_title, $rss_item_date) {
		$community_id = pg_escape_string($community_id);
		$rss_item_title = pg_escape_string($rss_item_title);
		$rss_item_date = pg_escape_string($rss_item_date);

		$sql  = "SELECT count(*)";
		$sql .= " FROM bbs, external_rss";
		$sql .= " WHERE bbs.community_id = '$community_id'";
		$sql .= "   AND bbs.bbs_id = external_rss.bbs_id";
		$sql .= "   AND external_rss.rss_item_title = '$rss_item_title'";
		$sql .= "   AND external_rss.rss_item_date = '$rss_item_date'::TIMESTAMP(0)";
		// 削除フラグ等の比較は不要

		$value = ACSDB::_get_value($sql);

		if ($value) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * RSS情報を登録する
	 *
	 * @param $form フォーム情報
	 * @param $rss_channel_row <channel>
	 * @param $rss_item_row <item>
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_bbs_and_external_rss($form, $rss_channel_row, $rss_item_row) {
		$org_form = $form;

		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		// BEGIN
		ACSDB::_do_query("BEGIN");

		$bbs_id_seq = ACSDB::get_next_seq('bbs_id_seq');

		// bbs
		$sql  = "INSERT INTO bbs";
		$sql .= " (bbs_id, community_id, user_community_id, subject, body, open_level_code, expire_date, ml_send_flag)";
		$sql .= " VALUES ($bbs_id_seq, $form[community_id], $form[user_community_id], $form[subject], $form[body], $form[open_level_code], ";
		$sql .= ($org_form['expire_date'] != NULL ? $org_form['expire_date'] : "null") . ", $form[ml_send_flag])";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		$form = $org_form;
		// bbs_trusted_community
		$open_level_master_array = ACSDB::get_master_array('open_level');
		// 非公開 (メンバのみ)
		if ($open_level_master_array[$form['open_level_code']] == ACSMsg::get_mst('open_level_master','D04')) {
			foreach ($form['trusted_community_row_array'] as $trusted_community_row) {
				$trusted_community_id = pg_escape_string($trusted_community_row['community_id']);

				$sql  = "INSERT INTO bbs_trusted_community";
				$sql .= " (bbs_id, trusted_community_id)";
				$sql .= " VALUES ($bbs_id_seq, $trusted_community_id)";

				$ret = ACSDB::_do_query($sql);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// external_rss
		ACSLib::escape_sql_array($rss_item_row);
		ACSLib::get_sql_value_array($rss_item_row);
		ACSLib::escape_sql_array($rss_channel_row);
		ACSLib::get_sql_value_array($rss_channel_row);
		$sql  = "INSERT INTO external_rss";
		$sql .= " (bbs_id, rss_url, rss_channel_title, rss_item_title, rss_item_content, rss_item_date, rss_item_link)";
		$sql .= " VALUES ($bbs_id_seq, $rss_channel_row[rss_url], $rss_channel_row[rss_channel_title], $rss_item_row[rss_item_title], $rss_item_row[rss_item_content], $rss_item_row[rss_item_date], $rss_item_row[rss_item_link])";
		$ret = ACSDB::_do_query($sql);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		if ($ret) {
			$ret = $bbs_id_seq;
		}

		// COMMIT
		ACSDB::_do_query("COMMIT");

		return $ret;
	}

	/**
	 * 外部RSS自動取込の設定内容を登録する
	 *
	 * @param $community_id コミュニティID
	 * @param $form 入力フォーム情報
	 * @return 成功(true) / 失敗(false)
	 */
	static function set_external_rss_contents($community_id, $form) {
		$contents_type_master_array = ACSDB::get_master_array('contents_type');
		$open_level_master_array = ACSDB::get_master_array('open_level');

		// 63: external_rss_url
		$contents_form = array();
		$contents_form['community_id'] = $community_id;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D63'), $contents_type_master_array);
		$contents_form['open_level_code'] = $form['external_rss_url_open_level_code'];
		$contents_form['contents_value'] = $form['external_rss_url'];
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}
		// 非公開 (メンバのみ) の公開コミュニティ
		if (is_array($form['external_rss_url_trusted_community_id_array'])) {
			$external_rss_url_trusted_community_form = array();
			$external_rss_url_trusted_community_form['community_id'] = $community_id;
			$external_rss_url_trusted_community_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D63'), $contents_type_master_array);
			$external_rss_url_trusted_community_form['open_level_code'] = $form['external_rss_url_open_level_code'];
			foreach ($form['external_rss_url_trusted_community_id_array'] as $trusted_community_id) {
				$external_rss_url_trusted_community_form['trusted_community_id'] = $trusted_community_id;
				$ret = ACSCommunity::set_contents_trusted_community($external_rss_url_trusted_community_form);
				if (!$ret) {
					ACSDB::_do_query("ROLLBACK");
					return $ret;
				}
			}
		}

		// 64: external_rss_post_user
		$contents_form = array();
		$contents_form['community_id'] = $community_id;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D64'), $contents_type_master_array);
		$contents_form['open_level_code'] = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D64'));
		$contents_form['contents_value'] = $form['external_rss_post_user'];
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// 65: external_rss_ml_send_flag
		$contents_form = array();
		$contents_form['community_id'] = $community_id;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D65'), $contents_type_master_array);
		$contents_form['open_level_code'] = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D65'));
		$contents_form['contents_value'] = ACSLib::get_pg_boolean($form['external_rss_ml_send_flag']); // 't', 'f'
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		// 66: external_rss_public_release_expire_term
		$contents_form = array();
		$contents_form['community_id'] = $community_id;
		$contents_form['contents_type_code'] = array_search(ACSMsg::get_mst('contents_type_master','D66'), $contents_type_master_array);
		$contents_form['open_level_code'] = ACSAccessControl::get_default_open_level_code(ACSMsg::get_mst('community_type_master','D40'), ACSMsg::get_mst('contents_type_master','D66'));
		$contents_form['contents_value'] = $form['external_rss_public_release_expire_term'];
		$ret = ACSCommunity::set_contents($contents_form);
		if (!$ret) {
			ACSDB::_do_query("ROLLBACK");
			return $ret;
		}

		return $ret;
	}

	/**
	 * RSSフィードのURLを指定して内容をparseする
	 *
	 * @param $rss_url RSSフィードのURL(http)
	 * @return RSS取得時=RSS情報(連想配列) / RSS取得エラー時=false
	 */
	static function get_external_rss_row_array($rss_url) {
		// XML_RSS, XML_Parser
		require_once('XML/RSS.php');

		// RSS情報
		$rss_row_array = array();

		$line_array = file($rss_url);
		if ($line_array == false) {
			return false;
		}

		$rss_data = implode(NULL, $line_array);
		$rss_encoding = mb_detect_encoding($rss_data, 'auto');

		// XML_RSS
		$rss = new XML_RSS();
		$rss->setInputString($rss_data);
		$rss->parse();

		// Channel Info
		$channel_info = $rss->getchannelInfo();
		$rss_row_array['channel_info'] = array();
		$rss_row_array['channel_info']['rss_channel_title'] = mb_convert_encoding($channel_info['title'], mb_internal_encoding(), $rss_encoding);
		$rss_row_array['channel_info']['rss_url'] = $rss_url;

		// Items
		$rss_row_array['items'] = array();
		foreach ($rss->getItems() as $item) {
			$item_row = array();
			$item_row['rss_item_title'] = mb_convert_encoding($item['title'], mb_internal_encoding(), $rss_encoding);
			$item_row['rss_item_content'] = strip_tags(mb_convert_encoding($item['content:encoded'], mb_internal_encoding(), $rss_encoding));
			if (trim($item_row['rss_item_content']) == '') {
				// <content:encoded>が無い場合は<description>を取得
				$item_row['rss_item_content'] = strip_tags(mb_convert_encoding($item['description'], mb_internal_encoding(), $rss_encoding));
			}
			$item_row['rss_item_date'] = mb_convert_encoding($item['dc:date'], mb_internal_encoding(), $rss_encoding);
			$item_row['rss_item_link'] = mb_convert_encoding($item['link'], mb_internal_encoding(), $rss_encoding);
			array_push($rss_row_array['items'], $item_row);
		}

		return $rss_row_array;
	}
}

?>
