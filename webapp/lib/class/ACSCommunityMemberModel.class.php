<?php
// $Id: ACSCommunityMemberModel.class.php,v 1.2 2006/01/19 10:03:36 w-ota Exp $

/*
 * community_member��ǥ�
 */
class ACSCommunityMemberModel {

	/*
	 * ���ߥ�˥ƥ�����INSERT
	 *
	 * @param $form community_member����
	 * @return ����(true) / ����(false)
	 */
	static function insert_community_member($form) {
		ACSLib::escape_sql_array($form);
		ACSLib::get_sql_value_array($form);

		$sql  = "INSERT INTO community_member";
		$sql .= " (community_id, user_community_id, community_member_type_code)";
		$sql .= " VALUES ($form[community_id], $form[user_community_id], $form[community_member_type_code])";

		$ret = ACSDB::_do_query($sql);
		return $ret;
	}
}

?>
