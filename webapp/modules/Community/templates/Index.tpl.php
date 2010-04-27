<?php
// +----------------------------------------------------------------------+
// | PHP version 5 & mojavi version 3                                     |
// +----------------------------------------------------------------------+
// | Authors: y-yuki v 1.0 2009/01/23 16:00:00                            |
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// | Authors: kuwayama       v 1.21 2006/03/08    @update akitsu          |
// +----------------------------------------------------------------------+
// Index.tpl.php
// $Id: Index.tpl.php,v 1.33 2007/03/28 05:58:19 w-ota Exp $
?>

<div class="ttl">
<?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?> <?= ACSMsg::get_msg('Community', 'Index.tpl.php','M001') ?>
<a href="<?= $this->_tpl_vars['PressRelease_community_url'] ?>"><img src="<?php echo ACS_IMAGE_DIR . "rss.png" ?>" border=0></a>
</div>

<br>
<table class="layout_table">
<tr>
    <td valign="top">
        <table class="layout_table" width="150px">
        <tr>
            <td align="center">
            <!-- 写真 -->
<?php
if ($this->_tpl_vars['edit_profile_image_url']) {
    echo "<a href=\"" .  $this->_tpl_vars['edit_profile_image_url'] . "\">";
    echo '<img src="' . $this->_tpl_vars['community_row']['image_url'] . '" style="margin-top:10px;margin-bottom:5px" width="120px" border="0"></a><br>';
    // このコミュニティの管理者の場合のみ変更できる
    echo "<a href=\"" . $this->_tpl_vars['edit_profile_image_url'] . "\">".ACSMsg::get_msg('Community', 'Index.tpl.php','M002')."</a><br>\n";
} else {
    echo '<img src="'. $this->_tpl_vars['community_row']['image_url'] . '" style="margin-top:10px;margin-bottom:5px" width="120px" border="0"><br>';
}
?>
            </td>
        </tr>
        <tr>
            <td class="nowrap">
                <br>
                    <a href="<?= $this->_tpl_vars['bbs_url'] ?>"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M003') ?></a><br>
                    <a href="<?= $this->_tpl_vars['community_folder_url'] ?>"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M004') ?></a><br>
<?php
if ($this->_tpl_vars['community_schedule_url']) {
    echo "<a href=\"".$this->_tpl_vars['community_schedule_url']."\">". ACSMsg::get_msg('Community', 'Index.tpl.php','M038') ."</a><br>\n";
}
if ($this->_tpl_vars['edit_community_admin_url']) {
    // このコミュニティの管理者の場合のみ変更できる
    echo "<a href=\"" . $this->_tpl_vars['community_change_url'] . "\">" . ACSMsg::get_msg('Community', 'Index.tpl.php','M005') . "</a><br>\n";
}
?>
<br>
<?php
if ($this->_tpl_vars['invite_to_community_url']) {
    echo "<a href=\"" . $this->_tpl_vars['invite_to_community_url'] . "\">".ACSMsg::get_msg('Community', 'Index.tpl.php','M006')."</a><br>\n";
}
if ($this->_tpl_vars['edit_community_admin_url']) {
    echo "<a href=\"" . $this->_tpl_vars['edit_community_admin_url'] . "\">".ACSMsg::get_msg('Community', 'Index.tpl.php','M007')."</a><br>\n";
}
if ($this->_tpl_vars['community_link_url']) {
    echo "<a href=\"" . $this->_tpl_vars['community_link_url'] . "\">".ACSMsg::get_msg('Community', 'Index.tpl.php','M008')."</a><br>\n";
}
if ($this->_tpl_vars['edit_external_rss_url']) {
    echo "<a href=\"" . $this->_tpl_vars['edit_external_rss_url'] . "\">".ACSMsg::get_msg('Community', 'Index.tpl.php','M039')."</a><br>\n";
}
if ($this->_tpl_vars['delete_community_url']) {
    echo "<a href=\"" . $this->_tpl_vars['delete_community_url'] . "\">".ACSMsg::get_msg('Community', 'Index.tpl.php','M009')."</a><br>\n";
}
?>
            </td>
        </tr>
        </table>
    </td>
    <td width="15px">&nbsp;</td>
    <td>
