<?php
// +----------------------------------------------------------------------+
// | PHP version 5 & mojavi version 3                                     |
// +----------------------------------------------------------------------+
// | Authors: y-yuki v 1.0 2009/01/23 16:00:00                            |
// +----------------------------------------------------------------------+
// $Id: SearchCommunity.tpl.php,v 1.19 2007/03/28 05:58:19 w-ota Exp $
?>

<div class="ttl">
<?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M001') ?>
<a href="<?= $this->_tpl_vars['PressRelease_community_url'] ?>"><img src="<?php echo ACS_IMAGE_DIR . "rss.png" ?>" border=0></a>
</div>

<p>
<?php
if ($this->_tpl_vars['create_community_url']) {
    echo '<a class="ichiran3" href="' . $this->_tpl_vars['create_community_url'] . '">'.ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M002').'</a><br><br>';
}
?>
</p>

<p>
<form action="<?= $this->_tpl_vars['action_url'] ?>" method="get">
<input type="hidden" name="module" value="<?= $this->_tpl_vars['module'] ?>">
<input type="hidden" name="action" value="<?= $this->_tpl_vars['action'] ?>">
<input type="hidden" name="search" value="1">

<table border="0" cellpadding="10" cellspacing="1" bgcolor="#555555">
<tr>
    <td bgcolor="#FFF5AA">
        <table class="layout_table">
        <tr>
            <td><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M003') ?></td>
            <td>
                <input type="text" name="q" value="<?= htmlspecialchars($this->_tpl_vars['form']['q']) ?>" size="30">
                <input type="submit" value="<?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M004') ?>">
            </td>
        </tr>
        <tr>
            <td><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M005') ?></td>
            <td>
                <select name="category_code">
<?php
unset($selected);
$selected[$this->_tpl_vars['form']['category_code']] = ' selected';
foreach ($this->_tpl_vars['category_master_row_array'] as $category_master_row) {
    echo "<option value=\"$category_master_row[category_code]\"{$selected[$category_master_row['category_code']]}>";
    echo htmlspecialchars($category_master_row['category_name']);
    // コミュニティ数
    if (isset($category_master_row['community_num'])) {
        echo ' (' . $category_master_row['community_num'] . ')';
    }
    echo "\n";
}
?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M006') ?></td>
            <td>
<?php
unset($selected);
if ($this->_tpl_vars['form']['admission_flag'] == 't' || $this->_tpl_vars['form']['admission_flag'] == 'f') {
    $selected[$this->_tpl_vars['form']['admission_flag']] = ' checked';
} else {
    $selected['0'] = ' checked';
}
?>
                <input type="radio" name="admission_flag" value="0"<?= $selected['0'] ?>><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M007') ?>&nbsp;
                <input type="radio" name="admission_flag" value="f"<?= $selected['f'] ?>><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M008') ?>&nbsp;
                <input type="radio" name="admission_flag" value="t"<?= $selected['t'] ?>><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M009') ?>
            </td>
        </tr>
        <tr>
            <td><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M010') ?></td>
            <td>
<?php
unset($selected);
if ($this->_tpl_vars['form']['order'] == 'name' || $this->_tpl_vars['form']['order'] == 'new' || $this->_tpl_vars['form']['order'] == 'community_member_num') {
    $selected[$this->_tpl_vars['form']['order']] = ' selected';
} else {
    $selected['name'] = ' selected';
}
?>
                <select name="order">
                    <option value="community_name"<?= $selected['community_name'] ?>><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M011') ?>
                    <option value="new"<?= $selected['new'] ?>><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M012') ?>
                    <option value="community_member_num"<?= $selected['community_member_num'] ?>><?= ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M013') ?>
                </select>
            </td>
        </tr>
        </table>
    </td>
</tr>
</table>

</form>
</p>

<?php
if ($this->_tpl_vars['form']['search'] != '') {

    if (count($this->_tpl_vars['community_row_array'])) {
        // ページング表示
        ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);

        echo "<table class=\"common_table\" border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";
        foreach ($this->_tpl_vars['community_row_array'] as $community_row) {
            echo "<tr>";
            // 写真
            echo "<td align=\"center\" rowspan=\"3\" bgcolor=\"#ffffff\">";
            echo "<a href=\"$community_row[top_page_url]\"><img src=\"$community_row[image_url]\" border=\"0\"></a><br>";
            echo "<a href=\"$community_row[top_page_url]\">" . htmlspecialchars($community_row['community_name']) . "</a>";
            echo "(" . $community_row['community_member_num'] . ")";
            echo "</td>";
            // 自己紹介
            echo "<td class=\"nowrap\" id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M014')."</td>";
            echo "<td bgcolor=\"#ffffff\">";
            echo nl2br(htmlspecialchars(ACSTemplateLib::trim_long_str($community_row['contents_row_array']['community_profile']['contents_value'], 500)));
            echo "</td>";
            echo "</tr>\n";

            // カテゴリ
            echo "<tr>";
            echo "<td class=\"nowrap\" id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M005')."</td>";
            echo "<td bgcolor=\"#ffffff\">" . $community_row['category_name'] . "</td>";
            echo "</tr>\n";

            // 管理者
            echo "<tr>";
            echo "<td class=\"nowrap\" id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M016')."</td>";
            echo "<td bgcolor=\"#ffffff\">";
            $str = '';
            foreach ($community_row['community_admin_user_info_row_array'] as $community_admin_user_info_row) {
                if ($str != '') {
                    $str .= ', ';
                }
                $str .= "<a href=\"" . $community_admin_user_info_row['top_page_url'] . "\">";
                $str .= htmlspecialchars($community_admin_user_info_row['community_name']);
                $str .= "</a>";
            }
            echo $str;
            echo "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        echo "<br>\n";

        // ページング表示
        ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);

    } else {
        echo ACSMsg::get_msg("Community", "SearchCommunity.tpl.php", 'M017')."<br>\n";
    }

} else {
    echo $this->_tpl_vars['NewCommunity'];
}
?>
