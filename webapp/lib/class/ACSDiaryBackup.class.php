<?php

//
// ��������ƥ�ĥХå����åס�index�ե���������
//
define( '_ACSDIARYBACKUP_INDEX_FORMAT', '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html;charset=Shift_JIS">
<title>ACS My Diaries</title>
<style type="text/css" media="screen">
<!--
td {font-size: 13px;}
body {background-color:#FFFFFF;color:#333333;font-size: 13px;}
table.common_table {padding: 3px;}
table.common_table td {padding: 3px;}
img.thumb {margin-top:10px;margin-bottom:10px;boder-style:none}
-->
</style></head><body bgcolor="#ffffff">@CONTENTS@</body></html>
');

define( '_ACSDIARYBACKUP_INDEX_MONTHLY_FORMAT', '
	<H1>@MONTHLY_TITLE@</H1>
	<table class="common_table">
	@DIARY_CONTENTS@
	</table>
');

define( '_ACSDIARYBACKUP_INDEX_DAIRY_FORMAT', '
	<tr>
		<td width="15"></td>
		<td bgcolor="#deeebd" width="180" align="center">@YMD@</td>
		<td bgcolor="#eeffcc" width="500"><a href="@DIARY_URL@">@DIARY_SUBJECT@</a></td>
	</tr>
');

//
// ��������ƥ�ĥХå����åס�diary�ե���������
//
define( '_ACSDIARYBACKUP_DIARY_FORMAT', '
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="content-type" content="text/html;charset=Shift_JIS">
<title>@SUBJECT@</title>
<style type="text/css" media="screen">
<!--
td {font-size: 13px;}
body {background-color:#FFFFFF;color:#333333;font-size: 13px;}
table.common_table {padding: 3px;}
table.common_table td {padding: 3px;}
table.open_level_table {background-color: #dddddd;padding: 0px;}
table.open_level_table td {padding: 0px;}
-->
</style>
</head>
<body bgcolor="#ffffff">
<table border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33" width="650px">
	<tr><td bgcolor="#deeebd"><b>@SUBJECT@</b>&nbsp;&nbsp;@POST_DATE@</td></tr>
	<tr><td bgcolor="#ffffff">
		<table class="open_level_table">
		<tr>
		<td>@OPEN_LEVEL_TITLE@ : @OPEN_LEVEL_NAME@</td>
		</tr>
		</table><br>
		@IMAGE@
		<div id="diary_body">@BODY@</div><br>
	</td>
	</tr>
</table>
<br>
<table border="0" cellpadding="6" cellspacing="5" bgcolor="#ffffff" width="650">
@COMMENTS@
</table>
</body>
</html>
');

define( '_ACSDIARYBACKUP_DIARY_COMMENT_FORMAT', '
	<tr>
	<td bgcolor="#eeffcc" valign="top">
		<table>
		<tr><td bgcolor="#eeffcc" width="500px">@POST_DATE@  [@COMMUNITY_NAME@]</td></tr>
		<tr><td>@BODY@</td></tr>
		</table>
	</td>
	</tr>
');

/**
 * ACS DiaryBackup
 *
 * @author  z-satosi
 * @version $Revision: 1.3 $ $Date: 2007/03/28 10:51:16 $
 */
class ACSDiaryBackup
{
	/* �桼�����ߥ�˥ƥ�id */
	var $user_community_id;

	/* �����Хå����åץ���ƥ�ĺ����ǥ��쥯�ȥ� */
	var $contents_dir;

	/* ��������ƥ�ĥǥ��쥯�ȥ� */
	var $diary_dir;

	/* ����ǥå����������������ƥ��URL */
	var $index_to_diary_url;

	/* ���᡼������ƥ�ĥǥ��쥯�ȥ� */
	var $img_dir;

	/* ��������ƥ�Ĥ���Υ��᡼��URL */
	var $diary_to_img_url;

	/* ��������ƥ�ĥե�����̾�Υ����� */
	var $diary_file_names;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * @param string $user_community_id �оݤȤʤ�桼�����ߥ�˥ƥ�id
	 * @param string $contents_dir ����ǥ��쥯�ȥ�
	 */
	function ACSDiaryBackup ($user_community_id, $contents_dir) {

		// �ǥ��쥯�ȥꡦ�գң̤�����
		$this->user_community_id = $user_community_id;
		$this->contents_dir = mb_ereg_replace('/$','',$contents_dir);
		$this->diary_dir = $this->contents_dir . '/diary';
		$this->img_dir = $this->contents_dir . '/img';
		$this->index_to_diary_url = './diary';
		$this->diary_to_img_url = '../img';
		$this->diary_file_names = array();

		// ����ǥ��쥯�ȥ꤬¸�ߤ��ʤ���硢�ǥ��쥯�ȥ�κ���
		ACSLib::make_dir($this->contents_dir);
	}

	/**
	 * ����ǥ��쥯�ȥ�ν����
	 */
	function clean_work_dir () {
		ACSLib::remove_dir($this->contents_dir);
	}

	/**
	 * �Хå����åץ���ƥ�Ĥκ���
	 * 
	 * @param string $encoding ���󥳡��ǥ���
	 */
	function make_contents ($encoding='') {

		$diary_row_array = ACSDiary::get_diary_row_array($this->user_community_id);

		// ������¸�ߤ��ʤ����ϲ��⤻����λ
		if (count($diary_row_array) == 0) {
			return;
		}

		// ��������ե�����ȥ��᡼���ե�����κ���
		ACSLib::make_dir($this->diary_dir);
		ACSLib::make_dir($this->img_dir);

		$contents = '';

		$diary_array = array();
		$months_array = array();

		foreach ($diary_row_array as $diary_row) {

			if ($diary_row['delete_flag']=='f') {

				// html���֥ե���������
				$html_file = $this->create_diary_html($diary_row, $encoding);

				$post_tm = ACSLib::convert_pg_date_to_timestamp($diary_row['post_date']);

				$ym = date("Ym",$post_tm);

				// ǯ���ѥե����ޥåȤ�����
				if (!array_key_exists($ym,$months_array)) {
					$ym_str = ACSMsg::get_mdmsg(__FILE__,'YEAR_MONTH');
					$ym_str = str_replace('{YEAR}',date("Y",$post_tm),$ym_str);
					$ym_str = str_replace('{MONTH}',date("m",$post_tm),$ym_str);
					$months_array[$ym] = mb_ereg_replace('@MONTHLY_TITLE@', 
							htmlspecialchars($ym_str), _ACSDIARYBACKUP_INDEX_MONTHLY_FORMAT);
				}

				$ymd = ACSLib::convert_pg_date_to_str($diary_row['post_date']);

				// ��������ǥå���������
				$diary_contents = _ACSDIARYBACKUP_INDEX_DAIRY_FORMAT;
				$diary_contents = mb_ereg_replace('@YMD@', 
							htmlspecialchars($ymd), $diary_contents);
				$diary_contents = mb_ereg_replace('@DIARY_URL@', 
							htmlspecialchars($this->index_to_diary_url . '/' . $html_file), 
							$diary_contents);
				$diary_contents = mb_ereg_replace('@DIARY_SUBJECT@', 
							htmlspecialchars($diary_row['subject']), $diary_contents);
				$diary_array[$ym] .= $diary_contents;
			}
		}

		// index.html������
		$contents = "";
		foreach ($diary_array as $ym => $diary_contents) {
			$contents .= mb_ereg_replace('@DIARY_CONTENTS@', 
					$diary_contents, $months_array[$ym]);
		}
		$contents = mb_ereg_replace('@CONTENTS@', 
				$contents, _ACSDIARYBACKUP_INDEX_FORMAT);

		// ���������ɤ��Ѵ�
		if ($encoding != '') {
			$contents = mb_convert_encoding($contents, $encoding);
		}

		// index�ե�����ν���
		$fp = fopen($this->contents_dir . '/index.html', "w");
		fputs($fp, $contents);
		fclose($fp);
	}

	/**
	 * ����html����ƥ�Ĥκ���
	 * 
	 * @param string $diary_row ��������
	 * @param string $encoding ���󥳡��ǥ���
	 */
	function create_diary_html ($diary_row, $encoding='') {

		$file_head = date("Ymd_His", 
				ACSLib::convert_pg_date_to_timestamp($diary_row['post_date']));

		$this->diary_file_names[$file_head]++;

		if ($this->diary_file_names[$file_head]>1) {
			$file_head .= '_' . ($this->diary_file_names[$html_file]-1);
		}
		$html_file = $file_head . '.html';

		// ���ܹ��ܤ��ִ�
		$contents = _ACSDIARYBACKUP_DIARY_FORMAT;
		$contents = mb_ereg_replace('@SUBJECT@', 
				htmlspecialchars($diary_row['subject']), $contents);
		$contents = mb_ereg_replace('@POST_DATE@', 
				htmlspecialchars(ACSLib::convert_pg_date_to_str($diary_row['post_date'])), 
				$contents);
		$contents = mb_ereg_replace('@BODY@', 
				nl2br(htmlspecialchars($diary_row['body'])), $contents);
		$contents = mb_ereg_replace('@OPEN_LEVEL_TITLE@', 
				htmlspecialchars(ACSMsg::get_mdmsg(__FILE__,'M001')), $contents);
		$contents = mb_ereg_replace('@OPEN_LEVEL_NAME@', 
				htmlspecialchars($diary_row['open_level_name']), $contents);

		// ���᡼���ե����뤬������
		$image_file_id = $diary_row['file_id'];
		if ($image_file_id != '') {

			$file_obj = ACSFile::get_file_info_instance($image_file_id);

			// ��ĥ�Ҥμ���
			mb_ereg("^.*(\.[^\.\/]*)", $file_obj->get_display_file_name(), $matches);
			$ext = $matches[1];

			// �ե�����̾������
			$img_from = ACS_FOLDER_DIR . $file_obj->get_server_file_name();
			$img_to = $this->img_dir . '/' . $file_head . $ext;
			$img_thumb_from = ACS_FOLDER_DIR . $file_obj->get_thumbnail_server_file_name();
			$img_thumb_to = $this->img_dir . '/thumb_' . $file_head . '.jpg';

			// �գң̤�����
			$img_url = $this->diary_to_img_url . '/' . $file_head . $ext;
			$img_thumb_url = $this->diary_to_img_url . '/thumb_' . $file_head . '.jpg';

			// ���᡼���ե�����κ���
			@copy($img_from, $img_to);
			@copy($img_thumb_from, $img_thumb_to);

			// ��󥯥���������
			$contents = mb_ereg_replace('@IMAGE@', 
					'<div><a href="' . $img_url . '">' . 
					'<img src="' . $img_thumb_url . '" border="0"></a></div><br>',
					$contents);
		} else {
			$contents = mb_ereg_replace('@IMAGE@', '',$contents);
		}

		// �����Ȥμ���
        $diary_comment_row_array = ACSDiary::get_diary_comment_row_array($diary_row['diary_id']);

		// ��������ʬ������
		$comments = "";
		foreach ($diary_comment_row_array as $diary_comment_row) {

			if ($diary_comment_row['diary_comment_delete_flag']=='f') {
				$comment_contents = _ACSDIARYBACKUP_DIARY_COMMENT_FORMAT;

				$comment_contents = mb_ereg_replace('@POST_DATE@', 
						htmlspecialchars(ACSLib::convert_pg_date_to_str(
							$diary_comment_row['post_date'])), $comment_contents);
				$comment_contents = mb_ereg_replace('@COMMUNITY_NAME@', 
						htmlspecialchars($diary_comment_row['community_name']), 
						$comment_contents);
				$comment_contents = mb_ereg_replace('@BODY@', 
						nl2br(htmlspecialchars($diary_comment_row['body'])), 
						$comment_contents);
				$comments .= $comment_contents;
			}
		}
		// �����Ȥ��ִ�
		$contents = mb_ereg_replace('@COMMENTS@', $comments, $contents);

		// contents ���Ȥ��Ѵ�
		if ($encoding != '') {
			$contents = mb_convert_encoding($contents, $encoding);
		}

		// �ե�����ؤν���
		$fp = fopen($this->diary_dir . '/' . $html_file, "w");
		fputs($fp, $contents);
		fclose($fp);

		return $html_file;
	}
}

?>
