<?php
// +----------------------------------------------------------------------+
// | PHP version 5 & mojavi version 3                                     |
// +----------------------------------------------------------------------+
// | Authors: y-yuki v 1.0 2009/01/23 14:40:00                            |
// +----------------------------------------------------------------------+
// $Id: NewFriendsFolder.tpl.php,v 1.6 2007/03/28 08:39:34 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "NewFriendsFolder.tpl.php", 'M001') ?></div>
<br>

<?php
if ($this->_tpl_vars['get_days'] > 0) {
?>
    <div class="getdays">
        <?= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "NewFriendsFolder.tpl.php", 'GETDAYS')
        ,array('{DAYS}'=>$this->_tpl_vars['get_days'])) ?>
    </div>
<?php
}
?>
<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>

<table class="layout_table">
<?php
foreach ($this->_tpl_vars['new_file_row_array'] as $new_file_row) {
?>
<tr>
    <td width="16px"><img src="<?= ACS_IMAGE_DIR.'file.gif' ?>"></td>
    <td> &nbsp;
<?php
    if ($new_file_row['is_unread']) {
        echo "<b>";
    }
?>
        <a href="<?= $new_file_row['file_detail_url'] ?>">
        <?= htmlspecialchars(ACSTemplateLib::trim_long_str($new_file_row['display_file_name'])) ?></a>
<?php
    if ($new_file_row['is_unread']) {
        echo "</b>";
    }
?>
    &nbsp; (<?= htmlspecialchars($new_file_row['community_name']) ?>)
    </td>
</tr>
<?php
}
// データが0件の場合
if (count($this->_tpl_vars['new_file_row_array']) == 0) {
    echo "<tr><td>".ACSMsg::get_msg("User", "NewFriendsFolder.tpl.php",'M002')."</td></tr>\n";
}
?>
</table>

<?php
if ($this->_tpl_vars['new_folder_url']) {
    echo "<br>\n";
    echo "<div align=\"right\">";
    echo "<a class=\"ichiran\" href=\"" . $this->_tpl_vars['new_folder_url']. "\">" . 
            ACSMsg::get_msg("User", "NewFriendsFolder.tpl.php",'M003')."</a> &nbsp; ";
    echo "</div>\n";
}
?>
