<?php
// +----------------------------------------------------------------------+
// | PHP version 5 & mojavi version 3                                     |
// +----------------------------------------------------------------------+
// | Authors: y-yuki v 1.0 2009/01/23 14:40:00                            |
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Authors: w-ota  v 1.5 2006/02/15 06:57:02                            |
// |         update: akitsu 2006/2/28 ver1.0                              |
// | Diary top画面                                                                  |
// +----------------------------------------------------------------------+
// $Id: Diary.tpl.php,v 1.25 2007/03/30 05:27:23 w-ota Exp $
?>

<div class="ttl">
<?php
if ($this->_tpl_vars['is_self_page']) {
	// 自分の日記ならば、リンクは無い
	echo ACSMsg::get_msg('User', 'Diary.tpl.php', 'M001');
} else {
	// アクセス制限内で他人の日記ならば、「○○さんのTOP」へ戻ることができる
	echo "<a href=\"" .$this->_tpl_vars['link_page_url']['else_user_diary_url'] ."\">";
	echo ACSMsg::get_tag_replace(ACSMsg::get_msg('User', 'Diary.tpl.php', 'NAME'),array(
			"{USER_NAME}" => htmlspecialchars($this->_tpl_vars['target_user_info_row']['community_name'])));
	echo "</a> :: ";
	echo ACSMsg::get_msg('User', 'Diary.tpl.php', 'M002');
}
?>
<a href="<?= $this->_tpl_vars['link_page_url']['diary_rss_url'] ?>"><img src="<?php echo ACS_IMAGE_DIR . "rss.png" ?>" border=0></a>
</div>
<br>

<?php
// 確認画面からキャンセルで戻ってきた時の処理　情報回帰

$value = '';
if ($this->_tpl_vars['move_id'] == 3) {
	$value['subject'] = $this->_tpl_vars['form']['subject'];
	$value['body'] = $this->_tpl_vars['form']['body'];
}
?>

<?php
// ダイアリー検索画面の表示
echo "<a class=\"ichiran3\" href=\"" .$this->_tpl_vars['link_page_url']['search_diary_url'] ."\">";
echo ACSMsg::get_msg('User', 'Diary.tpl.php', 'M003');
echo "</a>";
?>
<br>
<br>
<?php
if ($this->_tpl_vars['is_self_page']) {

    // form
    echo "<form action=\"" .$this->_tpl_vars['action_url']. "\" method=\"post\" name=\"diary_form\" enctype=\"multipart/form-data\">\n";

    // table
    echo "<table border=\"0\" cellpadding=\"6\" cellspacing=\"1\" bgcolor=\"#99CC33\">\n";
    echo "<tr>";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg('User', 'Diary.tpl.php', 'M004')."</td>";
    echo "<td bgcolor=\"#ffffff\"><input type=\"text\" name=\"subject\" value=\"" .$value['subject']  ."\" size=\"50\" style=\"width:400px\"></td>";
    echo "</tr>\n";
    echo "<tr>";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg('User', 'Diary.tpl.php', 'M005')."</td>";
    echo "<td bgcolor=\"#ffffff\"><textarea name=\"body\" cols=\"60\" rows=\"15\" style=\"width:600px\">";
    echo htmlspecialchars($value['body']);
    echo "</textarea></td>";
    echo "</tr>\n";
    echo "<tr>";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg('User', 'Diary.tpl.php', 'M006')."</td>";
    echo "<td bgcolor=\"#ffffff\"><input type=\"file\" name=\"new_file\" size=50 ></td>";
    echo "</tr>\n";

    // 公開範囲
    echo "<tr>";
    echo "<td id=\"myttl\" bgcolor=\"#DEEEBD\">".ACSMsg::get_msg('User', 'Diary.tpl.php', 'M007')."</td>";
    echo "<td bgcolor=\"#ffffff\">";
    echo "<select name=\"open_level_code\" onchange=\"print_sub_menu(this)\">\n";

    // 選択状態をセットする
    unset($selected);
    foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
        if ($this->_tpl_vars['last_open_level_code']){
            $selected[$this->_tpl_vars['last_open_level_code']] = ' selected';
        } else if ($open_level_master_row['is_default'] == 't') {
            $selected[$open_level_master_row['open_level_code']] = ' selected';
            break;
        }
    }

    // プルダウンメニュー表示
    foreach ($this->_tpl_vars['open_level_master_row_array'] as $open_level_master_row) {
        echo "<option value=\"$open_level_master_row[open_level_code]\"{$selected[$open_level_master_row['open_level_code']]}>";
        echo htmlspecialchars($open_level_master_row['open_level_name']) . "\n";
    }
    echo "</select><br>\n";

    // 確認画面からキャンセルで戻ってきた時の処理　情報回帰
    if ($this->_tpl_vars['move_id'] == 3) {
        unset($selected);
        $selected[$this->_tpl_vars['form']['open_level_code']] = ' selected';
    }

    // マイフレンズグループ指定
    echo "<div id=\"trusted_community_div\"></div>";
    echo "</td>";
    echo "</tr>\n";

    echo "</table>\n";
    echo "<br>\n";

    // submit
    echo "<input type=\"submit\" value=\"".ACSMsg::get_msg('User', 'Diary.tpl.php', 'M008')."\">\n";
    echo "</form>\n";
    echo "<br>\n";
    echo "<br>\n";
}
?>


