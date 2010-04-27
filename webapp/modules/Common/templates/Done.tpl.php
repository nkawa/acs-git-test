<?php
// $Id: Done.tpl.php,v 1.3 2006/03/01 05:25:59 kuwayama Exp $
?>

<span class="sub_title"><?= $this->_tpl_vars['title'] ?></span><br>
<br>

<?= $this->_tpl_vars['message'] ?><br>
<br>

<?php
$str = '';
foreach ($this->_tpl_vars['link_row_array'] as $link_row) {
	if ($str != '') {
		$str .= '&nbsp;&nbsp;';
	}
	$str .= "<a href=\"" .$link_row['url']. "\">" . htmlspecialchars($link_row['link_name']) . "</a>";
}
echo "$str<br>\n";
?>
