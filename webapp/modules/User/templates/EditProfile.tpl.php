<?php
// プロフィール編集画面
// $Id: EditProfile.tpl.php,v 1.18 2007/03/27 02:12:43 w-ota Exp $
?>
<SCRIPT language="JavaScript">
<!--
	function alert_dialog(str_msg , set_keys_obj){
			alert("<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M001') ?>:" + str_msg);
			set_keys_obj.focus();
	}

// ---------------------------------------------------------------
// 入力データの検査と取得（本人編集画面のみ）


//生年月日
	function age_check(form_obj){
		var const_msg = "<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M002') ?>\n" + "  <?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M003') ?>";
		var myform = form_obj.form;
	 
    var inp = myform.birthday.value;  
		if( inp == "" ){// 入力なしは許可
		 return;
		}	
		if(inp.indexOf("/") < 0){
			alert_dialog("<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M004') ?>\n\n" + const_msg , myform.birthday); return;
		}
		var year_set = inp.slice(0,4);
		var month_set = inp.substring(5,7);
		var day_set = inp.substring(8,10);
		
		var birthday = new Date(inp);			// 誕生日
		if(!birthday){
			alert_dialog("<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M005') ?>\n\n" + const_msg , myform.birthday); return;
		}
		// 検査 年
		if(year_set < 1901 || year_set > 2035 || isNaN(year_set)){
			alert_dialog("<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M006') ?>\n " + year_set + "\n\n" + const_msg , myform.birthday); return;
		}
		// 検査　月
		if( month_set < 1 || month_set > 12 || isNaN(month_set)){
			alert_dialog("<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M007') ?>\n " + month_set + "\n\n" + const_msg , myform.birthday); return;
		}
		// 検査　日
		var	day_check = 30;
			switch(parseInt(month_set)){
				case 1:
					day_check = 31;break;
				case 3:
					day_check = 31;break;
				case 5:
					day_check = 31;break;
				case 7:
					day_check = 31;break;
				case 8:
					day_check = 31;break;
				case 10:
					day_check = 31;break;
				case 12:
					day_check = 31;break;
				case 2:
					day_check = 29;
			}
		if(day_set > day_check || day_set < 1 || isNaN(day_set)){
			alert_dialog("<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M008') ?>\n " + day_set + "\n\n" + const_msg , myform.birthday); return;
		}
	}

//  ---------------------------------------------------------------
//-->
</SCRIPT>