<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>

<script language="JavaScript">
<!--
<?php
if ($this->_tpl_vars['is_self_page']) {
?>
    window.onload = function () {
        // 公開範囲のデフォルト選択
        select_obj = document.forms["diary_form"].elements["open_level_code"];
        selected_open_level_name = select_obj.options[select_obj.selectedIndex].text
        if (selected_open_level_name == '<?= ACSMsg::get_mst('open_level_master','D05') ?>') {
            print_sub_menu(select_obj);
        }
    }
<?
}
?>

// マイフレンズグループの連想配列
trusted_community_row_array = new Array(
<?php
$str = '';
foreach ($this->_tpl_vars['friends_group_row_array'] as $friends_group_row) {
    if ($str != '') {
        $str .= ", ";
    }
    $str .= "";
    $str .= "{";
    $str .= "\"community_id\" : \"$friends_group_row[community_id]\", ";
    $str .= "\"community_name\" : \"$friends_group_row[community_name]\", ";
    $str .= "\"top_page_url\" : \"$friends_group_row[top_page_url]\"";
    $str .= "}";
}
echo $str;
?>
);

// 友人に公開のサブメニューを表示する
function print_sub_menu(select_obj) {
    selected_open_level_name = select_obj.options[select_obj.selectedIndex].text

    div_obj = document.getElementById("trusted_community_div");

    while (div_obj.hasChildNodes()) {
        div_obj.removeChild(div_obj.firstChild);
    }

    if (selected_open_level_name == "<?= ACSMsg::get_mst('open_level_master','D05') ?>") {
        // <table>
        table_obj = document.createElement("table");
        table_obj.className = "layout_table";

        // <tr>
        new_row = table_obj.insertRow(0);
        // <td>
        new_cell = new_row.insertCell(0);
        // <input>
        if (document.all) {
            input_obj = document.createElement('<input name="trusted_community_flag">');
        } else {
            input_obj = document.createElement("input");
            input_obj.name = "trusted_community_flag";
        }
        input_obj.type = "radio";
        input_obj.value = "0";
        input_obj.defaultChecked = true;
        input_obj.onclick = function () {
            if (this.form.elements["trusted_community_id_array[]"]) {
                if (this.form.elements["trusted_community_id_array[]"].value) {
                    // 1つのチェックボックス
                    this.form.elements["trusted_community_id_array[]"].checked = false;
                } else {
                    // 複数のチェックボックス
                    for (i = 0; i < this.form.elements["trusted_community_id_array[]"].length; i++) {
                        this.form.elements["trusted_community_id_array[]"][i].checked = false;
                    }
                }
            }
        }
        new_cell.appendChild(input_obj);
        // </td>

        // <td>
        new_cell = new_row.insertCell(1);
        // text
        new_cell.appendChild(document.createTextNode("<?= ACSMsg::get_msg('User', 'Diary.tpl.php', 'M009') ?>"));
        new_cell.appendChild(document.createElement("br"));
        // </td>
        // </tr>

        // <tr>
        new_row = table_obj.insertRow(1);
        // <td>
        new_cell = new_row.insertCell(0);
        new_cell.setAttribute("vAlign", "top");
        if (document.all) {
            // <input>
            input_obj = document.createElement('<input name="trusted_community_flag">');
        } else {
            // <input>
            input_obj = document.createElement("input");
            input_obj.name = "trusted_community_flag";
        }
        input_obj.type = "radio";
        input_obj.value = "1";
        input_obj.defaultChecked = false;
        new_cell.appendChild(input_obj);
        // </td>
        
        // <td>
        new_cell = new_row.insertCell(1);
        // text
        new_cell.appendChild(document.createTextNode("<?= ACSMsg::get_msg('User', 'Diary.tpl.php', 'M010') ?>"));
        new_cell.appendChild(document.createElement("br"));
        for (i = 0; i < trusted_community_row_array.length; i++) {
            // <input>
            if (document.all) {
                input_obj = document.createElement('<input name="trusted_community_id_array[]">');
            } else {
                input_obj = document.createElement("input");
                input_obj.name = "trusted_community_id_array[]";
            }
            input_obj.type = "checkbox";
            input_obj.value = trusted_community_row_array[i]["community_id"];
            input_obj.defaultChecked = false;
            input_obj.onclick = function () {
                // 各マイフレンズグループのチェックボックスがクリックされたとき、
                // 「マイフレンズグループ」のラジオボタンを選択状態にする
                this.form.elements["trusted_community_flag"][1].checked = true;
            }
            new_cell.appendChild(input_obj);
            // text
            new_cell.appendChild(document.createTextNode(trusted_community_row_array[i]["community_name"]));
            new_cell.appendChild(document.createElement("br"));
        }
        // </td>
        // </tr>

        // </table>
        div_obj.appendChild(table_obj);
    }
}
// -->
</script>