<?php
// コミュニティ参加
if ($this->_tpl_vars['join_community_url']) {
    echo "<div align=\"right\">";
    echo "<form>";
    echo "<input type=\"button\" value=\"".ACSMsg::get_msg('Community', 'Index.tpl.php','M010')."\" onclick=\"location.href='" .$this->_tpl_vars['join_community_url']. "'\">";
    echo "</form>";
    echo "</div>";
    echo "<br>";
}
if ($this->_tpl_vars['leave_community_url']) {
    echo "<div align=\"right\">";
    echo "<form>";
    echo "<input type=\"button\" value=\"".ACSMsg::get_msg('Community', 'Index.tpl.php','M011')."\" onclick=\"location.href='" .$this->_tpl_vars['leave_community_url']. "'\">";
    echo "</form>";
    echo "</div>";
    echo "<br>";
}

// 待機: コミュニティ参加
if ($this->_tpl_vars['waiting_for_join_community_row_array_num']) {
    echo "<div align=\"left\">";
    echo "<a href=\"" . $this->_tpl_vars['waiting_for_join_community_url'] . "\">";
    echo ACSMsg::get_msg('Community', 'Index.tpl.php','M012')." (" . $this->_tpl_vars['waiting_for_join_community_row_array_num'] . ACSMsg::get_msg('Community', 'Index.tpl.php','M015').")";
    echo "</a>";
    echo "</div>";
    echo "<br>";
}
// 待機: 親コミュニティ追加
if ($this->_tpl_vars['waiting_for_parent_community_link_row_array_num']) {
    echo "<div align=\"left\">";
    echo "<a href=\"" . $this->_tpl_vars['waiting_for_parent_community_link_url'] . "\">";
    echo ACSMsg::get_msg('Community', 'Index.tpl.php','M013')." (" . $this->_tpl_vars['waiting_for_parent_community_link_row_array_num'] . ACSMsg::get_msg('Community', 'Index.tpl.php','M015').")";
    echo "</a>";
    echo "</div>";
    echo "<br>";
}
// 待機: サブコミュニティ追加
if ($this->_tpl_vars['waiting_for_sub_community_link_row_array_num']) {
    echo "<div align=\"left\">";
    echo "<a href=\"" . $this->_tpl_vars['waiting_for_sub_community_link_url'] . "\">";
    echo ACSMsg::get_msg('Community', 'Index.tpl.php','M014')." (" . $this->_tpl_vars['waiting_for_sub_community_link_row_array_num'] . ACSMsg::get_msg('Community', 'Index.tpl.php','M015').")";
    echo "</a>";
    echo "</div>";
    echo "<br>";
}
?>

<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33" width="500px">
<tr>
    <td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M016') ?></td>
    <td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['community_row']['community_name']) ?></td>
</tr>
<tr>
    <td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M017') ?></td>
    <td bgcolor="#ffffff"><?= $this->_tpl_vars['community_row']['register_date'] ?></td>
</tr>
<tr>
    <td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M018') ?></td>
    <td bgcolor="#ffffff"><?= nl2br(ACSTemplateLib::auto_link(htmlspecialchars($this->_tpl_vars['community_row']['contents_row_array']['community_profile']['contents_value']))) ?></td>
</tr>
<tr>
    <td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M019') ?></td>
    <td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['community_row']['category_name']) ?></td>
</tr>
<tr>
    <td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M020') ?></td>
    <td bgcolor="#ffffff">
<?php
foreach ($this->_tpl_vars['community_admin_user_info_row_array'] as $community_admin_user_info_row) {
    echo "<a href=\"" . $community_admin_user_info_row['top_page_url'] . "\">";
    echo htmlspecialchars($community_admin_user_info_row['community_name']);
    echo "</a><br>\n";
}
?>
    </td>
</tr>
<tr>
    <td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M021') ?></td>
    <td bgcolor="#ffffff"><?= $this->_tpl_vars['community_row']['community_member_num'] ?> <?= ACSMsg::get_msg('Community', 'Index.tpl.php','M022') ?></td>
</tr>
<tr>
    <td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M023') ?></td>
    <td bgcolor="#ffffff">
