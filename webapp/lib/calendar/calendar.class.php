<?
class calendar {
		
	var $wfrom;
	var $beforeandafterday;
		
	var $link = array();
	var $style = array();
		
	var $kind;
	var $bgcolor;
	var $week;
	var $holiday;
	var $holiday_name;

	var $move_arrow;
	var $str_url;

	/**
	 * コンストラクタ
	 *
	 * @param int $arg1
	 * @param int $arg2
	 * @return void
	 */
	function calendar($arg1 = 0, $arg2 = 0) {
			
		// 開始曜日（0-日曜, 6-土曜）
		$this->wfrom = $arg1;
			
		// 当月以外の日付を表示するかどうか（0-表示しない 1-表示する）
		$this->beforeandafterday = $arg2;
			
		// --- 以下、表示設定 ---
		// スタイルの設定
		$this->style["table"] = " class=\"calendar\"";
		$this->style["th"] = "";
		$this->style["tr"] = "";
		$this->style["td"] = "";
		$this->style["tf"] = " class=\"tf\"";
			
		// 曜日に対する背景色の設定（0-平日, 1-土, 2-日祝日, 3-当月以外の平日, 4-当日）
		$this->kind = array(2, 0, 0, 0, 0, 0, 1);
		$this->bgcolor = array("#eeeeee", "#ccffff", "#ffcccc", "#ffffff", "#ffffcc");
			
		// 曜日の名前
		$this->week = array(ACSMsg::get_mdmsg(__FILE__,'M001'), ACSMsg::get_mdmsg(__FILE__,'M002'), ACSMsg::get_mdmsg(__FILE__,'M003'), ACSMsg::get_mdmsg(__FILE__,'M004'), ACSMsg::get_mdmsg(__FILE__,'M005'), ACSMsg::get_mdmsg(__FILE__,'M006'), ACSMsg::get_mdmsg(__FILE__,'M007'));
			
	}
		
