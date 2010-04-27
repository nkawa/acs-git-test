<?php
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: nakau  v 1.0 2008/02/27 		　                            |
// |メッセージ　入力画面                             　　　　　　　　　　 |
// +----------------------------------------------------------------------+
// $Id: Message.tpl.php,v 1.1 2008/03/24 07:09:27 y-yuki Exp $
?>

<div class="ttl">
<?php
// アクセス制限内で他人のメッセージならば、「○○さんのTOP」へ戻ることができる
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_Message_url'] ."\">";
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "Message.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	echo "</a> :: ";
	echo ACSMsg::get_msg("User", "Message.tpl.php",'M001');
?>
</div><br>


<?php
//確認画面からキャンセルで戻ってきた時の処理　情報回帰
	$value = '';
if($this->_tpl_vars['move_id'] == 3){
	$value['subject'] = $this->_tpl_vars['form']['subject'];
	$value['body'] = $this->_tpl_vars['form']['body'];
	if ($this->_tpl_vars['form']['info_mail'] == "on") {
		$value['info_mail'] = "checked";
	}
}
//返信ボタン押下時の処理
if($this->_tpl_vars['move_id'] == 4){
	$value['subject'] = "Re: ".$this->_tpl_vars['form']['subject'];
	$value['body'] = ">".str_replace("\n","\n>", $this->_tpl_vars['form']['body']);
}
?>
<br>
<br>
<?php
	// form
	echo "<form action=\"" . $this->_tpl_vars["action_url"] . "\" method=\"post\" name=\"message_form\">\n";
	// table
	echo "<table border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";
	echo "<tr>";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">" .ACSMsg::get_msg("User", "Message.tpl.php",'M002')."</td>";
	echo "<td bgcolor=\"#ffffff\"><a href=\"" .$this->_tpl_vars['link_page_url']['else_user_Message_url'] ."\">";
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg("User", "Message.tpl.php",'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	echo "</a></td>";
	echo "</tr>\n";
	echo "<tr>";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "Message.tpl.php",'M003')."</td>";
	echo "<td bgcolor=\"#ffffff\"><input type=\"text\" name=\"subject\" value=\"" .htmlspecialchars($value['subject'])  ."\" size=\"50\" style=\"width:400px\"></td>";
	echo "</tr>\n";
	echo "<tr>";
	echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg("User", "Message.tpl.php",'M004')."</td>";
	echo "<td bgcolor=\"#ffffff\"><textarea name=\"body\" cols=\"60\" rows=\"15\" style=\"width:600px\">";
	echo htmlspecialchars($value['body']);
	echo "</textarea></td>";
	echo "</tr>\n";


	echo "</table>\n";
	echo "<br>\n";
	echo "<input type=\"checkbox\" name=\"info_mail\" ".$value['info_mail'].">".ACSMsg::get_msg("User", "Message.tpl.php",'M005');
	echo "<br>\n";
	echo "<br>\n";
	echo "<input type=\"hidden\" name=\"acs_user_id\" value=\"".$this->_tpl_vars['acs_user_info_row']['user_community_id']."\">";

	// submit
	echo "<input type=\"submit\" value=\"".ACSMsg::get_msg("User", "Message.tpl.php",'M006')."\">\n";
	echo "</form>\n";
	echo "<br>\n";
	echo "<br>\n";
?>
