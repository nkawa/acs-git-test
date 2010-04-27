<?php
// $Id: ACSExternalRSS.class.php,v 1.1 2007/03/28 05:58:13 w-ota Exp $

class ACSExternalRSS {

	/**
	 * ����RSS��ư�����ߤ�¹Ԥ���
	 *
	 * @param $community_row ���ߥ�˥ƥ�����
	 * @return $msg
	 */
	static function do_process($community_row) {
		$msg = "";
		// ������٥�ޥ���
		$open_level_master_array = ACSDB::get_master_array('open_level');

		// ����RSS����ƥ�ľ�����ղ�
		$community_row = ACSExternalRSS::add_contents_row_array($community_row);
		// RSS��������
		$rss_row_array = ACSExternalRSS::get_external_rss_row_array($community_row['contents_row_array']['external_rss_url']['contents_value']);

		if ($rss_row_array == false) {
			return;
		}

		// ����RSS.��ƼԤ��������ߥ�˥ƥ��Υ��ߥ�˥ƥ������ԤǤ��뤫�ɤ��������å�����
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

		// ��ƼԾ��� (ml_addr��ޤ����)
		$post_user_info_row = ACSUser::get_user_info_row_by_user_community_id($community_row['contents_row_array']['external_rss_post_user']['contents_value']);
		$post_user_info_row = ACSUser::get_user_info_row_by_user_id($post_user_info_row['user_id']);

		// ��ƾ���($form) //

		// �����
		$form = array();
		$form['community_id'] = $community_row['community_id'];
		$form['user_community_id'] = $community_row['contents_row_array']['external_rss_post_user']['contents_value'];
		$form['open_level_code'] = $community_row['contents_row_array']['external_rss_url']['open_level_code'];
		// �ѥ֥�å���꡼�� �ξ��
		if ($open_level_master_array[$form['open_level_code']] == ACSMsg::get_mst('open_level_master','D06')
			&& intval($community_row['contents_row_array']['external_rss_public_release_expire_term']['contents_value']) >= 1) {
			$form['expire_date'] = "CURRENT_DATE + '@ " . ($community_row['contents_row_array']['external_rss_public_release_expire_term']['contents_value'] - 1) . " days'::INTERVAL";
		} else {
			$form['expire_date'] = '';
		}
		// ����� (���ФΤ�) �ξ��
		if ($open_level_master_array[$form['open_level_code']] == ACSMsg::get_mst('open_level_master','D04')) {
			$form['trusted_community_row_array'] = $community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'];
		} else {
			$form['trusted_community_row_array'] = array();
		}
		$form['ml_send_flag'] = ACSLib::get_pg_boolean(ACSLib::get_boolean($community_row['contents_row_array']['external_rss_ml_send_flag']['contents_value']));


		// RSS�������Ȥν��� //

		foreach ($rss_row_array['items'] as $rss_item_row) {
			// �Ǻܤ���Ƥ��뤫�����å�
			if (!ACSExternalRSS::is_posted_value($community_row['community_id'], $rss_item_row['rss_item_title'], $rss_item_row['rss_item_date'])) {
				// �Ǻܤ���Ƥ��ʤ����
				$form['subject'] = $rss_item_row['rss_item_title'];
				$form['body'] = $rss_item_row['rss_item_content'];
				$form['post_date'] = $rss_item_row['rss_item_date'];

				$bbs_id = ACSExternalRSS::set_bbs_and_external_rss($form, $rss_row_array['channel_info'], $rss_item_row);
				if (!$bbs_id) {
					echo "ERROR\n";
					exit;
				} else {
					$msg .= "�Ǻ�: $community_row[community_name] (community_id=$community_row[community_id])\n";
					$msg .= "\t=> $form[subject] (bbs_id=$bbs_id)\n";

					if (ACSLib::get_boolean($form['ml_send_flag'])) {
						// ML���ơ������μ���
						$ml_status_row = ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D62'));
						$ml_status = $ml_status_row['contents_value'];

						// MLͭ��ξ��᡼�������
						if ($ml_status == 'ACTIVE') {
							// ��̾�Խ�
							$subject = str_replace('{BBSID}', $bbs_id, ACS_COMMUNITY_ML_SUBJECT_FORMAT) . $form['subject'];

							// ML����
							ACSCommunityMail::send_community_mailing_list(
																		  $community_row['community_id'],
																		  $post_user_info_row['mail_addr'],
																		  $form['subject'],
																		  $form['body']);
							$msg .= "\t=> ML����\n";
						}
					}
				}
			}
		}

		return $msg;
	}

	/**
	 * $community_row�˳���RSS�˴ؤ��륳��ƥ�ľ�����ղä���
	 *
	 * @param $community_row ���ߥ�˥ƥ�����
	 * @return �ʤ�
	 */
	static function add_contents_row_array($community_row) {
		// contents
		if (!is_array($community_row['contents_row_array'])) {
			$community_row['contents_row_array'] = array();
		}
		// ����RSS.URL
		$community_row['contents_row_array']['external_rss_url'] =
			 ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D63'));
		// ����RSS.��Ƽ�
		$community_row['contents_row_array']['external_rss_post_user'] =
			 ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D64'));
		// ����RSS.ML����
		$community_row['contents_row_array']['external_rss_ml_send_flag'] =
			 ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D65'));
		// ����RSS.�ѥ֥�å���꡼������
		$community_row['contents_row_array']['external_rss_public_release_expire_term'] =
			 ACSCommunity::get_contents_row($community_row['community_id'], ACSMsg::get_mst('contents_type_master','D66'));
		// ����RSS.URL contents_trusted_community
		$community_row['contents_row_array']['external_rss_url']['trusted_community_row_array'] =
			 ACSCommunity::get_contents_trusted_community_row_array($community_row['community_id'], $community_row['contents_row_array']['external_rss_url']['contents_type_code'], $community_row['contents_row_array']['external_rss_url']['open_level_code']);

		return $community_row;
	}

	/**
	 * RSS�ե�����URL�򥻥åȤ��Ƥ��륳�ߥ�˥ƥ�����������������
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
	 * @return ���ߥ�˥ƥ�������� (Ϣ�����������)
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
	 * ����RSS������������
	 *
	 * @param $bbs_id bbs_id
	 * @return ����RSS����(Ϣ������)
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
	 * ���˷Ǻܤ���Ƥ��뤫�ɤ���
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
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
		// ����ե饰������Ӥ�����

		$value = ACSDB::_get_value($sql);

		if ($value) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * RSS�������Ͽ����
	 *
	 * @param $form �ե��������
	 * @param $rss_channel_row <channel>
	 * @param $rss_item_row <item>
	 * @return ����(true) / ����(false)
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
		// ����� (���ФΤ�)
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
	 * ����RSS��ư������������Ƥ���Ͽ����
	 *
	 * @param $community_id ���ߥ�˥ƥ�ID
	 * @param $form ���ϥե��������
	 * @return ����(true) / ����(false)
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
		// ����� (���ФΤ�) �θ������ߥ�˥ƥ�
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
	 * RSS�ե����ɤ�URL����ꤷ�����Ƥ�parse����
	 *
	 * @param $rss_url RSS�ե����ɤ�URL(http)
	 * @return RSS������=RSS����(Ϣ������) / RSS�������顼��=false
	 */
	static function get_external_rss_row_array($rss_url) {
		// XML_RSS, XML_Parser
		require_once('XML/RSS.php');

		// RSS����
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
				// <content:encoded>��̵������<description>�����
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
