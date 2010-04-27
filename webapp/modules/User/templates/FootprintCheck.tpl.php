<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: teramoto 2007/3 ver1.0                                      |
// | 足跡確認画面                                  　　　　 			  |
// +----------------------------------------------------------------------+
// $Id: FootprintCheck.tpl.php,v 1.1 2007/03/27 02:12:43 w-ota Exp $
?>

<!-- HTML -->
<div class="ttl">
<a href="<?= $this->_tpl_vars['top_page_url'] ?>"> <?= ACSMsg::get_msg("User", "FootprintCheck.tpl.php",'M001') ?></a> :: <?= ACSMsg::get_msg("User", "FootprintCheck.tpl.php",'M002') ?>
</div>
<br>
<?= ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "FootprintCheck.tpl.php",'TITLE'),array(
        "{USER_NAME}" => htmlspecialchars($this->_tpl_vars['acs_user_info_row']['community_name']))) ?>
<br><br>

<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);

if (count($this->_tpl_vars['footprint_info_row_array']) > 0) {
?>

  <table class="common_table" border="0" cellpadding="6" cellspacing="1" bgcolor="#99CC33">
  <tr>
  <td id="myttl" bgcolor="#DEEEBD" align="center"><?= ACSMsg::get_msg("User", "FootprintCheck.tpl.php",'M003') ?></td>
  <td id="myttl" bgcolor="#DEEEBD" align="center"><?= ACSMsg::get_msg("User", "FootprintCheck.tpl.php",'M004') ?></td>
  <td id="myttl" bgcolor="#DEEEBD" align="center"><?= ACSMsg::get_msg("User", "FootprintCheck.tpl.php",'M005') ?></td>
  <td id="myttl" bgcolor="#DEEEBD" align="center"><?= ACSMsg::get_msg("User", "FootprintCheck.tpl.php",'M006') ?></td>
  </tr>
  <?php	
  
  foreach($this->_tpl_vars['footprint_info_row_array'] as $footprint_row){
  
	  echo '<tr>';
  
	  // アクセス日時
	  echo '<td bgcolor="#FFFFFF">' . $footprint_row['post_date_disp'] . '</td>';
	  // アクセス者
	  echo '<td bgcolor="#FFFFFF">';
	  echo '<a href="' . $footprint_row['visitor_url'] . '">' . $footprint_row['community_name'] . '</a>';
	  echo '</td>';
	  // コンテンツ種別
	  echo '<td bgcolor="#FFFFFF">' . $footprint_row['contents_type_name'] . '</td>';
	  // コンテンツ情報
	  echo '<td bgcolor="#FFFFFF">';
	  echo '<a href="' . $footprint_row['contents_link_url'] . '">' . $footprint_row['contents_title'] . '</a>';
	  echo ' ' . $footprint_row['contents_date_disp'];
	  echo '</td>';
  
	  echo '</tr>';
  }
  ?>
  </table>
<?php
} else {
	echo ACSMsg::get_msg("User", "FootprintCheck.tpl.php",'M010');
}
?>