<!--　全体の表示  -->
<table border="0" class="common_table"><tr><td valign="top">

<!-- まず日記の一覧 -->
<!-- <table border="1" rules='groups' class="commmon_table" cellspacing=0> 線の表示のため一時コメント化-->

<table class="commmon_table" border="0" cellspacing="0" rules="rows" width="400px">
<?php
foreach ($this->_tpl_vars['diary_row_array'] as $diary_row) {
    if($diary_row['diary_delete_flag']=='f'){            //削除フラグの無いもののみ表示する
    //echo "<THEAD><tr bgcolor='#ccffcc'>\n";
    echo "<tr bgcolor='#deeebd'>\n";
    echo "<td>\n";
    //タイトル
    if ($diary_row['diary_delete_flag']=='f') {
        echo "<b>" .htmlspecialchars($diary_row['subject']) . "</b>";
    } else {
        echo "<b>deleted</b>";
    }
    // 記入日
    echo "&nbsp;&nbsp;&nbsp;" . $diary_row['post_date'] . "\n</td>\n";

    // 削除ボタン
    echo "<td align=\"right\" valign='top'>\n";
    echo "<form>\n";
    // 削除ボタンの表示　＋自分
    if ($this->_tpl_vars['is_self_page'] && $diary_row['diary_delete_flag']=='f') {
        echo "<input type=\"button\" value=\"".ACSMsg::get_msg('User', 'Diary.tpl.php', 'M011')."\" onclick=\"location.href='$diary_row[diary_delete_url]'\">";
    }
    echo "\n</form>";
    echo "\n</td>";
    echo "</tr>\n";

// 本文部分（削除フラグの無いもののみ表示）
    echo "<tr>\n";
    echo "<td colspan=2>\n";
    // 公開範囲
    echo "<table class=\"open_level_table\">\n<tr>\n<td>\n";
    echo ACSMsg::get_msg('User', 'Diary.tpl.php', 'M007')." : " . htmlspecialchars($diary_row['open_level_name']);
    if ($this->_tpl_vars['is_self_page'] && $diary_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D05')
        && $diary_row['trusted_community_flag']) {
        if (count($diary_row['trusted_community_row_array'])) {
            $trusted_community_str = '';
            foreach ($diary_row['trusted_community_row_array'] as $trusted_community_row) {
                if ($trusted_community_row['community_name'] != '') {
                    if ($trusted_community_str != '') {
                        $trusted_community_str .= ', ';
                    }
                    $trusted_community_str .= $trusted_community_row['community_name'];
                }
            }
            echo " (" . htmlspecialchars($trusted_community_str) . ")";
        } else {
            echo " ".ACSMsg::get_msg('User', 'Diary.tpl.php', 'M012');
        }
    }
    echo "</td>\n";
    echo "</tr>\n</table>\n";

    // 写真
    if ($diary_row['file_url']) {
        echo "<a href=\"javascript:w=window.open('" . $diary_row['file_url_alink'] . "','popUp','scrollbars=yes,resizable=yes');w.focus();\">\n";
        echo "<img src=\"". $diary_row['file_url'] . "\" style=\"margin-top:10px;margin-bottom:10px\" BORDER=0></a><br>\n";
    }
    // 本文
    echo nl2br(ACSTemplateLib::auto_link(ACSLib::sp2nbsp(htmlspecialchars($diary_row['body']))));
    // コメントリンク
    echo "\n<div align=\"right\">\n";
    echo "<a href=\"" . $diary_row['diary_comment_url'] . "\">".ACSMsg::get_msg('User', 'Diary.tpl.php', 'M013')."(" . $diary_row['diary_comment_num'] . ")</a>\n";
    echo "</div>\n";
    echo "</tr>\n";
    }
}
?>
</table>
</td>
<!-- ここまで日記の一覧 -->
<td><br></td>
<!-- ここから日記の存在 -->
<td valign ="top">
<table border="0"><tr valign ="top"><td>
<!-- Calendar表示 -->
<?= $this->_tpl_vars['DiaryCalendar'] ?>
</td></tr>

<tr><td><br><br></td></tr>
<tr><td>
<?php
if(!$this->_tpl_vars['diary_row_array']){
    echo ACSMsg::get_msg('User', 'Diary.tpl.php', 'M014');
}else{
    echo "<b>".ACSMsg::get_msg('User', 'Diary.tpl.php', 'M015')."</b><br><br>";
    
    // スレッド一覧
    $str = '';
    foreach ($this->_tpl_vars['diary_row_array'] as $index => $diary_row) {
        if($diary_row['diary_delete_flag']=='f'){            //削除フラグの無いもののみ一覧表示する
            if ($str != '') {
                $str .= "<br>";
            }
            $str .= "<a href=\"" . $diary_row['diary_comment_url'] . "\">";
            $str .= htmlspecialchars(ACSTemplateLib::trim_long_str($diary_row['subject']));
            $str .= "</a>";
            $str .= " (" . $diary_row['short_post_date'] . ")";
        }
    }
    echo $str;
}
?>
</td></tr>
</table>

<!-- 表示全体の終了 -->
</td>
</tr></table>
<br>

<?php
// ページング表示
ACSTemplateLib::print_paging_link($this->_tpl_vars['paging_info']);
?>
