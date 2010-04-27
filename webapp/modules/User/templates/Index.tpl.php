<?php
// +----------------------------------------------------------------------+
// | PHP version 5 & mojavi version 3                                     |
// +----------------------------------------------------------------------+
// | Authors: y-yuki v 1.0 2009/01/23 14:40:00                            |
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: kuwayama  v 1.14 2006/03/04 00:40:31                        |
// |         update: akitsu 2006/3/8 ver1.0                               |
// | User  top画面                                                                  |
// +----------------------------------------------------------------------+
// $Id: Index.tpl.php,v 1.32 2008/03/24 07:00:36 y-yuki Exp $
?>

<table class="layout_table" width="665px">
<tr>
<td valign="top">
<!-------------------------------->
<table class="layout_table">
<tr>
    <td align="center">
        <!-- 写真 -->
        <a href="<?=$this->_tpl_vars['menu']['image_change_url'] ?>"><img class="pic" src="<?=$this->_tpl_vars['profile']['image_url'] ?>"></a><br>
<?php
if ($this->_tpl_vars['menu']['image_change_url']) {
    echo "<a href=\"" . $this->_tpl_vars['menu']['image_change_url'] . "\">".ACSMsg::get_msg("User", "Index.tpl.php",'M001')."</a><br>\n";
}
?>
        <br>
<?php
// 「○×さん」
echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "Index.tpl.php",'NAME'),array(
        "{USER_NAME}" => htmlspecialchars($this->_tpl_vars['profile']['community_name'])));
?>
        <br>
<?php
// 最終ログイン時間
if ($this->_tpl_vars['last_login']){
    echo "<br><font style='font-size: 8pt'>";
    echo ACSMsg::get_tag_replace(
            ACSMsg::get_msg("User", "Index.tpl.php",'LAST_LOGIN'), array(
            "{LAST_LOGIN}" => htmlspecialchars($this->_tpl_vars['last_login'])));
    echo "</font><br>";
}
?>
    </td>
</tr>
<tr>
    <td class="nowrap">
        <br><span class="sub_title"><?= ACSMsg::get_msg("User", "Index.tpl.php",'M002') ?></span><br>
        <br><a href="<?=$this->_tpl_vars['menu']['diary_url'] ?>">
<?php
if ($this->_tpl_vars['is_self_page']) {
    echo ACSMsg::get_msg("User", "Index.tpl.php",'M003');
} else {
    echo ACSMsg::get_msg("User", "Index.tpl.php",'M004');
}
?>
        </a><br>
<?php 
if ($this->_tpl_vars['profile_edit_url']) {
    echo "<a href=\"" . $this->_tpl_vars['message_box_url'] . "\">".ACSMsg::get_msg("User", "Index.tpl.php",'M036')."</a><br>\n";
}
?>
<a href="<?=$this->_tpl_vars['menu']['folder_url'] ?>">
<?php
if ($this->_tpl_vars['is_self_page']) {
    echo ACSMsg::get_msg("User", "Index.tpl.php", 'M005');
} else {
    echo ACSMsg::get_msg("User", "Index.tpl.php", 'M006');
}
?>
</a><br>
<?php
if ($this->_tpl_vars['menu']['change_password_url']) {
    echo "<a href=\"" . $this->_tpl_vars['menu']['change_password_url'] . "\">".ACSMsg::get_msg("User", "Index.tpl.php",'M007')."</a><br>\n";
}
if ($this->_tpl_vars['profile_edit_url']) {
    echo "<a href=\"" . $this->_tpl_vars['profile_edit_url'] . "\">".ACSMsg::get_msg("User", "Index.tpl.php",'M008')."</a><br>\n";
    echo "<a href=\"" . $this->_tpl_vars['profile_view_url'] . "\">".ACSMsg::get_msg("User", "Index.tpl.php",'M009')."</a><br>\n";
    echo "<a href=\"" . $this->_tpl_vars['footprint_url'] . "\">".ACSMsg::get_msg("User", "Index.tpl.php",'M033')."</a><br>\n";
    echo "<a href=\"" . $this->_tpl_vars['select_design_url'] . "\">".ACSMsg::get_msg("User", "Index.tpl.php",'M034')."</a><br>\n";
    echo "<a href=\"" . $this->_tpl_vars['backup_url'] . "\">".ACSMsg::get_msg("User", "Index.tpl.php",'M035')."</a>\n";

} else if ($this->_tpl_vars['peruse_mode'] != 9) {
    // 一般ではない
    echo "<form method=\"get\">";
    echo "<input type=\"button\" value=\"".ACSMsg::get_msg("User", "Index.tpl.php",'M037')."\" onclick=\"location.href='" .$this->_tpl_vars['message_btn_url']. "'\">";
}
?>
</td>
</tr>
</table>
<!-------------------------------->
</td>
<td width="15px">&nbsp;</td>
<td valign="top">

