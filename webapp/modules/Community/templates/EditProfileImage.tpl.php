<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4	    												  |
// | Authors: akitsu		 2006/2/15									  |
// +----------------------------------------------------------------------+
//　EditProfileImage.tpl.php
// $Id: EditProfileImage.tpl.php,v 1.5 2006/12/08 05:06:36 w-ota Exp $
//
// @image_new_mode = 画像情報の登録がない　not　登録がある
?>

<SCRIPT language="JavaScript">
<!--
// フォームに入力があったらアップロードボタンを有効とする
	function fmTurn(){
			// 使えるファイルならばボタンを有効とする
	    document.upload_file.upload_image.disabled = false;
/*
	　　// ファイルのサイズ確認用
	    var objid=document.getElementById('view_image');
	    objid.src='file://'+document.upload_file.new_file.value;
*/
	}
/*
// サーバに接続する前にファイルサイズを確認しておくscript
  function imagesize() {
    var objid=document.getElementById('view_image');
    var wid=document.getElementById('w');
    var hid=document.getElementById('h');
    var imgwidth=objid.width;
    var imgheight=objid.height;
    wid.innerHTML=imgwidth;
    hid.innerHTML=imgheight;
  }
*/
//-->
</SCRIPT>

<div class="sub_title"><?= ACSMsg::get_msg("Community", "EditProfileImage.tpl.php",'M001') ?></div>

   <table>
   <!-- 画像情報 -->
	 <tr align="center"><td>
	   <img src="<?=$this->_tpl_vars['profile']['image_url'] ?>" style="margin-top:10px;margin-bottom:10px" id="view_image"><br>
	 </td></tr>

   <!-- 削除リンク -->
	 <tr align="center"><td>
	  <?php
     /* 削除パス情報出力 */
        // 削除意思を確認する
		$path="";	 
		if(!$this->_tpl_vars['menu']['image_new_mode']){
				$path = '<a href="';
				$path .= $this->_tpl_vars['menu']['delete_image_url'];
				$path .='">';
				$path .= ACSMsg::get_msg("Community", "EditProfileImage.tpl.php",'M002').'</a>';
		}
		//画面に表示
		print $path . "\n";
	  ?>
	 </td></tr>
   </table>

		<p>
		  <form name="upload_file" action="<?= $this->_tpl_vars['upload_image_url'] ?>" method="POST" enctype="multipart/form-data">
			<input type="file" name="new_file" size="30" onchange="fmTurn()">
			<input type="submit" name="upload_image" value="<?= ACSMsg::get_msg("Community", "EditProfileImage.tpl.php",'M003') ?>" disabled>
		  </form>
		</p>
		<p>
		  <a href="<?= $this->_tpl_vars['back_url'] ?>"> <?= htmlspecialchars($this->_tpl_vars['profile']['community_name']) ?> <?= ACSMsg::get_msg("Community", "EditProfileImage.tpl.php",'M004') ?></a>
		</p>