	/**
	 * 設定された内容でカレンダーを表示します
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 */
	function show_calendar($year, $month, $day = 0) {
		// 休日の算出
		if(!isset($this->set_holiday)) $this->set_holiday($year, $month);
			
		// その月の開始とする数値を取得
		$from = 1;
		while(date("w", mktime(0, 0, 0, $month, $from, $year)) <> $this->wfrom) {
			$from--;
		}
			
		// 前月と次月の年月を取得
		list($ny, $nm, $nj) = explode("-", date("Y-n-j", mktime(0, 0, 0, $month+1, 1, $year)));
		list($by, $bm, $bj) = explode("-", date("Y-n-j", mktime(0, 0, 0, $month-1, 1, $year)));
			
		// 当日取得
		$date_array = getdate();

		// 今月計算
		$curr_year = $date_array['year'];
		$curr_month = $date_array['mon'];
		// 前月計算
		$prev_year = $year;
		$prev_month = $month - 1;
		if ($prev_month < 1) {
			$prev_month = 12;
			$prev_year = $year - 1;
			if ($prev_year < 1) {
				$prev_year = 1;
				$prev_month = 1;
			}
		}
		// 次月計算
		$next_year = $year;
		$next_month = $month + 1;
		if ($next_month > 12) {
			$next_month = 1;
			$next_year = $year + 1;
		}
			
		$strCal = "";
		// 表示開始
		$strCal = "<table>\n";
		$strCal = $strCal . "<tr>\n";
		$strCal = $strCal . "<th".$this->style["th"]." colspan=\"7\">\n";
		// 前月へ
		$strCal = $strCal . "<a href=\"" . $this->str_url . "&year=$prev_year&month=$prev_month\">" 
				. ACSMsg::get_msg('lib','calendar.class.php','M030')  . "</a>\n";
		// 表示月
		//$strCal = $strCal . "  " . $year . "年" . $month . "月  \n";
		$strCal = $strCal . "  " . ACSMsg::get_tag_replace(ACSMsg::get_msg('lib','calendar.class.php','YEAR_MONTH'),
				array("{YEAR}" => $year, "{MONTH}" => $month));

		// 次月へ
		$strCal = $strCal . "<a href=\"" . $this->str_url . "&year=$next_year&month=$next_month\">"
				. ACSMsg::get_msg('lib','calendar.class.php','M031')  . "</a>\n";

		$strCal = $strCal . "</th>\n";
		$strCal = $strCal . "</tr>\n";
			
		// 曜日表示
		$strCal = $strCal . "<tr".$this->style["tr"]." style=\"text-align:center\">\n";
		for($i = 0; $i < 7; $i++) {
			$wk = ($this->wfrom + $i) % 7;
			$strCal = $strCal . "<td".$this->style["td"]." bgcolor=\"".$this->bgcolor[$this->kind[$wk]]."\">".$this->week[$wk]."</td>\n";
		}
		$strCal = $strCal . "</tr>\n";
					
			
		// $dayがその月の日数を超えるまでループ
		$tday = $from;
		$mday = ACSLib::get_end_day($year, $month);
		while($tday <= $mday) {
			$strCal = $strCal . "<tr".$this->style["tr"]." style=\"text-align:right\">\n";
				
			for($i = 0; $i < 7; $i++) {
				$fstyle = "";
				$wk = ($this->wfrom + $i) % 7;
				$bgcolor = $this->bgcolor[$this->kind[$wk]];
					
				// 当月判定
				if($tday >= 1 && $tday <= $mday) {
					if($date_array["year"] == $year && $date_array["mon"] == $month && $date_array["mday"] == $tday) {
						// 当日
						$bgcolor = $this->bgcolor[4];
					} else if($this->holiday[$tday] == 1) {
						// 祝日
						$bgcolor = $this->bgcolor[2];
					}
						
					// 指定日
					if($day == $tday) {
						$fstyle = " style=\"font-weight:bold\"";
					}
				} else {
					// 当月以外の平日
					if($wk > 0 && $wk < 6) $bgcolor = $this->bgcolor[3];
				}
					
				$strCal = $strCal . "<td".$this->style["td"]." bgcolor=\"".$bgcolor."\"".$fstyle.">\n";
				list($lyear, $lmonth, $lday) = explode("-", date("Y-n-j", mktime(0, 0, 0, $month, $tday, $year)));
					
				// データ部分表示
				if(($tday >= 1 && $tday <= $mday) || $this->beforeandafterday) {
					if(isset($this->link[$tday])) {
						$strCal = $strCal . "<a href=\"".$this->link[$tday]["url"]."\" title=\"".$this->link[$tday]["title"]."\">".$lday."</a>"; 
					} else {
						$strCal = $strCal . $lday;
					}
					$strCal = $strCal . "</td>\n";
				} else {
					$strCal = $strCal . "&nbsp;";
				}
					
				$tday++;
			} 
			
			$strCal = $strCal . "</tr>\n";	
		}
			
		$strCal = $strCal . "</table>\n";

		// 今月へ戻るリンク
		$strCal .= "<br>\n";
		$strCal .= "<div><a href=\"$this->str_url\">".ACSMsg::get_mdmsg(__FILE__,'M010')."</a></div>\n";

		return $strCal;
	}
		
	/**
	 * 指定された日に対してリンクを設定します。
	 *
	 * @param int $day
	 * @param string $url
	 * @param string $title
	 */
	function set_link($day, $url, $title) {
		$this->link[$day]["url"] = $url;
		$this->link[$day]["title"] = $title;
	}
		
	/**
	 * 現在設定されているリンクを全て解除します。
	 *
	 */
	function clear_link() {
		$this->link = array();
	}