<?php
// システムからのお知らせ
if (count($this->_tpl_vars['system_announce_row_array'])) {
    echo "<table class=\"system_announce_table\" width=\"95%\">\n";
    echo "<tr><td>\n";
    foreach ($this->_tpl_vars['system_announce_row_array'] as $index => $system_announce_row) {
        if ($index > 0) {
            echo "<br>\n";
        }
        echo "<b>" . htmlspecialchars($system_announce_row['subject']) . "</b> (" .$system_announce_row['post_date']. ")<br>\n";
        echo "" . nl2br(htmlspecialchars($system_announce_row['body'])) . "<br>\n";
    }
    echo "</td></tr>\n";
    echo "</table><br>\n";
}
?>

<?php
if ($this->_tpl_vars['profile_edit_url']) {

    // ********** 自分のマイページを見ている場合 **********//

    $br_required = 0;   // 改行が必要

    // 待機: マイフレンズ追加依頼
    if ($this->_tpl_vars['waiting_for_add_friends_row_array_num']) {
        echo "<div align=\"left\">";
        echo "<a href=\"".$this->_tpl_vars['waiting_list_for_add_friends_url']."\">";
        echo ACSMsg::get_msg("User", "Index.tpl.php",'M010')." (" . $this->_tpl_vars['waiting_for_add_friends_row_array_num'] . ACSMsg::get_msg("User", "Index.tpl.php",'M011').")";
        echo "</a>";
        echo "</div>\n";
        $br_required = 1;
    }

    // 待機: 管理者になっているコミュニティのコミュニティ参加依頼
    foreach ($this->_tpl_vars['waiting_for_join_community_row_array_array'] as $waiting_for_join_community_row_array) {
        if ($waiting_for_join_community_row_array['waiting_for_join_community_row_array_num']) {
            echo "<div align=\"left\">";
            echo "<a href=\"".$waiting_for_join_community_row_array['waiting_list_for_join_community_url']."\">";
            echo htmlspecialchars($waiting_for_join_community_row_array['community_row']['community_name']) . ACSMsg::get_msg("User", "Index.tpl.php",'M012')." :: ";
            echo ACSMsg::get_msg("User", "Index.tpl.php",'M013')." (" . $waiting_for_join_community_row_array['waiting_for_join_community_row_array_num'] . ACSMsg::get_msg("User", "Index.tpl.php",'M011').")";
            echo "</a>";
            echo "</div>\n";
            $br_required = 1;
        }
    }

    // 待機: 管理者になっているコミュニティの親コミュニティ追加依頼
    foreach ($this->_tpl_vars['waiting_for_parent_community_link_row_array_array'] as $waiting_for_parent_community_link_row_array) {
        if ($waiting_for_parent_community_link_row_array['waiting_for_parent_community_link_row_array_num']) {
            echo "<div align=\"left\">";
            echo "<a href=\"".$waiting_for_parent_community_link_row_array['waiting_list_for_parent_community_link_url']."\">";
            echo htmlspecialchars($waiting_for_parent_community_link_row_array['community_row']['community_name']) . ACSMsg::get_msg("User", "Index.tpl.php",'M012')." :: ";
            echo ACSMsg::get_msg("User", "Index.tpl.php",'M014')." (" . $waiting_for_parent_community_link_row_array['waiting_for_parent_community_link_row_array_num'] . ACSMsg::get_msg("User", "Index.tpl.php",'M011').")";
            echo "</a>";
            echo "</div>\n";
            $br_required = 1;
        }
    }

    // 待機: 管理者になっているコミュニティのサブコミュニティ追加依頼
    foreach ($this->_tpl_vars['waiting_for_sub_community_link_row_array_array'] as $waiting_for_sub_community_link_row_array) {
        if ($waiting_for_sub_community_link_row_array['waiting_for_sub_community_link_row_array_num']) {
            echo "<div align=\"left\">";
            echo "<a href=\"".$waiting_for_sub_community_link_row_array['waiting_list_for_sub_community_link_url']."\">";
            echo htmlspecialchars($waiting_for_sub_community_link_row_array['community_row']['community_name']) . ACSMsg::get_msg("User", "Index.tpl.php",'M012')." :: ";
            echo ACSMsg::get_msg("User", "Index.tpl.php",'M015')." (" . $waiting_for_sub_community_link_row_array['waiting_for_sub_community_link_row_array_num'] . ACSMsg::get_msg("User", "Index.tpl.php",'M011').")";
            echo "</a>";
            echo "</div>\n";
            $br_required = 1;
        }
    }

    // 待機: コミュニティ招待
    if ($this->_tpl_vars['waiting_for_invite_to_community_row_array_num']) {
        echo "<div align=\"left\">";
        echo "<a href=\"".$this->_tpl_vars['waiting_list_for_invite_to_community_url']."\">";
        echo ACSMsg::get_msg("User", "Index.tpl.php",'M016')." (" . $this->_tpl_vars['waiting_for_invite_to_community_row_array_num'] . ACSMsg::get_msg("User", "Index.tpl.php",'M011').")";
        echo "</a>";
        echo "</div>\n";
        $br_required = 1;
    }

    // 新着コメント
    if ($this->_tpl_vars['new_comment_diary_row_array_num'] > 0) {
        echo "<div align=\"left\">";
        echo "<a href=\"" . $this->_tpl_vars['new_comment_diary_url'] . "\"><font color=\"red\"><b>";
        echo $this->_tpl_vars['new_comment_diary_row_array_num'] . ACSMsg::get_msg("User", "Index.tpl.php",'M017');
        echo "</b></font></a>";
        echo "</div>\n";
        $br_required = 1;
    }

    // 未読メッセージ
    if ($this->_tpl_vars['new_message_row_array_num'] > 0) {
        echo "<div align =\"left\">";
        echo "<a href=\"".$this->_tpl_vars['new_message_url']."\"><font color=\"red\"><b>";
        echo $this->_tpl_vars['new_message_row_array_num']. ACSMsg::get_msg("User", "Index.tpl.php",'M038');
        echo "</b></font></a>";
        echo "</div>\n";
        $br_required = 1;
    }

    if ($br_required) {
        echo "<br>\n";
    }

    // 各最新情報
    echo "<table class=\"layout_table\" width=\"95%\">\n";
    echo "<tr>\n";
    echo "<td valign=\"top\" width=\"220px\">".$this->_tpl_vars['NewDiary']."</td>\n";
    echo "<td valign=\"top\" width=\"220px\">".$this->_tpl_vars['DiaryCommentHistory']."</td>\n";
    echo "</tr>\n";
    echo "<tr><td height=\"10\" colspan=\"2\"></td></tr>\n";
    echo "<tr>\n";
    echo "<td valign=\"top\" width=\"440\" colspan=\"2\">".$this->_tpl_vars['NewBBS']."</td>\n";
    echo "</tr>";
    echo "<tr><td height=\"10\" colspan=\"2\"></td></tr>\n";
    echo "<tr>\n";
    echo "<td valign=\"top\" width=\"220px\">" . $this->_tpl_vars['NewFriendsFolder'] . "</td>\n";
    echo "<td valign=\"top\" width=\"220px\">" . $this->_tpl_vars['NewCommunityFolder'] ."</td>\n";
    echo "</tr>\n";
    echo "</table>";

} else {
    // ********** だれかのマイページを閲覧している場合 **********//

    echo "<div class=\"ttl\">";
    echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "Index.tpl.php",'NAME_PROFILE'),array(
            "{USER_NAME}" => htmlspecialchars($this->_tpl_vars['profile']['community_name'])));
    echo "</div><br><br>\n";

    if ($this->_tpl_vars['add_myfriends_url']) {
        echo "<div align=\"left\">";
        echo "<input type=\"button\" value=\"".ACSMsg::get_msg("User", "Index.tpl.php",'M018')."\" onclick=\"location.href='" .$this->_tpl_vars[add_myfriends_url]. "'\">";
        echo "</form>";
        echo "</div>";
        echo "<br>";
    } else if ($this->_tpl_vars['peruse_mode'] != 9) {
        echo "</form>";
    }

    // プロフィールテーブル
    echo "<table class=\"common_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";
    if (is_array($this->_tpl_vars['profile']['contents_row_array']['user_name'])) {
        echo "<tr>";
        echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "Index.tpl.php",'M019')."</td>";
        echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['user_name']) . "</td>";
        echo "</tr>\n";
    }
    echo "<tr>";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "Index.tpl.php",'M020')."</td>";
    echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['community_name']) . "</td>";
    echo "</tr>\n";
    echo "<tr>";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "Index.tpl.php",'M021')."</td>";
    echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['belonging']) . "</td>";
    echo "</tr>\n";
    echo "<tr>";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "Index.tpl.php",'M022')."</td>";
    echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['speciality']) . "</td>";
    echo "</tr>\n";
    if (is_array($this->_tpl_vars['profile']['contents_row_array']['birthplace'])) {
        echo "<tr>";
        echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "Index.tpl.php",'M023')."</td>";
        echo "<td bgcolor=\"#FFFFFF\">" . htmlspecialchars($this->_tpl_vars['profile']['birthplace']) . "</td>";
        echo "</tr>\n";
    }
    if (is_array($this->_tpl_vars['profile']['contents_row_array']['birthday'])) {
        echo "<tr>";
        echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "Index.tpl.php",'M024')."</td>";
        echo "<td bgcolor=\"#FFFFFF\">" . $this->_tpl_vars['profile']['birthday'] . "</td>";
        echo "</tr>\n";
    }

    // 自己紹介は、閲覧者別に登録されている
    echo "<tr>";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "Index.tpl.php",'M025')."</td>";
    if ($this->_tpl_vars['peruse_mode'] == 1) {		//ログインユーザ
        echo "<td bgcolor=\"#FFFFFF\">" . nl2br(ACSTemplateLib::auto_link(htmlspecialchars($this->_tpl_vars['profile']['community_profile_login']))) . "</td>";
    }
    if ($this->_tpl_vars['peruse_mode'] == 2) {		//すべての友人
        echo "<td bgcolor=\"#FFFFFF\">" . nl2br(ACSTemplateLib::auto_link(htmlspecialchars($this->_tpl_vars['profile']['community_profile_friend']))) . "</td>";
    }
    if ($this->_tpl_vars['peruse_mode'] == 9) {		//一般
        echo "<td bgcolor=\"#FFFFFF\">" . nl2br(ACSTemplateLib::auto_link(htmlspecialchars($this->_tpl_vars['profile']['community_profile']))) . "</td>";
    }
    echo "</tr>\n";
    echo "</table>\n";
    echo "<br>";
}