<?php
if ($this->_tpl_vars['community_row']['admission_flag'] == 't') {
    echo ACSMsg::get_msg('Community', 'Index.tpl.php','M024')."<br>\n";
    if (count($this->_tpl_vars['community_row']['join_trusted_community_row_array'])) {
        $trusted_community_str = '';
        foreach ($this->_tpl_vars['community_row']['join_trusted_community_row_array'] as $join_trusted_community_row) {
            if ($trusted_community_str != '') {
                $trusted_community_str .= ', ';
            }
            $trusted_community_str .= "<a href=\"" .$join_trusted_community_row['top_page_url']. "\">";
            $trusted_community_str .= htmlspecialchars($join_trusted_community_row['community_name']);
            $trusted_community_str .= "</a>";
        }
        echo "<table class=\"inner_layout_table\" bgcolor=\"#dddddd\"><tr>";
        echo "<td class=\"layout_table\">".ACSMsg::get_msg('Community', 'Index.tpl.php', 'M025')." : " . $trusted_community_str . "</td>";
        echo "</tr></table>\n";
    }
} else {
    echo ACSMsg::get_msg('Community', 'Index.tpl.php', 'M026')."<br>\n";
}
?>
    </td>
</tr>
<tr>
    <td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php', 'M027') ?></td>
    <td bgcolor="#ffffff">
<?php
echo htmlspecialchars($this->_tpl_vars['community_row']['contents_row_array']['bbs']['open_level_name']);
if ($this->_tpl_vars['community_row']['contents_row_array']['bbs']['open_level_name'] == ACSMsg::get_mst('open_level_master', 'D04')) {
    if (count($this->_tpl_vars['community_row']['contents_row_array']['bbs']['trusted_community_row_array'])) {
        $trusted_community_str = '';
        foreach ($this->_tpl_vars['community_row']['contents_row_array']['bbs']['trusted_community_row_array'] as $trusted_community_row) {
            if ($trusted_community_str != '') {
                $trusted_community_str .= ', ';
            }
            $trusted_community_str .= "<a href=\"" . $trusted_community_row['top_page_url'] . "\">";
            $trusted_community_str .= htmlspecialchars($trusted_community_row['community_name']);
            $trusted_community_str .= "</a>";
        }
        echo "<table class=\"inner_layout_table\" bgcolor=\"#dddddd\"><tr>";
        echo "<td class=\"layout_table\">".ACSMsg::get_msg('Community', 'Index.tpl.php', 'M028')." : " . $trusted_community_str . "</td>";
        echo "</tr></table>\n";
    }
}
?>
    </td>
</tr>
<tr>
    <td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php', 'M029') ?></td>
    <td bgcolor="#ffffff">
<?php
echo htmlspecialchars($this->_tpl_vars['community_row']['contents_row_array']['community_folder']['open_level_name']);
if ($this->_tpl_vars['community_row']['contents_row_array']['community_folder']['open_level_name'] == ACSMsg::get_mst('open_level_master','D04')) {
    if (count($this->_tpl_vars['community_row']['contents_row_array']['community_folder']['trusted_community_row_array'])) {
        $trusted_community_str = '';
        foreach ($this->_tpl_vars['community_row']['contents_row_array']['community_folder']['trusted_community_row_array'] as $trusted_community_row) {
            if ($trusted_community_str != '') {
                $trusted_community_str .= ', ';
            }
            $trusted_community_str .= "<a href=\"" . $trusted_community_row['top_page_url'] . "\">";
            $trusted_community_str .= htmlspecialchars($trusted_community_row['community_name']);
            $trusted_community_str .= "</a>";
        }
        echo "<table class=\"inner_layout_table\" bgcolor=\"#dddddd\"><tr>";
        echo "<td class=\"layout_table\">".ACSMsg::get_msg('Community', 'Index.tpl.php','M028')." : " . $trusted_community_str . "</td>";
        echo "</tr></table>\n";
    }
}
?>
</td>
</tr>
<tr>
<td id="myttl" bgcolor="#DEEEBD"><?= ACSMsg::get_msg('Community', 'Index.tpl.php', 'M030') ?></td>
<td bgcolor="#ffffff"><?= htmlspecialchars($this->_tpl_vars['community_row']['contents_row_array']['self']['open_level_name']) ?>
</td>
</tr>

