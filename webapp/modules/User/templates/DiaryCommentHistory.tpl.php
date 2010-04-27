<?php
// +----------------------------------------------------------------------+
// | PHP version 5 & mojavi version 3                                     |
// +----------------------------------------------------------------------+
// | Authors: y-yuki v 1.0 2009/01/23 14:40:00                            |
// +----------------------------------------------------------------------+
// $Id: DiaryCommentHistory.tpl.php,v 1.6 2007/03/01 09:01:43 w-ota Exp $
?>

<div class="ttl"><?= ACSMsg::get_msg("User", "DiaryCommentHistory.tpl.php", 'M001') ?></div>
<br>

<?php
if ($this->_tpl_vars['get_days'] > 0) {
?>
<div class="getdays">
    <?= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "DiaryCommentHistory.tpl.php", 'GETDAYS'), 
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
// �롼�׳���
foreach ($this->_tpl_vars['commented_diary_row_array'] as $commented_diary_row) {
?>
<tr>
    <td width="16px"><img src="<?= ACS_IMAGE_DIR ?>yaj2.gif"></td>
    <td>&nbsp;
<?php
    // ������URL�����������ȥ�
    if ($commented_diary_row['is_unread']) {
        echo "<b>";
    }
?>
    <a href="<?= $commented_diary_row['diary_comment_url'] ?>"><?= htmlspecialchars(ACSTemplateLib::trim_long_str($commented_diary_row['subject'])) ?></a>
<?php
    // �����ȿ�
    if ($commented_diary_row['is_unread']) {
        echo "</b>";
    }
?>
    &nbsp; (<?= $commented_diary_row['diary_comment_num'] ?>) (<?= htmlspecialchars($commented_diary_row['community_name']) ?>)
    </td>
</tr>
<?php
}
// ���롼�׽�λ

// ������0��ξ��
if (count($this->_tpl_vars['commented_diary_row_array']) == 0) {
    echo "<tr><td>" . ACSMsg::get_msg("User", "DiaryCommentHistory.tpl.php", 'M002') . "</td></tr>\n";
}
?>
</table>

<?php
if ($this->_tpl_vars['diary_comment_history_url']) {
?>
<br>
<div align="right">
    <a class="ichiran" href="<?= $this->_tpl_vars['diary_comment_history_url'] ?>">
    <?= ACSMsg::get_msg("User", "DiaryCommentHistory.tpl.php", 'M003') ?>
    </a> &nbsp;
</div>
<?php
}
?>