?>

</td>
</tr>
</table>
<!---------------------------------------------------------------->

<br><br>

<!---------------------------------------------------------------->
<table class="layout_table" width="660px">
<tr>
<td valign="top">
<!-- マイフレンズ -->
<table class="mylist_table">
<tr>
<td id="myttl" class="mlttl"><?= ACSMsg::get_msg("User", "Index.tpl.php",'M026') ?> (<?= $this->_tpl_vars['friends_row_array_num'] ?>)</td>
</tr>
<tr>
<td class="mltd">
<table width="320px" border="0" cellspacing="10" cellpadding="5">
<?php
if (count($this->_tpl_vars['friends_row_array'])) {
    $count = 0;
    foreach ($this->_tpl_vars['friends_row_array'] as $user_info_row) {
        if ($count % 3 == 0) {
            echo "<tr>";
        }
        echo '<td align="center" id="mytbl">';
        echo "<a href=\"" . htmlspecialchars($user_info_row['top_page_url']) . "\">" . '<img src="' . $user_info_row['image_url'] . '" border="0">' . "</a><br>";
        echo "<a href=\"" . htmlspecialchars($user_info_row['top_page_url']) . "\">";
        echo htmlspecialchars($user_info_row['community_name']);
        echo "</a>";
        echo "(" . $user_info_row['friends_row_array_num'] . ")";
        echo "</td>";
        if ($count % 3 == 2) {
            echo "</tr>\n";
        }
        $count++;
    }
} else {
    echo "<tr>";
    echo "<td id=\"mytbl\">".ACSMsg::get_msg("User", "Index.tpl.php",'M027')."</td>";
    echo "</tr>\n";
}
?>
<tr>
<td colspan="3" style="padding:5px;" bgcolor="ffffff">
<?php
if ($this->_tpl_vars['friends_list_url']) {
    echo "<a class=\"ichiran\" href=\"".$this->_tpl_vars['friends_list_url']."\">".ACSMsg::get_msg("User", "Index.tpl.php",'M028')."</a><br>\n";
}
if ($this->_tpl_vars['friends_group_list_url']) {
    echo "<a class=\"ichiran\" href=\"".$this->_tpl_vars['friends_group_list_url']."\">".ACSMsg::get_msg("User", "Index.tpl.php",'M029')."</a><br>\n";
}
?>
</td>
</tr>