<?php
if ($this->_tpl_vars['is_new_ldap_user']) {
	//echo "<span class=\"sub_title\">".ACSMsg::get_msg("User", "EditProfile.tpl.php",'M009')."</span><br><br>\n";
	echo "<div class=\"ttl\">".ACSMsg::get_msg("User", "EditProfile.tpl.php",'M009')."</div><br><br>\n";
	echo ACSMsg::get_msg("User", "EditProfile.tpl.php",'M010')."<br>\n";
} else {
	echo "<div class=\"ttl\">".ACSMsg::get_msg("User", "EditProfile.tpl.php",'M011')."</div><br>\n";
}
?>
( <span class="required">*</span> <?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M012') ?>)<br>
<br>

<?php
ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
?>

<form name="edit_profile_form" action="<?= $this->_tpl_vars['action_url'] ?>" method="post">

<?php
// inputタグ(hidden)の自動生成
foreach ($this->_tpl_vars['target_user_info_row']['contents_row_array'] as $contents_key => $contents_row) {
	// open_level_code_array
	echo "<input type=\"hidden\" name=\"open_level_code_array[$contents_key]\" value=\"" .$contents_row['open_level_code'] . "\">\n";
	// trusted_community_id_csv_array
	echo "<input type=\"hidden\" name=\"trusted_community_id_csv_array[$contents_key]\" value=\"" .$contents_row['trusted_community_id_csv'] . "\">\n";
	// trusted_community_flag
	echo "<input type=\"hidden\" name=\"trusted_community_flag[$contents_key]\" value=\"" .$contents_row['trusted_community_flag'] . "\">\n";
	echo "\n";
}
?>

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<th bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M013') ?></th>
<th bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M014') ?></th>
<th bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M015') ?></th>
<th bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M016') ?></th>
</tr>
<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M017') ?></td>
<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['user_name']['contents_value']) ?></td>
<td id="user_name_td" bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['user_name']['open_level_name']) ?></td>
<td align="center" bgcolor="#ffffff">
<input type="button" value="<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M018') ?>" onclick="window.open('<?= $this->_tpl_vars['set_open_level_for_profile_url'] ?>&contents_key=user_name&contents_type_code=<?= $this->_tpl_vars['target_user_info_row']['contents_row_array']['user_name']['contents_type_code'] ?>', 'SetOpenLevelForProfile', 'width=350,height=300,top=250,left=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes')">
</td>
</tr>
<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M020') ?> <span class="required">*</span></td>
<td bgcolor="#ffffff"><input type="text" name="mail_addr" value="<?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['mail_addr']['contents_value']) ?>" size="40"></td>
<td id="mail_addr_td" bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['mail_addr']['open_level_name']) ?></td>
<td align="center" bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M019') ?></td>
</tr>
<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M021') ?> <span class="required">*</span></td>
<td bgcolor="#ffffff"><input type="text" name="community_name" value="<?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name']) ?>" size="40"></td>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M022') ?></td>
<td align="center" bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M019') ?></td>
</tr>
<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M023') ?></td>
<td bgcolor="#ffffff"><input type="text" name="belonging" value="<?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['belonging']['contents_value']) ?>" size="40"></td>
<td id="belonging_td" bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['belonging']['open_level_name']) ?></td>
<td align="center" bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M019') ?></td>
</tr>
<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M024') ?></td>
<td bgcolor="#ffffff"><input type="text" name="speciality" value="<?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['speciality']['contents_value']) ?>" size="40"></td>
<td id="speciality_td" bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['speciality']['open_level_name']) ?></td>
<td align="center" bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M019') ?></td>
</tr>

