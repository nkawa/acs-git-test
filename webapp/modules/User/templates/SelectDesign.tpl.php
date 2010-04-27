<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: teramoto 2007/3 ver1.0                                      |
// | デザイン選択画面                                  　　　　 		  |
// +----------------------------------------------------------------------+
// $Id: SelectDesign.tpl.php,v 1.1 2007/03/27 02:12:43 w-ota Exp $
?>

<!-- HTML -->
<div class="ttl">
<a href="<?= $this->_tpl_vars['top_page_url'] ?>"> <?= ACSMsg::get_msg("User", "SelectDesign.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("User", "SelectDesign.tpl.php",'M002') ?>
</div>
<br>
<?= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "SelectDesign.tpl.php",'TITLE'),array(
        "{USER_NAME}" => htmlspecialchars($this->_tpl_vars['acs_user_info_row']['community_name']))) ?>
<br>
<?= ACSMsg::get_msg("User", "SelectDesign.tpl.php",'M010') ?><br>
<?php
ACSTemplateLib::print_error_message($this->_tpl_vars['error_message']);
?>

<form name="design_select" action="<?= $this->_tpl_vars['action_url'] ?>" method="post">
<table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
<tr>
<td id="myttl" bgcolor="#DEEEBD" align="center"><?= ACSMsg::get_msg("User", "SelectDesign.tpl.php",'M003') ?></td>
<td id="myttl" bgcolor="#DEEEBD" align="center"><?= ACSMsg::get_msg("User", "SelectDesign.tpl.php",'M004') ?></td>
<td id="myttl" bgcolor="#DEEEBD" align="center"><?= ACSMsg::get_msg("User", "SelectDesign.tpl.php",'M005') ?></td>
<td id="myttl" bgcolor="#DEEEBD" align="center"><?= ACSMsg::get_msg("User", "SelectDesign.tpl.php",'M006') ?></td>
</tr>
<?php	
foreach($this->_tpl_vars['select_design_row_array'] as $selectdesign_row){
  if ($selectdesign_row['show_list'] == 'yes') {
?>

    <tr>
      <td bgcolor="#FFFFFF">
        <?php 
        if ($selectdesign_row['thumbnail']) {
        ?>
          <img src="<?= $this->_tpl_vars['style_url'] ?>/<?= $selectdesign_row['thumbnail'] ?>">
        <?php 
        }
        ?>
      </td>
      <td bgcolor="#FFFFFF">
        <input type="radio" name="css_file" value="<?= $selectdesign_row['filename'] ?>"
         <?= ($this->_tpl_vars['selection_css']==$selectdesign_row['filename'] ? 'CHECKED' : '') ?>>
      </td>
      <td bgcolor="#FFFFFF">
        <?= nl2br($selectdesign_row['name']) ?>
      </td>
      <td bgcolor="#FFFFFF">
        <?= nl2br($selectdesign_row['description']) ?>
      </td>
    </tr>

<?php
  }
}
?>
</table>
<br>
<input type="submit" value="<?= ACSMsg::get_msg("User", "SelectDesign.tpl.php",'M011') ?>">&nbsp;
<input type="button" value="<?= ACSMsg::get_msg("User", "SelectDesign.tpl.php",'M012') ?>" onclick="location.href='<?= $this->_tpl_vars['top_page_url'] ?>'">
</form>
