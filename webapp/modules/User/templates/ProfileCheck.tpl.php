<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: akitsu 2006/3/8 ver1.0                                      |
// | �ץ�ե�����ɽ���γ�ǧ����                                  �������� |
// +----------------------------------------------------------------------+
// $Id: ProfileCheck.tpl.php,v 1.5 2007/03/01 09:01:43 w-ota Exp $
?>

<!-- HTML -->
<div class="ttl">
<a href="<?= $this->_tpl_vars['top_page_url'] ?>"> <?= ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M002') ?>
</div>
<br><br>
<div class="confirm_msg">
<?= ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M003') ?>
</div>

<?php	
	//���ߤ�ɽ���ϡ�
	$strComment = "";
	switch($this->_tpl_vars['view_mode']){
		case 1:
			$strComment = ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M004');	break;
		case 2:
			$strComment = ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M005');	break;
		default:
			$strComment = ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M006');	
	}
	
	//ɽ����ˡ������
	echo "<a href=\"" .$this->_tpl_vars['menu']['all_url'] . "\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M007')."</a>&nbsp;&nbsp;&nbsp;";
	echo "<a href=\"" .$this->_tpl_vars['menu']['login_url'] . "\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M008')."</a>&nbsp;&nbsp;&nbsp;";
	echo "<a href=\"" .$this->_tpl_vars['menu']['friend_url'] . "\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M009')."</a>";
	echo "<br><br><br>";

	echo "<span class=\"comment\">" .$strComment ."</span><br>";

	// ɽ��table
	//echo "<table class=\"common_table\" border>\n";
    echo "<table class=\"common_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";

	if ($this->_tpl_vars['profile']['contents_row_array']['user_name']['not_open'] == 1) {
		echo "<tr>";
		echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M010')."</td>";
		echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['contents_row_array']['user_name']['contents_value']) . "</td>";
		echo "</tr>\n";
	}
//�������顡���̸���������
	echo "<tr>";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M011')."</td>";
	echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['community_name']) . "</td>";
	echo "</tr>\n";
	echo "<tr>";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M012')."</td>";
	echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['belonging']) . "</td>";
	echo "</tr>\n";
	echo "<tr>";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M013')."</td>";
	echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['speciality']) . "</td>";
	echo "</tr>\n";
//�����ޤǡ����̸���������

	if ($this->_tpl_vars['profile']['contents_row_array']['birthplace']['not_open'] == 1) {
		echo "<tr>";
		echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M014')."</td>";
		echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['birthplace']) . "</td>";
		echo "</tr>\n";
	}
	if ($this->_tpl_vars['profile']['contents_row_array']['birthday']['not_open'] == 1) {
		echo "<tr>";
		echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M015')."</td>";
		echo "<td bgcolor=\"#FFFFFF\">" . $this->_tpl_vars['profile']['birthday'] . "</td>";
		echo "</tr>\n";
	}

//���ʾҲ�ϡ��������̤���Ͽ����Ƥ���
		echo "<tr>";
		echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "ProfileCheck.tpl.php",'M016')."</td>";
	if ($this->_tpl_vars['view_mode'] == 1) {		//������桼��
		echo "<td bgcolor=\"#FFFFFF\">" . nl2br(htmlspecialchars($this->_tpl_vars['profile']['community_profile_login'])) . "</td>";
	}
	if ($this->_tpl_vars['view_mode'] == 2) {		//���٤Ƥ�ͧ��
		echo "<td bgcolor=\"#FFFFFF\">" . nl2br(htmlspecialchars($this->_tpl_vars['profile']['community_profile_friend'])) . "</td>";
	}
	if ($this->_tpl_vars['view_mode'] == 0) {		//����
		echo "<td bgcolor=\"#FFFFFF\">" . nl2br(htmlspecialchars($this->_tpl_vars['profile']['community_profile'])) . "</td>";
	}
		echo "</tr>\n";
	echo "</table>\n";
	echo "<br>";

	
?>