<?php
// 親コミュニティ列
if ($this->_tpl_vars['parent_community_row_array']) {
    echo "<tr>\n";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg('Community', 'Index.tpl.php', 'M031')."</td>\n";
    echo "<td bgcolor=\"#ffffff\">";
    foreach ($this->_tpl_vars['parent_community_row_array'] as $parent_community_row) {
        echo "<a href=\"" . $parent_community_row['top_page_url'] . "\">" . htmlspecialchars($parent_community_row['community_name']) . "</a><br>\n";
    }
    echo "</td>";
    echo "</tr>\n";
}

// サブコミュニティ列
if ($this->_tpl_vars['sub_community_row_array']) {
    echo "<tr>\n";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg('Community', 'Index.tpl.php', 'M032')."</td>\n";
    echo "<td bgcolor=\"#ffffff\">";
    foreach ($this->_tpl_vars['sub_community_row_array'] as $sub_community_row) {
        echo "<a href=\"" . $sub_community_row['top_page_url'] . "\">" . htmlspecialchars($sub_community_row['community_name']) . "</a><br>\n";
    }
    echo "</td>";
    echo "</tr>\n";
}
?>

<?php
// 新着記事
if ($this->_tpl_vars['bbs_row_array']) {
    echo "<tr>\n";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg('Community', 'Index.tpl.php', 'M033')."</td>\n";
    echo "<td bgcolor=\"#ffffff\">";
    foreach ($this->_tpl_vars['bbs_row_array'] as $bbs_row) {
        echo $bbs_row['bbs_last_post_date'] . " ";
        echo "<a href=\"" . $bbs_row['bbs_res_url'] . "\">";
        echo htmlspecialchars(ACSTemplateLib::trim_long_str($bbs_row['subject']));
        echo "</a> (" . $bbs_row['bbs_res_num'] . ")<br>\n";
    }
    echo "</td>";
    echo "</tr>\n";
}
?>

</table>

<?php
if ($this->_tpl_vars['edit_community_profile_url']) {
    echo "<br><div align=\"right\">\n";
    echo "<a href=\"" . $this->_tpl_vars['edit_community_profile_url'] . "\">".ACSMsg::get_msg('Community', 'Index.tpl.php', 'M034')."</a>\n";
    echo "</div>\n";
}
?>

</td>
</tr>
</table>
<br><br>


<table border="0" cellpadding="4" cellspacing="1" bgcolor="#CCCC33">
<tr>
<td bgcolor="#F6F2B8" id="myttl" style="padding:5px;"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M035') ?> (<?= $this->_tpl_vars['community_row']['community_member_num'] ?><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M022') ?>)</td>
</tr>
<tr>
<td bgcolor="#FFFFFF">

<table class="common_table" border="0" cellspacing="10" cellpadding="5">
<?php
if ($this->_tpl_vars['community_member_display_user_info_row_array']) {
    $count = 0;
    foreach ($this->_tpl_vars['community_member_display_user_info_row_array'] as $community_member_user_info_row) {
        if ($count % 6 == 0) {
            echo "<tr>";
        }
        echo '<td align="center" id="mytbl">';
        echo '<a href="' . $community_member_user_info_row['top_page_url'] . '"><img src="' . 
                $community_member_user_info_row['image_url'] . '" border="0"></a><br>';
        echo "<a href=\"" . $community_member_user_info_row['top_page_url'] . "\">";
        echo htmlspecialchars($community_member_user_info_row['community_name']);
        echo "</a>";
        echo "(" . $community_member_user_info_row['friends_row_array_num'] . ")";
        echo "</td>";
        if ($count % 6 == 5) {
            echo "</tr>\n";
        }
        $count++;
    }
} else {
    echo "<tr>";
    echo "<td id=\"mytbl\">No Member</td>";
    echo "</tr>\n";
}
?>

<tr>
<td colspan="6" style="padding:5px;" bgcolor="ffffff">

<a class="ichiran3" href="<?= $this->_tpl_vars['community_member_list_url'] ?>"><?= ACSMsg::get_msg('Community', 'Index.tpl.php','M036') ?></a><br>
<?php
if ($this->_tpl_vars['delete_community_member_list_url']) {
    echo '<a class="ichiran3" href="' . $this->_tpl_vars['delete_community_member_list_url'] . '">'.ACSMsg::get_msg('Community', 'Index.tpl.php','M037').'</a><br>';
    echo "\n";
}

?>
</td>
</tr>
</table>


</tr>
</table>