</table></td>
</tr>
</table>
</td>
<!-------------------------------->
<td width="20px">&nbsp;</td>
<td valign="top">
<!-- マイコミュニティ -->


<table class="mylist_table">
<tr>
<td id="myttl" class="mlttl"><?= ACSMsg::get_msg("User", "Index.tpl.php",'M030') ?> (<?= $this->_tpl_vars['community_row_array_num'] ?>)</td>
</tr>
<tr>
<td class="mltd">
<table width="320px" border="0" cellspacing="10" cellpadding="5">

<?php
if (count($this->_tpl_vars['community_list'])) {
    $count = 0;
    foreach ($this->_tpl_vars['community_list'] as $community_row) {
        if ($count % 3 == 0) {
            echo "<tr>";
        }
        echo '<td align="center" id="mytbl">';
        echo "<a href=\"" . $community_row['top_page_url'] ."\">" . 
                '<img src="' . $community_row['image_url'] . '" border="0">' . "</a><br>";
        echo "<a href=\"" . $community_row['top_page_url'] . "\">";
        echo htmlspecialchars($community_row['community_name']);
        echo "</a>";
        echo "(" . $community_row['community_member_num'] . ")";
        echo "</td>";
        if ($count % 3 == 2) {
            echo "</tr>\n";
        }
        $count++;
    }
} else {
    echo "<tr>";
    echo "<td id=\"mytbl\">".ACSMsg::get_msg("User", "Index.tpl.php",'M027')."</td>";
    echo "</tr>\n";
}
?>
<tr>
<td colspan="3" style="padding:5px;" bgcolor="ffffff">

<a class="ichiran" href="<?= $this->_tpl_vars['community_list_url'] ?>"><?= ACSMsg::get_msg("User", "Index.tpl.php",'M032') ?></a><br>

</td>
</tr>

</table></td>
</tr>
</table>


</td>
</table>