	/**
	 * 次月・前月のリンク先をセット
	 */
	function set_str_url($set_url) {
		$this->str_url = $set_url;
	}
	/**
	 * 休日の計算を行います。
	 * （休日名もセットしていますが、現在は出力していません。）
	 *
	 * @param int $year
	 * @param int $month
	 */
	function set_holiday($year, $month) {
			
		// その月の最初の月曜日が何日かを算出
		$day = 1;
		while(date("w",mktime(0 ,0 ,0 , $month, $day, $year)) <> 1 && checkdate($month, $day, $year)) {
			$day++;
		}
	
		// 祝日をセット
		switch($month){
				
		case 1:
			// 元旦
			$this->holiday[1] = 1;
		$this->holiday_name[1] = ACSMsg::get_mdmsg(__FILE__,'M011'); 
					
		// 成人の日
		if($year < 2000) {
			$this->holiday[15] = 1;
			$this->holiday_name[15] = ACSMsg::get_mdmsg(__FILE__,'M012'); 
		} else {
			$this->holiday[$day+7] = 1;
			$this->holiday_name[$day+7] = ACSMsg::get_mdmsg(__FILE__,'M012'); 
		}
		break;
					
		case 2:
			// 建国記念日
			$this->holiday[11] = 1;
		$this->holiday_name[11] = "建国記念日"; 
		break;
					
		case 3:
			// 春分の日
			if($year > 1979 && $year < 2100) {
				$tmp = floor(20.8431+($year-1980)*0.242194-floor(($year-1980)/4));
				$this->holiday[$tmp] = 1;
				$this->holiday_name[$tmp] = ACSMsg::get_mdmsg(__FILE__,'M013'); 
			}
			break;
					
		case 4:
			// 天皇誕生日 or みどりの日
			$this->holiday[29] = 1;
		if($year < 1989) {
			$this->holiday_name[29] = ACSMsg::get_mdmsg(__FILE__,'M014');
		} else {
			$this->holiday_name[29] = ACSMsg::get_mdmsg(__FILE__,'M015');
		} 
		break;
					
		case 5:
			// 憲法記念日
			$this->holiday[3] = 1;
		$this->holiday_name[3] = ACSMsg::get_mdmsg(__FILE__,'M016');
					
		// 子どもの日
		$this->holiday[5] = 1;
		$this->holiday_name[5] = ACSMsg::get_mdmsg(__FILE__,'M017');
		break;
					
		case 7:
			// 海の日
			if($year > 2002) {
				$this->holiday[$day+14] = 1;
				$this->holiday_name[$day+14] = ACSMsg::get_mdmsg(__FILE__,'M018');
			} elseif($year > 1994) {
				$this->holiday[21] = 1;
				$this->holiday_name[21] = ACSMsg::get_mdmsg(__FILE__,'M018');
			}
			break;
	
		case 9:
			// 敬老の日
			if($year < 2003) {
				$this->holiday[15] = 1;
				$this->holiday_name[15] = ACSMsg::get_mdmsg(__FILE__,'M020');
			} else {
				$this->holiday[$day+14] = 1;
				$this->holiday_name[$day+14] = ACSMsg::get_mdmsg(__FILE__,'M020');
			}
					
			// 秋分の日
			if($year > 1979 && $year < 2100) {
				$tmp = floor(23.2488+($year-1980)*0.242194-floor(($year-1980)/4));
				$this->holiday[$tmp] = 1;
				$this->holiday_name[$tmp] = ACSMsg::get_mdmsg(__FILE__,'M022');
			}
			break;
					
		case 10;
// 体育の日
 if($year < 2000) {
	 $this->holiday[10] = 1;
	 $this->holiday_name[10] = ACSMsg::get_mdmsg(__FILE__,'M023');
 } else {
	 $this->holiday[$day+7] = 1;
	 $this->holiday_name[$day+7] = ACSMsg::get_mdmsg(__FILE__,'M023');
 }
 break;
	
		case 11:
			// 文化の日
			$this->holiday[3] = 1;
		$this->holiday_name[3] = ACSMsg::get_mdmsg(__FILE__,'M025');
					
		// 勤労感謝の日
		$this->holiday[23] = 1;
		$this->holiday_name[23] = ACSMsg::get_mdmsg(__FILE__,'M026');
		break;
					
		case 12:			
			// 天皇誕生日
			if($year > 1988) {
				$this->holiday[23] = 1;
				$this->holiday_name[23] = ACSMsg::get_mdmsg(__FILE__,'M014');
			}
			break;	
		}
			
		// 国民の休日をセット
		if($year > 1985) {
			for($i = 1;$i < ACSLib::get_end_day($year, $month);$i++) {
				if(isset($this->holiday[$i]) && isset($this->holiday[$i+2])) {
					$this->holiday[$i+1] = 1;
					$this->holiday_name[$i+1] = ACSMsg::get_mdmsg(__FILE__,'M028');
					$i = $i + 3;
				}
			}
		}
			
		// 振り替え休日をセット
		$sday = $day - 1;
		if($sday == 0) $sday = 7;
		for($i = $sday;$i < ACSLib::get_end_day($year, $month);$i = $i + 7) {
			if(isset($this->holiday[$i])) {
				$this->holiday[$i+1] = 1;
				$this->holiday_name[$i+1] = ACSMsg::get_mdmsg(__FILE__,'M029');
			}
		}
	}
}




?>