<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M025') ?></td>
<td bgcolor="#ffffff"><input type="text" name="birthplace" value="<?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthplace']['contents_value']) ?>" size="40"></td>
<td id="birthplace_td" bgcolor="#ffffff"><?php
echo htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthplace']['open_level_name']);
if ($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthplace']['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')
	&& $this->_tpl_vars['target_user_info_row']['contents_row_array']['birthplace']['trusted_community_flag']) {
	if (count($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthplace']['trusted_community_row_array'])) {
		$trusted_community_str = '';
		foreach ($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthplace']['trusted_community_row_array'] as $trusted_community_row) {
			if ($trusted_community_row['community_name'] != '') {
				if ($trusted_community_str != '') {
					$trusted_community_str .= ', ';
				}
				$trusted_community_str .= $trusted_community_row['community_name'];
			}
		}
		echo " (" . htmlspecialchars($trusted_community_str) . ")";
	} else {
		echo ACSMsg::get_msg("User", "EditProfile.tpl.php",'M026');
	}
}
?>
</td>
<td align="center" bgcolor="#ffffff">
<input type="button" value="<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M018') ?>" onclick="window.open('<?= $this->_tpl_vars['set_open_level_for_profile_url'] ?>&contents_key=birthplace&contents_type_code=<?= $this->_tpl_vars['target_user_info_row']['contents_row_array']['birthplace']['contents_type_code'] ?>', 'SetOpenLevelForProfile', 'width=350,height=300,top=250,left=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes')">
</td>
</tr>


<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M034') ?></td>
<td bgcolor="#ffffff">
<input type="text" name="birthday" value="<?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthday']['contents_value']) ?>" size="20" onChange="age_check(this)"> <span class="notice">(YYYY/MM/DD)</span>
</td>
<td id="birthday_td" bgcolor="#ffffff">
<?php
echo htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthday']['open_level_name']);
if ($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthday']['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')
	&& $this->_tpl_vars['target_user_info_row']['contents_row_array']['birthday']['trusted_community_flag']) {
	if (count($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthday']['trusted_community_row_array'])) {
		$trusted_community_str = '';
		foreach ($this->_tpl_vars['target_user_info_row']['contents_row_array']['birthday']['trusted_community_row_array'] as $trusted_community_row) {
			if ($trusted_community_row['community_name'] != '') {
				if ($trusted_community_str != '') {
					$trusted_community_str .= ', ';
				}
				$trusted_community_str .= $trusted_community_row['community_name'];
			}
		}
		echo " (" . htmlspecialchars($trusted_community_str) . ")";
	} else {
		echo ACSMsg::get_msg("User", "EditProfile.tpl.php",'M026');
	}
}
?>
</td>
<td align="center" bgcolor="#ffffff">
<input type="button" value="<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M018') ?>" onclick="window.open('<?= $this->_tpl_vars['set_open_level_for_profile_url'] ?>&contents_key=birthday&contents_type_code=<?= $this->_tpl_vars['target_user_info_row']['contents_row_array']['birthday']['contents_type_code'] ?>', 'SetOpenLevelForProfile', 'width=350,height=300,top=250,left=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes')">
</td>
</tr>


<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M035') ?></td>
<td bgcolor="#ffffff">
<select name="mail_lang">
<?php
$mail_lang =& $this->_tpl_vars['target_user_info_row']['contents_row_array']['mail_lang'];

if ($mail_lang['contents_value'] == "") {
	$mail_lang['contents_value'] = ACS_DEFAULT_LANG;
}

foreach (ACSMsg::get_lang_list_array() as $lang => $lang_name) {
    echo '<option value="' . $lang . '"' . 
			($mail_lang['contents_value'] == $lang ? ' selected' : '') . '>' . 
			htmlspecialchars($lang_name) . "\n";
}
?>
</select>
</td>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M022') ?></td>
<td align="center" bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M019') ?></td>
</tr>


<!-- 自己紹介 閲覧者ごとに登録 -->
<tr>
<td colspan=4 bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M027') ?></td>
</tr>

<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M028') ?></td>
<td bgcolor="#ffffff"><textarea name="community_profile" cols="40" rows="5"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['community_profile']['contents_value']) ?></textarea></td>
<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['community_profile']['open_level_name']) ?></td>
<td align="center" bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M019') ?></td>
</tr>

<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M029') ?></td>
<td bgcolor="#ffffff"><textarea name="community_profile_login" cols="40" rows="5"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['community_profile_login']['contents_value']) ?></textarea></td>
<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['community_profile_login']['open_level_name']) ?></td>
<td align="center" bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M019') ?></td>
</tr>

<tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M030') ?></td>
<td bgcolor="#ffffff"><textarea name="community_profile_friend" cols="40" rows="5"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['community_profile_friend']['contents_value']) ?></textarea></td>
<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['community_profile_friend']['open_level_name']) ?></td>
<td align="center" bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M019') ?></td>
</tr>
</table>
<br>

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<th bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M031') ?></th>
<th bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M015') ?></th>
<th bgcolor="#DEEEBD"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M016') ?></th>
</tr>
<td bgcolor="#ffffff"><?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M032') ?></td>
<td id="friends_list_td" bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['target_user_info_row']['contents_row_array']['friends_list']['open_level_name']) ?></td>
<td align="center" bgcolor="#ffffff">
<input type="button" value="<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M018') ?>" onclick="window.open('<?= $this->_tpl_vars['set_open_level_for_profile_url'] ?>&contents_key=friends_list&contents_type_code=<?= $this->_tpl_vars['target_user_info_row']['contents_row_array']['friends_list']['contents_type_code'] ?>', 'SetOpenLevelForProfile', 'width=350,height=300,top=250,left=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes')">
</td>
</tr>
</table>
<br>

<input type="submit" value="OK" name="update_button">&nbsp;
    <input type="button" value="<?= ACSMsg::get_msg("User", "EditProfile.tpl.php",'M033') ?>" onclick="location.href='<?= $this->_tpl_vars['back_url'] ?>'">
</form>
