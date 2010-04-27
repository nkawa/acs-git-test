<?php
// +----------------------------------------------------------------------+
// | PHP version 5 & mojavi version 3                                     |
// +----------------------------------------------------------------------+
// | Authors: y-yuki v 1.0 2009/01/23 14:40:00                            |
// +----------------------------------------------------------------------+
// $Id: NewDiary.tpl.php,v 1.7 2007/03/27 02:12:43 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "NewDiary.tpl.php",'M001') ?></div>
<br>

<?php
if ($this->_tpl_vars['get_days']>0) {
?>
<div class="getdays">
    <?= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "NewDiary.tpl.php",'GETDAYS'),
    array('{DAYS}'=>$this->_tpl_vars['get_days'])) ?>
</div>
<?php
}
?>
<?php
// �ڡ�����ɽ��
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>

<table class="layout_table">
<?php
foreach ($this->_tpl_vars['new_diary_row_array'] as $new_diary_row) {
    echo "<tr>";
    echo "<td width=\"16px\">";
    echo "<img src=\"" . ACS_IMAGE_DIR . 'yaj2.gif' . "\">";
    echo "</td>";
    echo "<td> &nbsp;";
    if ($new_diary_row['is_unread']) {
        echo "<b>";
    }
    echo "<a href=\"$new_diary_row[diary_comment_url]\">" . htmlspecialchars(ACSTemplateLib::trim_long_str($new_diary_row['subject'])) . "</a>";
    if ($new_diary_row['is_unread']) {
        echo "</b>";
    }
    echo "&nbsp; ($new_diary_row[diary_comment_num]) (" . htmlspecialchars($new_diary_row['community_name']) . ")";

    echo "</td>";
    echo "</tr>\n";
}
if (count($this->_tpl_vars['new_diary_row_array']) == 0) {
    echo "<tr><td>".ACSMsg::get_msg("User", "NewDiary.tpl.php", 'M002')."</td></tr>\n";
}
?>
</table>

<?php
if ($this->_tpl_vars['new_diary_url']) {
    echo "<br>\n";
    echo "<div align=\"right\">";
    echo "<a class=\"ichiran\" href=\"" . $this->_tpl_vars['new_diary_url'] . "\">" . ACSMsg::get_msg("User", "NewDiary.tpl.php", 'M003')."</a> &nbsp; ";
    echo "</div>\n";
}
?>
